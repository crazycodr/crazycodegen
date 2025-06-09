<?php

namespace CrazyCodeGen\Definition\Expressions\Structures;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Models\ConversionRule;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Common\Traits\ValidationTrait;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ElseToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

/**
 * Use this to link multiple condition blocks together in an automatically formated way.
 * If you pass multiple Condition, it will generate the necessary if/elseif/else structure
 * based on the content of the Condition object it finds. If the Condition object does not feature
 * a condition, it assumes that it should be a simple else block.
 *
 * Note that this will not generate an exception if there are invalid Condition objects in a chain
 * generating incorrect if/elseif/else blocks. It is your responsibility to generate proper Condition
 * objects.
 */
class ConditionChain extends Tokenizes
{
    use FlattenFunction;
    use TokenFunctions;
    use ValidationTrait;

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function __construct(
        /** @var Condition[] $chain */
        public array $chain = [],
    ) {
        $this->chain = $this->convertOrThrowForEachValues($this->chain, [
            new ConversionRule(inputType: Condition::class),
        ]);
    }

    /**
     * @return Token[]
     */
    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        $conditionsLeft = count($this->chain);
        foreach ($this->chain as $condition) {
            $conditionsLeft--;
            $tokens[] = $condition->getTokens($context, $rules);
            if ($conditionsLeft > 0) {
                if ($rules->conditions->closingBrace === BracePositionEnum::DIFF_LINE) {
                    $tokens[] = new NewLinesToken();
                } else {
                    $tokens[] = new SpacesToken($rules->conditions->spacesAfterClosingBrace);
                }
                $tokens[] = new ElseToken();
            }
        }
        return $this->flatten($tokens);
    }
}
