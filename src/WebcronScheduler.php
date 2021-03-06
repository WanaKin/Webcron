<?php

namespace WanaKin\Webcron;

use Cron\CronExpression;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use WanaKin\Webcron\Models\CronEvent;
use function dispatch;
use function is_callable;
use function method_exists;

class WebcronScheduler
{
    /**
     * @var array<string, array<object>>
     */
    protected array $scheduled = [];

    /**
     * Add a job to the scheduler
     *
     * @param  string $cronString
     * @param  object $job
     * @return void
     */
    public function schedule(string $cronString, object $job)
    {
        if (!array_key_exists($cronString, $this->scheduled)) {
            $this->scheduled[$cronString] = [];
        }

        $this->scheduled[$cronString][] = $job;
    }

    /**
     * Get the currently scheduled jobs
     *
     * @return array
     */
    public function getScheduledJobs(): array
    {
        return $this->scheduled;
    }

    /**
     * Clear the scheduled jobs. This is useful during testing.
     *
     * @return void
     */
    public function clearScheduledJobs()
    {
        $this->scheduled = [];
    }

    /**
     * Dispatch jobs that are due
     *
     * @return void
     */
    public function dispatchScheduledJobs()
    {
        foreach ($this->getScheduledJobs() as $cronString => $jobs) {
            $cron = resolve(CronExpression::class, [
                'expression' => $cronString
            ]);
            $previousDate = $cron->getPreviousRunDate();

            foreach ($jobs as $job) {
                $uniqueId = null;

                // Allow setting a custom unique id
                if ($job instanceof ShouldBeUnique) {
                    $uniqueId = method_exists($job, 'uniqueId')
                        ? $job->uniqueId()
                        : ($job->uniqueId ?? null);
                }

                $uniqueId = md5($job::class . ':' . ($uniqueId ?? 'any'));

                // Check if the job has already been dispatched since its last due date
                $exists = CronEvent::query()
                    ->where('job', $uniqueId)
                    ->where('dispatched_at', '>=', $previousDate)
                    ->exists();

                if (!$exists) {
                    dispatch($job);

                    CronEvent::create([
                        'job' => $job::class,
                        'dispatched_at' => now()
                    ]);
                }
            }
        }
    }
}