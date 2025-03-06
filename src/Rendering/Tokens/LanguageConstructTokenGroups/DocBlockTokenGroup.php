<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLineTokens;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class DocBlockTokenGroup extends TokenGroup
{
    use FlattenFunction;
    use TokenFunctions;

    public function __construct(
        /** @var string[] $texts */
        public readonly array $texts,
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
        if (empty($this->texts)) {
            return $tokens;
        }
        $tokens[] = new Token('/**');
        $tokens[] = new NewLineTokens();
        $textsLeft = count($this->texts);
        foreach ($this->texts as $text) {
            $textsLeft--;
            if (strlen($text) === 0) {
                continue;
            }
            do {
                if (strlen($text) > $rules->docBlocks->lineLength) {
                    $textSampleForCutoff = substr($text, 0, $rules->docBlocks->lineLength + 1);
                    $nextSpaceToCutAt = strrpos($textSampleForCutoff, ' ');
                    $extractedText = trim(substr($text, 0, $nextSpaceToCutAt));
                    $text = trim(substr($text, $nextSpaceToCutAt));
                    $tokens[] = new Token(' * ');
                    $tokens[] = new Token($extractedText);
                    $tokens[] = new NewLineTokens();
                } else {
                    $tokens[] = new Token(' * ');
                    $tokens[] = new Token($text);
                    $tokens[] = new NewLineTokens();
                    $text = '';
                }
            } while (!empty($text));
            if ($textsLeft > 0) {
                $tokens[] = new Token(' *');
                $tokens[] = new NewLineTokens();
            }
        }
        $tokens[] = new Token(' */');
        return $this->flatten($tokens);
    }
}