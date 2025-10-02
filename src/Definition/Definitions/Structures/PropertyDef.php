<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Enums\VisibilityEnum;
use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\ProvidesCallableReference;
use CrazyCodeGen\Definition\Base\ProvidesClassReference;
use CrazyCodeGen\Definition\Base\ProvidesVariableReference;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Definitions\Types\TypeDef;
use CrazyCodeGen\Definition\Definitions\Types\TypeInferenceTrait;
use CrazyCodeGen\Definition\Definitions\Values\ValueInferenceTrait;
use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\EqualToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SemiColonToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\StaticToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\VisibilityToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class PropertyDef extends Tokenizes implements ProvidesVariableReference, ProvidesCallableReference
{
    use FlattenFunction;
    use TokenFunctions;
    use TypeInferenceTrait;
    use ValueInferenceTrait;

    public const UNSET_DEFAULT_VALUE = '@!#UNSET@!#';

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function __construct(
        public string            $name,
        public null|string|DocBlockDef $docBlock = null,
        public null|string|TypeDef     $type = null,
        public VisibilityEnum          $visibility = VisibilityEnum::PUBLIC,
        public bool                    $static = false,
        public mixed                   $defaultValue = self::UNSET_DEFAULT_VALUE,
    ) {
        if (is_string($this->type)) {
            $this->type = $this->inferType($this->type);
        }
        if ($this->defaultValue === self::UNSET_DEFAULT_VALUE) {
            // Do nothing or isSupportedValue will change to StringVal
        } elseif ($this->isInferableValue($this->defaultValue)) {
            $this->defaultValue = $this->inferValue($this->defaultValue);
        } elseif ($this->defaultValue instanceof ProvidesClassReference) {
            $this->defaultValue = $this->defaultValue->getClassReference();
        } else {
            $this->defaultValue = self::UNSET_DEFAULT_VALUE;
        }
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

        $tokens[] = new VisibilityToken($this->visibility);
        $tokens[] = new SpacesToken();
        $tokens[] = $this->renderSimpleModifiers();
        $tokens[] = $this->renderSimpleType($context);
        $tokens[] = (new VariableDef($this->name))->getSimpleTokens($context);
        $tokens[] = $this->renderSimpleDefaultValue($context);
        $tokens[] = new SemicolonToken();
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    public function renderSimpleModifiers(): array
    {
        $tokens = [];
        if ($this->static) {
            $tokens[] = new StaticToken();
            $tokens[] = new SpacesToken();
        }
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    public function renderSimpleType(TokenizationContext $context): array
    {
        $tokens = [];
        if (!is_null($this->type)) {
            $tokens[] = $this->type->getSimpleTokens($context);
            $tokens[] = new SpacesToken();
        }
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    public function renderSimpleDefaultValue(TokenizationContext $context): array
    {
        $tokens = [];
        if ($this->defaultValue !== self::UNSET_DEFAULT_VALUE) {
            $tokens[] = new EqualToken();
            $tokens[] = $this->defaultValue->getSimpleTokens($context);
        }
        return $this->flatten($tokens);
    }

    public function getVariableReference(): VariableDef
    {
        return new VariableDef($this->name);
    }

    public function getCallableReference(): Tokenizes
    {
        return $this->getVariableReference();
    }
}
