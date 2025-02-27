<?php

namespace CrazyCodeGen\Renderers;

class RenderingRules
{
    public function getMaxLineLength(): int
    {
        return 120;
    }

    public function getIndentationToken(): string
    {
        return '    ';
    }

    public function indent(RenderContext        $context): void {
        $context->indents .= $this->getIndentationToken();
    }

    public function unindent(RenderContext        $context): void {
        $context->indents = substr($context->indents, 0, -strlen($this->getIndentationToken()));
    }

    public function applyContextShiftToBuffer(
        null|ContextTypeEnum $shiftingFrom,
        null|ContextTypeEnum $shiftedTo,
        RenderContext        $context
    ): void
    {
        if ($shiftingFrom === ContextTypeEnum::function && $shiftedTo === ContextTypeEnum::block) {
            $context->buffer .= "\n" . $context->indents;
        }
        if ($shiftingFrom === ContextTypeEnum::block && $shiftedTo === ContextTypeEnum::blockBody) {
            $this->indent($context);
            $context->buffer .= "\n";
        }
        if ($shiftingFrom === ContextTypeEnum::blockBody && $shiftedTo === ContextTypeEnum::block) {
            $this->unindent($context);
        }
        if ($shiftedTo === ContextTypeEnum::instruction) {
            $context->buffer .= $context->indents;
        }
        if ($shiftingFrom === ContextTypeEnum::instruction) {
            $context->buffer .= "\n";
        }
    }

    public function applyTokenPrefix(string $token, RenderContext $context): void
    {
        if ($token == '**') {
            $context->buffer .= ' ';
        } elseif (in_array($token, ['*', '/', '%'])) {
            $context->buffer .= ' ';
        } elseif (in_array($token, ['+', '-'])) {
            $context->buffer .= ' ';
        } elseif ($token == '.') {
            $context->buffer .= ' ';
        } elseif (in_array($token, ['<', '<=', '>', '>='])) {
            $context->buffer .= ' ';
        } elseif (in_array($token, ['==', '===', '!=', '!==', '<>'])) {
            $context->buffer .= ' ';
        } elseif ($token == '&&') {
            $context->buffer .= ' ';
        } elseif ($token == '||') {
            $context->buffer .= ' ';
        } elseif (in_array($token, ['=', '.='])) {
            $context->buffer .= ' ';
        } elseif ($token == 'and') {
            $context->buffer .= ' ';
        } elseif ($token == 'xor') {
            $context->buffer .= ' ';
        } elseif ($token == 'or') {
            $context->buffer .= ' ';
        }
    }

    public function applyToken(ContextShift|string $token, RenderContext $context): void
    {
        $context->buffer .= $token;
    }

    public function applyTokenSuffix(string $token, RenderContext $context): void
    {
        if ($context->is(ContextTypeEnum::type) && !$context->has(ContextTypeEnum::returnType)) {
            $context->buffer .= ' ';
        }
        if ($token === 'namespace' && $context->is(ContextTypeEnum::namespace)) {
            $context->buffer .= " ";
        }
        if ($token === ';' && $context->is(ContextTypeEnum::namespace)) {
            $context->buffer .= "\n\n" . $context->indents;
        }
        if ($token === ':' && $context->has(ContextTypeEnum::returnType)) {
            $context->buffer .= ' ';
        } elseif ($token == 'function') {
            $context->buffer .= ' ';
        } elseif ($token == ',') {
            $context->buffer .= ' ';
        } elseif ($token == '**') {
            $context->buffer .= ' ';
        } elseif ($token == '!') {
            $context->buffer .= '';
        } elseif (in_array($token, ['*', '/', '%'])) {
            $context->buffer .= ' ';
        } elseif (in_array($token, ['+', '-'])) {
            $context->buffer .= ' ';
        } elseif ($token == '.') {
            $context->buffer .= ' ';
        } elseif (in_array($token, ['<', '<=', '>', '>='])) {
            $context->buffer .= ' ';
        } elseif (in_array($token, ['==', '===', '!=', '!==', '<>'])) {
            $context->buffer .= ' ';
        } elseif ($token == '&&') {
            $context->buffer .= ' ';
        } elseif ($token == '||') {
            $context->buffer .= ' ';
        } elseif (in_array($token, ['=', '.='])) {
            $context->buffer .= ' ';
        } elseif ($token == 'and') {
            $context->buffer .= ' ';
        } elseif ($token == 'xor') {
            $context->buffer .= ' ';
        } elseif ($token == 'or') {
            $context->buffer .= ' ';
        }
    }
}