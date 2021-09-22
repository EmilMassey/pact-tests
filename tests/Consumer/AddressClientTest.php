<?php

namespace Emil\PactTests\Tests\Consumer;

use Emil\PactTests\Consumer\AddressClient;
use Emil\PactTests\Consumer\HttpClient;
use Emil\PactTests\Domain\Exception\ResourceNotFound;
use Emil\PactTests\Domain\Model\Address;
use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Consumer\Matcher\Matcher;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Standalone\MockService\MockServerEnvConfig;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class AddressClientTest extends TestCase
{
    public function testGetAddressList(): void
    {
        $matcher = new Matcher();

        $request = new ConsumerRequest();
        $request
            ->setMethod('GET')
            ->setPath('/address')
            ->addHeader('Content-Type', 'application/json');

        $response = new ProviderResponse();
        $response
            ->setStatus(200)
            ->addHeader('Content-Type', 'application/json')
            ->setBody(
                $matcher->eachLike([
                    'street' => 'lorem',
                    'city' => 'ipsum',
                    'postcode' => '00-000',
                ])
            );

        $config = new MockServerEnvConfig();
        $builder = new InteractionBuilder($config);
        $builder
            ->given('There is at least one address')
            ->uponReceiving('A get request to /address')
            ->with($request)
            ->willRespondWith($response);

        $client = new AddressClient(new HttpClient($config->getBaseUri()), $this->getSerializer());
        $result = $client->getAddressList();

        $builder->verify();

        $this->assertEquals([new Address('lorem', 'ipsum', '00-000')], $result);
    }

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
                    'street' => 'lorem',
                    'city' => 'ipsum',
                    'postcode' => '00-000',
                ])
            );

        $config = new MockServerEnvConfig();
        $builder = new InteractionBuilder($config);
        $builder
            ->given('Address 52d4387f-e880-4dbc-8c28-5f5d27aee05f exists')
            ->uponReceiving('A get request to /address/{id}')
            ->with($request)
            ->willRespondWith($response);

        $client = new AddressClient(new HttpClient($config->getBaseUri()), $this->getSerializer());
        $result = $client->getAddress('52d4387f-e880-4dbc-8c28-5f5d27aee05f');

        $builder->verify();

        $this->assertEquals(new Address('lorem', 'ipsum', '00-000'), $result);
    }

    public function testGetAddressWhenNotExist(): void
    {
        $matcher = new Matcher();

        $request = new ConsumerRequest();
        $request
            ->setMethod('GET')
            ->setPath('/address/non-existent')
            ->addHeader('Content-Type', 'application/json');

        $response = new ProviderResponse();
        $response
            ->setStatus(404)
            ->addHeader('Content-Type', 'application/json')
            ->setBody(
                $matcher->like([
                    'type' => 'https://tools.ietf.org/html/rfc2616#section-10',
                    'title' => 'Resource not found',
                    'status' => 404,
                ])
            );

        $config = new MockServerEnvConfig();
        $builder = new InteractionBuilder($config);
        $builder
            ->given('Address "non-existent" does not exist')
            ->uponReceiving('A get request to /address/{id}')
            ->with($request)
            ->willRespondWith($response);

        $client = new AddressClient(new HttpClient($config->getBaseUri()), $this->getSerializer());

        $exception = null;

        try {
            $client->getAddress('non-existent');
        } catch (ResourceNotFound $e) {
            $exception = $e;
        }

        $builder->verify();

        $this->assertInstanceOf(ResourceNotFound::class, $exception, 'Request for non-existent address did not throw exception');
    }

    private function getSerializer(): SerializerInterface
    {
        return new Serializer([new ObjectNormalizer(), new ArrayDenormalizer(), new JsonSerializableNormalizer()], [new JsonEncoder()]);
    }
}
