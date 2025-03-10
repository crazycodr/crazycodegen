<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\MemberAccessTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\PropertyTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ThisRefTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\VariableTokenGroup;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class PropertyAccessTokenGroupTest extends TestCase
{
    use TokenFunctions;

    public function testSubjectIsConvertedToVariableTokenGroupIfString()
    {
        $token = new MemberAccessTokenGroup('foo', new Token('bar'));

        $this->assertEquals(
            <<<EOS
            \$foo->bar
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }

    public function testPropertyIsConvertedToTokenIfString()
    {
        $token = new MemberAccessTokenGroup(new VariableTokenGroup('foo'), 'bar');

        $this->assertEquals(
            <<<EOS
            \$foo->bar
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }

    public function testThisVariableIsAcceptedAndRendered()
    {
        $token = new MemberAccessTokenGroup(new ThisRefTokenGroup(), new Token('bar'));

        $this->assertEquals(
            <<<EOS
            \$this->bar
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }

    public function testPropertyTokenGroupIsSubstitutedForTheReferenceInsteadOfRendered()
    {
        $token = new MemberAccessTokenGroup(new ThisRefTokenGroup(), new PropertyTokenGroup(name: 'bar', type: 'int'));

        $this->assertEquals(
            <<<EOS
            \$this->bar
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }

    public function testNestedPropertyAccessGroupIsRenderedInChains()
    {
        $token = new MemberAccessTokenGroup(
            new ThisRefTokenGroup(),
            new MemberAccessTokenGroup(new PropertyTokenGroup('bar'), 'baz'),
        );

        $this->assertEquals(
            <<<EOS
            \$this->bar->baz
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }
}
