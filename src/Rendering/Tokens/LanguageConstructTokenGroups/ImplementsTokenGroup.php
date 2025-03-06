<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Enums\WrappingDecision;
use CrazyCodeGen\Rendering\Renderers\Enums\ContextTypeEnum;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ColonToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\CommaToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLineTokens;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\FunctionToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ImplementsToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;
use CrazyCodeGen\Rendering\Tokens\UserLandTokens\IdentifierToken;
use CrazyCodeGen\Rendering\Traits\RenderTokensToStringTrait;

class ImplementsTokenGroup extends TokenGroup
{
    use FlattenFunction;
    use RenderTokensToStringTrait;

    public function __construct(
        /** @var string[]|SingleTypeTokenGroup[] $implementations */
        public readonly array $implementations,
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
        if ($rules->classes->implementsOnNextLine === WrappingDecision::NEVER) {
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
        if (!empty($this->implementations)) {
            $tokens[] = new ImplementsToken();
            $tokens[] = new SpacesToken($rules->classes->spacesAfterImplementsKeyword);
        }
        $implementsLeft = count($this->implementations);
        foreach ($this->implementations as $implement) {
            $implementsLeft--;
            if (!$implement instanceof SingleTypeTokenGroup) {
                $tokens[] = (new SingleTypeTokenGroup($implement))->render($context, $rules);
            } else {
                $tokens[] = $implement->render($context, $rules);
            }
            if ($implementsLeft > 0) {
                $tokens[] = new CommaToken();
                $tokens[] = new SpacesToken($rules->classes->spacesAfterImplementCommaIfSameLine);
            }
        }
        return $this->flatten($tokens);
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function renderChopDownScenario(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if (empty($this->implementations)) {
            return $tokens;
        }
        $tokens[] = new ImplementsToken();
        $tokens[] = new SpacesToken($rules->classes->spacesAfterImplementsKeyword);
        $paddingSpaces = strlen((new ImplementsToken())->render())
            + $rules->classes->spacesAfterImplementsKeyword;
        $implementsLeft = count($this->implementations);
        foreach ($this->implementations as $implement) {
            if ($implementsLeft !== count($this->implementations)) {
                $tokens[] = new NewLineTokens();
                $tokens[] = new SpacesToken($paddingSpaces);
            }
            $implementsLeft--;
            if (!$implement instanceof SingleTypeTokenGroup) {
                $tokens[] = (new SingleTypeTokenGroup($implement))->render($context, $rules);
            } else {
                $tokens[] = $implement->render($context, $rules);
            }
            if ($implementsLeft > 0) {
                $tokens[] = new CommaToken();
            }
        }
        return $this->flatten($tokens);
    }

    /**
     * @return ContextTypeEnum[]
     */
    public function getContexts(): array
    {
        return array_merge([
            ContextTypeEnum::function,
        ], parent::getContexts());
    }

    /**
     * @param RenderingRules $rules
     * @param array $tokens
     * @return array
     */
    public function addInlineBraceTokens(RenderingRules $rules, array $tokens): array
    {
        if (
            $rules->functions->funcOpeningBrace === BracePositionEnum::SAME_LINE
            && $rules->functions->funcClosingBrace === BracePositionEnum::SAME_LINE
        ) {
            if ($rules->functions->spacesBeforeOpeningBraceIfSameLine) {
                $tokens[] = new SpacesToken($rules->functions->spacesBeforeOpeningBraceIfSameLine);
            }
            $tokens[] = new BraceStartToken();
            $tokens[] = new BraceEndToken();
        } elseif (
            $rules->functions->funcOpeningBrace === BracePositionEnum::SAME_LINE
            && $rules->functions->funcClosingBrace === BracePositionEnum::NEXT_LINE
        ) {
            if ($rules->functions->spacesBeforeOpeningBraceIfSameLine) {
                $tokens[] = new SpacesToken($rules->functions->spacesBeforeOpeningBraceIfSameLine);
            }
            $tokens[] = new BraceStartToken();
            $tokens[] = new NewLineTokens();
            $tokens[] = new BraceEndToken();
        } elseif (
            $rules->functions->funcOpeningBrace === BracePositionEnum::NEXT_LINE
            && $rules->functions->funcClosingBrace === BracePositionEnum::SAME_LINE
        ) {
            $tokens[] = new NewLineTokens();
            $tokens[] = new BraceStartToken();
            $tokens[] = new BraceEndToken();
        } elseif (
            $rules->functions->funcOpeningBrace === BracePositionEnum::NEXT_LINE
            && $rules->functions->funcClosingBrace === BracePositionEnum::NEXT_LINE
        ) {
            $tokens[] = new NewLineTokens();
            $tokens[] = new BraceStartToken();
            $tokens[] = new NewLineTokens();
            $tokens[] = new BraceEndToken();
        }
        return $tokens;
    }

    /**
     * @param RenderingRules $rules
     * @param array $tokens
     * @return array
     */
    public function addChopDownBraceTokens(RenderingRules $rules, array $tokens): array
    {
        if ($rules->functions->spacesBeforeOpeningBraceIfSameLine) {
            $tokens[] = new SpacesToken($rules->functions->spacesBeforeOpeningBraceIfSameLine);
        }
        $tokens[] = new BraceStartToken();
        $tokens[] = new NewLineTokens();
        $tokens[] = new BraceEndToken();
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
            if ($rules->functions->spacesBetweenArgumentListAndReturnColon) {
                $tokens[] = new SpacesToken($rules->functions->spacesBetweenArgumentListAndReturnColon);
            }
            $tokens[] = new ColonToken();
            if ($rules->functions->spacesBetweenReturnColonAndType) {
                $tokens[] = new SpacesToken($rules->functions->spacesBetweenReturnColonAndType);
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
        if ($rules->functions->spacesBetweenIdentifierAndArgumentList) {
            $tokens[] = new SpacesToken($rules->functions->spacesBetweenIdentifierAndArgumentList);
        }
        return $tokens;
    }
}