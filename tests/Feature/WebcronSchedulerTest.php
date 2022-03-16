<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\Dummies\DummyJob;
use Tests\Dummies\UniqueDummyJob;
use WanaKin\Webcron\Facades\WebcronScheduler;
use WanaKin\Webcron\Models\CronEvent;

class WebcronSchedulerTest extends TestCase
{
    use RefreshDatabase;

    public function test_adding_scheduled_job()
    {
        WebcronScheduler::schedule('* * * * *', new DummyJob());

        $this->assertEquals(DummyJob::class, WebcronScheduler::getScheduledJobs()['* * * * *'][0]::class);
    }

    public function test_clear_scheduled_jobs()
    {
        WebcronScheduler::schedule('* * * * *', new DummyJob());

        WebcronScheduler::clearScheduledJobs();

        $this->assertCount(0, WebcronScheduler::getScheduledJobs());
    }

    public function test_dispatches_job_when_not_run()
    {
        WebcronScheduler::schedule('* * * * *', new DummyJob());

        Bus::fake();

        WebcronScheduler::dispatchScheduledJobs();

        Bus::assertDispatched(DummyJob::class);
    }

    public function test_calls_unique_id()
    {
        $mock = $this->mock(UniqueDummyJob::class);

        $mock->shouldReceive('uniqueId')
            ->andReturn('abc123');

        WebcronScheduler::schedule('* * * * *', $mock);

        Bus::fake();

        WebcronScheduler::dispatchScheduledJobs();
    }

    public function test_does_not_dispatch_previously_run_job()
    {
        WebcronScheduler::schedule('0 0 * * *', new DummyJob());

        CronEvent::create([
            'job' => md5(DummyJob::class . ':any'),
            'dispatched_at' => now()->subMinutes(15)
        ]);

        Bus::fake();

        WebcronScheduler::dispatchScheduledJobs();

        Bus::assertNotDispatched(DummyJob::class);
    }
}