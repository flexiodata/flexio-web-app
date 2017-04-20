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
 * @subpackage Object
 */


declare(strict_types=1);
namespace Flexio\Object;


class Scheduler
{
    const JOB_SCHEDULE_CHECK_INTERVAL = 5; // check for jobs every 5 seconds

    const FREQ_ONE_MINUTE      = 'one-minute';
    const FREQ_FIVE_MINUTES    = 'five-minutes';
    const FREQ_FIFTEEN_MINUTES = 'fifteen-minutes';
    const FREQ_THIRTY_MINUTES  = 'thirty-minutes';
    const FREQ_HOURLY          = 'hourly';
    const FREQ_DAILY           = 'daily';
    const FREQ_WEEKLY          = 'weekly';
    const FREQ_MONTHLY         = 'monthly';

    const DAY_MONDAY    = 'mon';
    const DAY_TUESDAY   = 'tue';
    const DAY_WEDNESDAY = 'wed';
    const DAY_THURSDAY  = 'thu';
    const DAY_FRIDAY    = 'fri';
    const DAY_SATURDAY  = 'sat';
    const DAY_SUNDAY    = 'sun';

    const MONTH_FIRST     = 1;
    const MONTH_FIFTEENTH = 15;
    const MONTH_LAST      = 'last';

    private $table = array();
    private $last_update = '';

    public static function run()
    {
        // the scheduler script uses UTC as its timezone
        date_default_timezone_set('UTC');
        $dt = self::getDateTimeParts();
        printf("Scheduler time is: %02d:%02d\n", $dt['hours'], $dt['minutes']);

        $scheduler = new \Flexio\Object\Scheduler;
        $scheduler->loop();
    }

    private function loop()
    {
        $lastkey = '';

        while (true)
        {
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
                            $parts = self::getDateTimeParts(time(), $tz);
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

                if (($second < self::JOB_SCHEDULE_CHECK_INTERVAL) && array_key_exists(self::FREQ_ONE_MINUTE, $this->table))
                {
                    foreach ($this->table[self::FREQ_ONE_MINUTE] as $job)
                    {
                        print("running job...");
                        $pipe_eid = $job['pipe_eid'];
                        self::runPipe($pipe_eid);
                        print("\n");
                    }
                }

                if (($minute % 5 == 0) && array_key_exists(self::FREQ_FIVE_MINUTES, $this->table))
                {
                    foreach ($this->table[self::FREQ_FIVE_MINUTES] as $job)
                    {
                        print("running job...");
                        $pipe_eid = $job['pipe_eid'];
                        self::runPipe($pipe_eid);
                        print("\n");
                    }
                }

                if (($minute % 15 == 0) && array_key_exists(self::FREQ_FIFTEEN_MINUTES, $this->table))
                {
                    foreach ($this->table[self::FREQ_FIFTEEN_MINUTES] as $job)
                    {
                        print("running job...");
                        $pipe_eid = $job['pipe_eid'];
                        self::runPipe($pipe_eid);
                        print("\n");
                    }
                }

                if (($minute % 30 == 0) && array_key_exists(self::FREQ_THIRTY_MINUTES, $this->table))
                {
                    foreach ($this->table[self::FREQ_THIRTY_MINUTES] as $job)
                    {
                        print("running job...");
                        $pipe_eid = $job['pipe_eid'];
                        self::runPipe($pipe_eid);
                        print("\n");
                    }
                }

                if ($minute == 0 && array_key_exists(self::FREQ_HOURLY, $this->table))
                {
                    foreach ($this->table[self::FREQ_HOURLY] as $job)
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

    private function refreshSchedulerTable()
    {
        $pipe_model = \Flexio\Object\Store::getModel()->pipe;
        $current_update = $pipe_model->getLastSchedulerUpdateTime();

        print("Current update time " . $current_update . "; Last update time " . $this->last_update . "\n");
        if ($current_update == $this->last_update)
            return false; // scheduler needs no updates
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
                if ($schedule['frequency'] == self::FREQ_ONE_MINUTE ||
                    $schedule['frequency'] == self::FREQ_FIVE_MINUTES ||
                    $schedule['frequency'] == self::FREQ_FIFTEEN_MINUTES ||
                    $schedule['frequency'] == self::FREQ_THIRTY_MINUTES ||
                    $schedule['frequency'] == self::FREQ_HOURLY)
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


            $timezone = isset($schedule['timezone']) ? $schedule['timezone'] : 'UTC';


            if (isset($schedule['times']) && is_array($schedule['times']))
            {
                foreach ($schedule['times'] as $time)
                {
                    if (!isset($time['hour']) || !isset($time['minute']))
                        continue;

                    // find out what that time is in UTC
                    $dt = new \DateTime('now', new \DateTimeZone($timezone));
                    $dt->setTime($time['hour'], $time['minute']);
                    $gmtime_parts = self::getDateTimeParts($dt->getTimestamp(), 'UTC');
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

    private static function runPipe(string $pipe_eid)
    {
        // TODO: following run code is similar to \Flexio\Api\Process::create()
        // should factor; for example, the \Flexio\Api\Process::create()
        // adds on the parent and owner

        // TODO: check permissions based on the owner of the pipe

        // STEP 1: load the pipe
        $pipe = \Flexio\Object\Pipe::load($pipe_eid);
        if ($pipe === false)
            return false;

        $pipe_eid = $pipe->getEid();
        $pipe_properties = $pipe->get();
        if ($pipe_properties === false)
            return false;

        // STEP 2: create the process
        $process = \Flexio\Object\Process::create($pipe_properties);

        // STEP 3: run the process
        $process->run();
    }

    private static function getDateTimeParts($t = null, $tz = null) // TODO: add input parameter types
    {
        if (is_null($tz))
            $tz = date_default_timezone_get();

        $dt = new \DateTime('now', new \DateTimeZone($tz));
        $dt->setTimestamp(is_null($t) ? time() : $t);
        $s = $dt->format('s:i:G:j:w:n:Y:z:l:w:F:U');

        $k = array('seconds','minutes','hours','mday','wday','mon','year','yday','weekday','nweekday','month',0);

        return array_combine($k, explode(":", $s));
    }
}
