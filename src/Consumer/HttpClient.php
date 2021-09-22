<?php

namespace Emil\PactTests\Consumer;

use GuzzleHttp\Client;
use Webmozart\Assert\Assert;

final class HttpClient extends Client implements HttpClientInterface
{
    private string $baseUri;

    public function __construct(string $baseUri, array $config = [])
    {
        Assert::keyNotExists($config, 'base_uri', 'The "base_uri" option is no-op, use constructor parameter instead.');
        Assert::notEmpty(trim($baseUri), 'The "base_uri" must not be empty.');
        Assert::notFalse(filter_var($baseUri, FILTER_VALIDATE_URL), 'The "base_uri" parameter is invalid.');

        $config['base_uri'] = $baseUri;

        parent::__construct($config);

        $this->baseUri = $baseUri;
    }

    public function getBaseUri(): string
    {
        return $this->baseUri;
    }
}