<?php
declare(strict_types=1);

namespace Ytake\PrestoClient;

/**
 * Class QueryResult
 */
final class QueryResult
{
    /** @var string */
    private $id;

    /** @var string */
    private $infoUri;

    /** @var string */
    private $partialCancelUri;

    /** @var string */
    private $nextUri;

    /** @var \stdClass[] */
    private $columns = [];

    /** @var array */
    private $data = [];

    /** @var StatementStats|null */
    private $stats;

    /** @var QueryError|null */
    private $error;

    /**
     * QueryResult constructor.
     *
     * @param string $content
     */
    public function set(string $content)
    {
        $parsed = $this->parseContent($content);
        $this->id = $parsed->id;
        $this->infoUri = $parsed->infoUri;
        $this->partialCancelUri = $parsed->partialCancelUri ?? null;
        $this->nextUri = $parsed->nextUri ?? null;
        $this->columns = [];
        if (isset($parsed->columns)) {
            $this->columnTransfer($parsed->columns);
        }
        $this->data = $parsed->data ?? [];
        $this->stats = isset($parsed->stats) ? $this->statsTransfer($parsed->stats) : null;
        $this->error = isset($parsed->error) ? $this->errorTransfer($parsed->error) : null;
    }

    /**
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getInfoUri()
    {
        return $this->infoUri;
    }

    /**
     * @return string|null
     */
    public function getNextUri()
    {
        return $this->nextUri;
    }

    /**
     * @return QueryError|null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return string|null
     */
    public function getPartialCancelUri()
    {
        return $this->partialCancelUri;
    }

    /**
     * @return \Generator
     */
    public function getData(): \Generator
    {
        if (!count($this->data)) {
            yield;
        }
        foreach ($this->data as $data) {
            $fixData = new FixData();
            $column = $this->getColumns();
            for ($i = 0; $i < count($column); $i++) {
                $fixData->add($column[$i]->getName(), $data[$i]);
            }
            yield $fixData;
        }
    }

    /**
     * @param string $content
     *
     * @return \stdClass
     */
    private function parseContent(string $content): \stdClass
    {
        $parsed = json_decode($content);
        if ($parsed === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException;
        }

        return $parsed;
    }

    /**
     * @param \stdClass $jsonContent
     *
     * @return StatementStats
     */
    private function statsTransfer(\stdClass $jsonContent): StatementStats
    {
        return new StatementStats($jsonContent);
    }

    /**
     * @param \stdClass $jsonContent
     *
     * @return QueryError
     */
    private function errorTransfer(\stdClass $jsonContent): QueryError
    {
        return new QueryError($jsonContent);
    }

    /**
     * @param array $columns
     */
    private function columnTransfer(array $columns)
    {
        foreach ($columns as $column) {
            $this->columns[] = new Column($column);
        }
    }

    /**
     * @return StatementStats|null
     */
    public function getStats()
    {
        return $this->stats;
    }

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }
}
