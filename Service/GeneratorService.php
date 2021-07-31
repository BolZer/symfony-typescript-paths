<?php

declare(strict_types=1);

namespace Bolzer\SymfonyTypescriptRoutes\Service;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

class GeneratorService
{
    private const LOCALES = ['de', 'en'];

    public function __construct(
        private RouterInterface $router,
    ) {
    }

    public function generate(): array
    {
        $buffer = [
            ...$this->getTypescriptUtilityFunctions(),
        ];

        foreach ($this->router->getRouteCollection()->all() as $name => $route) {
            $buffer[] = $this->buildFunctionForRoute($name, $route);
        }

        return $buffer;
    }

    private function getTypescriptUtilityFunctions(): array
    {
        return [
            'type L = ' . implode('|', array_map(static fn (string $v): string => "'{$v}'", self::LOCALES)),
            'const rRP = (rawRoute: string, routeParams: Record<string, string>): string => {Object.entries(routeParams).forEach(([key, value]) => rawRoute = rawRoute.replace(`{${key}}`, value)); return rawRoute;}',
            'const aQP = (route: string, queryParams?: Record<string, string>): string => queryParams ? route + "?" + new URLSearchParams(queryParams).toString() : route;',
        ];
    }

    private function buildFunctionForRoute(string $routeName, Route $route): string
    {
        if ($variables = $this->retrieveVariablesFromRoutePath($route)) {
            $buffer = [
                'export const ',
                $this->sanitizeRouteFunctionName($routeName),
                ' = (',
                $this->createRouteParamFunctionArgument($variables),
                ', ',
                $this->createQueryParamFunctionArgument(),
                '): string => ',
                'aQP(',
                "rRP('",
                $route->getPath(),
                "', routeParams",
                '), queryParams',
                ');',
            ];

            return \implode('', $buffer);
        }

        $buffer = [
            'export const ',
            $this->sanitizeRouteFunctionName($routeName),
            ' = (',
            $this->createQueryParamFunctionArgument(),
            '): string => ',
            "aQP('",
            $route->getPath(),
            "', queryParams",
            ');',
        ];

        return \implode('', $buffer);
    }

    private function retrieveVariablesFromRoutePath(Route $route): array
    {
        $matches = [];

        preg_match_all(
            '/{(.*?)}/m',
            $route->getPath(),
            $matches,
            PREG_SET_ORDER,
            0
        );

        if (!$matches) {
            return [];
        }

        $buffer = [];
        foreach ($matches as $match) {
            $buffer[] = $match[1];
        }

        return $buffer;
    }

    private function sanitizeRouteFunctionName(string $routeName): string
    {
        $sanitizedPath = preg_replace('/\W/', '_', $routeName);

        if (\str_starts_with($sanitizedPath, '_')) {
            return 'path' . $sanitizedPath;
        }

        return 'path_' . preg_replace('/\W/', '_', $routeName);
    }

    private function createRouteParamFunctionArgument(array $variables): string
    {
        return 'routeParams: {' . \implode(', ', array_map(
            static function (string $variable) {
                if (\str_contains($variable, 'locale')) {
                    return $variable . ': L';
                }

                return $variable . ': string';
            },
            $variables
        )) . '}';
    }

    private function createQueryParamFunctionArgument(): string
    {
        return 'queryParams?: Record<string, string>';
    }
}
