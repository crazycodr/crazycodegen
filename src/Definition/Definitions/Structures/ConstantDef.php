<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Enums\VisibilityEnum;
use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\ProvidesVariableReference;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Definitions\Types\TypeDef;
use CrazyCodeGen\Definition\Definitions\Types\TypeInferenceTrait;
use CrazyCodeGen\Definition\Definitions\Values\ValueInferenceTrait;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\EqualToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SemiColonToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ConstToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\VisibilityToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ConstantDef extends Tokenizes implements ProvidesVariableReference
{
    use FlattenFunction;
    use TokenFunctions;
    use TypeInferenceTrait;
    use ValueInferenceTrait;

    public null|TypeDef     $type;

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function __construct(
        public string           $name,
        public null|DocBlockDef $docBlock = null,
        null|TypeDef            $type = null,
        public VisibilityEnum   $visibility = VisibilityEnum::PUBLIC,
        public mixed            $value = null,
    ) {
        $this->type = $type;
        $this->value = $this->inferValue($this->value);
    }

    /**
     * @return Token[]
     */
    public function getTokens(RenderingContext $context): array
    {
        $tokens = [];

        if ($this->docBlock) {
            $tokens[] = $this->docBlock->getTokens($context);
        }

        $tokens[] = new VisibilityToken($this->visibility);
        $tokens[] = new SpacesToken();
        $tokens[] = new ConstToken();
        $tokens[] = new SpacesToken();
        $tokens[] = $this->renderType($context);
        $tokens[] = (new VariableDef($this->name))->getTokens($context);
        $tokens[] = $this->renderValue($context);
        $tokens[] = new SemicolonToken();
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    public function renderType(RenderingContext $context): array
    {
        $tokens = [];
        if (!is_null($this->type)) {
            $tokens[] = $this->type->getTokens($context);
            $tokens[] = new SpacesToken();
        }
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    public function renderValue(RenderingContext $context): array
    {
        $tokens = [];
        $tokens[] = new EqualToken();
        $tokens[] = $this->value->getTokens($context);
        return $this->flatten($tokens);
    }

    public function getVariableReference(): VariableDef
    {
        return new VariableDef($this->name);
    }
}
