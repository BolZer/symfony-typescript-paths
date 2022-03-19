<?php

declare(strict_types=1);

namespace Bolzer\SymfonyTypescriptRoutes\Service;

use Bolzer\SymfonyTypescriptRoutes\Dto\GeneratorConfig;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

class GeneratorService
{
    public function __construct(
        private RouterInterface $router,
    ) {
    }

    public function generate(GeneratorConfig $config): array
    {
        $this->assertValidConfiguration($config);

        $buffer = [
            ...\array_values($this->getTypescriptUtilityFunctions()),
        ];

        foreach ($this->router->getRouteCollection()->all() as $name => $route) {
            $buffer[] = $this->buildFunctionForRoute($config, $name, $route);
        }

        return $buffer;
    }

    private function getTypescriptUtilityFunctions(): array
    {
        return [
            'const rRP = (rawRoute: string, routeParams: Record<string, string|number>): string => {Object.entries(routeParams).forEach(([key, value]) => rawRoute = rawRoute.replace(`{${key}}`, value as string)); return rawRoute;}',
            'const aQP = (route: string, queryParams?: Record<string, string>): string => queryParams ? route + "?" + new URLSearchParams(queryParams).toString() : route;',
        ];
    }

    private function buildFunctionForRoute(GeneratorConfig $config, string $routeName, Route $route): string
    {
        $relativeRouteVariables = $this->retrieveVariablesFromRoutePath($route);
        $absoluteRouteVariables = $this->retrieveVariablesFromAbsoluteRoutePath($route);

        $buffer = [
            'export const ',
            $this->sanitizeRouteFunctionName($routeName),
            ' = (): { ',
        ];

        if ($config->isGenerateRelativeUrls()) {
            $buffer = array_merge($buffer, [
                'relative: (',
                $this->createRouteParamFunctionArgument($route, $relativeRouteVariables),
                $this->createQueryParamFunctionArgument(),
                ') => string, ',
            ]);
        }

        if ($config->isGenerateAbsoluteUrls()) {
            $buffer = array_merge($buffer, [
                'absolute: (',
                $this->createRouteParamFunctionArgument($route, $absoluteRouteVariables),
                $this->createQueryParamFunctionArgument(),
                ') => string',
            ]);
        }

        $buffer = array_merge($buffer, [
            '} => {',
            'return {',
        ]);

        if ($config->isGenerateRelativeUrls()) {
            $buffer = array_merge($buffer, [
                'relative: (',
                $this->createRouteParamFunctionArgument($route, $relativeRouteVariables),
                $this->createQueryParamFunctionArgument(),
                '): string => ' . $this->createFunctionCallForRelativePath($route, $relativeRouteVariables) . ', ',
            ]);
        }

        if ($config->isGenerateAbsoluteUrls()) {
            $buffer = array_merge($buffer, [
                'absolute: (',
                $this->createRouteParamFunctionArgument($route, $absoluteRouteVariables),
                $this->createQueryParamFunctionArgument(),
                '): string => ' . $this->createFunctionCallForAbsolutePath($route, $absoluteRouteVariables),
            ]);
        }

        $buffer = array_merge($buffer, [
            '}',
            '};',
        ]);

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

        return array_map(static fn (array $match) => $match[1], $matches);
    }

    private function sanitizeRouteFunctionName(string $routeName): string
    {
        $sanitizedPath = preg_replace('/\W/', '_', $routeName);

        if (\str_starts_with($sanitizedPath, '_')) {
            return 'path' . $sanitizedPath;
        }

        return 'path_' . preg_replace('/\W/', '_', $routeName);
    }

    private function createRouteParamFunctionArgument(Route $route, array $variables): string
    {
        if (!$variables) {
            return '';
        }

        return 'routeParams: {' . \implode(', ', array_map(
            function (string $variable) use ($route) {
                return sprintf('%s:%s', $variable, $this->guessTypeOfPathVariable($route, $variable));
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
        $availableSchemes = $route->getSchemes();

        $usedScheme = null;

        if (\in_array('http', $availableSchemes, true)) {
            $usedScheme = 'http';
        }

        if (\in_array('https', $availableSchemes, true)) {
            $usedScheme = 'https';
        }

        if ($usedScheme === null) {
            throw new \InvalidArgumentException('Route must have https or http as scheme.');
        }

        $absolutePath = $usedScheme . '://' . $route->getHost() . $route->getPath();

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

    private function guessTypeOfPathVariable(Route $route, string $variable): string
    {
        $requirement = $route->getRequirement($variable);

        if ($requirement === null) {
            return 'string';
        }

        if ($this->isDigitRegex($requirement)) {
            return 'number';
        }

        if ($this->isEitherAOrBRegex($requirement)) {
            return $this->deriveEitherAOrBRegexExpressionForTypescript($requirement);
        }

        return 'string';
    }

    private function isDigitRegex(string $requirement): bool
    {
        return $requirement === '\d+';
    }

    private function isEitherAOrBRegex(string $requirement): bool
    {
        $matches = [];

        preg_match_all($this->getEitherAOrBRegexGuessRegex(), $requirement, $matches, PREG_SET_ORDER);

        return count($matches) > 0;
    }

    private function deriveEitherAOrBRegexExpressionForTypescript(string $requirement): string
    {
        $matches = [];

        preg_match_all($this->getEitherAOrBRegexGuessRegex(), $requirement, $matches, PREG_SET_ORDER);

        if (!$matches) {
            throw new \LogicException('At this point the either A Or B regex must have matches');
        }

        $matchingGroup = $matches[0] ?? [];

        if (!$matchingGroup) {
            throw new \InvalidArgumentException('At this point the MatchingGroup must exist');
        }

        $buffer = [];
        foreach ($matchingGroup as $key => $match) {
            if ($key === 0) {
                continue;
            }

            $buffer[] = $match;
        }

        return implode('|', array_map(static fn (string $matchFromBuffer) => sprintf("'%s'", $matchFromBuffer), $buffer));
    }

    private function getEitherAOrBRegexGuessRegex(): string
    {
        return '/([a-zA-Z]+)(?>\|)([a-zA-Z]+)/m';
    }

    private function assertValidConfiguration(GeneratorConfig $config): void
    {
        if (!$config->isGenerateAbsoluteUrls() && !$config->isGenerateRelativeUrls()) {
            throw new \InvalidArgumentException('Configuration invalid. You should not set generateAbsoluteUrls and generateRelativeUrls to false.');
        }
    }
}
