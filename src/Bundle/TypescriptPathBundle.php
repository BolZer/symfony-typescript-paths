<?php

declare(strict_types=1);

namespace Bolzer\SymfonyTypescriptRoutes\src\Bundle;

use Bolzer\SymfonyTypescriptRoutes\src\Extension\TypescriptPathExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class TypescriptPathBundle extends Bundle
{
    public function getContainerExtension(): ExtensionInterface
    {
        return new TypescriptPathExtension();
    }
}
