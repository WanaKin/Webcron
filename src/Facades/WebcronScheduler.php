<?php

namespace WanaKin\Webcron\Facades;

/**
 * @method static void schedule(string $cronString, object $job) Add a job to the scheduler
 * @method static array getScheduledJobs() Get the currently scheduled jobs
 * @method static void clearScheduledJobs() Clear the scheduled jobs array (useful for testing)
 * @method static void dispatchScheduledJobs() Dispatch jobs that are due
 */
class WebcronScheduler extends \Illuminate\Support\Facades\Facade
{
    public static function getFacadeAccessor(): string
    {
        return \WanaKin\Webcron\WebcronScheduler::class;
    }
}