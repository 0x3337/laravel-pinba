<?php

namespace Chocofamilyme\LaravelPinba\Profiler;

use Log;

class PinbaDestination implements ProfilerInterface
{
    /**
     * @var array
     */
    private $timers = [];

    /**
     * PinbaDestination constructor.
     */
    public function __construct()
    {
        $this->initializePinba();
    }

    /**
     * Initialize pinba with some configuration
     */
    protected function initializePinba()
    {
        if ($hostname = config('pinba.pinba_hostname')) {
            pinba_hostname_set($hostname);
        }

        if ($servername = config('pinba.pinba_servername')) {
            pinba_server_name_set($servername);
        }

        if ($schema = config('pinba.pinba_schema')) {
            pinba_schema_set($schema);
        }
    }

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
    public function startTimer(string $group, string $type, string $method, string $category): int
    {
        $tags = [
            'group' => $group,
            'type' => $type,
            'method' => $method,
            'category' => $category,
        ];

        $timerId = count($this->timers);

        $this->timers[$timerId] = pinba_timer_start($tags);

        return $timerId;
    }

    /**
     * Stop the timer by timerId
     *
     * @param int $timerId
     */
    public function stopTimer(int $timerId): void
    {
        if (isset($this->timers[$timerId])) {
            pinba_timer_stop($this->timers[$timerId]);
            unset($this->timers[$timerId]);
        } else {
            Log::warning("Could not stop timer by id $timerId, because it does not exists in timer array");
        }
    }

    /**
     * Stop all timers
     */
    public function stopAllTimers(): void
    {
        pinba_timers_stop();
        $this->timers = [];
    }

    /**
     * Set's the script name of profiled request, useful where MVC is used and all content
     * is served by single script. With this method you can rewrite it
     *
     * @param string $url
     */
    public function setScriptName(string $url): void
    {
        pinba_script_name_set($url);
    }

    /**
     * Return all timers
     *
     * @return array
     */
    public function getTimers()
    {
        return $this->timers;
    }

    /**
     * Useful when you need to send request data to the server immediately (for long running scripts)
     *
     * @param string|null $scriptName
     * @param int|null $flag
     */
    public function flush(?string $scriptName = null, ?int $flag = null): void
    {
        pinba_flush($scriptName, $flag);
    }
}