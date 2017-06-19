<?php
declare(strict_types=1);

namespace Ytake\PrestoClient;

/**
 * Class QueryError
 */
final class QueryError
{
    /** @var string */
    private $message = '';

    /** @var string */
    private $sqlState = '';

    /** @var int */
    private $errorCode;

    /** @var string */
    private $errorName;

    /** @var string */
    private $errorType;

    /** @var \stdClass */
    private $failureInfo;

    /**
     * QueryError constructor.
     *
     * @param \stdClass $jsonContent
     */
    public function __construct(\stdClass $jsonContent)
    {
        $this->message = strval($jsonContent->message);
        $this->sqlState = $jsonContent->sqlState ?? '';
        $this->errorCode = intval($jsonContent->errorCode);
        $this->errorName = strval($jsonContent->errorName);
        $this->errorType = strval($jsonContent->errorType);
        $this->failureInfo = $jsonContent->failureInfo;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getSqlState(): string
    {
        return $this->sqlState;
    }

    /**
     * @return int
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * @return string
     */
    public function getErrorName(): string
    {
        return $this->errorName;
    }

    /**
     * @return string
     */
    public function getErrorType(): string
    {
        return $this->errorType;
    }

    /**
     * @return \stdClass
     */
    public function getFailureInfo(): \stdClass
    {
        return $this->failureInfo;
    }
}
