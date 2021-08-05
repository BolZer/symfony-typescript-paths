<?php

declare(strict_types=1);

namespace Bolzer\SymfonyTypescriptRoutes\Tests;

use Bolzer\SymfonyTypescriptRoutes\Bundle\TypescriptPathBundle;
use Bolzer\SymfonyTypescriptRoutes\Extension\TypescriptPathExtension;
use PHPUnit\Framework\TestCase;

class BundleTest extends TestCase
{
    public function testBundle(): void
    {
        $bundle = new TypescriptPathBundle();
        $returnedExtension = $bundle->getContainerExtension();
        self::assertInstanceOf(TypescriptPathExtension::class, $returnedExtension);
    }
}
