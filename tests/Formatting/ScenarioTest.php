<?php

namespace CrazyCodeGen\Tests\Formatting;

use CrazyCodeGen\Common\Enums\VisibilityEnum;
use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Definition\Definitions\Structures\ConstantDef;
use CrazyCodeGen\Definition\Definitions\Types\BuiltInTypeSpec;
use CrazyCodeGen\Definition\Definitions\Values\ArrayVal;
use CrazyCodeGen\Formatting\Exceptions\NoSupportingFormatterFoundException;
use CrazyCodeGen\Formatting\Exceptions\NoSupportingPrinterFoundException;
use CrazyCodeGen\Formatting\FormattingContext;
use CrazyCodeGen\Formatting\PrintingContext;
use CrazyCodeGen\Formatting\Rules\ExpressionRules;
use CrazyCodeGen\Formatting\Rules\ExpressionRules\ValueRules;
use CrazyCodeGen\Formatting\Rules\ExpressionRules\ValueRules\ArrayMultilineStrategyEnum;
use CrazyCodeGen\Formatting\Rules\ExpressionRules\ValueRules\ArrayRules;
use CrazyCodeGen\Formatting\Rules\ExpressionRules\ValueRules\ArraySyntaxEnum;
use CrazyCodeGen\Formatting\Rules\ExpressionRules\ValueRules\ArrayTrailingCommaEnum;
use CrazyCodeGen\Formatting\Rules\Rules;
use CrazyCodeGen\Tests\Formatting\Traits\SharedDocumentFormatterSetupTrait;
use CrazyCodeGen\Tests\Formatting\Traits\SharedDocumentPrinterSetupTrait;
use PHPUnit\Framework\TestCase;

class ScenarioTest extends TestCase
{
    use SharedDocumentPrinterSetupTrait;
    use SharedDocumentFormatterSetupTrait;

    /**
     * @throws NoSupportingPrinterFoundException
     * @throws NoValidConversionRulesMatchedException
     * @throws NoSupportingFormatterFoundException
     */
    public function testNestedArrayValIsRenderedProperlyWithIndentationAndFavoredDropdown()
    {
        $value = new ArrayVal([
            'foo' => new ArrayVal([
                'bar' => 'baz',
                'hello' => 'world',
            ]),
            'programming' => 'rules',
        ]);

        $rules = new Rules(
            expressions: new ExpressionRules(
                values: new ValueRules(
                    arrays: new ArrayRules(
                        trailingComma: ArrayTrailingCommaEnum::MultilineOnly,
                        alignKeyValues: true,
                    ),
                ),
            ),
        );
        $formatter = $this->getDocumentFormatterWithAllFormatters($rules);
        $formattingContext = new FormattingContext();
        $printer = $this->getDocumentPrinterWithAllPrinters();
        $printingContext = new PrintingContext(inlineSpaceAvailable: 30, newLineSpaceAvailable: 30);

        $arrayValToDocument = $formatter->format($formattingContext, $value);
        $printedDocument = $printer->printDocument($printingContext, $arrayValToDocument);

        $this->assertEquals(<<<EOT
            [
                'foo'         => [
                    'bar'   => 'baz',
                    'hello' => 'world',
                ],
                'programming' => 'rules',
            ]
            EOT,
            $printedDocument
        );
    }

    /**
     * @throws NoSupportingPrinterFoundException
     * @throws NoValidConversionRulesMatchedException
     * @throws NoSupportingFormatterFoundException
     */
    public function testArrayWithLongSyntaxAndAlwaysMultiline()
    {
        $value = new ArrayVal([
            'a' => 'b',
        ]);

        $rules = new Rules(
            expressions: new ExpressionRules(
                values: new ValueRules(
                    arrays: new ArrayRules(
                        syntax: ArraySyntaxEnum::Long,
                        multilineStrategy: ArrayMultilineStrategyEnum::AlwaysMultiline,
                        trailingComma: ArrayTrailingCommaEnum::Always
                    ),
                ),
            ),
        );
        $formatter = $this->getDocumentFormatterWithAllFormatters($rules);
        $formattingContext = new FormattingContext();
        $printer = $this->getDocumentPrinterWithAllPrinters();
        $printingContext = new PrintingContext(inlineSpaceAvailable: 60, newLineSpaceAvailable: 60);

        $formattedComponent = $formatter->format($formattingContext, $value);
        $printedComponent = $printer->printDocument($printingContext, $formattedComponent);
        $this->assertEquals(<<<EOT
            array(
                'a' => 'b',
            )
            EOT,
            $printedComponent
        );
    }

    /**
     * @throws NoSupportingPrinterFoundException
     * @throws NoValidConversionRulesMatchedException
     * @throws NoSupportingFormatterFoundException
     */
    public function testConstantDefinitionReturnsExpectedResult()
    {
        $constant = new ConstantDef(
            name: 'foo',
            type: BuiltInTypeSpec::stringType(),
            visibility: VisibilityEnum::PUBLIC,
            value: 'bar'
        );

        $rules = new Rules();
        $formatter = $this->getDocumentFormatterWithAllFormatters($rules);
        $formattingContext = new FormattingContext();
        $printer = $this->getDocumentPrinterWithAllPrinters();
        $printingContext = new PrintingContext(inlineSpaceAvailable: 60, newLineSpaceAvailable: 60);

        $formattedComponent = $formatter->format($formattingContext, $constant);
        $printedComponent = $printer->printDocument($printingContext, $formattedComponent);
        $this->assertEquals(<<<EOT
            public const string foo = 'bar';
            EOT,
            $printedComponent
        );
    }
}
