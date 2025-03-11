<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\RendersChopDownVersion;
use CrazyCodeGen\Rendering\Renderers\RendersInlineVersion;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\CommaToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ParameterListTokenGroup extends TokenGroup implements RendersInlineVersion, RendersChopdownVersion
{
    use FlattenFunction;
    use TokenFunctions;

    public function __construct(
        /** @var ParameterTokenGroup[] $parameters */
        public array $parameters = [],
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
        if (!empty($this->parameters)) {
            $rules->indent($context);
            if ($rules->parameterLists->padTypes || $rules->parameterLists->padIdentifiers) {
                $types = [];
                $identifiers = [];
                foreach ($this->parameters as $parameter) {
                    $types[] = $parameter->renderType($context, $rules);
                    $identifiers[] = $parameter->renderIdentifier($context, $rules);
                }
                $longestType = max(array_map(fn (array $tokens) => strlen($this->renderTokensToString($tokens)), $types));
                $longestIdentifier = max(array_map(fn (array $tokens) => strlen($this->renderTokensToString($tokens)), $identifiers));
                $context->chopDown->paddingSpacesForTypes = $rules->parameterLists->padTypes ? $longestType : null;
                $context->chopDown->paddingSpacesForIdentifiers = $rules->parameterLists->padIdentifiers ? $longestIdentifier : null;
            }
            $parametersLeft = count($this->parameters);
            $parameterListTokens = [];
            foreach ($this->parameters as $parameter) {
                $parametersLeft--;
                $parameterListTokens[] = $parameter->render($context, $rules);
                if ($parametersLeft > 0 || $rules->parameterLists->addSeparatorToLastItem) {
                    $parameterListTokens[] = new CommaToken();
                }
                $parameterListTokens[] = new NewLinesToken();
            }
            $tokens[] = $this->insertIndentationTokens($rules, $parameterListTokens);
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
        $parametersLeft = count($this->parameters);
        foreach ($this->parameters as $parameter) {
            $parametersLeft--;
            $tokens[] = $parameter->render($context, $rules);
            if ($parametersLeft > 0) {
                $tokens[] = new CommaToken();
                $tokens[] = new SpacesToken();
            }
        }
        $tokens[] = new ParEndToken();
        return $this->flatten($tokens);
    }
}
