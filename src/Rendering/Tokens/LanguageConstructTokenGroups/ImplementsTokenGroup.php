<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\WrappingDecision;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\CommaToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLineTokens;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ImplementsToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ImplementsTokenGroup extends TokenGroup
{
    use FlattenFunction;
    use TokenFunctions;

    public function __construct(
        /** @var string[]|SingleTypeTokenGroup[] $implementations */
        public readonly array $implementations,
    ) {
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
            $tokens[] = new SpacesToken($rules->classes->spacesAfterImplements);
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
                $tokens[] = new SpacesToken($rules->classes->spacesAfterImplementSeparator);
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
        $tokens[] = new SpacesToken($rules->classes->spacesAfterImplements);
        $paddingSpaces = strlen((new ImplementsToken())->render())
            + $rules->classes->spacesAfterImplements;
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
}
