<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\RateLimiter\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\RateLimiter\FixedWindowLimiter;
use Symfony\Component\RateLimiter\NoLimiter;
use Symfony\Component\RateLimiter\RateLimiter;
use Symfony\Component\RateLimiter\SlidingWindowLimiter;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;
use Symfony\Component\RateLimiter\TokenBucketLimiter;

class RateLimiterTest extends TestCase
{
    /**
     * @dataProvider validConfigProvider
     */
    public function testValidConfig(string $expectedClass, array $config)
    {
        $factory = new RateLimiter($config, new InMemoryStorage());
        $rateLimiter = $factory->create('key');
        $this->assertInstanceOf($expectedClass, $rateLimiter);
    }

    public function validConfigProvider()
    {
        yield [TokenBucketLimiter::class, [
            'strategy' => 'token_bucket',
            'id' => 'test',
            'limit' => 5,
            'rate' => [
                'interval' => '5 seconds',
            ],
        ]];
        yield [FixedWindowLimiter::class, [
            'strategy' => 'fixed_window',
            'id' => 'test',
            'limit' => 5,
            'interval' => '5 seconds',
        ]];
        yield [SlidingWindowLimiter::class, [
            'strategy' => 'sliding_window',
            'id' => 'test',
            'limit' => 5,
            'interval' => '5 seconds',
        ]];
        yield [NoLimiter::class, [
            'strategy' => 'no_limit',
            'id' => 'test',
        ]];
    }

    /**
     * @dataProvider invalidConfigProvider
     */
    public function testInvalidConfig(string $exceptionClass, array $config)
    {
        $this->expectException($exceptionClass);
        $factory = new RateLimiter($config, new InMemoryStorage());
        $factory->create('key');
    }

    public function invalidConfigProvider()
    {
        yield [MissingOptionsException::class, [
            'strategy' => 'token_bucket',
        ]];
    }
}
