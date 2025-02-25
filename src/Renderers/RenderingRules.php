<?php

namespace CrazyCodeGen\Renderers;

class RenderingRules
{
    public function getMaxLineLength(): int
    {
        return 120;
    }

    public function getPrefixForToken(string $token, RenderContext $context): string
    {
        return match ($token) {
            '.=', '.', '&&', '||', 'and', 'or', 'xor', '=', '==', '===', '!=', '!==', '<>', '<', '<=', '>', '>=', '+', '-', '*', '/', '%', '**' => ' ',
            default => '',
        };
    }

    public function getSuffixForToken(string $token, RenderContext $context): string
    {
        return match ($token) {
            '.=', '.', '&&', '||', 'and', 'or', 'xor', '=', '==', '===', '!=', '!==', '<>', '<', '<=', '>', '>=', '+', '-', '*', '/', '%', '**' => ' ',
            default => '',
        };
    }

    public function getIndentationToken(): string
    {
        return '  ';
    }
}