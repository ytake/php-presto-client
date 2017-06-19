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
 * Class QueryError
 *
 * @author Yuuki Takezawa <yuuki.takezawa@comnect.jp.net>
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
