<?php
declare(strict_types=1);

namespace Ytake\PrestoClient;

/**
 * Class Column
 */
final class Column
{
    /** @var string */
    private $name = '';

    /** @var string */
    private $type = '';

    /** @var \stdClass */
    private $typeSignature;

    /**
     * Column constructor.
     *
     * @param \stdClass $jsonContent
     */
    public function __construct(\stdClass $jsonContent)
    {
        $this->name = $jsonContent->name;
        $this->type = $jsonContent->type;
        $this->typeSignature = $jsonContent->typeSignature;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getTypeSignature()
    {
        return $this->typeSignature;
    }
}
