<?php

namespace Chocofamilyme\LaravelPinba\Profiler;

interface ProfilerInterface
{
    /**
     * Creates a timer which should be stopped somewhere
     *
     * @param string $group
     * @param string $type
     * @param string $method
     * @param string $category
     *
     * @return int It's the timerId
     */
    public function startTimer(string $group, string $type, string $method, string $category): int;

    /**
     * Stop the timer by timerId
     *
     * @param int $timerId
     */
    public function stopTimer(int $timerId): void;

    /**
     * Stop all timers
     */
    public function stopAllTimers(): void;

    /**
     * Set's the script name of profiled request, useful where MVC is used and all content
     * is served by single script. With this method you can rewrite it
     *
     * @param string $url
     */
    public function setScriptName(string $url): void;

    /**
     * Return all timers
     *
     * @return array
     */
    public function getTimers(): array;

    /**
     * Useful when you need to send request data to the server immediately (for long running scripts)
     *
     * @param string|null $scriptName
     * @param int|null $flag
     */
    public function flush(string $scriptName = null, ?int $flag = null): void;
}
