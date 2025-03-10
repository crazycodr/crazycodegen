<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\DoubleColonToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;

class StaticMemberAccessTokenGroup extends ExpressionTokenGroup
{
    use FlattenFunction;

    public function __construct(
        public string|Token|TokenGroup|VariableTokenGroup|PropertyTokenGroup $subject,
        public string|Token|TokenGroup                                       $member,
    ) {
        if (is_string($this->subject)) {
            $this->subject = new VariableTokenGroup($this->subject);
        } elseif ($this->subject instanceof PropertyTokenGroup) {
            $this->subject = new Token($this->subject->name);
        }
        if (is_string($this->member)) {
            $this->member = new Token($this->member);
        } elseif ($this->member instanceof PropertyTokenGroup) {
            $this->member = new Token($this->member->name);
        }
        parent::__construct([$this->subject, new DoubleColonToken(), $this->member]);
    }
}
