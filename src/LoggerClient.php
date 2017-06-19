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

use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\MessageFormatter;
use Psr\Log\LoggerInterface;

/**
 * Class LoggerClient
 *
 * @author Yuuki Takezawa <yuuki.takezawa@comnect.jp.net>
 */
class LoggerClient
{
    /** @var LoggerInterface */
    protected $logger;

    /** @var string */
    protected $template = '{date_common_log} {uri} {req_headers} {req_body} {res_headers}';

    /**
     * LoggerClient constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param callable|null $handler
     *
     * @return ClientInterface
     */
    public function client(callable $handler = null): ClientInterface
    {
        $handlerStack = HandlerStack::create($handler);
        $handlerStack->push(
            Middleware::log($this->logger, new MessageFormatter($this->template))
        );
        return new Client([
            'handler' => $handlerStack,
        ]);
    }

    /**
     * @codeCoverageIgnore
     * @param string $template
     */
    public function setTemplate(string $template)
    {
        $this->template = $template;
    }
}
