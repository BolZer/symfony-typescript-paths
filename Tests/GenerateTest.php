<?php

declare(strict_types=1);

namespace Bolzer\SymfonyTypescriptRoutes\Tests;

use Bolzer\SymfonyTypescriptRoutes\Service\GeneratorService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

class GenerateTest extends TestCase
{
    use ProphecyTrait;

    private const UPDATE_OUTPUT_FILES = false;

    public function generationServiceDataProvider(): \Generator
    {
        $routeCollection = new RouteCollection();
        $routeCollection->add('test_route', new Route('/test'));
        $routeCollection->add('user_route', new Route('/user/{id}/{nodeID}'));
        yield ['output.ts', $routeCollection];
    }

    /** @dataProvider generationServiceDataProvider */
    public function testGenerationService(string $outputFileName, RouteCollection $collection): void
    {
        $file = __DIR__ . '/' . $outputFileName;

        $service = new GeneratorService($this->getMockedRouter($collection));
        $result = implode("\n", $service->generate());

        if (self::UPDATE_OUTPUT_FILES) {
            \file_put_contents($file, $result);
        }

        static::assertStringEqualsFile(
            $file,
            $result
        );
    }

    /** @depends testGenerationService */
    public function testTSCCompilationOfOutput(): void
    {
        $output = null;
        $code = null;

        exec(__DIR__ . '/../node_modules/.bin/tsc', $output, $code);

        static::assertSame(0, $code);
        static::assertEmpty($output);
    }

    private function getMockedRouter(RouteCollection $collection): RouterInterface
    {
        $mock = $this->prophesize(RouterInterface::class);
        $mock->getRouteCollection()->willReturn($collection);
        return $mock->reveal();
    }
}
