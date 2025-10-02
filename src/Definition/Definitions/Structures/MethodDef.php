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
use CrazyCodeGen\Definition\Expression;
use CrazyCodeGen\Definition\Expressions\Instruction;
use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ColonToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SemiColonToken;
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
     * @return Token[]
     */
    public function getSimpleTokens(TokenizationContext $context): array
    {
        $tokens = [];
        if ($this->docBlock) {
            $tokens[] = $this->docBlock->getSimpleTokens($context);
        }
        $tokens[] = $this->getSimpleFunctionDeclarationTokens();
        if ($this->parameters) {
            $tokens[] = $this->parameters->getSimpleTokens($context);
        } else {
            $tokens[] = (new ParameterListDef())->getSimpleTokens($context);
        }
        $tokens[] = $this->addSimpleReturnTypeTokens($context);
        if (!$this->abstract) {
            $tokens[] = $this->addSimpleBraceTokensAndInstructions($context);
        } else {
            $tokens[] = new SemiColonToken();
        }
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    public function getSimpleFunctionDeclarationTokens(): array
    {
        $tokens = [];
        if ($this->abstract) {
            $tokens[] = new AbstractToken();
            $tokens[] = new SpacesToken();
        }
        $tokens[] = new VisibilityToken($this->visibility);
        $tokens[] = new SpacesToken();
        if ($this->static) {
            $tokens[] = new StaticToken();
            $tokens[] = new SpacesToken();
        }
        $tokens[] = new FunctionToken();
        $tokens[] = new SpacesToken();
        $tokens[] = new Token($this->name);
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    public function addSimpleReturnTypeTokens(TokenizationContext $context): array
    {
        $tokens = [];
        if ($this->returnType) {
            $tokens[] = new ColonToken();
            $tokens[] = $this->returnType->getSimpleTokens($context);
        }
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    public function addSimpleBraceTokensAndInstructions(TokenizationContext $context): array
    {
        $tokens = [];
        $tokens[] = new BraceStartToken();
        $tokens = array_merge($tokens, $this->renderSimpleInstructionsFromFlexibleTokenValue($context, $this->instructions));
        $tokens[] = new BraceEndToken();
        return $this->flatten($tokens);
    }

    public function getCallableReference(): Tokenizes
    {
        return new Expression($this->name);
    }
}
