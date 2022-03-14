<?php

namespace WanaKin\Webcron\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use function ignore_user_abort;

class WorkerController extends Controller
{
    /**
     * Take a job off the queue
     *
     * @return Response
     */
    public function __invoke(): Response
    {
        ignore_user_abort(true);

        if (!$this->locked('worker')) {
            $this->lock('worker');

            Artisan::call('queue:work', [
                '--max-time' => config('webcron.worker.max_time')
            ]);

            $this->unlock('worker');
        }

        return response(null, 204);
    }
}