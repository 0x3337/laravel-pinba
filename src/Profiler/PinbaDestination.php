<?php

namespace Chocofamilyme\LaravelPinba\Profiler;

use Illuminate\Support\Facades\Log;

class PinbaDestination implements ProfilerInterface
{
    private array $timers = [];

    /**
     * PinbaDestination constructor.
     */
    public function __construct()
    {
        $this->initializePinba();
    }

    /**
     * Initialize pinba with some configuration
     *
     * @psalm-suppress UndefinedFunction
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
     * @psalm-suppress UndefinedFunction
     * @return int It's the timerId
     */
    public function startTimer(string $group, string $type, string $method, string $category): int
    {
        $tags = [
            'group'    => $group,
            'type'     => $type,
            'method'   => $method,
            'category' => $category,
        ];

        $timerId = count($this->timers);

        $this->timers[$timerId] = pinba_timer_start($tags);

        return $timerId;
    }

    /**
     * Stop the timer by timerId
     *
     * @psalm-suppress UndefinedFunction
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
     *
     * @psalm-suppress UndefinedFunction
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
     *
     * @psalm-suppress UndefinedFunction
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
    public function getTimers(): array
    {
        return $this->timers;
    }

    /**
     * Useful when you need to send request data to the server immediately (for long running scripts)
     *
     * @param string   $scriptName
     * @param int|null $flag
     *
     * @psalm-suppress UndefinedFunction
     * @psalm-suppress UndefinedConstant
     */
    public function flush(string $scriptName, ?int $flag = null): void
    {
        if (null === $flag) {
            $flag = PINBA_FLUSH_ONLY_STOPPED_TIMERS;
        }

        pinba_flush($scriptName, $flag);
    }
}
