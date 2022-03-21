<?php

namespace GloCurrency\UnionBank\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use GloCurrency\UnionBank\UnionBankServiceProvider;
use GloCurrency\UnionBank\UnionBank;
use GloCurrency\UnionBank\Tests\Fixtures\TransactionFixture;
use GloCurrency\UnionBank\Tests\Fixtures\ProcessingItemFixture;

abstract class TestCase extends OrchestraTestCase
{
    protected function getEnvironmentSetUp($app)
    {
        UnionBank::useTransactionModel(TransactionFixture::class);
        UnionBank::useProcessingItemModel(ProcessingItemFixture::class);
    }

    protected function getPackageProviders($app)
    {
        return [UnionBankServiceProvider::class];
    }

    /**
     * Create the HTTP mock for API.
     *
     * @return array<\GuzzleHttp\Handler\MockHandler|\GuzzleHttp\HandlerStack> [$httpMock, $handlerStack]
     */
    protected function mockApiFor(string $class): array
    {
        $httpMock = new \GuzzleHttp\Handler\MockHandler();
        $handlerStack = \GuzzleHttp\HandlerStack::create($httpMock);

        $this->app->when($class)
            ->needs(\GuzzleHttp\ClientInterface::class)
            ->give(function () use ($handlerStack) {
                return new \GuzzleHttp\Client(['handler' => $handlerStack]);
            });

        return [$httpMock, $handlerStack];
    }
}
