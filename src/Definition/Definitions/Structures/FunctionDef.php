<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Definitions\Structures\Types\TypeDef;
use CrazyCodeGen\Definition\Definitions\Structures\Types\TypeInferenceTrait;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Enums\WrappingDecision;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ColonToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\FunctionToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class FunctionDef extends Tokenizes
{
    use FlattenFunction;
    use TokenFunctions;
    use TypeInferenceTrait;

    public function __construct(
        public string|Token                          $name,
        public null|NamespaceDef                     $namespace = null,
        public null|string|DocBlockDef               $docBlock = null,
        public null|ParameterListDef                 $arguments = null,
        public null|string|TypeDef $returnType = null,
        public null|array                            $bodyInstructions = null,
    ) {
        if (is_string($this->returnType)) {
            $this->returnType = $this->inferVariableOnlyType($this->returnType);
        }
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if ($this->namespace) {
            $tokens[] = $this->namespace->getTokens($context, $rules);
        }
        if ($this->docBlock) {
            $tokens[] = $this->docBlock->getTokens($context, $rules);
            $tokens[] = new NewLinesToken($rules->functions->newLinesAfterDocBlock);
        }
        if ($rules->functions->argumentsOnDifferentLines === WrappingDecision::NEVER) {
            $tokens[] = $this->renderInlineScenario($context, $rules);
        } elseif ($rules->functions->argumentsOnDifferentLines === WrappingDecision::ALWAYS) {
            $tokens[] = $this->renderChopDownScenario($context, $rules);
        } else {
            $inlineScenario = $this->renderInlineScenario($context, $rules);
            if (!$rules->exceedsAvailableSpace($context->getCurrentLine(), $this->renderTokensToString($inlineScenario))) {
                $tokens[] = $inlineScenario;
            } else {
                $tokens[] = $this->renderChopDownScenario($context, $rules);
            }
        }

        return $this->flatten($tokens);
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function renderInlineScenario(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        $tokens = $this->getFunctionDeclarationTokens($tokens, $rules);
        if ($this->arguments) {
            $tokens[] = $this->arguments->getTokens($context, $rules);
        } else {
            $tokens[] = (new ParameterListDef())->getTokens($context, $rules);
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
        if (!$this->name instanceof Token) {
            $tokens[] = new Token($this->name);
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
            if ($this->returnType instanceof Tokenizes) {
                $tokens[] = $this->returnType->getTokens($context, $rules);
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
            && $rules->functions->closingBrace === BracePositionEnum::DIFF_LINE
        ) {
            if ($rules->functions->spacesBeforeOpeningBrace) {
                $tokens[] = new SpacesToken($rules->functions->spacesBeforeOpeningBrace);
            }
            $tokens[] = new BraceStartToken();
            $tokens[] = new NewLinesToken();
            $tokens[] = new BraceEndToken();
        } elseif (
            $rules->functions->openingBrace === BracePositionEnum::DIFF_LINE
            && $rules->functions->closingBrace === BracePositionEnum::SAME_LINE
        ) {
            $tokens[] = new NewLinesToken();
            $tokens[] = new BraceStartToken();
            $tokens[] = new BraceEndToken();
        } elseif (
            $rules->functions->openingBrace === BracePositionEnum::DIFF_LINE
            && $rules->functions->closingBrace === BracePositionEnum::DIFF_LINE
        ) {
            $tokens[] = new NewLinesToken();
            $tokens[] = new BraceStartToken();
            $tokens[] = new NewLinesToken();
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
        $tokens = $this->getFunctionDeclarationTokens($tokens, $rules);
        if ($this->arguments) {
            $tokens[] = $this->arguments->getChopDownTokens($context, $rules);
        } else {
            $tokens[] = (new ParameterListDef())->getChopDownTokens($context, $rules);
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
        $tokens[] = new NewLinesToken();
        $tokens[] = new BraceEndToken();
        return $tokens;
    }
}
