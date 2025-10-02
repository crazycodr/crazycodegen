<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\ProvidesCallableReference;
use CrazyCodeGen\Definition\Base\ProvidesVariableReference;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Definitions\Types\TypeDef;
use CrazyCodeGen\Definition\Definitions\Types\TypeInferenceTrait;
use CrazyCodeGen\Definition\Definitions\Values\ValueInferenceTrait;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\EqualToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ExpansionToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ParameterDef extends Tokenizes implements ProvidesVariableReference, ProvidesCallableReference
{
    use FlattenFunction;
    use TokenFunctions;
    use TypeInferenceTrait;
    use ValueInferenceTrait;

    public const UNSET_DEFAULT_VALUE = '@!#UNSET@!#';

    public null|TypeDef $type;

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function __construct(
        public string        $name,
        null|string|TypeDef $type = null,
        public mixed               $defaultValue = self::UNSET_DEFAULT_VALUE,
        public bool                $isVariadic = false,
    ) {
        if (is_string($type)) {
            $this->type = $this->inferType($type);
        } else {
            $this->type = $type;
        }
        if ($this->defaultValue === self::UNSET_DEFAULT_VALUE) {
            // Do nothing or isSupportedValue will change to StringVal
        } elseif ($this->isInferableValue($this->defaultValue)) {
            $this->defaultValue = $this->inferValue($this->defaultValue);
        } else {
            $this->defaultValue = self::UNSET_DEFAULT_VALUE;
        }
    }

    /**
     * @return Token[]
     */
    public function getTokens(RenderingContext $context): array
    {
        $tokens = [];
        $tokens[] = $this->renderType($context);
        $tokens[] = $this->renderIdentifier($context);
        $tokens[] = $this->renderDefaultValue($context);
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    public function renderType(RenderingContext $context): array
    {
        $tokens = [];
        if ($this->type) {
            $tokens[] = $this->type->getTokens($context);
            $tokens[] = new SpacesToken();
        }
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    public function renderIdentifier(RenderingContext $context): array
    {
        $tokens = [];
        if ($this->isVariadic) {
            $tokens[] = new ExpansionToken();
        }
        $tokens[] = (new VariableDef($this->name))->getTokens($context);
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    public function renderDefaultValue(RenderingContext $context): array
    {
        $tokens = [];
        if ($this->defaultValue !== self::UNSET_DEFAULT_VALUE) {
            $tokens[] = new EqualToken();
            $tokens[] = $this->defaultValue->getTokens($context);
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
