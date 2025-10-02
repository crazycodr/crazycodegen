<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Exceptions\InvalidIdentifierFormatException;
use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Common\Traits\ValidationTrait;
use CrazyCodeGen\Definition\Base\ProvidesCallableReference;
use CrazyCodeGen\Definition\Base\ShouldNotBeNestedIntoInstruction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Definitions\Traits\HasDocBlockTrait;
use CrazyCodeGen\Definition\Definitions\Traits\HasInstructionsTrait;
use CrazyCodeGen\Definition\Definitions\Traits\HasNamespaceTrait;
use CrazyCodeGen\Definition\Definitions\Traits\HasNameTrait;
use CrazyCodeGen\Definition\Definitions\Traits\HasParametersTrait;
use CrazyCodeGen\Definition\Definitions\Traits\HasReturnTypeTrait;
use CrazyCodeGen\Definition\Definitions\Types\TypeDef;
use CrazyCodeGen\Definition\Definitions\Types\TypeInferenceTrait;
use CrazyCodeGen\Definition\Expression;
use CrazyCodeGen\Definition\Expressions\Instruction;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ColonToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\FunctionToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class FunctionDef extends Tokenizes implements ProvidesCallableReference
{
    use FlattenFunction;
    use TokenFunctions;
    use TypeInferenceTrait;
    use ValidationTrait;

    // Property helpers
    use HasNamespaceTrait;
    use HasDocBlockTrait;
    use HasNameTrait;
    use HasParametersTrait;
    use HasReturnTypeTrait;
    use HasInstructionsTrait;

    /**
     * @throws InvalidIdentifierFormatException
     * @throws NoValidConversionRulesMatchedException
     */
    public function __construct(
        string                        $name,
        null|string|NamespaceDef      $namespace = null,
        null|string|array|DocBlockDef $docBlock = null,
        /** @var string[]|ParameterDef[] $parameters */
        array                         $parameters = [],
        null|string|TypeDef           $returnType = null,
        /** @var NewLinesToken[]|Tokenizes[]|ShouldNotBeNestedIntoInstruction[]|Instruction[] $instructions */
        array                         $instructions = [],
    ) {
        $this->setNamespace($namespace);
        $this->setDocBlock($docBlock);
        $this->setName($name);
        $this->setParameters($parameters);
        $this->setReturnType($returnType);
        $this->setInstructions($instructions);
    }

    /**
     * @param RenderingContext $context
     * @return Token[]
     */
    public function getTokens(RenderingContext $context): array
    {
        $tokens = [];
        if ($this->namespace) {
            $tokens[] = $this->namespace->getTokens($context);
        }
        if ($this->docBlock) {
            $tokens[] = $this->docBlock->getTokens($context);
        }
        $tokens[] = $this->getFunctionDeclarationTokens();
        if ($this->parameters) {
            $tokens[] = $this->parameters->getTokens($context);
        } else {
            $tokens[] = (new ParameterListDef())->getTokens($context);
        }
        $tokens[] = $this->addReturnTypeTokens($context);
        $tokens[] = new BraceStartToken();
        $tokens[] = new BraceEndToken();
        return $this->flatten($tokens);
    }

    /**
     * @return array
     */
    public function getFunctionDeclarationTokens(): array
    {
        $tokens = [];
        $tokens[] = new FunctionToken();
        $tokens[] = new SpacesToken();
        if (!$this->name instanceof Token) {
            $tokens[] = new Token($this->name);
        } else {
            $tokens[] = $this->name;
        }
        return $this->flatten($tokens);
    }

    /**
     * @param RenderingContext $context
     * @return Token[]
     */
    public function addReturnTypeTokens(RenderingContext $context): array
    {
        $tokens = [];
        if ($this->returnType) {
            $tokens[] = new ColonToken();
            if ($this->returnType instanceof Tokenizes) {
                $tokens[] = $this->returnType->getTokens($context);
            }
        }
        return $this->flatten($tokens);
    }

    public function getCallableReference(): Tokenizes
    {
        return new Expression($this->name);
    }
}
