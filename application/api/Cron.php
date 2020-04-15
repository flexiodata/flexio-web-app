<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
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
    private const JOB_NOTIFICATION_LASTRUN_KEY = 'job.notification.lastrun';

    private $table = array();
    private $last_update = '';

    public static function run() : void
    {
        // the scheduler script uses UTC as its timezone
        date_default_timezone_set('UTC');

        $cron = new static();
        $cron->sendTrialNotificationEmails();
        $cron->runScheduledPipes(1); // loop once; this function is called by an api endpoint at regular intervals, so don't need to loop
    }

    public function sendTrialNotificationEmails() : void
    {
        // only allow trial-ending-notifications when tests are deactivated to
        // prevent possibility of a flood of emails attempting to be sent for
        // users created by the test suite when cron is activated
        if (IS_TESTING())
            return;

        // run the notification job only if it's near 12pm UTC and we haven't yet run the notification job
        // on that day yet (> 23 hours since it last ran instead of >= 24 for an easy comparison that allows
        // a little time for the job to run); when we run the job, we have an extra check to make sure a notice
        // only goes out once for a user, but we want to make sure that if the notice job takes time, it can't be
        // invoked by multiple calls to the cron admin api endpoint that would cause the job to run independently
        // twice and potentially allow a notice to be sent out multiple times depending on database read/write timing

        $registry_model = \Flexio\System\System::getModel()->registry;

        // get the current time and the last time the notification job ran
        $current_timestamp = \Flexio\Base\Util::getCurrentTimestamp();
        $current_time = strtotime($current_timestamp);

        $notification_job_lastrun_timestamp = $registry_model->getString('', self::JOB_NOTIFICATION_LASTRUN_KEY, '1900-01-01 00:00:00');
        $notification_job_lastrun_time = strtotime($notification_job_lastrun_timestamp);
        $notification_job_todayrun_time = strtotime(date('Y-m-d 12:00:00', $current_time));

        if ($current_time - $notification_job_lastrun_time < 23*60*60) // it's been less than 23 hours since we ran the job
            return;
        if ($current_time < $notification_job_todayrun_time) // it isn't time to run the job yet
            return;

        // set the new job runtime
        $registry_model->setString('', self::JOB_NOTIFICATION_LASTRUN_KEY, $current_timestamp);

        // get a list of active, non-customer users that have trial periods ending in 1 day
        $trial_expires_one_day_date = strtotime($current_timestamp . ' + 1 days'); // TRIAL_DATE_CONFIG
        $trial_expires_one_day_date = date('Y-m-d', $trial_expires_one_day_date);
        $filter = array('eid_status' => \Model::STATUS_AVAILABLE, 'is_customer' => 'false', 'trialend_min' => $trial_expires_one_day_date, 'trialend_max' => $trial_expires_one_day_date);
        $users_expires_one_day_list = \Flexio\Object\User::list($filter);

        // get a list of active, non-customer users that have trial periods ending in 4 days (3-days in for a 7 day period)
        $trial_expires_one_week_date = strtotime($current_timestamp . ' + 4 days'); // TRIAL_DATE_CONFIG
        $trial_expires_one_week_date = date('Y-m-d', $trial_expires_one_week_date);
        $filter = array('eid_status' => \Model::STATUS_AVAILABLE, 'is_customer' => 'false', 'trialend_min' => $trial_expires_one_week_date, 'trialend_max' => $trial_expires_one_week_date);
        $users_expires_one_week_list = \Flexio\Object\User::list($filter);

        self::sendTrialEndingNotice($users_expires_one_day_list);
        self::sendTrialHalfwayNotice($users_expires_one_week_list);
    }

    public function runScheduledPipes(int $count = null /*use null to loop continuously*/) : void
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
                        \Flexio\Api\Pipe::runFromCron($pipe_eid);
                        print("\n");
                    }
                }

                if (($second < self::JOB_SCHEDULE_CHECK_INTERVAL) && array_key_exists(\Flexio\Base\Schedule::FREQ_ONE_MINUTE, $this->table))
                {
                    foreach ($this->table[\Flexio\Base\Schedule::FREQ_ONE_MINUTE] as $job)
                    {
                        print("running job...");
                        $pipe_eid = $job['pipe_eid'];
                        \Flexio\Api\Pipe::runFromCron($pipe_eid);
                        print("\n");
                    }
                }

                if (($minute % 5 == 0) && array_key_exists(\Flexio\Base\Schedule::FREQ_FIVE_MINUTES, $this->table))
                {
                    foreach ($this->table[\Flexio\Base\Schedule::FREQ_FIVE_MINUTES] as $job)
                    {
                        print("running job...");
                        $pipe_eid = $job['pipe_eid'];
                        \Flexio\Api\Pipe::runFromCron($pipe_eid);
                        print("\n");
                    }
                }

                if (($minute % 15 == 0) && array_key_exists(\Flexio\Base\Schedule::FREQ_FIFTEEN_MINUTES, $this->table))
                {
                    foreach ($this->table[\Flexio\Base\Schedule::FREQ_FIFTEEN_MINUTES] as $job)
                    {
                        print("running job...");
                        $pipe_eid = $job['pipe_eid'];
                        \Flexio\Api\Pipe::runFromCron($pipe_eid);
                        print("\n");
                    }
                }

                if (($minute % 30 == 0) && array_key_exists(\Flexio\Base\Schedule::FREQ_THIRTY_MINUTES, $this->table))
                {
                    foreach ($this->table[\Flexio\Base\Schedule::FREQ_THIRTY_MINUTES] as $job)
                    {
                        print("running job...");
                        $pipe_eid = $job['pipe_eid'];
                        \Flexio\Api\Pipe::runFromCron($pipe_eid);
                        print("\n");
                    }
                }

                if ($minute == 0 && array_key_exists(\Flexio\Base\Schedule::FREQ_HOURLY, $this->table))
                {
                    foreach ($this->table[\Flexio\Base\Schedule::FREQ_HOURLY] as $job)
                    {
                        print("running job...");
                        $pipe_eid = $job['pipe_eid'];
                        \Flexio\Api\Pipe::runFromCron($pipe_eid);
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

    private static function sendTrialEndingNotice(array $user_object_list)
    {
        foreach ($user_object_list as $user)
        {
            try
            {
                // if we haven't already sent a notice, send it and log it
                if (self::isNoticeSent($user->getEid(), \Flexio\Api\Message::NOTICE_TRIAL_ENDING))
                    continue;

                // send the notice and log it
                $user_info = $user->get();
                $success = \Flexio\Api\Message::sendTrialEndingEmail($user_info);
                self::logNotice($user->getEid(), \Flexio\Api\Message::NOTICE_TRIAL_ENDING);
            }
            catch (\Exception $e)
            {
            }
        }
    }

    private static function sendTrialHalfwayNotice(array $user_object_list)
    {
        foreach ($user_object_list as $user)
        {
            try
            {
                // if we haven't already sent a notice, send it and log it
                if (self::isNoticeSent($user->getEid(), \Flexio\Api\Message::NOTICE_TRIAL_HALFWAY))
                    continue;

                // send the notice and log it
                $user_info = $user->get();
                $success = \Flexio\Api\Message::sendTrialHalfwayEmail($user_info);
                self::logNotice($user->getEid(), \Flexio\Api\Message::NOTICE_TRIAL_HALFWAY);
            }
            catch (\Exception $e)
            {
            }
        }
    }

    private static function logNotice(string $owner, string $notice_type) : void
    {
        $params = array(
            'owned_by' => $owner,
            'notice_type' => $notice_type
        );

        $notice_model = \Flexio\System\System::getModel()->notice;
        $notice_model->create($params);
    }

    private static function isNoticeSent(string $owner, string $notice_type) : bool
    {
        $filter = array('owned_by' => $owner, 'notice_type' => $notice_type);
        $notice_model = \Flexio\System\System::getModel()->notice;
        $notices = $notice_model->list($filter);

        if (count($notices) > 0)
            return true;

        return false;
    }
}
