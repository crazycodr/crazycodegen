<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Enums\VisibilityEnum;
use CrazyCodeGen\Common\Exceptions\InvalidIdentifierFormatException;
use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\ProvidesCallableReference;
use CrazyCodeGen\Definition\Base\ShouldNotBeNestedIntoInstruction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Definitions\Traits\HasAbstractModifierTrait;
use CrazyCodeGen\Definition\Definitions\Traits\HasDocBlockTrait;
use CrazyCodeGen\Definition\Definitions\Traits\HasInstructionsTrait;
use CrazyCodeGen\Definition\Definitions\Traits\HasStaticModifierTrait;
use CrazyCodeGen\Definition\Definitions\Traits\HasVisibilityModifierTrait;
use CrazyCodeGen\Definition\Definitions\Traits\HasNameTrait;
use CrazyCodeGen\Definition\Definitions\Traits\HasParametersTrait;
use CrazyCodeGen\Definition\Definitions\Traits\HasReturnTypeTrait;
use CrazyCodeGen\Definition\Definitions\Types\TypeDef;
use CrazyCodeGen\Definition\Definitions\Types\TypeInferenceTrait;
use CrazyCodeGen\Definition\Expressions\Expression;
use CrazyCodeGen\Definition\Expressions\Instruction;
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
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class MethodDef extends Tokenizes implements ProvidesCallableReference
{
    use FlattenFunction;
    use TokenFunctions;
    use TypeInferenceTrait;

    // Property helpers
    use HasDocBlockTrait;
    use HasAbstractModifierTrait;
    use HasVisibilityModifierTrait;
    use HasStaticModifierTrait;
    use HasNameTrait;
    use HasParametersTrait;
    use HasReturnTypeTrait;
    use HasInstructionsTrait;

    /**
     * @throws NoValidConversionRulesMatchedException
     * @throws InvalidIdentifierFormatException
     */
    public function __construct(
        string                        $name,
        null|string|array|DocBlockDef $docBlock = null,
        bool                          $abstract = false,
        VisibilityEnum                $visibility = VisibilityEnum::PUBLIC,
        bool                          $static = false,
        /** @var string[]|ParameterDef[] $parameters */
        array                         $parameters = [],
        null|string|TypeDef           $returnType = null,
        /** @var NewLinesToken[]|Tokenizes[]|ShouldNotBeNestedIntoInstruction[]|Instruction[] $instructions */
        array                         $instructions = [],
    ) {
        $this->setDocBlock($docBlock);
        $this->setAbstract($abstract);
        $this->setVisibility($visibility);
        $this->setStatic($static);
        $this->setName($name);
        $this->setParameters($parameters);
        $this->setReturnType($returnType);
        $this->setInstructions($instructions);
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if ($this->docBlock) {
            $tokens[] = $this->docBlock->getTokens($context, $rules);
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
            $tokens[] = $this->parameters->getTokens($context, $rules);
        } else {
            $tokens[] = (new ParameterListDef())->getTokens($context, $rules);
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
            $tokens[] = $this->returnType->getTokens($context, $rules);
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
            $tokens[] = $this->parameters->getChopDownTokens($context, $rules);
        } else {
            $tokens[] = (new ParameterListDef())->getChopDownTokens($context, $rules);
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

    public function getCallableReference(): Tokenizes
    {
        return new Expression($this->name);
    }
}
