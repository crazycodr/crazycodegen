<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\CommaToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ArgumentListTokenGroup extends TokenGroup
{
    use FlattenFunction;
    use TokenFunctions;

    public function __construct(
        /** @var ArgumentTokenGroup[] $arguments */
        public array $arguments = [],
    ) {
    }

    /**
     * @return Token[]
     */
    public function renderChopDownScenario(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        $tokens[] = new ParStartToken();
        $tokens[] = new NewLinesToken();
        if (!empty($this->arguments)) {
            $rules->indent($context);
            if ($rules->argumentLists->padTypes || $rules->argumentLists->padIdentifiers) {
                $types = [];
                $identifiers = [];
                foreach ($this->arguments as $argument) {
                    $types[] = $argument->renderType($context, $rules);
                    $identifiers[] = $argument->renderIdentifier($context, $rules);
                }
                $longestType = max(array_map(fn (array $tokens) => strlen($this->renderTokensToString($tokens)), $types));
                $longestIdentifier = max(array_map(fn (array $tokens) => strlen($this->renderTokensToString($tokens)), $identifiers));
                $context->chopDown->paddingSpacesForTypes = $rules->argumentLists->padTypes ? $longestType : null;
                $context->chopDown->paddingSpacesForIdentifiers = $rules->argumentLists->padIdentifiers ? $longestIdentifier : null;
            }
            $argumentsLeft = count($this->arguments);
            $argumentListTokens = [];
            foreach ($this->arguments as $argument) {
                $argumentsLeft--;
                $argumentListTokens[] = $argument->render($context, $rules);
                if ($argumentsLeft > 0 || $rules->argumentLists->addSeparatorToLastItem) {
                    $argumentListTokens[] = new CommaToken();
                }
                $argumentListTokens[] = new NewLinesToken();
            }
            $tokens[] = $this->insertIndentationTokens($rules, $argumentListTokens);
            $context->chopDown->paddingSpacesForTypes = null;
            $context->chopDown->paddingSpacesForIdentifiers = null;
            $rules->unindent($context);
        }
        $tokens[] = new ParEndToken();
        return $this->flatten($tokens);
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
}
