<?php
declare(strict_types=1);

namespace Ytake\PrestoClient;

/**
 * Class StatementStats
 */
final class StatementStats
{
    /** @var string */
    private $state = '';

    /** @var bool */
    private $queued;

    /** @var bool */
    private $scheduled;

    /** @var int */
    private $nodes;

    /** @var int */
    private $totalSplits;

    /** @var int */
    private $queuedSplits;

    /** @var int */
    private $runningSplits;

    /** @var int */
    private $completedSplits;

    /** @var int */
    private $userTimeMillis;

    /** @var int */
    private $cpuTimeMillis;

    /** @var int */
    private $wallTimeMillis;

    /** @var int */
    private $processedRows;

    /** @var int */
    private $processedBytes;

    /** @var \stdClass */
    private $rootStage;

    /** @var string[] */
    private $primitiveCasts = [
        'state'           => 'strval',
        'queued'          => 'boolval',
        'scheduled'       => 'boolval',
        'nodes'           => 'intval',
        'totalSplits'     => 'intval',
        'queuedSplits'    => 'intval',
        'runningSplits'   => 'intval',
        'completedSplits' => 'intval',
        'userTimeMillis'  => 'intval',
        'cpuTimeMillis'   => 'intval',
        'wallTimeMillis'  => 'intval',
        'processedRows'   => 'intval',
        'processedBytes'  => 'intval',
    ];

    /**
     * StatementStats constructor.
     *
     * @param \stdClass $jsonContent
     */
    public function __construct(\stdClass $jsonContent)
    {
        foreach ($jsonContent as $element => $value) {
            if (property_exists($this, $element)) {
                if (isset($this->primitiveCasts[$element])) {
                    $castFunction = $this->primitiveCasts[$element];
                    $this->$element = $castFunction($value);
                }
            }
        }
        if (isset($jsonContent->rootStage)) {
            $this->rootStage = $jsonContent->rootStage;
        }
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @return bool
     */
    public function isQueued(): bool
    {
        return $this->queued;
    }

    /**
     * @return bool
     */
    public function isScheduled(): bool
    {
        return $this->scheduled;
    }

    /**
     * @return int
     */
    public function getNodes(): int
    {
        return $this->nodes;
    }

    /**
     * @return int
     */
    public function getTotalSplits(): int
    {
        return $this->totalSplits;
    }

    /**
     * @return int
     */
    public function getQueuedSplits(): int
    {
        return $this->queuedSplits;
    }

    /**
     * @return int
     */
    public function getRunningSplits(): int
    {
        return $this->runningSplits;
    }

    /**
     * @return int
     */
    public function getCompletedSplits(): int
    {
        return $this->completedSplits;
    }

    /**
     * @return int
     */
    public function getUserTimeMillis(): int
    {
        return $this->userTimeMillis;
    }

    /**
     * @return int
     */
    public function getCpuTimeMillis(): int
    {
        return $this->cpuTimeMillis;
    }

    /**
     * @return int
     */
    public function getWallTimeMillis(): int
    {
        return $this->wallTimeMillis;
    }

    /**
     * @return int
     */
    public function getProcessedRows(): int
    {
        return $this->processedRows;
    }

    /**
     * @return int
     */
    public function getProcessedBytes(): int
    {
        return $this->processedBytes;
    }

    /**
     * @return \stdClass|null
     */
    public function getRootStage()
    {
        return $this->rootStage;
    }
}
