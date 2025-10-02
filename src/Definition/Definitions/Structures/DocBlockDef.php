<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class DocBlockDef extends Tokenizes
{
    use FlattenFunction;
    use TokenFunctions;

    public function __construct(
        /** @var string[] $texts */
        public array $texts,
    ) {
    }

    /**
     * @return Token[]
     */
    public function getSimpleTokens(TokenizationContext $context): array
    {
        $tokens = [];
        if (empty($this->texts)) {
            return $tokens;
        }
        $tokens[] = new Token('/**');
        $tokens[] = new NewLinesToken();
        $textsLeft = count($this->texts);
        foreach ($this->texts as $text) {
            $textsLeft--;
            if (strlen($text) === 0) {
                continue;
            }
            do {
                if (strlen($text) > 80) {
                    $textSampleForCutoff = substr($text, 0, 80 + 1);
                    $nextSpaceToCutAt = strrpos($textSampleForCutoff, ' ');
                    if ($nextSpaceToCutAt === false) {
                        $nextSpaceToCutAt = strpos($text, ' ', 80);
                        if ($nextSpaceToCutAt === false) {
                            $nextSpaceToCutAt = null;
                        }
                    }
                    $extractedText = trim(substr($text, 0, $nextSpaceToCutAt));
                    $text = trim(substr($text, strlen($extractedText)));
                    $tokens[] = new Token(' * ');
                    $tokens[] = new Token($extractedText);
                    $tokens[] = new NewLinesToken();
                } else {
                    $tokens[] = new Token(' * ');
                    $tokens[] = new Token($text);
                    $tokens[] = new NewLinesToken();
                    $text = '';
                }
            } while (!empty($text));
            if ($textsLeft > 0) {
                $tokens[] = new Token(' *');
                $tokens[] = new NewLinesToken();
            }
        }
        $tokens[] = new Token(' */');
        $tokens[] = new NewLinesToken();
        return $this->flatten($tokens);
    }
}
