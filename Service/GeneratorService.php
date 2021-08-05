<?php

declare(strict_types=1);

namespace Bolzer\SymfonyTypescriptRoutes\Service;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

class GeneratorService
{
    public function __construct(
        private RouterInterface $router,
    ) {
    }

    public function generate(): array
    {
        $buffer = [
            ...\array_values($this->getTypescriptUtilityFunctions()),
        ];

        foreach ($this->router->getRouteCollection()->all() as $name => $route) {
            $buffer[] = $this->buildFunctionForRoute($name, $route);
        }

        return $buffer;
    }

    private function getTypescriptUtilityFunctions(): array
    {
        return [
            'const rRP = (rawRoute: string, routeParams: Record<string, string>): string => {Object.entries(routeParams).forEach(([key, value]) => rawRoute = rawRoute.replace(`{${key}}`, value)); return rawRoute;}',
            'const aQP = (route: string, queryParams?: Record<string, string>): string => queryParams ? route + "?" + new URLSearchParams(queryParams).toString() : route;',
        ];
    }

    private function buildFunctionForRoute(string $routeName, Route $route): string
    {
        $relativeRouteVariables = $this->retrieveVariablesFromRoutePath($route);
        $absoluteRouteVariables = $this->retrieveVariablesFromAbsoluteRoutePath($route);

        $buffer = [
            'export const ',
            $this->sanitizeRouteFunctionName($routeName),
            ' = ():',
            '{ relative: (',
            $this->createRouteParamFunctionArgument($relativeRouteVariables),
            $this->createQueryParamFunctionArgument(),
            ') => string, ',
            'absolute: (',
            $this->createRouteParamFunctionArgument($absoluteRouteVariables),
            $this->createQueryParamFunctionArgument(),
            ') => string',
            '} => {',
            'return {',
            'relative: (',
            $this->createRouteParamFunctionArgument($relativeRouteVariables),
            $this->createQueryParamFunctionArgument(),
            '): string => ' . $this->createFunctionCallForRelativePath($route, $relativeRouteVariables) . ', ',
            'absolute: (',
            $this->createRouteParamFunctionArgument($absoluteRouteVariables),
            $this->createQueryParamFunctionArgument(),
            '): string => ' . $this->createFunctionCallForAbsolutePath($route, $absoluteRouteVariables),
            '}',
            '};',
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

    private function retrieveVariablesFromAbsoluteRoutePath(Route $route): array
    {
        $url = \sprintf('%s%s', $route->getHost(), $route->getPath());

        $matches = [];

        preg_match_all(
            '/{(.*?)}/m',
            $url,
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
        if (!$variables) {
            return '';
        }

        return 'routeParams: {' . \implode(', ', array_map(
            static function (string $variable) {
                return $variable . ': string';
            },
            $variables
        )) . '}, ';
    }

    private function createQueryParamFunctionArgument(): string
    {
        return 'queryParams?: Record<string, string>';
    }

    private function createFunctionCallForRelativePath(Route $route, array $variables): string
    {
        if ($variables) {
            return \implode('', [
                'aQP(',
                "rRP('",
                $route->getPath(),
                "', routeParams",
                '), queryParams',
                ')',
            ]);
        }

        return \implode('', [
            "aQP('",
            $route->getPath(),
            "', queryParams",
            ')',
        ]);
    }

    private function createFunctionCallForAbsolutePath(Route $route, array $variables): string
    {
        $absolutePath = $route->getSchemes()[0] . '://' . $route->getHost() . $route->getPath();

        if ($variables) {
            return \implode('', [
                'aQP(',
                "rRP('",
                $absolutePath,
                "', routeParams",
                '), queryParams',
                ')',
            ]);
        }

        return \implode('', [
            "aQP('",
            $absolutePath,
            "', queryParams",
            ')',
        ]);
    }
}
