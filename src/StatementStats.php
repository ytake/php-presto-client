<?php
declare(strict_types=1);

/**
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace Ytake\PrestoClient;

/**
 * Class StatementStats
 *
 * @author Yuuki Takezawa <yuuki.takezawa@comnect.jp.net>
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
        $arrayContent = (array)$jsonContent;
        foreach ($arrayContent as $element => $value) {
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
