# Ytake\PrestoClient

[![Build Status](http://img.shields.io/travis/ytake/php-presto-client/master.svg?style=flat-square)](https://travis-ci.org/ytake/php-presto-client)
[![Coverage Status](http://img.shields.io/coveralls/ytake/php-presto-client/master.svg?style=flat-square)](https://coveralls.io/r/ytake/php-presto-client?branch=master)
[![Scrutinizer Code Quality](http://img.shields.io/scrutinizer/g/ytake/php-presto-client.svg?style=flat-square)](https://scrutinizer-ci.com/g/ytake/php-presto-client/?branch=master)

[![License](http://img.shields.io/packagist/l/ytake/php-presto-client.svg?style=flat-square)](https://packagist.org/packages/ytake/php-presto-client)
[![Latest Version](http://img.shields.io/packagist/v/ytake/php-presto-client.svg?style=flat-square)](https://packagist.org/packages/ytake/php-presto-client)
[![Total Downloads](http://img.shields.io/packagist/dt/ytake/php-presto-client.svg?style=flat-square)](https://packagist.org/packages/ytake/php-presto-client)
[![StyleCI](https://styleci.io/repos/94699825/shield?branch=master)](https://styleci.io/repos/94699825)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/9a13a5c0-7588-459f-835e-d73dabd22843/mini.png)](https://insight.sensiolabs.com/projects/9a13a5c0-7588-459f-835e-d73dabd22843)

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

