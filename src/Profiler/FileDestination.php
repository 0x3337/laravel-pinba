<?php

namespace Chocofamilyme\LaravelPinba\Profiler;

use Illuminate\Support\Facades\Log;

class FileDestination implements ProfilerInterface
{
    private array $timers = [];

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
            'group'    => $group,
            'type'     => $type,
            'method'   => $method,
            'category' => $category,
        ];

        $timerId = count($this->timers);

        $this->timers[$timerId] = [
            'startTime' => microtime(true),
            'tags'      => $tags,
        ];

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
            $stopTime       = microtime(true);
            $timeDifference = $stopTime - $this->timers[$timerId]['startTime'];
            Log::info('Timer ' . print_r($this->timers[$timerId]['tags'], true) . ' : ' . $timeDifference . ' секунд');
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
        foreach ($this->timers as $timerId => $timer) {
            $this->stopTimer($timerId);
        }
    }

    /**
     * Set's the script name of profiled request, useful where MVC is used and all content
     * is served by single script. With this method you can rewrite it
     *
     * @param string $url
     */
    public function setScriptName(string $url): void
    {
        Log::info('Pinba: ' . $url);
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
     * @param string|null $scriptName
     * @param int|null    $flag
     */
    public function flush(string $scriptName = null, ?int $flag = null): void
    {
        $this->setScriptName($scriptName);
        $this->stopAllTimers();
    }
}
