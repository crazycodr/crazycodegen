<?php

namespace CrazyCodeGen\Formatting\Enums;

enum VerticalLayoutStrategyEnum: string
{
    /**
     * Defines that a component should never drop down when rendering itself.
     */
    case Refuse = 'refuse';
    /**
     * Defines that a component should avoid dropping down when rendering itself unless it doesn't fit inline.
     *
     * This is the default.
     */
    case Avoid = 'avoid';
    /**
     * This is a special status that states that this component and all underlying components should favor
     * dropping down when rendering but should still avoid it. When this status is set in a context, it signals
     * that a downstream component that requires to render itself in a drop-down format should signal the parent
     * that it did render itself in a drop-down format and all rendering starting from that parent should restart
     * with FORCE strategy instead.
     */
    case Favors = 'favors';
    /**
     * Defines that a component should always render itself in a drop-down format.
     */
    case Force = 'force';
}
