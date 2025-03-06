<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\ContextTypeEnum;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\CommaToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLineTokens;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ArgumentListDeclarationTokenGroup extends TokenGroup
{
    use FlattenFunction;
    use TokenFunctions;

    public function __construct(
        /** @var ArgumentDeclarationTokenGroup[] $arguments */
        public array $arguments = [],
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
        return $this->renderInlineScenario($context, $rules);
    }

    /**
     * @return ContextTypeEnum[]
     */
    public function getContexts(): array
    {
        return array_merge([
            ContextTypeEnum::argumentList,
        ], parent::getContexts());
    }

    /**
     * @return Token[]
     */
    public function renderInlineScenario(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        $tokens[] = new ParStartToken();
        $argumentsLeft = count($this->arguments);
        foreach ($this->arguments as $argument) {
            $argumentsLeft--;
            $tokens[] = $argument->render($context, $rules);
            if ($argumentsLeft > 0) {
                $tokens[] = new CommaToken();
                $tokens[] = new SpacesToken();
            }
        }
        $tokens[] = new ParEndToken();
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    public function renderChopDownScenario(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        $tokens[] = new ParStartToken();
        $tokens[] = new NewLineTokens();
        if (!empty($this->arguments)) {
            $rules->indent($context);
            if ($rules->argumentLists->padTypeNames || $rules->argumentLists->padIdentifiers) {
                $types = [];
                $identifiers = [];
                foreach ($this->arguments as $argument) {
                    $types[] = $argument->renderType($context, $rules);
                    $identifiers[] = $argument->renderIdentifier($context, $rules);
                }
                $longestType = max(array_map(fn (array $tokens) => strlen($this->renderTokensToString($tokens)), $types));
                $longestIdentifier = max(array_map(fn (array $tokens) => strlen($this->renderTokensToString($tokens)), $identifiers));
                $context->chopDown->paddingSpacesForTypes = $rules->argumentLists->padTypeNames ? $longestType : null;
                $context->chopDown->paddingSpacesForIdentifiers = $rules->argumentLists->padIdentifiers ? $longestIdentifier : null;
            }
            $argumentsLeft = count($this->arguments);
            foreach ($this->arguments as $argument) {
                $argumentsLeft--;
                $tokens[] = new SpacesToken(strlen($context->indents));
                $tokens[] = $argument->render($context, $rules);
                if ($argumentsLeft > 0 || $rules->argumentLists->addTrailingCommaToLastItemInChopDown) {
                    $tokens[] = new CommaToken();
                }
                $tokens[] = new NewLineTokens();
            }
            $context->chopDown->paddingSpacesForTypes = null;
            $context->chopDown->paddingSpacesForIdentifiers = null;
            $rules->unindent($context);
        }
        $tokens[] = new ParEndToken();
        return $this->flatten($tokens);
    }
}