<?php

use Monolog\Logger;
use Monolog\Handler\TestHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use Ytake\PrestoClient\LoggerClient;
use Ytake\PrestoClient\ClientSession;

/**
 * Class LoggerClientTest
 */
class LoggerClientTest extends \PHPUnit\Framework\TestCase
{
    public function testShouldOutputLogger()
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/data/success.json'))),
        ]);

        $logger = new Logger('client.testing');
        $testHandler = new TestHandler(Logger::INFO);
        $logger->pushHandler($testHandler);
        $loggerClient = new LoggerClient($logger);

        $client = new \Ytake\PrestoClient\StatementClient(
            $this->session(),
            'SELECT * FROM example.hoge.fuga',
            $loggerClient->client($mock)
        );
        $client->execute();
        $records = $testHandler->getRecords();
        $this->assertCount(1, $records);
    }

    /**
     * @return ClientSession
     */
    private function session(): ClientSession
    {
        return new ClientSession('http://localhost', 'testing');
    }
}
