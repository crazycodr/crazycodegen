<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Base\ProvidesChopDownTokens;
use CrazyCodeGen\Definition\Base\ProvidesInlineTokens;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\CommaToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ParameterListDef extends Tokenizes implements ProvidesInlineTokens, ProvidesChopDownTokens
{
    use FlattenFunction;
    use TokenFunctions;

    public function __construct(
        /** @var ParameterDef[] $parameters */
        public array $parameters = [],
    ) {
    }

    /**
     * @return Token[]
     */
    public function getChopDownTokens(RenderContext $context, RenderingRules $rules): array
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
                $parameterListTokens[] = $parameter->getTokens($context, $rules);
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
    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        return $this->getInlineTokens($context, $rules);
    }

    /**
     * @return Token[]
     */
    public function getInlineTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        $tokens[] = new ParStartToken();
        $parametersLeft = count($this->parameters);
        foreach ($this->parameters as $parameter) {
            $parametersLeft--;
            $tokens[] = $parameter->getTokens($context, $rules);
            if ($parametersLeft > 0) {
                $tokens[] = new CommaToken();
                $tokens[] = new SpacesToken();
            }
        }
        $tokens[] = new ParEndToken();
        return $this->flatten($tokens);
    }
}
