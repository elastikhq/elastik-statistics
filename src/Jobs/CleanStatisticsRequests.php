<?php

declare(strict_types=1);

namespace Elastik\Statistics\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CleanStatisticsRequests implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        ! config('elastik.statistics.lifetime') || app('elastik.statistics.request')->where('created_at', '<=', Carbon::now()->subDays(config('elastik.statistics.lifetime')))->delete();
    }
}
