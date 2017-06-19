<?php
declare(strict_types=1);

namespace Ytake\PrestoClient;

/**
 * Class ResultsSession
 */
class ResultsSession
{
    /** @var QueryResult[] */
    private $results = [];

    /** @var StatementClient */
    private $prestoClient;

    /** @var int */
    private $timeout = 500000;

    /** @var bool */
    private $debug = false;

    /**
     * @param StatementClient $prestoClient
     * @param int             $timeout
     * @param bool            $debug
     */
    public function __construct(StatementClient $prestoClient, int $timeout = 500000, bool $debug = false)
    {
        $this->prestoClient = $prestoClient;
        $this->timeout = $timeout;
        $this->debug = $debug;
    }

    /**
     * @return ResultsSession
     */
    public function execute(): ResultsSession
    {
        $this->prestoClient->execute($this->timeout, $this->debug);

        return $this;
    }

    /**
     * @return \Generator
     */
    public function yieldResults(): \Generator
    {
        while ($this->prestoClient->isValid()) {
            yield $this->prestoClient->current();
            $this->prestoClient->advance();
        }
    }

    /**
     * @return array
     */
    public function getResults(): array
    {
        while ($this->prestoClient->isValid()) {
            $this->addResults($this->prestoClient->current());
            $this->prestoClient->advance();
        }

        return $this->results;
    }

    /**
     * @param QueryResult $queryResult
     */
    private function addResults(QueryResult $queryResult)
    {
        $this->results[] = $queryResult;
    }
}
