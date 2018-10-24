<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2015-04-07
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api;


class Cron
{
    public const JOB_SCHEDULE_CHECK_INTERVAL = 5; // check for jobs every 5 seconds

    private $table = array();
    private $last_update = '';

    public static function run() : void
    {
        // the scheduler script uses UTC as its timezone
        date_default_timezone_set('UTC');

        // run the loop once
        $scheduler = new static();
        $scheduler->loop(1);
    }

    public function loop(int $count = null) : void
    {
        $lastkey = '';

        $loop_count = 0;
        while (true)
        {
            if (isset($count) && $loop_count >= $count)
                break;

            $loop_count++;

            $dt = getdate();
            $hour = $dt['hours'];
            $minute = $dt['minutes'];
            $second = $dt['seconds'];
            $timekey = sprintf("%02d%02d", $hour, $minute);
            echo "Tick. $timekey\n";


            if ($lastkey != $timekey)
            {
                $lastkey = $timekey;
                print("New minute.\n");

                $this->refreshSchedulerTable();

                if (array_key_exists($timekey, $this->table))
                {
                    foreach ($this->table[$timekey] as $job)
                    {
                        // check days-of-week
                        if (isset($job['days']) && is_array($job['days']) && count($job['days']) > 0)
                        {
                            $run = false;

                            $tz = isset($job['timezone']) ? $job['timezone'] : 'UTC';

                            // what day of the week is it in the job's time zone?
                            $parts = \Flexio\Base\Util::getDateTimeParts(time(), $tz);
                            $weekdays = ['sun','mon','tue','wed','thu','fri','sat'];
                            $weekday = $weekdays[$parts['nweekday'] % 7];
                            if (in_array($weekday, $job['days']))
                            {
                                // current day has been found in the run days, so run this job
                                $run = true;
                            }

                            // see if the job is supposed to run on the first, 15th, or last day of the month
                            if (in_array(''.$parts['mday'], $job['days']))
                            {
                                // current day has been found in the run days, so run this job
                                $run = true;
                            }

                            if (in_array('last', $job['days']))
                            {
                                $last_month_days = array(31,28,31,30,31,30,31,31,30,31,30,31);
                                $last_month_day = 31;
                                if ($parts['mon'] >= 1 && $parts['mon'] <= 12)
                                    $last_month_day = $last_month_days[$parts['mon'] - 1];
                                if ($parts['mday'] == $last_month_day)
                                    $run = true;
                            }

                            if (!$run)
                                continue;
                        }


                        print("running job...");
                        $pipe_eid = $job['pipe_eid'];
                        self::runPipe($pipe_eid);
                        print("\n");
                    }
                }

                if (($second < self::JOB_SCHEDULE_CHECK_INTERVAL) && array_key_exists(\Flexio\Base\Schedule::FREQ_ONE_MINUTE, $this->table))
                {
                    foreach ($this->table[\Flexio\Base\Schedule::FREQ_ONE_MINUTE] as $job)
                    {
                        print("running job...");
                        $pipe_eid = $job['pipe_eid'];
                        self::runPipe($pipe_eid);
                        print("\n");
                    }
                }

                if (($minute % 5 == 0) && array_key_exists(\Flexio\Base\Schedule::FREQ_FIVE_MINUTES, $this->table))
                {
                    foreach ($this->table[\Flexio\Base\Schedule::FREQ_FIVE_MINUTES] as $job)
                    {
                        print("running job...");
                        $pipe_eid = $job['pipe_eid'];
                        self::runPipe($pipe_eid);
                        print("\n");
                    }
                }

                if (($minute % 15 == 0) && array_key_exists(\Flexio\Base\Schedule::FREQ_FIFTEEN_MINUTES, $this->table))
                {
                    foreach ($this->table[\Flexio\Base\Schedule::FREQ_FIFTEEN_MINUTES] as $job)
                    {
                        print("running job...");
                        $pipe_eid = $job['pipe_eid'];
                        self::runPipe($pipe_eid);
                        print("\n");
                    }
                }

                if (($minute % 30 == 0) && array_key_exists(\Flexio\Base\Schedule::FREQ_THIRTY_MINUTES, $this->table))
                {
                    foreach ($this->table[\Flexio\Base\Schedule::FREQ_THIRTY_MINUTES] as $job)
                    {
                        print("running job...");
                        $pipe_eid = $job['pipe_eid'];
                        self::runPipe($pipe_eid);
                        print("\n");
                    }
                }

                if ($minute == 0 && array_key_exists(\Flexio\Base\Schedule::FREQ_HOURLY, $this->table))
                {
                    foreach ($this->table[\Flexio\Base\Schedule::FREQ_HOURLY] as $job)
                    {
                        print("running job...");
                        $pipe_eid = $job['pipe_eid'];
                        self::runPipe($pipe_eid);
                        print("\n");
                    }
                }
            }

            sleep(self::JOB_SCHEDULE_CHECK_INTERVAL);
        }
    }

    private function refreshSchedulerTable() : void
    {
        $pipe_model = \Flexio\System\System::getModel()->pipe;
        $current_update = $pipe_model->getLastSchedulerUpdateTime();

        print("Current update time " . $current_update . "; Last update time " . $this->last_update . "\n");
        if ($current_update == $this->last_update)
            return; // scheduler needs no updates
        $this->last_update = $current_update;

        print("Refreshing scheduler table due to new changes.\n");

        // clear out existing table
        $this->table = array();

        $rows = $pipe_model->getScheduledPipes();
        foreach ($rows as $row)
        {
            $pipe_eid = $row['eid'];
            $schedule = $row['schedule'];

            $schedule = @json_decode($schedule,true);
            if (is_null($schedule))
                continue;

            if (isset($schedule['enabled']) && ($schedule['enabled'] === 'false' || $schedule['enabled'] === false))
                continue;

            if (isset($schedule['frequency']))
            {
                if ($schedule['frequency'] == \Flexio\Base\Schedule::FREQ_ONE_MINUTE ||
                    $schedule['frequency'] == \Flexio\Base\Schedule::FREQ_FIVE_MINUTES ||
                    $schedule['frequency'] == \Flexio\Base\Schedule::FREQ_FIFTEEN_MINUTES ||
                    $schedule['frequency'] == \Flexio\Base\Schedule::FREQ_THIRTY_MINUTES ||
                    $schedule['frequency'] == \Flexio\Base\Schedule::FREQ_HOURLY)
                {
                    $timekey = $schedule['frequency'];

                    if (!array_key_exists($timekey, $this->table))
                        $this->table[$timekey] = array();

                    $tmp = $schedule;
                    $tmp['pipe_eid'] = $pipe_eid;
                    unset($tmp['times']);
                    $this->table[$timekey][] = $tmp;

                    continue;
                }
            }

            // make sure the timezone is valid; if it's empty or invalid, use UTC
            $timezone = isset($schedule['timezone']) ? $schedule['timezone'] : '';
            if (\Flexio\Base\Schedule::isValidTimeZone($timezone) === false || $timezone === '')
                $timezone = 'UTC';

            if (isset($schedule['times']) && is_array($schedule['times']))
            {
                foreach ($schedule['times'] as $time)
                {
                    if (!isset($time['hour']) || !isset($time['minute']))
                        continue;

                    // find out what the time is in UTC
                    $dt = new \DateTime('now', new \DateTimeZone($timezone));
                    $dt->setTime($time['hour'], $time['minute']);
                    $gmtime_parts = \Flexio\Base\Util::getDateTimeParts($dt->getTimestamp(), 'UTC');
                    $hour = $gmtime_parts['hours'];
                    $minute = $gmtime_parts['minutes'];

                    $timekey = sprintf("%02d%02d", $hour, $minute);

                    if (!array_key_exists($timekey, $this->table))
                        $this->table[$timekey] = array();

                    $tmp = $schedule;
                    $tmp['pipe_eid'] = $pipe_eid;
                    unset($tmp['times']);
                    $this->table[$timekey][] = $tmp;
                }
            }
        }
    }

    private static function runPipe(string $pipe_eid) : void
    {
        // TODO: following run code is similar to \1\Process::create()
        // should factor; for example, the \Flexio\Api\Process::create()
        // adds on the parent and owner

        // TODO: check permissions based on the owner of the pipe

        // STEP 1: load the pipe
        $pipe = false;
        $pipe_properties = false;
        try
        {
            $pipe = \Flexio\Object\Pipe::load($pipe_eid);
            if ($pipe->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
            $pipe_properties = $pipe->get();
        }
        catch (\Flexio\Base\Exception $e)
        {
            return;
        }

        $process_properties = array(
            'parent_eid' => $pipe_properties['eid'],
            'pipe_info' => $pipe_properties,
            'task' => $pipe_properties['task'],
            'triggered_by' => \Model::PROCESS_TRIGGERED_SCHEDULER,
            'owned_by' => $pipe_properties['owned_by']['eid'],
            'created_by' => $pipe_properties['owned_by']['eid'] // scheduled processes are created by the owner
        );

        // STEP 2: create the process
        $process = \Flexio\Object\Process::create($process_properties);
        $process->setRights($pipe->getRights());

        // STEP 3: run the process
        $engine = \Flexio\Jobs\StoredProcess::create($process);
        $engine->run();
    }
}
