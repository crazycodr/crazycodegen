<?php

namespace CrazyCodeGen\Definition\Definitions\Types;

use CrazyCodeGen\Definition\Base\ShouldBeAccessedStatically;
use CrazyCodeGen\Definition\Base\ProvidesClassReference;
use CrazyCodeGen\Definition\Definitions\Values\ClassRefVal;
use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\SelfToken;
use CrazyCodeGen\Rendering\Tokens\Token;

class SelfTypeSpec extends TypeDef implements ShouldBeAccessedStatically, ProvidesClassReference
{
    /**
     * @return Token[]
     */
    public function getSimpleTokens(TokenizationContext $context): array
    {
        return [new SelfToken()];
    }

    public function isAccessedStatically(): bool
    {
        return true;
    }

    public function getClassReference(): ClassRefVal
    {
        return new ClassRefVal($this);
    }
}
