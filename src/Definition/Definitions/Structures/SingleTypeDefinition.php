<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\IsStaticAccessContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;

class SingleTypeDefinition extends TokenGroup implements IsStaticAccessContext
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
    public function render(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if (in_array($this->type, $context->importedClasses)) {
            $tokens[] = new Token($this->shortName);
        } else {
            $tokens[] = new Token($this->type);
        }
        return $tokens;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function getNamespace(): null|string
    {
        return $this->namespace;
    }

    public function isStaticAccessContext(): bool
    {
        return true;
    }
}
