<?php
declare(strict_types=1);

namespace Ytake\PrestoClient;

use Ramsey\Uuid\UuidInterface;
use Ytake\PrestoClient\Session\Property;
use Ytake\PrestoClient\Session\PreparedStatement;

/**
 * Class PrestoSession
 */
class ClientSession
{
    /** @var string */
    protected $host;

    /** @var string */
    protected $catalog;

    /** @var UuidInterface */
    protected $transactionId;

    /** @var string */
    protected $schema = 'default';

    /** @var string */
    protected $user = 'presto';

    /** @var string */
    protected $source = PrestoHeaders::PRESTO_SOURCE_VALUE;

    /** @var Property[] */
    protected $property = [];

    /** @var PreparedStatement[] */
    protected $preparedStatement = [];

    /**
     * PrestoSession constructor.
     *
     * @param string $host
     * @param string $catalog
     */
    public function __construct(string $host, string $catalog)
    {
        $this->host = $host;
        $this->catalog = $catalog;
    }

    /**
     * @param string $schema
     */
    public function setSchema(string $schema)
    {
        $this->schema = $schema;
    }

    /**
     * @param string $user
     */
    public function setUser(string $user)
    {
        $this->user = $user;
    }

    /**
     * @param string $source
     */
    public function setSource(string $source)
    {
        $this->source = $source;
    }

    /**
     * @param UuidInterface $transactionId
     */
    public function setTransactionId(UuidInterface $transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * @param Property $property
     */
    public function setProperty(Property $property)
    {
        $this->property[] = $property;
    }

    /**
     * @param PreparedStatement $preparedStatement
     */
    public function setPreparedStatement(PreparedStatement $preparedStatement)
    {
        $this->preparedStatement[] = $preparedStatement;
    }

    /**
     * @return Property[]
     */
    public function getProperty(): array
    {
        return $this->property;
    }

    /**
     * @return PreparedStatement[]
     */
    public function getPreparedStatement(): array
    {
        return $this->preparedStatement;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getCatalog(): string
    {
        return $this->catalog;
    }

    /**
     * @return string
     */
    public function getSchema(): string
    {
        return $this->schema;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @return UuidInterface|null
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }
}
