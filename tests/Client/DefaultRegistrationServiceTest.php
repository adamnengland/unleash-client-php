<?php

namespace Unleash\Client\Tests\Client;

use ArrayIterator;
use GuzzleHttp\Psr7\HttpFactory;
use Unleash\Client\Client\DefaultRegistrationService;
use Unleash\Client\Configuration\UnleashConfiguration;
use Unleash\Client\Strategy\DefaultStrategyHandler;
use Unleash\Client\Tests\AbstractHttpClientTest;
use Unleash\Client\Tests\Traits\RealCacheImplementationTrait;

final class DefaultRegistrationServiceTest extends AbstractHttpClientTest
{
    use RealCacheImplementationTrait;

    public function testRegister()
    {
        $configuration = (new UnleashConfiguration('', '', ''))
            ->setHeaders([
                'Some-Header' => 'some-value',
            ])
            ->setCache($this->getCache())
            ->setTtl(0);
        $instance = new DefaultRegistrationService(
            $this->httpClient,
            new HttpFactory(),
            $configuration
        );

        $this->pushResponse([], 2, 202);
        self::assertTrue($instance->register([
            new DefaultStrategyHandler(),
        ]));
        self::assertTrue($instance->register(new ArrayIterator([
            new DefaultStrategyHandler(),
        ])));

        $this->pushResponse([
            'type' => 'password',
            'path' => '/auth/simple/login',
            'message' => 'You must sign in order to use Unleash',
        ], 1, 401);
        self::assertFalse($instance->register([]));

        $this->pushResponse([], 1, 400);
        self::assertFalse($instance->register([]));

        $configuration->setTtl(30);
        $this->pushResponse([]);
        self::assertTrue($instance->register([]));
        self::assertTrue($instance->register([]));
    }
}
