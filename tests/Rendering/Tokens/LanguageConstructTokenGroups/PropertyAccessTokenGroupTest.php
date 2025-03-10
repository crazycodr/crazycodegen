<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\PropertyAccessTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\PropertyTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ThisVariableTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\VariableTokenGroup;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\UserLandTokens\IdentifierToken;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class PropertyAccessTokenGroupTest extends TestCase
{
    use TokenFunctions;

    public function testSubjectIsConvertedToVariableTokenGroupIfString()
    {
        $token = new PropertyAccessTokenGroup('foo', new Token('bar'));

        $this->assertEquals(<<<EOS
            \$foo->bar
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }

    public function testPropertyIsConvertedToTokenIfString()
    {
        $token = new PropertyAccessTokenGroup(new VariableTokenGroup('foo'), 'bar');

        $this->assertEquals(<<<EOS
            \$foo->bar
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }

    public function testThisVariableIsAcceptedAndRendered()
    {
        $token = new PropertyAccessTokenGroup(new ThisVariableTokenGroup(), new Token('bar'));

        $this->assertEquals(<<<EOS
            \$this->bar
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }

    public function testPropertyTokenGroupIsSubstitutedForTheReferenceInsteadOfRendered()
    {
        $token = new PropertyAccessTokenGroup(new ThisVariableTokenGroup(), new PropertyTokenGroup(name: 'bar', type: 'int'));

        $this->assertEquals(<<<EOS
            \$this->bar
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }

    public function testNestedPropertyAccessGroupIsRenderedInChains()
    {
        $token = new PropertyAccessTokenGroup(
            new ThisVariableTokenGroup(),
            new PropertyAccessTokenGroup(new PropertyTokenGroup('bar'), 'baz'),
        );

        $this->assertEquals(<<<EOS
            \$this->bar->baz
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }
}
