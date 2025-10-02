<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\CommaToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ImplementsToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ImplementationsDef extends Tokenizes
{
    use FlattenFunction;
    use TokenFunctions;

    /**
     * @var ClassTypeDef[]
     */
    public array $implementations;

    /**
     * @param string[]|ClassTypeDef[] $implementations
     */
    public function __construct(array $implementations)
    {
        foreach ($implementations as $implementationIndex => $implementation) {
            if (is_string($implementation)) {
                $this->implementations[$implementationIndex] = new ClassTypeDef($implementation);
            } elseif ($implementation instanceof ClassTypeDef) {
                $this->implementations[$implementationIndex] = $implementation;
            }
        }
    }

    /**
     * @return Token[]
     */
    public function getTokens(RenderingContext $context): array
    {
        $tokens = [];
        if (!empty($this->implementations)) {
            $tokens[] = new ImplementsToken();
            $tokens[] = new SpacesToken();
        }
        $implementsLeft = count($this->implementations);
        foreach ($this->implementations as $implement) {
            $implementsLeft--;
            $tokens[] = $implement->getTokens($context);
            if ($implementsLeft > 0) {
                $tokens[] = new CommaToken();
            }
        }
        return $this->flatten($tokens);
    }
}
