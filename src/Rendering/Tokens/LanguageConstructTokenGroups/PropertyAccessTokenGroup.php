<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\MemberAccessToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;
use CrazyCodeGen\Rendering\Tokens\UserLandTokens\IdentifierToken;

class PropertyAccessTokenGroup extends ExpressionTokenGroup
{
    use FlattenFunction;

    public function __construct(
        public string|IdentifierToken|VariableTokenGroup|PropertyTokenGroup $subject,
        public string|Token|TokenGroup $property,
    ) {
        if (is_string($this->subject)) {
            $this->subject = new VariableTokenGroup($this->subject);
        } elseif ($this->subject instanceof PropertyTokenGroup) {
            $this->subject = new IdentifierToken($this->subject->name);
        }
        if (is_string($this->property)) {
            $this->property = new Token($this->property);
        } elseif ($this->property instanceof PropertyTokenGroup) {
            $this->property = new IdentifierToken($this->property->name);
        }
        parent::__construct([$this->subject, new MemberAccessToken(), $this->property]);
    }
}
