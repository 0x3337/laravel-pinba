<?php

declare(strict_types=1);

namespace Chocofamilyme\LaravelPinba\Listeners;

use Chocofamilyme\LaravelPinba\Profiler\ProfilerInterface;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobFailed;

final class ProfileJob
{
    private ProfilerInterface $profiler;
    private static int $timerId;

    public function __construct(ProfilerInterface $profiler)
    {
        $this->profiler = $profiler;
    }

    public function handle($event)
    {
        if ($event instanceof JobProcessing) {
            self::$timerId = $this->profiler->startTimer(
                'worker',
                'run',
                $event->job->getName(),
                'messenger:consume'
            );

            return;
        }

        if ($event instanceof JobProcessed) {
            $this->profiler->stopTimer(self::$timerId);
            $this->profiler->flush($event->job->resolveName());

            return;
        }

        if ($event instanceof JobFailed) {
            $this->profiler->stopTimer(self::$timerId);
            $this->profiler->flush($event->job->resolveName());

            return;
        }
    }
}
