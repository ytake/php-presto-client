<?php
declare(strict_types=1);

namespace Ytake\PrestoClient;

use Fig\Http\Message\StatusCodeInterface;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\UriNormalizer;
use GuzzleHttp\Exception\ClientException;
use Guzzle\Http\Message\RequestInterface;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Ytake\PrestoClient\Exception\QueryErrorException;
use Ytake\PrestoClient\Exception\RequestFailedException;
use Ytake\PrestoClient\Session\Property;

/**
 * Class StatementClient
 */
class StatementClient
{
    const STATEMENT_URI = '/v1/statement';

    /** @var ClientInterface */
    private $client;

    /** @var ClientSession */
    private $session;

    /** @var QueryResult */
    protected $queryResult;

    /** @var string[] */
    protected $headers = [];

    /** @var string */
    protected $query;

    /** @var string */
    protected $nextUri;

    /** @var bool */
    private $gone = false;

    /** @var bool */
    private $valid = true;

    /** @var bool */
    private $closed = false;

    /** @var bool */
    private $fulfilled = false;

    /** @var int */
    protected $nanoseconds = 5000000000;

    /**
     * PrestoClient constructor.
     *
     * @param ClientSession        $session
     * @param string               $query
     * @param ClientInterface|null $client
     */
    public function __construct(ClientSession $session, string $query, ClientInterface $client = null)
    {
        $this->session = $session;
        $this->query = $query;
        $this->client = (is_null($client)) ? new Client : $client;
        $this->queryResult = new QueryResult();
        $this->prepareRequest();
    }

    private function prepareRequest()
    {
        $this->headers = [
            PrestoHeaders::PRESTO_USER => $this->session->getUser(),
            'User-Agent'               => $this->session->getSource() . '/' . PrestoHeaders::VERSION,
        ];
    }

    /**
     * @param Request $request
     *
     * @return Request
     */
    protected function buildQueryRequest(Request $request): Request
    {
        $transactionId = is_null($this->session->getTransactionId()) ? 'NONE' : $this->session->getTransactionId()->toString();
        $request = $request->withAddedHeader(PrestoHeaders::PRESTO_CATALOG, $this->session->getCatalog())
            ->withAddedHeader(PrestoHeaders::PRESTO_SCHEMA, $this->session->getSchema())
            ->withAddedHeader(PrestoHeaders::PRESTO_SOURCE, $this->session->getSource())
            ->withAddedHeader(PrestoHeaders::PRESTO_TRANSACTION_ID, $transactionId);
        $sessionProperty = $this->session->getProperty();
        if (count($sessionProperty)) {
            $sessions = [];
            /** @var Property $property */
            foreach ($sessionProperty as $property) {
                $sessions[] = $property->getKey() . '=' . $property->getValue();
            }
            $request = $request->withAddedHeader(
                PrestoHeaders::PRESTO_SESSION,
                implode(',', $sessions)
            );
        }
        $preparedStatements = $this->session->getPreparedStatement();
        if (count($preparedStatements)) {
            $statements = [];
            foreach ($preparedStatements as $preparedStatement) {
                $statements[] = urlencode($preparedStatement->getKey()) . '=' . urlencode($preparedStatement->getValue());
            }
            $request = $request->withAddedHeader(
                PrestoHeaders::PRESTO_PREPARED_STATEMENT,
                implode(',', $statements)
            );
        }

        return $request;
    }

    /**
     * @param int  $timeout
     * @param bool $debug
     *
     * @return void
     * @throws QueryErrorException
     */
    public function execute(int $timeout = 500000, bool $debug = false)
    {
        $normalize = UriNormalizer::normalize(
            new Uri($this->session->getHost() . StatementClient::STATEMENT_URI),
            UriNormalizer::REMOVE_DUPLICATE_SLASHES
        );
        $request = new Request(RequestInterface::POST, $normalize, $this->headers);
        try {
            $response = $this->client->send($this->buildQueryRequest($request), [
                'timeout' => $timeout,
                'body'    => $this->query,
                'debug'   => $debug,
            ]);
            if ($response->getStatusCode() === StatusCodeInterface::STATUS_OK) {
                $this->queryResult->set($response->getBody()->getContents());
            }
        } catch (ClientException $e) {
            throw new QueryErrorException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @return QueryResult
     * @throws QueryErrorException
     */
    public function current(): QueryResult
    {
        return $this->queryResult;
    }

    /**
     * @return bool
     */
    public function advance(): bool
    {
        $nextUri = $this->current()->getNextUri();
        if (is_null($nextUri) || $this->isClosed()) {
            $this->valid = false;

            return false;
        }
        $this->prepareRequest();
        $start = microtime(true);
        $cause = null;
        $attempts = 0;
        do {
            if ($attempts > 0) {
                usleep($attempts * 100);
            }
            $attempts++;
            try {
                $response = $this->client->get($nextUri);
                if ($response->getStatusCode() === StatusCodeInterface::STATUS_OK) {
                    $this->queryResult->set($response->getBody()->getContents());

                    return true;
                }
            } catch (ClientException $e) {
                $cause = $e;
                if ($e->getCode() != StatusCodeInterface::STATUS_SERVICE_UNAVAILABLE) {
                    throw $this->requestFailedException("fetching next", $nextUri, $e->getResponse());
                }
            }
        } while (((microtime(true) - $start) < $this->nanoseconds) && !$this->isClosed());

        $this->gone = true;
        throw new \RuntimeException('Error fetching next', 0, $cause);
    }

    /**
     * @param int  $timeout
     * @param bool $debug
     *
     * @return bool
     */
    public function cancelLeafStage(int $timeout = 500000, bool $debug = false): bool
    {
        if (!$this->isClosed()) {
            $cancelUri = $this->current()->getPartialCancelUri();
            if (is_null($cancelUri)) {
                return false;
            }
            $promise = $this->client->deleteAsync($cancelUri, [
                'timeout' => $timeout,
                'debug'   => $debug,
            ]);
            $promise->then(function (ResponseInterface $response) {
                $this->fulfilled = (StatusCodeInterface::STATUS_NO_CONTENT === $response->getStatusCode());
            }, function (RequestException $e) {
                throw new RequestFailedException($e->getMessage(), $e->getCode(), $e);
            });
            $promise->wait();
        }

        return $this->fulfilled;
    }

    /**
     * @param int $nanoseconds
     */
    public function setNanoseconds(int $nanoseconds)
    {
        $this->nanoseconds = $nanoseconds;
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->queryResult->getError() != null;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid && (!$this->isGone()) && (!$this->isClosed());
    }

    /**
     * @return bool
     */
    public function isGone(): bool
    {
        return $this->gone;
    }

    /**
     * @return bool
     */
    public function isClosed(): bool
    {
        return $this->closed;
    }

    /**
     * close
     * HTTP method DELETE
     */
    public function close()
    {
        $uri = $this->current()->getNextUri();
        if (!is_null($uri)) {
            $this->client->deleteAsync($uri)->wait();
        }
        $this->closed = true;
    }

    /**
     * @param string            $message
     * @param string            $uri
     * @param ResponseInterface $response
     *
     * @return RequestFailedException
     */
    private function requestFailedException(
        string $message,
        string $uri,
        ResponseInterface $response
    ): RequestFailedException {
        $this->gone = true;
        if (!$response->getBody()->getSize()) {
            return new RequestFailedException(
                sprintf("Error %s at %s returned an invalid response: %s [Error: %s]",
                    $message,
                    $uri,
                    $response->getStatusCode(),
                    $response->getBody()->getContents()
                )
            );
        }

        return new RequestFailedException(
            sprintf("Error %s at %s returned %s: %s",
                $message,
                $uri,
                $response->getStatusCode(),
                $response->getBody()->getContents()
            )
        );
    }
}
