<?php

namespace CrazyCodeGen\Rendering\Renderers;

use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\FunctionDefinitionTokenGroup;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;
use CrazyCodeGen\Definition\Traits\ComputableTrait;
use CrazyCodeGen\Definition\Traits\FlattenFunction;

class FunctionDefinitionTokenGroupRenderer
{
    use FlattenFunction;
    use ComputableTrait;

    public function render(
        FunctionDefinitionTokenGroup $target,
        RenderingRules $rules,
        RenderRenderContext $context
    ): string {
        $declaration = $target->tokens;

        foreach ($target->getTokens() as $token) {
            if ($token instanceof Token) {
                $rules->applyRenderedToken($token, $context);
            } elseif ($token instanceof TokenGroup) {
                $tokenGroup = $token;
                foreach ($tokenGroup->tokens as $subToken) {
                    $newContext = $context->cloneWithContextsOnly();
                    foreach ($tokenGroup->getContexts() as $appliedContext) {
                        $newContext->applyContextShift(ContextShift::push($appliedContext));
                    }
                    foreach ($this->makeComputed($subToken)->getTokens() as $renderableSubToken) {
                        $tokenResult = $this->renderToken($renderableSubToken, $rules, $newContext);
                    }
                }
//                $subTokens = $token->tokens;
//                foreach ($subTokens as $subToken) {
//                    if ($subToken instanceof Token) {
//                        $rules->applyRenderedToken($subToken, $context);
//                    } elseif ($subToken instanceof ContextShift) {
//                        $previousContextType = $context->getCurrent();
//                        $context->applyContextShift($subToken);
//                        $newContextType = $context->getCurrent();
//                        $rules->applyContextShiftToBuffer($previousContextType, $newContextType, $context);
//                    } else {
//                        $rules->applyNonTokenPrefix($subToken, $context);
//                        $rules->applyNonToken($subToken, $context);
//                        $rules->applyNonTokenSuffix($subToken, $context);
//                    }
//                }
            } elseif ($token instanceof ContextShift) {
                $previousContextType = $context->getCurrent();
                $context->applyContextShift($token);
                $newContextType = $context->getCurrent();
                $rules->applyContextShiftToBuffer($previousContextType, $newContextType, $context);
            } else {
                $rules->applyNonTokenPrefix($token, $context);
                $rules->applyNonToken($token, $context);
                $rules->applyNonTokenSuffix($token, $context);
            }
        }
        return $context->buffer;
    }

    private function renderToken(Token $token, RenderingRules $rules, RenderRenderContext $context): string
    {

    }
}