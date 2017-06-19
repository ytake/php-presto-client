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
 * Class PrestoHeaders
 *
 * @author Yuuki Takezawa <yuuki.takezawa@comnect.jp.net>
 */
final class PrestoHeaders
{
    const PRESTO_USER = "X-Presto-User";
    const PRESTO_SOURCE = "X-Presto-Source";
    const PRESTO_CATALOG = "X-Presto-Catalog";
    const PRESTO_SCHEMA = "X-Presto-Schema";
    const PRESTO_TIME_ZONE = "X-Presto-Time-Zone";
    const PRESTO_LANGUAGE = "X-Presto-Language";
    const PRESTO_SESSION = "X-Presto-Session";
    const PRESTO_SET_SESSION = "X-Presto-Set-Session";
    const PRESTO_CLEAR_SESSION = "X-Presto-Clear-Session";
    const PRESTO_PREPARED_STATEMENT = "X-Presto-Prepared-Statement";
    const PRESTO_ADDED_PREPARE = "X-Presto-Added-Prepare";
    const PRESTO_DEALLOCATED_PREPARE = "X-Presto-Deallocated-Prepare";
    const PRESTO_TRANSACTION_ID = "X-Presto-Transaction-Id";
    const PRESTO_STARTED_TRANSACTION_ID = "X-Presto-Started-Transaction-Id";
    const PRESTO_CLEAR_TRANSACTION_ID = "X-Presto-Clear-Transaction-Id";
    const PRESTO_CLIENT_INFO = "X-Presto-Client-Info";
    const PRESTO_CURRENT_STATE = "X-Presto-Current-State";
    const PRESTO_MAX_WAIT = "X-Presto-Max-Wait";
    const PRESTO_MAX_SIZE = "X-Presto-Max-Size";
    const PRESTO_TASK_INSTANCE_ID = "X-Presto-Task-Instance-Id";
    const PRESTO_PAGE_TOKEN = "X-Presto-Page-Sequence-Id";
    const PRESTO_PAGE_NEXT_TOKEN = "X-Presto-Page-End-Sequence-Id";
    const PRESTO_BUFFER_COMPLETE = "X-Presto-Buffer-Complete";

    /** library version */
    const VERSION = '0.1.0';
    const PRESTO_SOURCE_VALUE = 'PrestoClient';

    private function __construct()
    {
        //
    }
}
