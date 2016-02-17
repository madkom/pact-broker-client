<?php
require __DIR__ . '/../vendor/autoload.php';

$pactSimpleV1     = __DIR__ .'/../tests/stub/files/simple-contract.json';
$pactSimpleV2     = __DIR__ .'/../tests/stub/files/simple-contract-2.json';
$pactAdvancedV1   = __DIR__ .'/../tests/stub/files/advanced-contract.json';
$pactAdvancedV2   = __DIR__ .'/../tests/stub/files/advanced-contract-2.json';

// Pass here your pact-broker IP
$baseUrl = '172.17.0.3:80';

$client  = new \Http\Adapter\Guzzle6\Client();
$client = new \Madkom\PactBrokerClient\HttpBrokerClient($baseUrl, $client, new \Madkom\PactBrokerClient\RequestBuilder());


// publishing new versions and tagging
$response = $client->publishPact('TestProvider1', 'TestConsumer1', '1.0.5', $pactSimpleV1);
$response = $client->publishPact('TestProvider2', 'TestConsumer1', '1.0.5', $pactAdvancedV1);
$client->tagVersion('TestConsumer1', '1.0.5', 'prod');
$response = $client->publishPact('TestProvider1', 'TestConsumer1', '1.1.0', $pactSimpleV2);
$response = $client->publishPact('TestProvider2', 'TestConsumer1', '1.1.0', $pactAdvancedV2);

echo "\n\nProvider 1 Consumer 1\n\n";
$responseProd   = $client->retrievePact('TestProvider1', 'TestConsumer1', 'latest', 'prod');
$responseLatest = $client->retrievePact('TestProvider1', 'TestConsumer1', 'latest');
dump(json_decode($responseProd->getBody()->getContents(), true));
dump(json_decode($responseLatest->getBody()->getContents(), true));

echo "\n\n---------------------------------------------------------------------------\n\n";

echo "\n\nProvider 2 Consumer 1\n\n";
$responseProd   = $client->retrievePact('TestProvider2', 'TestConsumer1', 'latest', 'prod');
$responseLatest = $client->retrievePact('TestProvider2', 'TestConsumer1', 'latest');
dump(json_decode($responseProd->getBody()->getContents(), true));
dump(json_decode($responseLatest->getBody()->getContents(), true));
