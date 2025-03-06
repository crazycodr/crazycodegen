<?php

namespace CrazyCodeGen\Rendering\Renderers\Enums;

enum ContextTypeEnum
{
    case none;
    case function;
    case argumentList;
    case returnType;
    case block;
    case argument;
    case type;
    case namespace;
    case namespaceIdentifier;
    case instruction;
    case blockBody;
    case classDeclaration;
    case visibility;
    case extends;
    case implements;
    case identifier;
    case functionDefinition;
    case functionDeclaration;
    case multiType;
    case string;
}