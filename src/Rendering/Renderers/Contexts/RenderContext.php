<?php

namespace CrazyCodeGen\Rendering\Renderers\Contexts;

use CrazyCodeGen\Rendering\Renderers\Enums\ContextShiftOperationEnum;
use CrazyCodeGen\Rendering\Renderers\Enums\ContextTypeEnum;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLineTokens;
use CrazyCodeGen\Rendering\Tokens\Token;

class RenderContext
{
    private null|Token $lastRenderedToken = null;
    private string $currentLine = '';

    public function __construct(
        public string                 $buffer = '',
        public string                 $indents = '',
        /** @var ContextTypeEnum[] $contextStack */
        public array                  $contextStack = [],
        public int                    $argumentDefinitionTypePaddingSize = 0,
        public int                    $argumentDefinitionIdentifierPaddingSize = 0,
        public bool                   $forcedWrap = false,
        public bool                   $forcedChopDown = false,
        public ChopDownPaddingContext $chopDown = new ChopDownPaddingContext(),
        public array                  $importedClasses = [],
    )
    {
    }

    public function getLastRenderedToken(): null|Token
    {
        return $this->lastRenderedToken;
    }

    public function applyRenderedToken(Token $token): void
    {
        $this->lastRenderedToken = $token;
        $this->buffer .= $token->render();
        if ($token instanceof NewLineTokens) {
            $this->currentLine = '';
        } else {
            $this->currentLine .= $token->text;
        }
    }

    public function getCurrentLine(): string
    {
        return $this->currentLine;
    }

    public function applyContextShift(ContextShift $contextShift): void
    {
        if ($contextShift->shiftOperation === ContextShiftOperationEnum::push) {
            $this->contextStack[] = $contextShift->shiftedType;
        } else {
            $poppedType = array_pop($this->contextStack);
            if ($poppedType !== $contextShift->shiftedType) {
                $this->contextStack[] = $poppedType;
            }
        }
    }

    public function is(ContextTypeEnum $seekedContext): bool
    {
        return $this->contextStack[count($this->contextStack) - 1] === $seekedContext;
    }

    public function has(ContextTypeEnum $seekedContext): bool
    {
        return in_array($seekedContext, $this->contextStack);
    }

    public function getCurrent(): null|ContextTypeEnum
    {
        return $this->contextStack[count($this->contextStack) - 1] ?? null;
    }

    public function cloneWithContextsOnly(): self
    {
        $newContext = new self();
        $newContext->contextStack = $this->contextStack;
        return $newContext;
    }
}