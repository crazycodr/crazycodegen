<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Exceptions\InvalidIdentifierFormatException;
use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Common\Traits\ValidationTrait;
use CrazyCodeGen\Definition\Base\ProvidesCallableReference;
use CrazyCodeGen\Definition\Base\ProvidesClassReference;
use CrazyCodeGen\Definition\Base\ProvidesClassType;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Definitions\Traits\HasAbstractModifierTrait;
use CrazyCodeGen\Definition\Definitions\Traits\HasConstantsTrait;
use CrazyCodeGen\Definition\Definitions\Traits\HasDocBlockTrait;
use CrazyCodeGen\Definition\Definitions\Traits\HasExtendsTrait;
use CrazyCodeGen\Definition\Definitions\Traits\HasImplementationsTrait;
use CrazyCodeGen\Definition\Definitions\Traits\HasImportsTrait;
use CrazyCodeGen\Definition\Definitions\Traits\HasMethodsTrait;
use CrazyCodeGen\Definition\Definitions\Traits\HasNamespaceTrait;
use CrazyCodeGen\Definition\Definitions\Traits\HasNameTrait;
use CrazyCodeGen\Definition\Definitions\Traits\HasPropertiesTrait;
use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;
use CrazyCodeGen\Definition\Definitions\Values\ClassRefVal;
use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\AbstractToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ClassToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ExtendsToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ClassDef extends Tokenizes implements ProvidesClassType, ProvidesClassReference, ProvidesCallableReference
{
    use FlattenFunction;
    use TokenFunctions;
    use ValidationTrait;
    use HasNamespaceTrait;
    use HasImportsTrait;
    use HasDocBlockTrait;
    use HasAbstractModifierTrait;
    use HasNameTrait;
    use HasExtendsTrait;
    use HasImplementationsTrait;
    use HasConstantsTrait;
    use HasPropertiesTrait;
    use HasMethodsTrait;

    /**
     * @throws InvalidIdentifierFormatException
     * @throws NoValidConversionRulesMatchedException
     */
    public function __construct(
        string                        $name,
        null|string|NamespaceDef      $namespace = null,
        /** @var string[]|ClassTypeDef[]|ImportDef[] $imports */
        array                         $imports = [],
        null|string|array|DocBlockDef $docBlock = null,
        bool                          $abstract = false,
        null|string|ClassTypeDef      $extends = null,
        /** @var string[]|ClassTypeDef[] $implementations */
        array                         $implementations = [],
        /** @var string[]|ConstantDef[] $constants */
        array                         $constants = [],
        /** @var string[]|PropertyDef[] $properties */
        array                         $properties = [],
        /** @var MethodDef[] $methods */
        array                         $methods = [],
    ) {
        $this->setNamespace($namespace);
        $this->setImports($imports);
        $this->setDocBlock($docBlock);
        $this->setAbstract($abstract);
        $this->setName($name);
        $this->setExtends($extends);
        $this->setImplementations($implementations);
        $this->setConstants($constants);
        $this->setProperties($properties);
        $this->setMethods($methods);
    }

    /**
     * @return Token[]
     */
    private function getSimpleExtendsTokens(TokenizationContext $context): array
    {
        $tokens = [];
        if ($this->extends) {
            $tokens[] = new SpacesToken();
            $tokens[] = new ExtendsToken();
            $tokens[] = new SpacesToken();
            $tokens[] = $this->extends->getSimpleTokens($context);
        }
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    public function getSimpleTokens(TokenizationContext $context): array
    {
        $tokens = [];
        if ($this->namespace) {
            $tokens[] = $this->namespace->getSimpleTokens($context);
        }

        $previousImportedClasses = $context->importedClasses;
        foreach ($this->imports as $import) {
            $tokens[] = $import->getSimpleTokens($context);
            if (!$import->alias) {
                $context->importedClasses[] = $import->type->type;
            }
        }

        if ($this->docBlock) {
            $tokens[] = $this->docBlock->getSimpleTokens($context);
        }

        $tokens[] = $this->getSimpleDeclarationTokens();
        $tokens[] = $this->getSimpleExtendsTokens($context);
        $tokens[] = $this->addSpaceIfNotEmpty($this->getSimpleImplementsTokens($context));

        $tokens[] = new BraceStartToken();

        foreach ($this->constants as $constant) {
            $tokens[] = $constant->getSimpleTokens($context);
        }
        foreach ($this->properties as $property) {
            $tokens[] = $property->getSimpleTokens($context);
        }
        foreach ($this->methods as $method) {
            $tokens[] = $method->getSimpleTokens($context);
        }
        $tokens[] = new BraceEndToken();

        $context->importedClasses = $previousImportedClasses;

        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    private function getSimpleDeclarationTokens(): array
    {
        $tokens = [];
        if ($this->abstract) {
            $tokens[] = new AbstractToken();
            $tokens[] = new SpacesToken();
        }
        $tokens[] = new ClassToken();
        $tokens[] = new SpacesToken();
        $tokens[] = new Token($this->name);
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    private function getSimpleImplementsTokens(TokenizationContext $context): array
    {
        $tokens = [];
        if (empty($this->implementations)) {
            return $tokens;
        }
        $implementsTokenGroup = new ImplementationsDef($this->implementations);
        return $implementsTokenGroup->getSimpleTokens($context);
    }

    public function getClassReference(): ClassRefVal
    {
        return new ClassRefVal($this->getClassType());
    }

    public function getClassType(): ClassTypeDef
    {
        if ($this->namespace) {
            return new ClassTypeDef($this->namespace->path . '\\' . $this->name);
        }
        return new ClassTypeDef($this->name);
    }

    public function getCallableReference(): Tokenizes
    {
        return $this->getClassType()->getCallableReference();
    }

    /**
     * @param Token[] $tokensToAdd
     * @return Token[]
     */
    private function addSpaceIfNotEmpty(array $tokensToAdd): array
    {
        if (!empty($tokensToAdd)) {
            return $this->flatten([new SpacesToken(), $tokensToAdd]);
        }
        return $tokensToAdd;
    }
}
