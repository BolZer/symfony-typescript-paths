<?php

declare(strict_types=1);

use Bolzer\SymfonyTypescriptRoutes\Extension\TypescriptPathExtension;
use Bolzer\SymfonyTypescriptRoutes\Service\GeneratorService;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ExtensionTest extends TestCase
{
    use ProphecyTrait;

    public function testExtension(): void
    {
        $containerBuilderMock = $this->prophesize(ContainerBuilder::class);
        $containerBuilderMock->fileExists(str_replace('Tests', 'Extension/../services.yml', __DIR__))->willReturn(true);
        $containerBuilderMock->removeBindings(GeneratorService::class)->shouldBeCalled();
        $containerBuilderMock->setDefinition(GeneratorService::class, Argument::any())->shouldBeCalled();
        $extension = new TypescriptPathExtension();
        $extension->load([], $containerBuilderMock->reveal());
    }
}
