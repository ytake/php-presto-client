# Ytake\PrestoClient

prestodb http protocol client for php 

[prestodb](https://prestodb.io/)

## What is Presto

Presto is an open source distributed SQL query engine for running interactive analytic queries against data sources of all sizes ranging from gigabytes to petabytes.

## Install

*required >= PHP 7.0*

```bash
$ composer require ytake/php-presto-client
```

## Usage

### Standard
 
```php
<?php

$client = new \Ytake\PrestoClient\StatementClient(
    new \Ytake\PrestoClient\ClientSession('http://localhost:8080/', 'acme'),
    'SELECT * FROM acme.acme.acme'
);
// execute http request
$client->execute();
// next call uri
$client->advance();

/** @var \Ytake\PrestoClient\QueryResult $result */
// current result
$result = $client->current();

// request cancel
$client->cancelLeafStage();
```

### bulk operations

```php
<?php

$client = new \Ytake\PrestoClient\StatementClient(
    new \Ytake\PrestoClient\ClientSession('http://localhost:8080/', 'acme'),
    'SELECT * FROM acme.acme.acme'
);
$resultSession = new \Ytake\PrestoClient\ResultsSession($client);
// yield results instead of returning them. Recommended.
$result = $resultSession->execute()->yieldResults();

// array
$result = $resultSession->execute()->getResults();
```

