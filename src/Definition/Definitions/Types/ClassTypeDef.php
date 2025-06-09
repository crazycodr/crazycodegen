<?php

namespace CrazyCodeGen\Definition\Definitions\Types;

use CrazyCodeGen\Definition\Base\ShouldBeAccessedStatically;
use CrazyCodeGen\Definition\Base\ProvidesCallableReference;
use CrazyCodeGen\Definition\Base\ProvidesClassReference;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Definitions\Contexts\MemberAccessContext;
use CrazyCodeGen\Definition\Definitions\Structures\MethodDef;
use CrazyCodeGen\Definition\Definitions\Structures\PropertyDef;
use CrazyCodeGen\Definition\Definitions\Values\ClassRefVal;
use CrazyCodeGen\Definition\Expressions\Operations\CallOp;
use CrazyCodeGen\Definition\Expressions\Operations\ChainOp;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\Token;

class ClassTypeDef extends TypeDef implements ShouldBeAccessedStatically, ProvidesClassReference, ProvidesCallableReference
{
    private null|string $namespace = null;
    private string $shortName;

    public function __construct(
        public string $type,
    ) {
        if (str_contains($type, '\\')) {
            $this->namespace = substr($type, 0, strrpos($type, '\\'));
            $this->shortName = substr($type, strrpos($type, '\\') + 1);
        } else {
            $this->shortName = $type;
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
        if (in_array($this->type, $context->importedClasses)) {
            $tokens[] = new Token($this->shortName);
        } else {
            $tokens[] = new Token($this->namespace . '\\' . $this->shortName);
        }
        return $tokens;
    }

    public function isAccessedStatically(): bool
    {
        return true;
    }

    public function getClassReference(): ClassRefVal
    {
        return new ClassRefVal($this);
    }

    public function to(PropertyDef|MethodDef|CallOp|MemberAccessContext $what): ChainOp
    {
        return new ChainOp([$this, $what]);
    }

    public function asNullable(): MultiTypeDef
    {
        return new MultiTypeDef([$this, new BuiltInTypeSpec('null')]);
    }

    public function getCallableReference(): Tokenizes
    {
        return $this;
    }
}
