<?php

declare(strict_types=1);

namespace Chocofamilyme\LaravelPinba\Listeners;

use Chocofamilyme\LaravelPinba\Profiler\ProfilerInterface;
use Illuminate\Console\Events\CommandStarting;

final class ProfileStartCommand
{
    private ProfilerInterface $profiler;

    public function __construct(ProfilerInterface $profiler)
    {
        $this->profiler = $profiler;
    }

    /** @psalm-suppress UndefinedClass */
    public function handle(CommandStarting $event)
    {
        $this->profiler->setScriptName($event->command);
    }
}
