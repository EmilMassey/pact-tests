<?php

namespace Emil\PactTests\Consumer;

use Emil\PactTests\Domain\Exception\ResourceNotFound;
use Emil\PactTests\Domain\Model\Address;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\Serializer\SerializerInterface;

final class AddressClient
{
    public function __construct(private HttpClientInterface $client, private SerializerInterface $serializer)
    {
    }

    /** @return Address[] */
    public function getAddressList(): array
    {
        $response = $this->client->request('GET', '/address', [
            'headers' => ['Content-Type' => 'application/json']
        ]);

        return $this->serializer->deserialize($response->getBody()->getContents(), Address::class . '[]', 'json');
    }

    public function getAddress(string $id): Address
    {
        try {
            $response = $this->client->request('GET', "/address/$id", [
                'headers' => ['Content-Type' => 'application/json']
            ]);

            return $this->serializer->deserialize($response->getBody()->getContents(), Address::class, 'json');
        } catch (ClientException $exception) {
            if ($exception->getResponse()->getStatusCode() === 404) {
                throw new ResourceNotFound("Address $id not found", 404, $exception);
            }

            throw $exception;
        }
    }
}