<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Enums\VisibilityEnum;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Enums\WrappingDecision;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ColonToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\AbstractToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\FunctionToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\StaticToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\VisibilityToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class MethodTokenGroup extends TokenGroup
{
    use FlattenFunction;
    use TokenFunctions;

    public function __construct(
        public string                                               $name,
        public null|string|array|DocBlockTokenGroup                 $docBlock = null,
        public bool                                                 $abstract = false,
        public VisibilityEnum                                       $visibility = VisibilityEnum::PUBLIC,
        public bool                                                 $static = false,
        public null|ParameterListTokenGroup                         $parameters = null,
        public null|string|SingleTypeTokenGroup|MultiTypeTokenGroup $returnType = null,
        /** @var Token|TokenGroup|Token[]|TokenGroup[] $instructions */
        public array|Token|TokenGroup                               $instructions = [],
    )
    {
        $this->setDocBlock($docBlock);
        $this->setReturnType($returnType);
    }

    /**
     * @param string|string[]|DocBlockTokenGroup $docBlock
     * @return $this
     */
    public function setDocBlock(null|string|array|DocBlockTokenGroup $docBlock): self
    {
        if (is_string($docBlock)) {
            $docBlock = new DocBlockTokenGroup([$docBlock]);
        } elseif (is_array($docBlock)) {
            $docBlock = array_filter($docBlock, fn($value) => is_string($value));
            $docBlock = new DocBlockTokenGroup($docBlock);
        }
        $this->docBlock = $docBlock;
        return $this;
    }

    public function setAbstract(bool $isAbstract): self
    {
        $this->abstract = $isAbstract;
        return $this;
    }

    public function setVisibility(VisibilityEnum $visibility): self
    {
        $this->visibility = $visibility;
        return $this;
    }

    public function setStatic(bool $isStatic): self
    {
        $this->static = $isStatic;
        return $this;
    }

    public function addParameter(string|ParameterTokenGroup $parameter): self
    {
        if ($this->parameters === null) {
            $this->parameters = new ParameterListTokenGroup();
        }
        if (is_string($parameter)) {
            $parameter = new ParameterTokenGroup($parameter);
        }
        $this->parameters->parameters[] = $parameter;
        return $this;
    }

    public function addParameterExploded(
        string                                               $name,
        null|string|SingleTypeTokenGroup|MultiTypeTokenGroup $type,
        null|int|float|string|bool|Token                     $defaultValue = null,
        bool                                                 $defaultValueIsNull = false,
        bool                                                 $isVariadic = false,
    ): self
    {
        if ($this->parameters === null) {
            $this->parameters = new ParameterListTokenGroup();
        }
        $this->parameters->parameters[] = new ParameterTokenGroup(
            $name,
            $type,
            $defaultValue,
            $defaultValueIsNull,
            $isVariadic,
        );
        return $this;
    }

    public function setReturnType(null|string|SingleTypeTokenGroup|MultiTypeTokenGroup $type): self
    {
        if (is_string($type)) {
            $type = new SingleTypeTokenGroup($type);
        }
        $this->returnType = $type;
        return $this;
    }

    public function addInstruction(Token|TokenGroup $instruction): self
    {
        $this->instructions[] = $instruction;
        return $this;
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function render(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if ($this->docBlock) {
            $tokens[] = $this->docBlock->render($context, $rules);
            $tokens[] = new NewLinesToken($rules->methods->newLinesAfterDocBlock);
        }
        if ($rules->methods->argumentsOnDifferentLines === WrappingDecision::NEVER) {
            $tokens[] = $this->renderInlineScenario($context, $rules);
        } elseif ($rules->methods->argumentsOnDifferentLines === WrappingDecision::ALWAYS) {
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
        if ($this->parameters) {
            $tokens[] = $this->parameters->render($context, $rules);
        } else {
            $tokens[] = (new ParameterListTokenGroup())->render($context, $rules);
        }
        $tokens = $this->addReturnTypeTokens($rules, $tokens, $context);
        $tokens = $this->addInlineBraceTokensAndInstructions($context, $rules, $tokens);
        return $this->flatten($tokens);
    }

    /**
     * @param array $tokens
     * @param RenderingRules $rules
     * @return array
     */
    public function getFunctionDeclarationTokens(array $tokens, RenderingRules $rules): array
    {
        if ($this->abstract) {
            $tokens[] = new AbstractToken();
            $tokens[] = new SpacesToken($rules->methods->spacesAfterAbstract);
        }
        $tokens[] = new VisibilityToken($this->visibility);
        $tokens[] = new SpacesToken($rules->methods->spacesAfterVisibility);
        if ($this->static) {
            $tokens[] = new StaticToken();
            $tokens[] = new SpacesToken($rules->methods->spacesAfterStatic);
        }
        $tokens[] = new FunctionToken();
        $tokens[] = new SpacesToken($rules->methods->spacesAfterFunction);
        $tokens[] = new Token($this->name);
        $tokens[] = new SpacesToken($rules->methods->spacesAfterIdentifier);
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
            if ($rules->methods->spacesAfterArguments) {
                $tokens[] = new SpacesToken($rules->methods->spacesAfterArguments);
            }
            $tokens[] = new ColonToken();
            if ($rules->methods->spacesAfterReturnColon) {
                $tokens[] = new SpacesToken($rules->methods->spacesAfterReturnColon);
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
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @param array $tokens
     * @return array
     */
    public function addInlineBraceTokensAndInstructions(RenderContext $context, RenderingRules $rules, array $tokens): array
    {
        if (
            $rules->methods->openingBrace === BracePositionEnum::SAME_LINE
            && $rules->methods->closingBrace === BracePositionEnum::SAME_LINE
        ) {
            if ($rules->methods->spacesBeforeOpeningBrace) {
                $tokens[] = new SpacesToken($rules->methods->spacesBeforeOpeningBrace);
            }
            $tokens[] = new BraceStartToken();
            if (!empty($this->instructions)) {
                $tokens[] = new NewLinesToken();
                $rules->indent($context);
                $bodyTokens = $this->renderInstructionsFromFlexibleTokenValue($this->instructions, $context, $rules);
                if (!empty($bodyTokens)) {
                    $tokens[] = $this->insertIndentationTokens($rules, $bodyTokens);
                }
                $rules->unindent($context);
            }
            $tokens[] = new BraceEndToken();
        } elseif (
            $rules->methods->openingBrace === BracePositionEnum::SAME_LINE
            && $rules->methods->closingBrace === BracePositionEnum::DIFF_LINE
        ) {
            if ($rules->methods->spacesBeforeOpeningBrace) {
                $tokens[] = new SpacesToken($rules->methods->spacesBeforeOpeningBrace);
            }
            $tokens[] = new BraceStartToken();
            $tokens[] = new NewLinesToken();
            if (!empty($this->instructions)) {
                $rules->indent($context);
                $bodyTokens = $this->renderInstructionsFromFlexibleTokenValue($this->instructions, $context, $rules);
                if (!empty($bodyTokens)) {
                    $tokens[] = $this->insertIndentationTokens($rules, $bodyTokens);
                }
                $rules->unindent($context);
            }
            $tokens[] = new BraceEndToken();
        } elseif (
            $rules->methods->openingBrace === BracePositionEnum::DIFF_LINE
            && $rules->methods->closingBrace === BracePositionEnum::SAME_LINE
        ) {
            $tokens[] = new NewLinesToken();
            $tokens[] = new BraceStartToken();
            if (!empty($this->instructions)) {
                $rules->indent($context);
                $bodyTokens = $this->renderInstructionsFromFlexibleTokenValue($this->instructions, $context, $rules);
                if (!empty($bodyTokens)) {
                    $tokens[] = $this->insertIndentationTokens($rules, $bodyTokens);
                }
                $rules->unindent($context);
            }
            $tokens[] = new BraceEndToken();
        } elseif (
            $rules->methods->openingBrace === BracePositionEnum::DIFF_LINE
            && $rules->methods->closingBrace === BracePositionEnum::DIFF_LINE
        ) {
            $tokens[] = new NewLinesToken();
            $tokens[] = new BraceStartToken();
            $tokens[] = new NewLinesToken();
            if (!empty($this->instructions)) {
                $rules->indent($context);
                $bodyTokens = $this->renderInstructionsFromFlexibleTokenValue($this->instructions, $context, $rules);
                if (!empty($bodyTokens)) {
                    $tokens[] = $this->insertIndentationTokens($rules, $bodyTokens);
                }
                $rules->unindent($context);
            }
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
        if ($this->parameters) {
            $tokens[] = $this->parameters->renderChopDownScenario($context, $rules);
        } else {
            $tokens[] = (new ParameterListTokenGroup())->renderChopDownScenario($context, $rules);
        }
        $tokens = $this->addReturnTypeTokens($rules, $tokens, $context);
        $tokens = $this->addChopDownBraceTokensAndInstructions($context, $rules, $tokens);
        return $this->flatten($tokens);
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @param array $tokens
     * @return array
     */
    public function addChopDownBraceTokensAndInstructions(RenderContext $context, RenderingRules $rules, array $tokens): array
    {
        if ($rules->methods->spacesBeforeOpeningBrace) {
            $tokens[] = new SpacesToken($rules->methods->spacesBeforeOpeningBrace);
        }
        $tokens[] = new BraceStartToken();
        $tokens[] = new NewLinesToken();
        if (!empty($this->instructions)) {
            $rules->indent($context);
            $trueTokens = $this->renderInstructionsFromFlexibleTokenValue($this->instructions, $context, $rules);
            if (!empty($trueTokens)) {
                $tokens[] = $this->insertIndentationTokens($rules, $trueTokens);
            }
            $rules->unindent($context);
        }
        $tokens[] = new BraceEndToken();
        return $tokens;
    }
}
