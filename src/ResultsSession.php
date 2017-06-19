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

/**
 * Class ResultsSession
 *
 * @author Yuuki Takezawa <yuuki.takezawa@comnect.jp.net>
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
