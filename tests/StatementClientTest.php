<?php
declare(strict_types=1);

use Fig\Http\Message\StatusCodeInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Ytake\PrestoClient\ClientSession;
use Ytake\PrestoClient\Column;
use Ytake\PrestoClient\FixData;
use Ytake\PrestoClient\PrestoHeaders;
use Ytake\PrestoClient\QueryError;
use Ytake\PrestoClient\QueryResult;
use Ytake\PrestoClient\Session\PreparedStatement;
use Ytake\PrestoClient\Session\Property;
use Ytake\PrestoClient\StatementStats;

/**
 * Class StatementClientTest
 */
class StatementClientTest extends \PHPUnit\Framework\TestCase
{
    use MockClientTrait, TestReflectionTrait;

    public function testShouldNoCurrent()
    {
        $client = new \Ytake\PrestoClient\StatementClient(
            $this->session(),
            'SELECT * FROM example.hoge.fuga',
            $this->mockClient(StatusCodeInterface::STATUS_OK)
        );
        $this->assertSame('SELECT * FROM example.hoge.fuga', $client->getQuery());
        $queryResult = $client->current();
        $this->assertInstanceOf(QueryResult::class, $queryResult);
        $this->assertNull($queryResult->getId());
        $this->assertNull($queryResult->getInfoUri());
        $this->assertNull($queryResult->getPartialCancelUri());
        $this->assertNull($queryResult->getNextUri());
        $this->assertNull($queryResult->getStats());
        $this->assertNull($queryResult->getError());
        $this->assertCount(0, $queryResult->getColumns());
        $this->assertInstanceOf(\Generator::class, $queryResult->getData());
        $this->assertFalse($client->cancelLeafStage());
        $this->assertFalse($client->isClosed());
        $this->assertFalse($client->advance());
        $this->assertFalse($client->isValid());
        $this->assertFalse($client->isGone());
        $this->assertFalse($client->isFailed());
        $client->close();
        $this->assertTrue($client->isClosed());
    }

    public function testShouldBeErrorQueryResult()
    {
        $error = file_get_contents(realpath(__DIR__ . '/data/error.json'));
        $client = new \Ytake\PrestoClient\StatementClient(
            $this->session(),
            'SELECT * FROM example.hoge.fuga',
            $this->mockClient(StatusCodeInterface::STATUS_OK, $error)
        );
        $this->assertNull($client->execute());
        $this->assertFalse($client->advance());
        $queryResult = $client->current();
        $this->assertInstanceOf(QueryResult::class, $queryResult);
        $this->assertNotNull($queryResult->getId());
        $this->assertNotNull($queryResult->getInfoUri());
        $this->assertNull($queryResult->getPartialCancelUri());
        $this->assertNull($queryResult->getNextUri());
        $stats = $queryResult->getStats();
        $this->assertInstanceOf(StatementStats::class, $stats);
        $this->assertSame('FAILED', $stats->getState());
        $this->assertFalse($stats->isQueued());
        $this->assertFalse($stats->isScheduled());
        $this->assertSame(0, $stats->getCompletedSplits());
        $this->assertSame(0, $stats->getCpuTimeMillis());
        $this->assertSame(0, $stats->getNodes());
        $this->assertSame(0, $stats->getProcessedBytes());
        $this->assertSame(0, $stats->getProcessedRows());
        $this->assertSame(0, $stats->getCpuTimeMillis());
        $this->assertSame(0, $stats->getQueuedSplits());
        $this->assertSame(0, $stats->getRunningSplits());
        $this->assertSame(0, $stats->getCompletedSplits());
        $this->assertSame(0, $stats->getTotalSplits());
        $this->assertSame(0, $stats->getUserTimeMillis());
        $this->assertSame(0, $stats->getWallTimeMillis());
        $error = $queryResult->getError();
        $this->assertInstanceOf(QueryError::class, $error);
        $this->assertInternalType('string', $error->getMessage());
        $this->assertInternalType('int', $error->getErrorCode());
        $this->assertInternalType('string', $error->getErrorName());
        $this->assertInternalType('string', $error->getErrorType());
        $this->assertInstanceOf(\stdClass::class, $error->getFailureInfo());
        $this->assertInternalType('string', $error->getSqlState());
    }

    /**
     * @expectedException \GuzzleHttp\Exception\RequestException
     */
    public function testShouldThrowRequestException()
    {
        $client = new \Ytake\PrestoClient\StatementClient(
            $this->session(),
            'SELECT * FROM example.hoge.fuga',
            $this->throwRequestExceptionClient()
        );
        $client->execute();
    }

    /**
     * @expectedException \Ytake\PrestoClient\Exception\QueryErrorException
     */
    public function testShouldThrowQueryErrorException()
    {
        $client = new \Ytake\PrestoClient\StatementClient(
            $this->session(),
            'SELECT * FROM example.hoge.fuga',
            $this->throwClientExceptionClient()
        );
        $client->execute();
    }

    public function testFunctionalClientProperties()
    {
        $client = new \Ytake\PrestoClient\StatementClient(
            $this->session(),
            'SELECT * FROM example.hoge.fuga',
            $this->mockClient(StatusCodeInterface::STATUS_OK)
        );
        $property = $this->getProtectProperty($client, 'headers');
        $defaultHeaders = $property->getValue($client);
        $this->assertArrayHasKey(PrestoHeaders::PRESTO_USER, $defaultHeaders);
        $this->assertArrayHasKey('User-Agent', $defaultHeaders);

        $session = $this->session();
        $session->setPreparedStatement(new PreparedStatement('testing', '1'));
        $session->setProperty(new Property('testing', 'testing'));
        $body = file_get_contents(realpath(__DIR__ . '/data/success.json'));
        $client = new \Ytake\PrestoClient\StatementClient(
            $session,
            'SELECT * FROM example.hoge.fuga',
            $this->mockClient(StatusCodeInterface::STATUS_OK, $body)
        );
        $client->execute();
        $queryResult = $client->current();
        $this->assertInstanceOf(QueryResult::class, $queryResult);
        $this->assertNotNull($queryResult->getId());
        $this->assertNotNull($queryResult->getInfoUri());
        $this->assertNull($queryResult->getPartialCancelUri());
        $this->assertNotNull($queryResult->getNextUri());
        $stats = $queryResult->getStats();
        $this->assertInstanceOf(StatementStats::class, $stats);
        $this->assertSame('QUEUED', $stats->getState());
        $this->assertTrue($stats->isQueued());
        $this->assertFalse($stats->isScheduled());
    }

    public function testFunctionalStackTwo()
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/data/success.json'))),
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/data/next_response.json'))),
        ]);
        $client = new \Ytake\PrestoClient\StatementClient(
            $this->session(),
            'SELECT * FROM example.hoge.fuga',
            new Client(['handler' => HandlerStack::create($mock)])
        );
        $client->execute();
        $this->assertTrue($client->advance());
        $queryResult = $client->current();
        $this->assertInstanceOf(\stdClass::class, $queryResult->getStats()->getRootStage());
        $columns = $queryResult->getColumns();
        $column = $columns[0];
        $this->assertInstanceOf(Column::class, $column);
        $this->assertSame('test_id', $column->getName());
        $this->assertSame('integer', $column->getType());
        $this->assertInstanceOf(\stdClass::class, $column->getTypeSignature());
    }

    public function testFunctionalStackFour()
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/data/success.json'))),
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/data/next_response.json'))),
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/data/third_response.json'))),
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/data/fourth_response.json'))),
        ]);
        $client = new \Ytake\PrestoClient\StatementClient(
            $this->session(),
            'SELECT * FROM example.hoge.fuga',
            new Client(['handler' => HandlerStack::create($mock)])
        );
        $client->execute();
        $this->assertTrue($client->advance());
        $queryResult = $client->current();
        $this->assertInstanceOf(\stdClass::class, $queryResult->getStats()->getRootStage());
        $columns = $queryResult->getColumns();
        $column = $columns[0];
        $this->assertInstanceOf(Column::class, $column);
        $this->assertSame('test_id', $column->getName());
        $this->assertSame('integer', $column->getType());
        $this->assertInstanceOf(\stdClass::class, $column->getTypeSignature());
        $this->assertTrue($client->advance());
        $this->assertTrue($client->advance());
        $queryResult = $client->current();
        $this->assertInstanceOf(\Generator::class, $queryResult->getData());
        /** @var FixData[] $array */
        $array = iterator_to_array($queryResult->getData());
        $this->assertCount(1, $array);
        $this->assertContainsOnly(FixData::class, $array);
        $this->assertSame(1, $array[0]['test_id']);
        $this->assertSame(1, $array[0]->test_id);
        $this->assertSame(1, $array[0]->offsetGet('test_id'));
        $this->assertTrue($array[0]->offsetExists('test_id'));
        $array[0]->offsetUnset('test_id');
        $this->assertNull($array[0]->offsetGet('test_id'));
        $array[0]->offsetSet('test_id', 12);
        $this->assertSame(12, $array[0]->offsetGet('test_id'));
        $this->assertFalse($client->advance());
    }

    public function testShouldBeCancel()
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/data/success.json'))),
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/data/next_response.json'))),
            new Response(204, [], file_get_contents(realpath(__DIR__ . '/data/third_response.json'))),
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/data/fourth_response.json'))),
        ]);
        $client = new \Ytake\PrestoClient\StatementClient(
            $this->session(),
            'SELECT * FROM example.hoge.fuga',
            new Client(['handler' => HandlerStack::create($mock)])
        );
        $client->execute();
        $this->assertTrue($client->advance());
        $this->assertTrue($client->cancelLeafStage());
        $this->assertTrue($client->advance());
        $this->assertFalse($client->advance());
    }

    /**
     * @expectedException \Ytake\PrestoClient\Exception\RequestFailedException
     */
    public function testShouldThrowRequestFailedException()
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/data/success.json'))),
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/data/success.json'))),
            new Response(404, [], file_get_contents(realpath(__DIR__ . '/data/next_response.json'))),
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/data/success.json'))),
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/data/success.json'))),
        ]);
        $client = new \Ytake\PrestoClient\StatementClient(
            $this->session(),
            'SELECT * FROM example.hoge.fuga',
            new Client(['handler' => HandlerStack::create($mock)])
        );
        $client->execute();
        $client->advance();
        $client->advance();
    }

    /**
     * @return ClientSession
     */
    private function session(): ClientSession
    {
        return new ClientSession('http://localhost', 'testing');
    }
}
