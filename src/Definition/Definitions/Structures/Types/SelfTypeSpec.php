<?php

namespace CrazyCodeGen\Definition\Definitions\Structures\Types;

use CrazyCodeGen\Definition\Base\DefinesIfStaticallyAccessed;
use CrazyCodeGen\Definition\Base\ProvidesClassReference;
use CrazyCodeGen\Definition\Definitions\Values\ClassRefVal;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\SelfToken;
use CrazyCodeGen\Rendering\Tokens\Token;

class SelfTypeSpec extends TypeDef implements DefinesIfStaticallyAccessed, ProvidesClassReference
{
    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        return [new SelfToken()];
    }

    public function shouldAccessWithStatic(): bool
    {
        return true;
    }

    public function getClassReference(): ClassRefVal
    {
        return new ClassRefVal($this);
    }
}
