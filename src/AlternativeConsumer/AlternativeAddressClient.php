<?php

namespace Emil\PactTests\AlternativeConsumer;

use Emil\PactTests\Consumer\HttpClientInterface;

final class AlternativeAddressClient
{
    public function __construct(private HttpClientInterface $client)
    {
    }

    /** @return array{"firstname": string, "lastname": string} */
    public function getAddress(string $id): array
    {
        $response = $this->client->request('GET', "/address/$id", [
            'headers' => ['Content-Type' => 'application/json']
        ]);

        $body = json_decode($response->getBody()->getContents(), true);

        return [
            'firstname' => $body['firstname'],
            'lastname' => $body['lastname'],
        ];
    }
}