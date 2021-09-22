<?php

namespace Emil\PactTests\Tests\Provider;

use GuzzleHttp\Psr7\Uri;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PhpPact\Standalone\ProviderVerifier\Verifier;
use PhpPact\Standalone\Runner\ProcessRunner;
use PHPUnit\Framework\TestCase;

class PactVerificationTest extends TestCase
{
    private ProcessRunner $processRunner;

    protected function setUp(): void
    {
        $publicPath = __DIR__ . '/../../src/Provider/';

        $this->processRunner = new ProcessRunner('APP_ENV=test php', ['-S', 'localhost:7202', '-t', $publicPath]);
        $this->processRunner->run();
    }

    protected function tearDown(): void
    {
        $this->processRunner->stop();
    }

    public function testPactVerifyConsumer(): void
    {
        $config = new VerifierConfig();
        $config
            ->setProviderName('someProvider')
            ->setProviderVersion('2.0.0')
            ->setProviderBaseUrl(new Uri('http://localhost:7202'))
            ->setProviderStatesSetupUrl('http://localhost:7202/setup-pact-state')
            ->setBrokerUri(new Uri('http://localhost:9292'))
            ->setPublishResults(true)
            ->setProcessTimeout(60)
            ->setProcessIdleTimeout(10)
            ->setEnablePending(true);

        $verifier = new Verifier($config);
        $verifier->verifyAll();

        $this->assertTrue(true, 'Pact Verification has failed.');
    }
}