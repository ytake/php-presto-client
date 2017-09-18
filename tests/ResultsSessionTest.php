<?php
declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Ytake\PrestoClient\ClientSession;
use Ytake\PrestoClient\QueryResult;
use Ytake\PrestoClient\ResultsSession;

/**
 * Class ResultsSessionTest
 */
class ResultsSessionTest extends \PHPUnit\Framework\TestCase
{
    public function testShouldReturnQueryResultInstance()
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
        $resultSession = new ResultsSession($client);
        $result = $resultSession->execute()->getResults();
        $this->assertNotCount(0, $result);
        $this->assertContainsOnlyInstancesOf(QueryResult::class, $result);
    }

    public function testShouldReturnQueryResultGenerator()
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
        $resultSession = new ResultsSession($client);
        $result = $resultSession->execute()->yieldResults();
        $this->assertInstanceOf(\Generator::class, $result);
        /** @var \Ytake\PrestoClient\QueryResult $row */
        foreach ($result as $row) {
            $this->assertInstanceOf(QueryResult::class, $row);
            $this->assertInstanceOf(\Generator::class, $row->yieldData());
            foreach ($row->yieldData() as $item) {
                if (!is_null($item)) {
                    $this->assertInstanceOf(\Ytake\PrestoClient\FixData::class, $item);
                }
            }
        }
    }

    public function testShouldReturnQueryResultWithDataArray()
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
        $resultSession = new ResultsSession($client);
        $result = $resultSession->execute()->yieldResults();
        foreach ($result as $row) {
            foreach ($row->yieldDataArray() as $item) {
                if (!is_null($item)) {
                    $this->assertInternalType('array', $item);
                    $this->assertArrayHasKey('test_id', $item);
                }
            }
        }
    }

    public function testShouldBeExpectInstance()
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
        $resultSession = new ResultsSession($client);
        $result = $resultSession->execute()->yieldResults();
        /** @var \Ytake\PrestoClient\QueryResult $row */
        foreach ($result as $row) {
            foreach ($row->yieldObject(MockResultTest::class) as $item) {
                if (!is_null($item)) {
                    $this->assertInstanceOf(MockResultTest::class, $item);
                    $this->assertSame(1, $item->testId());
                }
            }
        }
    }

    /**
     * @return ClientSession
     */
    private function session(): ClientSession
    {
        return new ClientSession('http://localhost', 'testing');
    }
}

class MockResultTest
{
    /** @var int */
    private $test_id;

    /**
     * @return int
     */
    public function testId(): int
    {
        return $this->test_id;
    }
}
