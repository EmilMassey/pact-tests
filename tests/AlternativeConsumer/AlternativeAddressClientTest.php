<?php

namespace Emil\PactTests\Tests\AlternativeConsumer;

use Emil\PactTests\AlternativeConsumer\AlternativeAddressClient;
use Emil\PactTests\Consumer\HttpClient;
use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Consumer\Matcher\Matcher;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Standalone\MockService\MockServerEnvConfig;
use PHPUnit\Framework\TestCase;

class AlternativeAddressClientTest extends TestCase
{
    public function testGetAddress(): void
    {
        $matcher = new Matcher();

        $request = new ConsumerRequest();
        $request
            ->setMethod('GET')
            ->setPath('/address/52d4387f-e880-4dbc-8c28-5f5d27aee05f')
            ->addHeader('Content-Type', 'application/json');

        $response = new ProviderResponse();
        $response
            ->setStatus(200)
            ->addHeader('Content-Type', 'application/json')
            ->setBody(
                $matcher->like([
                    'firstname' => 'John',
                    'lastname' => 'Doe',
                ])
            );

        $config = new MockServerEnvConfig();
        $builder = new InteractionBuilder($config);
        $builder
            ->given('Address 52d4387f-e880-4dbc-8c28-5f5d27aee05f exists')
            ->uponReceiving('A get request to /address/{id}')
            ->with($request)
            ->willRespondWith($response);

        $client = new AlternativeAddressClient(new HttpClient($config->getBaseUri()));
        $result = $client->getAddress('52d4387f-e880-4dbc-8c28-5f5d27aee05f');

        $builder->verify();

        $this->assertSame(['firstname' => 'John', 'lastname' => 'Doe'], $result);
    }
}
