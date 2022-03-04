<?php

declare(strict_types=1);

namespace Bolzer\SymfonyTypescriptRoutes\Tests;

use Bolzer\SymfonyTypescriptRoutes\Dto\GeneratorConfig;
use Bolzer\SymfonyTypescriptRoutes\Service\GeneratorService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

class GenerateTest extends TestCase
{
    use ProphecyTrait;

    private const UPDATE_OUTPUT_FILES = true;

    public function generationServiceDataProvider(): \Generator
    {
        $routeCollection = new RouteCollection();
        $routeCollection->add('user_route', new Route('/user/{id}/notes/{noteId}', host: 'app.development.org', schemes: 'https'));
        $routeCollection->add('user_route_http', new Route('/user/{id}/notes/{noteId}', host: 'app.development.org', schemes: 'http'));
        $routeCollection->add('users_route', new Route('/users', host: 'app.development.org', schemes: 'https'));
        yield ['output.ts', $routeCollection];
    }

    /** @dataProvider generationServiceDataProvider */
    public function testGenerationService(string $outputFileName, RouteCollection $collection): void
    {
        $file = __DIR__ . '/' . $outputFileName;

        $service = new GeneratorService($this->getMockedRouter($collection));
        $result = implode("\n", $service->generate(GeneratorConfig::generateEverything()));

        if (self::UPDATE_OUTPUT_FILES) {
            \file_put_contents($file, $result);
        }

        static::assertStringEqualsFile(
            $file,
            $result
        );
    }

    public function testGenerationServiceWithAInvalidRoute(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectDeprecationMessage('Route must have https or http as scheme.');

        $routeCollection = new RouteCollection();
        $routeCollection->add('user_route', new Route('/user/{id}/notes/{noteId}', host: 'app.development.org'));

        (new GeneratorService($this->getMockedRouter($routeCollection)))->generate(GeneratorConfig::generateEverything());
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
