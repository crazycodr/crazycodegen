<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Renderers\Contexts\ChopDownRenderContext;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\ContextTypeEnum;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\CommaToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLineToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;
use CrazyCodeGen\Rendering\Traits\RenderTokensToStringTrait;

class ArgumentListDeclarationTokenGroup extends TokenGroup
{
    use FlattenFunction;
    use RenderTokensToStringTrait;

    public function __construct(
        /** @var ArgumentDeclarationTokenGroup[] $arguments */
        public array $arguments = [],
    )
    {
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return array<Token[]>
     */
    public function renderScenarios(RenderContext $context, RenderingRules $rules): array
    {
        return [
            $this->renderInlineScenario($context, $rules),
            $this->renderChopDownScenario($context, $rules),
        ];
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
        $tokens[] = new NewLineToken();
        if (!empty($this->arguments)) {
            $rules->indent($context);
            if ($rules->argumentListDefinitionRenderingRules->padTypeNames || $rules->argumentListDefinitionRenderingRules->padIdentifiers) {
                $types = [];
                $identifiers = [];
                foreach ($this->arguments as $argument) {
                    $types[] = $argument->renderType($context, $rules);
                    $identifiers[] = $argument->renderIdentifier($context, $rules);
                }
                $longestType = max(array_map(fn (array $tokens) => strlen($this->renderTokensToString($tokens)), $types));
                $longestIdentifier = max(array_map(fn (array $tokens) => strlen($this->renderTokensToString($tokens)), $identifiers));
                $context->chopDown = new ChopDownRenderContext(
                    paddingSpacesForTypes: $rules->argumentListDefinitionRenderingRules->padTypeNames ? $longestType : null,
                    paddingSpacesForIdentifiers: $rules->argumentListDefinitionRenderingRules->padIdentifiers ? $longestIdentifier : null,
                );
            }
            $argumentsLeft = count($this->arguments);
            foreach ($this->arguments as $argument) {
                $argumentsLeft--;
                $tokens[] = new SpacesToken(strlen($context->indents));
                $tokens[] = $argument->render($context, $rules);
                if ($argumentsLeft > 0 || $rules->argumentListDefinitionRenderingRules->addTrailingCommaToLastItemInChopDown) {
                    $tokens[] = new CommaToken();
                }
                $tokens[] = new NewLineToken();
            }
            $context->chopDown = null;
            $rules->unindent($context);
        }
        $tokens[] = new ParEndToken();
        return $this->flatten($tokens);
    }
}