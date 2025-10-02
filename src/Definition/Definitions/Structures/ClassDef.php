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
use CrazyCodeGen\Rendering\RenderingContext;
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
     * @param string[]|ClassTypeDef[]|ImportDef[] $imports
     * @param null|string|string[]|DocBlockDef $docBlock
     * @param string[]|ClassTypeDef[] $implementations
     * @param string[]|ConstantDef[] $constants
     * @param string[]|PropertyDef[] $properties
     * @param MethodDef[] $methods
     * @throws NoValidConversionRulesMatchedException
     * @throws InvalidIdentifierFormatException
     */
    public function __construct(
        string                        $name,
        null|NamespaceDef             $namespace = null,
        array                         $imports = [],
        null|string|array|DocBlockDef $docBlock = null,
        bool                          $abstract = false,
        null|string|ClassTypeDef      $extends = null,
        array                         $implementations = [],
        array                         $constants = [],
        array                         $properties = [],
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
    private function getExtendsTokens(RenderingContext $context): array
    {
        $tokens = [];
        if ($this->extends) {
            $tokens[] = new SpacesToken();
            $tokens[] = new ExtendsToken();
            $tokens[] = new SpacesToken();
            $tokens[] = $this->extends->getTokens($context);
        }
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    public function getTokens(RenderingContext $context): array
    {
        $tokens = [];
        if ($this->namespace) {
            $tokens[] = $this->namespace->getTokens($context);
        }

        $previousImportedClasses = $context->importedClasses;
        foreach ($this->imports as $import) {
            $tokens[] = $import->getTokens($context);
            if (!$import->alias) {
                $context->importedClasses[] = $import->type->type;
            }
        }

        if ($this->docBlock) {
            $tokens[] = $this->docBlock->getTokens($context);
        }

        $tokens[] = $this->getDeclarationTokens();
        $tokens[] = $this->getExtendsTokens($context);
        $tokens[] = $this->addSpaceIfNotEmpty($this->getImplementsTokens($context));

        $tokens[] = new BraceStartToken();

        foreach ($this->constants as $constant) {
            $tokens[] = $constant->getTokens($context);
        }
        foreach ($this->properties as $property) {
            $tokens[] = $property->getTokens($context);
        }
        foreach ($this->methods as $method) {
            $tokens[] = $method->getTokens($context);
        }
        $tokens[] = new BraceEndToken();

        $context->importedClasses = $previousImportedClasses;

        return $this->flatten($tokens);
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
        $tokens[] = new Token($this->name);
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    private function getImplementsTokens(RenderingContext $context): array
    {
        $tokens = [];
        if (empty($this->implementations)) {
            return $tokens;
        }
        $implementsTokenGroup = new ImplementationsDef($this->implementations);
        return $implementsTokenGroup->getTokens($context);
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
