<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Enums\WrappingDecision;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ColonToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLineTokens;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\FunctionToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;
use CrazyCodeGen\Rendering\Tokens\UserLandTokens\IdentifierToken;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class FunctionTokenGroup extends TokenGroup
{
    use FlattenFunction;
    use TokenFunctions;

    public function __construct(
        public readonly string|IdentifierToken             $name,
        public readonly null|NamespaceTokenGroup           $namespace = null,
        public readonly null|string|DocBlockTokenGroup     $docBlock = null,
        public readonly null|ArgumentListTokenGroup        $arguments = null,
        public readonly null|string|AbstractTypeTokenGroup $returnType = null,
        public readonly null|array                         $bodyInstructions = null,
    )
    {
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function render(RenderContext $context, RenderingRules $rules): array
    {
        if ($rules->functions->argumentsOnDifferentLines === WrappingDecision::NEVER) {
            return $this->renderInlineScenario($context, $rules);
        } elseif ($rules->functions->argumentsOnDifferentLines === WrappingDecision::ALWAYS) {
            return $this->renderChopDownScenario($context, $rules);
        } else {
            $inlineScenario = $this->renderInlineScenario($context, $rules);
            if (!$rules->exceedsAvailableSpace($context->getCurrentLine(), $this->renderTokensToString($inlineScenario))) {
                return $inlineScenario;
            } else {
                return $this->renderChopDownScenario($context, $rules);
            }
        }
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function renderInlineScenario(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if ($this->namespace) {
            $tokens[] = $this->namespace->render($context, $rules);
        }

        if ($this->docBlock) {
            $tokens[] = $this->docBlock->render($context, $rules);
            $tokens[] = new NewLineTokens($rules->functions->newLinesAfterDocBlock);
        }

        $tokens = $this->getFunctionDeclarationTokens($tokens, $rules);
        if ($this->arguments) {
            $tokens[] = $this->arguments->render($context, $rules);
        } else {
            $tokens[] = (new ArgumentListTokenGroup())->render($context, $rules);
        }
        $tokens = $this->addReturnTypeTokens($rules, $tokens, $context);
        $tokens = $this->addInlineBraceTokens($rules, $tokens);
        return $this->flatten($tokens);
    }

    /**
     * @param array $tokens
     * @param RenderingRules $rules
     * @return array
     */
    public function getFunctionDeclarationTokens(array $tokens, RenderingRules $rules): array
    {
        $tokens[] = new FunctionToken();
        $tokens[] = new SpacesToken();
        if (!$this->name instanceof IdentifierToken) {
            $tokens[] = new IdentifierToken($this->name);
        } else {
            $tokens[] = $this->name;
        }
        if ($rules->functions->spacesAfterIdentifier) {
            $tokens[] = new SpacesToken($rules->functions->spacesAfterIdentifier);
        }
        return $tokens;
    }

    /**
     * @param RenderingRules $rules
     * @param array $tokens
     * @param RenderContext $context
     * @return array
     */
    public function addReturnTypeTokens(RenderingRules $rules, array $tokens, RenderContext $context): array
    {
        if ($this->returnType) {
            if ($rules->functions->spacesAfterArguments) {
                $tokens[] = new SpacesToken($rules->functions->spacesAfterArguments);
            }
            $tokens[] = new ColonToken();
            if ($rules->functions->spacesAfterReturnColon) {
                $tokens[] = new SpacesToken($rules->functions->spacesAfterReturnColon);
            }
            if (is_string($this->returnType)) {
                $tokens[] = (new SingleTypeTokenGroup($this->returnType))->render($context, $rules);
            } else {
                $tokens[] = $this->returnType->render($context, $rules);
            }
        }
        return $tokens;
    }

    /**
     * @param RenderingRules $rules
     * @param array $tokens
     * @return array
     */
    public function addInlineBraceTokens(RenderingRules $rules, array $tokens): array
    {
        if (
            $rules->functions->openingBrace === BracePositionEnum::SAME_LINE
            && $rules->functions->closingBrace === BracePositionEnum::SAME_LINE
        ) {
            if ($rules->functions->spacesBeforeOpeningBrace) {
                $tokens[] = new SpacesToken($rules->functions->spacesBeforeOpeningBrace);
            }
            $tokens[] = new BraceStartToken();
            $tokens[] = new BraceEndToken();
        } elseif (
            $rules->functions->openingBrace === BracePositionEnum::SAME_LINE
            && $rules->functions->closingBrace === BracePositionEnum::NEXT_LINE
        ) {
            if ($rules->functions->spacesBeforeOpeningBrace) {
                $tokens[] = new SpacesToken($rules->functions->spacesBeforeOpeningBrace);
            }
            $tokens[] = new BraceStartToken();
            $tokens[] = new NewLineTokens();
            $tokens[] = new BraceEndToken();
        } elseif (
            $rules->functions->openingBrace === BracePositionEnum::NEXT_LINE
            && $rules->functions->closingBrace === BracePositionEnum::SAME_LINE
        ) {
            $tokens[] = new NewLineTokens();
            $tokens[] = new BraceStartToken();
            $tokens[] = new BraceEndToken();
        } elseif (
            $rules->functions->openingBrace === BracePositionEnum::NEXT_LINE
            && $rules->functions->closingBrace === BracePositionEnum::NEXT_LINE
        ) {
            $tokens[] = new NewLineTokens();
            $tokens[] = new BraceStartToken();
            $tokens[] = new NewLineTokens();
            $tokens[] = new BraceEndToken();
        }
        return $tokens;
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function renderChopDownScenario(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if ($this->namespace) {
            $tokens[] = $this->namespace->render($context, $rules);
        }

        if ($this->docBlock) {
            $tokens[] = $this->docBlock->render($context, $rules);
            $tokens[] = new NewLineTokens($rules->functions->newLinesAfterDocBlock);
        }

        $tokens = $this->getFunctionDeclarationTokens($tokens, $rules);
        if ($this->arguments) {
            $tokens[] = $this->arguments->renderChopDownScenario($context, $rules);
        } else {
            $tokens[] = (new ArgumentListTokenGroup())->renderChopDownScenario($context, $rules);
        }
        $tokens = $this->addReturnTypeTokens($rules, $tokens, $context);
        $tokens = $this->addChopDownBraceTokens($rules, $tokens);
        return $this->flatten($tokens);
    }

    /**
     * @param RenderingRules $rules
     * @param array $tokens
     * @return array
     */
    public function addChopDownBraceTokens(RenderingRules $rules, array $tokens): array
    {
        if ($rules->functions->spacesBeforeOpeningBrace) {
            $tokens[] = new SpacesToken($rules->functions->spacesBeforeOpeningBrace);
        }
        $tokens[] = new BraceStartToken();
        $tokens[] = new NewLineTokens();
        $tokens[] = new BraceEndToken();
        return $tokens;
    }
}