<?php

declare(strict_types=1);

namespace Bolzer\SymfonyTypescriptRoutes\src\Tests;

use Bolzer\SymfonyTypescriptRoutes\src\Bundle\TypescriptPathBundle;
use Bolzer\SymfonyTypescriptRoutes\src\Extension\TypescriptPathExtension;
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
