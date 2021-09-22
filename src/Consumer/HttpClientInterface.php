<?php

namespace Emil\PactTests\Consumer;

use GuzzleHttp\ClientInterface;

interface HttpClientInterface extends ClientInterface
{
    public function getBaseUri(): string;
}