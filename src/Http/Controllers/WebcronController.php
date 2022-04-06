<?php

namespace WanaKin\Webcron\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use WanaKin\Webcron\Facades\WebcronScheduler;
use function set_time_limit;

class WebcronController extends Controller
{
    /**
     * Dispatch scheduled jobs if not locked
     *
     * @return Response
     */
    public function __invoke(): Response
    {
        ignore_user_abort(true);

        if ($lock = $this->lock('scheduler')) {
            WebcronScheduler::dispatchScheduledJobs();

            $lock->release();
        }

        return response(null, 204);
    }
}