<?php

namespace CrazyCodeGen\Tests\Common\Formatters;

use PhpCsFixer\Config;
use PhpCsFixer\Console\ConfigurationResolver;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\ToolInfo;
use SplFileInfo;

class PhpCsFixerFormatter
{
    public function format(string $code): string
    {
        $tokens = Tokens::fromCode('<?php ' . $code);
        $config = (new Config())
            ->setRules([
                '@PSR12' => true,
                'align_multiline_comment' => true,
                'no_unused_imports' => true,

                // Controls how arrays are formatted
                'array_syntax' => ['syntax' => 'short'],
                'array_indentation' => true,
                'trim_array_spaces' => true,
                'whitespace_after_comma_in_array' => true,

                // Optional: put multiline arrays on multiple lines
                'multiline_whitespace_before_semicolons' => true,

                // One blank line between class-level elements
                'class_attributes_separation' => [
                    'elements' => [
                        'method' => 'one',
                        'const'  => 'none',
                        'property' => 'none',
                        'trait_import' => 'none',
                    ],
                ],

                // Force blank line before the class-level docblock
                'blank_line_before_statement' => [
                    'statements' => [
                        'phpdoc',
                    ],
                ],
            ]);
        $fixers = (new ConfigurationResolver(
            $config,
            [], // no CLI args
            (string)getcwd(),
            new ToolInfo()
        ))->getFixers();

        foreach ($fixers as $fixer) {
            if ($fixer->isCandidate($tokens) && $fixer->supports(new SplFileInfo('dummy.php'))) {
                $fixer->fix(new SplFileInfo('dummy.php'), $tokens);
            }
        }

        return $tokens->generateCode();
    }
}
