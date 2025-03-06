<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\ContextTypeEnum;
use CrazyCodeGen\Rendering\Renderers\RenderContext;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\UserLandTokens\IdentifierToken;

class SingleTypeTokenGroup extends AbstractTypeTokenGroup
{
    private null|string $namespace = null;
    private string $shortName;

    public function __construct(
        public readonly string $type,
        public readonly bool $shorten = false,
    )
    {
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
        if ($this->shorten) {
            $tokens[] = new IdentifierToken($this->shortName);
        } else {
            $tokens[] = new IdentifierToken($this->type);
        }
        return $tokens;
    }

    /**
     * @return ContextTypeEnum[]
     */
    public function getContexts(): array
    {
        return array_merge([
            ContextTypeEnum::type,
        ], parent::getContexts());
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function getNamespace(): null|string
    {
        return $this->namespace;
    }
}