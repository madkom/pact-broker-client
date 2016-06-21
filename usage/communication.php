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
$responseFormatter = new \Madkom\PactBrokerClient\Formatter\ToPactContractFormatter();

// publishing new versions and tagging
$response = $client->publishPact('TestProvider1', 'TestConsumer1', '1.0.5', $pactSimpleV1);
$response = $client->publishPact('TestProvider2', 'TestConsumer1', '1.0.5', $pactAdvancedV1);
$client->tagVersion('TestConsumer1', '1.0.5', 'prod');
$response = $client->publishPact('TestProvider1', 'TestConsumer1', '1.1.0', $pactSimpleV2);
$response = $client->publishPact('TestProvider2', 'TestConsumer1', '1.1.0', $pactAdvancedV2);

echo "\n\nProvider 1 Consumer 1\n\n";
$responseProd   = $client->retrievePact('TestProvider1', 'TestConsumer1', 'latest', 'prod', $responseFormatter);
$responseLatest = $client->retrievePact('TestProvider1', 'TestConsumer1', 'latest', null, $responseFormatter);
var_dump($responseProd);
dump($responseProd->version(), $responseProd);
dump($responseLatest->version(), $responseLatest);

echo "\n\n---------------------------------------------------------------------------\n\n";

echo "\n\nProvider 2 Consumer 1\n\n";
$responseProd   = $client->retrievePact('TestProvider2', 'TestConsumer1', 'latest', 'prod');
$responseLatest = $client->retrievePact('TestProvider2', 'TestConsumer1', 'latest');
dump($responseProd->version(), json_decode($responseProd->data()->getBody()->getContents(), true));
dump($responseLatest->version(), json_decode($responseLatest->data()->getBody()->getContents(), true));


echo "\n\n Removing Participant\n\n";

$client->removeParticipant('TestProvider2');
try {
    //should throw exception
    $client->retrievePact('TestProvider2', 'TestConsumer1', 'latest', 'prod');
    throw new \Exception('should never enter this');
}catch(\Madkom\PactBrokerClient\PactBrokerException $e) {

}

$client->removeParticipant('TestConsumer1');
try {
    $client->retrievePact('TestProvider1', 'TestConsumer1', 'latest', 'prod');
    throw new \Exception('should never enter this');
}catch(\Madkom\PactBrokerClient\PactBrokerException $e) {

}