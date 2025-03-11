<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\RendersChopDownVersion;
use CrazyCodeGen\Rendering\Renderers\RendersInlineVersion;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\CommaToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\NewToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class NewInstanceTokenGroup extends TokenGroup implements RendersInlineVersion, RendersChopDownVersion
{
    use FlattenFunction;
    use TokenFunctions;

    public function __construct(
        public string|Token|TokenGroup|ClassTokenGroup $class,
        /** @var Token[]|TokenGroup[]|Token|TokenGroup $arguments */
        public string|array|Token|TokenGroup             $arguments = [],
    ) {
        if (is_string($this->class)) {
            $this->class = new Token($this->class);
        } elseif ($this->class instanceof ClassTokenGroup) {
            $this->class = new SingleTypeTokenGroup($this->class->namespace->path . '\\' . $this->class->name);
        }
        if (is_string($this->arguments)) {
            $this->arguments = new Token($this->arguments);
        }
        if (!is_array($this->arguments)) {
            $this->arguments = [$this->arguments];
        }
    }

    /**
     * @return Token[]
     */
    public function renderChopDownScenario(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        $tokens[] = new NewToken();
        $tokens[] = new SpacesToken();
        if ($this->class instanceof TokenGroup) {
            $tokens[] = $this->class->render($context, $rules);
        } else {
            $tokens[] = $this->class;
        }
        $tokens[] = new ParStartToken();
        if (!empty($this->arguments)) {
            $tokens[] = new NewLinesToken();
        }
        $argumentsLeft = count($this->arguments);
        $argumentTokens = [];
        foreach ($this->arguments as $argument) {
            $argumentsLeft--;
            if ($argument instanceof RendersChopDownVersion) {
                $argumentTokens[] = $argument->renderChopDownScenario($context, $rules);
            } elseif ($argument instanceof Token) {
                $argumentTokens[] = $argument;
            } else {
                $argumentTokens[] = $argument->render($context, $rules);
            }
            $argumentTokens[] = new CommaToken();
            if ($argumentsLeft > 0) {
                $argumentTokens[] = new NewLinesToken();
            }
        }
        if (!empty($this->arguments)) {
            $tokens[] = $this->insertIndentationTokens($rules, $argumentTokens);
            $tokens[] = new NewLinesToken();
        }
        $tokens[] = new ParEndToken();
        return $this->flatten($tokens);
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function render(RenderContext $context, RenderingRules $rules): array
    {
        return $this->renderInlineScenario($context, $rules);
    }

    /**
     * @return Token[]
     */
    public function renderInlineScenario(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        $tokens[] = new NewToken();
        $tokens[] = new SpacesToken();
        if ($this->class instanceof TokenGroup) {
            $tokens[] = $this->class->render($context, $rules);
        } else {
            $tokens[] = $this->class;
        }
        $tokens[] = new ParStartToken();
        $argumentsLeft = count($this->arguments);
        $argumentTokens = [];
        foreach ($this->arguments as $argument) {
            $argumentsLeft--;
            if ($argument instanceof RendersInlineVersion) {
                $argumentTokens[] = $argument->renderInlineScenario($context, $rules);
            } elseif ($argument instanceof Token) {
                $argumentTokens[] = $argument;
            } else {
                $argumentTokens[] = $argument->render($context, $rules);
            }
            if ($argumentsLeft > 0) {
                $argumentTokens[] = new CommaToken();
                $argumentTokens[] = new SpacesToken();
            }
        }
        $tokens[] = $argumentTokens;
        $tokens[] = new ParEndToken();
        return $this->flatten($tokens);
    }
}
