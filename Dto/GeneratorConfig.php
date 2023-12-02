<?php

declare(strict_types=1);

namespace Bolzer\SymfonyTypescriptRoutes\Dto;

final readonly class GeneratorConfig
{
    private function __construct(
        private bool $generateAbsoluteUrls,
        private bool $generateRelativeUrls,
    ) {}

    public static function generateOnlyRelativeUrls(): self
    {
        return new self(
            generateAbsoluteUrls: false,
            generateRelativeUrls: true,
        );
    }

    public static function generateOnlyAbsoluteUrls(): self
    {
        return new self(
            generateAbsoluteUrls: true,
            generateRelativeUrls: false,
        );
    }

    public static function generateEverything(): self
    {
        return new self(
            generateAbsoluteUrls: true,
            generateRelativeUrls: true,
        );
    }

    public function isGenerateAbsoluteUrls(): bool
    {
        return $this->generateAbsoluteUrls;
    }

    public function isGenerateRelativeUrls(): bool
    {
        return $this->generateRelativeUrls;
    }
}
