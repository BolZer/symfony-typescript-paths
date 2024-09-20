<?php

declare(strict_types=1);

namespace Bolzer\SymfonyTypescriptRoutes\Bundle;

use Bolzer\SymfonyTypescriptRoutes\Extension\TypescriptPathExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class TypescriptPathBundle extends Bundle
{
    public function getContainerExtension(): ExtensionInterface
    {
        return new TypescriptPathExtension();
    }
}
