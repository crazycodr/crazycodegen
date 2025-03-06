<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Enums\ChopWrapDecisionEnum;
use CrazyCodeGen\Rendering\Renderers\Enums\ContextTypeEnum;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLineTokens;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\AbstractToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ClassToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ExtendsToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;
use CrazyCodeGen\Rendering\Tokens\UserLandTokens\IdentifierToken;
use CrazyCodeGen\Rendering\Traits\RenderTokensToStringTrait;

class ClassDefinitionTokenGroup extends TokenGroup
{
    use FlattenFunction;
    use RenderTokensToStringTrait;

    public function __construct(
        public readonly string|IdentifierToken             $name,
        public readonly null|NamespaceTokenGroup           $namespace = null,
        public readonly null|string|AbstractTypeTokenGroup $extends = null,
        /** @var string[]|AbstractTypeTokenGroup[] $implements */
        public readonly array                              $implements = [],
        public readonly bool                               $abstract = false,
    )
    {
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function render(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if ($this->namespace) {
            $tokens[] = $this->namespace->render($context, $rules);
        }

        $scenarios = [];
        /** @var array{extends: ChopWrapDecisionEnum, implements: ChopWrapDecisionEnum, individualImplements: ChopWrapDecisionEnum} $permutation */
        foreach ($this->getWrapRulePermutations($rules) as $permutation) {
            $scenario = $this->getDeclarationByWrappingPermutation(
                $permutation['extends'],
                $permutation['implements'],
                $permutation['individualImplements'],
                $context,
                $rules,
            );
            $textScenario = $this->renderTokensToString($scenario);
            $textScenarioLines = explode("\n", $textScenario);
            foreach ($textScenarioLines as $textScenarioLine) {
                if ($rules->exceedsAvailableSpace($context->getCurrentLine(), $textScenarioLine)) {
                    if (
                        str_contains($textScenarioLine, 'extends ')
                        && $rules->classes->extendsOnNextLine === ChopWrapDecisionEnum::NEVER_WRAP
                    ) {
                        continue;
                    }
                    if (
                        str_contains($textScenarioLine, 'implements ')
                        && (
                            $rules->classes->implementsOnNextLine === ChopWrapDecisionEnum::NEVER_WRAP
                            || $rules->classes->implementsOnDifferentLines === ChopWrapDecisionEnum::NEVER_WRAP
                        )
                    ) {
                        continue;
                    }
                    continue 2;
                }
            }
            $scenarios[] = $scenario;
        }

        /** @var Token[] $tokens */
        $tokens = array_merge($tokens, array_pop($scenarios));

        if ($rules->classes->classOpeningBrace === BracePositionEnum::SAME_LINE) {
            $tokens[] = new SpacesToken($rules->classes->spacesBeforeOpeningBraceIfSameLine);
            $tokens[] = new BraceStartToken();
        } else {
            $tokens[] = new NewLineTokens();
            if (!empty($context->indents)) {
                $tokens[] = SpacesToken::fromString($context->indents);
            }
            $tokens[] = new BraceStartToken();
            $rules->indent($context);
        }

        if ($rules->classes->classClosingBrace !== BracePositionEnum::SAME_LINE) {
            $rules->unindent($context);
            $tokens[] = new NewLineTokens();
            if (!empty($context->indents)) {
                $tokens[] = SpacesToken::fromString($context->indents);
            }
        }
        $tokens[] = new BraceEndToken();

        return $this->flatten($tokens);
    }

    /**
     * @return ContextTypeEnum[]
     */
    public function getContexts(): array
    {
        return array_merge([
            ContextTypeEnum::function,
        ], parent::getContexts());
    }

    /**
     * @return Token[]
     */
    private function getDeclarationTokens(): array
    {
        $tokens = [];
        if ($this->abstract) {
            $tokens[] = new AbstractToken();
            $tokens[] = new SpacesToken();
        }
        $tokens[] = new ClassToken();
        $tokens[] = new SpacesToken();
        if (!$this->name instanceof IdentifierToken) {
            $tokens[] = new IdentifierToken($this->name);
        } else {
            $tokens[] = $this->name;
        }
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    private function getExtendsTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if ($this->extends) {
            $tokens[] = new ExtendsToken();
            $tokens[] = new SpacesToken();
            if (!$this->extends instanceof AbstractTypeTokenGroup) {
                $tokens[] = (new SingleTypeTokenGroup($this->extends))->render($context, $rules);
            } else {
                $tokens[] = $this->extends->render($context, $rules);
            }
        }
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    private function getInlineImplementsTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if (empty($this->implements)) {
            return $tokens;
        }
        $implementsTokenGroup = new ImplementsTokenGroup($this->implements);
        return $implementsTokenGroup->renderInlineScenario($context, $rules);
    }

    /**
     * @return Token[]
     */
    private function getChopDownImplementsTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if (empty($this->implements)) {
            return $tokens;
        }
        $implementsTokenGroup = new ImplementsTokenGroup($this->implements);
        return $implementsTokenGroup->renderChopDownScenario($context, $rules);
    }

    /**
     * @return Token[]
     */
    public function getDeclarationByWrappingPermutation(
        ChopWrapDecisionEnum $wrapExtends,
        ChopWrapDecisionEnum $wrapImplements,
        ChopWrapDecisionEnum $wrapIndividualImplements,
        RenderContext        $context,
        RenderingRules       $rules,
    ): array
    {
        $scenario = [];
        $scenario[] = $this->getDeclarationTokens();
        $extendsTokens = $this->getExtendsTokens($context, $rules);
        $inlineImplementsTokens = $this->getInlineImplementsTokens($context, $rules);
        $chopDownImplementsTokens = $this->getChopDownImplementsTokens($context, $rules);
        if ($wrapExtends === ChopWrapDecisionEnum::NEVER_WRAP && !empty($extendsTokens)) {
            $scenario[] = new SpacesToken();
            $scenario[] = $extendsTokens;
        } elseif (!empty($extendsTokens)) {
            $scenario[] = new NewLineTokens();
            $rules->indent($context);
            $scenario[] = SpacesToken::fromString($context->indents);
            $scenario[] = $extendsTokens;
            $rules->unindent($context);
        }
        if ($wrapImplements === ChopWrapDecisionEnum::NEVER_WRAP && !empty($inlineImplementsTokens)) {
            $scenario[] = new SpacesToken();
            $scenario[] = $inlineImplementsTokens;
        } elseif (
            ($wrapImplements === ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP || $wrapImplements === ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG)
            && $wrapIndividualImplements === ChopWrapDecisionEnum::NEVER_WRAP
            && !empty($inlineImplementsTokens)
        ) {
            $scenario[] = new NewLineTokens();
            $rules->indent($context);
            $scenario[] = $this->insertIndentationTokens($context, $inlineImplementsTokens);
            $rules->unindent($context);
        } elseif (!empty($chopDownImplementsTokens)) {
            $scenario[] = new NewLineTokens();
            $rules->indent($context);
            $scenario[] = $this->insertIndentationTokens($context, $chopDownImplementsTokens);
            $rules->unindent($context);
        }
        return $this->flatten($scenario);
    }

    /**
     * @param Token[] $declarationTokens
     * @param Token[] $extendsTokens
     * @param Token[] $inlineImplementsTokens
     * @return Token[]
     */
    public function getInlineExtendsImplementsNextLineDeclaration(
        array $declarationTokens,
        array $extendsTokens,
        array $inlineImplementsTokens
    ): array
    {
        $scenario = [];
        $scenario[] = $declarationTokens;
        $scenario[] = new SpacesToken();
        $scenario[] = $extendsTokens;
        $scenario[] = new SpacesToken();
        $scenario[] = $inlineImplementsTokens;
        return $this->flatten($scenario);
    }

    /**
     * @param RenderContext $context
     * @param Token[] $tokens
     * @return Token[]
     */
    private function insertIndentationTokens(RenderContext $context, array $tokens): array
    {
        if (strlen($context->indents) === 0) {
            return $tokens;
        }
        $newTokens = [];
        $newTokens[] = SpacesToken::fromString($context->indents);
        foreach ($tokens as $token) {
            $newTokens[] = $token;
            if ($token instanceof NewLineTokens) {
                $newTokens[] = SpacesToken::fromString($context->indents);
            }
        }
        return $newTokens;
    }

    /**
     * @param RenderingRules $rules
     * @return array<array{extends: ChopWrapDecisionEnum, implements: ChopWrapDecisionEnum, individualImplements: ChopWrapDecisionEnum}>
     */
    private function getWrapRulePermutations(RenderingRules $rules): array
    {
        $permutations = [];

        $extendsChopWrapDecisions = [];
        if ($rules->classes->extendsOnNextLine === ChopWrapDecisionEnum::NEVER_WRAP) {
            $extendsChopWrapDecisions[] = ChopWrapDecisionEnum::NEVER_WRAP;
        } elseif ($rules->classes->extendsOnNextLine === ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG) {
            $extendsChopWrapDecisions[] = ChopWrapDecisionEnum::NEVER_WRAP;
            $extendsChopWrapDecisions[] = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        } elseif ($rules->classes->extendsOnNextLine === ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP) {
            $extendsChopWrapDecisions[] = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        }

        $implementsChopWrapDecisions = [];
        if ($rules->classes->implementsOnNextLine === ChopWrapDecisionEnum::NEVER_WRAP) {
            $implementsChopWrapDecisions[] = ChopWrapDecisionEnum::NEVER_WRAP;
        } elseif ($rules->classes->implementsOnNextLine === ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG) {
            $implementsChopWrapDecisions[] = ChopWrapDecisionEnum::NEVER_WRAP;
            $implementsChopWrapDecisions[] = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        } elseif ($rules->classes->implementsOnNextLine === ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP) {
            $implementsChopWrapDecisions[] = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        }

        $individualImplementsChopWrapDecisions = [];
        if (in_array(ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP, $implementsChopWrapDecisions)) {
            if ($rules->classes->implementsOnDifferentLines === ChopWrapDecisionEnum::NEVER_WRAP) {
                $individualImplementsChopWrapDecisions[ChopWrapDecisionEnum::NEVER_WRAP->name] = ChopWrapDecisionEnum::NEVER_WRAP;
            } elseif ($rules->classes->implementsOnDifferentLines === ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG) {
                $individualImplementsChopWrapDecisions[ChopWrapDecisionEnum::NEVER_WRAP->name] = ChopWrapDecisionEnum::NEVER_WRAP;
                $individualImplementsChopWrapDecisions[ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP->name] = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
            } elseif ($rules->classes->implementsOnDifferentLines === ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP) {
                $individualImplementsChopWrapDecisions[ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP->name] = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
            }
        }
        if (in_array(ChopWrapDecisionEnum::NEVER_WRAP, $implementsChopWrapDecisions)) {
            $individualImplementsChopWrapDecisions[ChopWrapDecisionEnum::NEVER_WRAP->name] = ChopWrapDecisionEnum::NEVER_WRAP;
        }

        foreach ($extendsChopWrapDecisions as $extendsChopWrapDecision) {
            foreach ($implementsChopWrapDecisions as $implementsChopWrapDecision) {
                foreach ($individualImplementsChopWrapDecisions as $individualImplementsChopWrapDecision) {
                    $permutations[] = [
                        'extends' => $extendsChopWrapDecision,
                        'implements' => $implementsChopWrapDecision,
                        'individualImplements' => $individualImplementsChopWrapDecision,
                    ];
                }
            }
        }

        return $permutations;
    }
}