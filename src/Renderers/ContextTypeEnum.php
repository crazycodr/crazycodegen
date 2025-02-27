<?php

namespace CrazyCodeGen\Renderers;

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
    case namespaceName;
    case instruction;
    case blockBody;
}