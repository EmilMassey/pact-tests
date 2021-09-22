<?php

namespace Emil\PactTests\Tests\Consumer;

use Emil\PactTests\Consumer\HttpClient;
use PHPUnit\Framework\TestCase;

class HttpClientTest extends TestCase
{
    public function testPreventSettingBaseUriInConfig(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The "base_uri" option is no-op, use constructor parameter instead.');

        new HttpClient('http://example.com', ['base_uri' => 'http://lorem-ipsum.com']);
    }

    public function testBaseUriMustNotBeEmptyString(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The "base_uri" must not be empty.');

        new HttpClient('');
    }

    public function testBaseUriMustNotBeBlank(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The "base_uri" must not be empty.');

        new HttpClient('  ');
    }

    /** @dataProvider provideInvalidUrls */
    public function testBaseUriMustBeValidUrl(string $invalidUrl): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The "base_uri" parameter is invalid.');

        new HttpClient($invalidUrl);
    }

    public function testBaseUriCanBeGet(): void
    {
        $client = new HttpClient('http://example.com');
        $this->assertSame('http://example.com', $client->getBaseUri());
    }

    public function testBaseUriGuzzleOptionIsSet(): void
    {
        $client = new HttpClient('http://example.com');

        $clientReflection = new \ReflectionClass($client);
        $config = $clientReflection->getParentClass()->getProperty('config');
        $config->setAccessible(true);

        $this->assertSame('http://example.com', (string) $config->getValue($client)['base_uri']);
    }

    /** @return string[][] */
    public function provideInvalidUrls(): array
    {
        return [
            ['example'],
            ['@@@'],
            ['-----'],
            ['_'],
            ['.com'],
        ];
    }
}
