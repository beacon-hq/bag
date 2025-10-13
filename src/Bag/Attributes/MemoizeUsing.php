<?php

declare(strict_types=1);

namespace Bag\Attributes;

use Attribute;
use Bag\Attributes\Attribute as AttributeInterface;
use Illuminate\Support\Collection;

#[Attribute(Attribute::TARGET_CLASS)]
class MemoizeUsing implements AttributeInterface
{
    /**
     * @param string|array<string>|Collection<array-key, string> $cacheKeyAttributeNames
     */
    public function __construct(public string|array|Collection $cacheKeyAttributeNames)
    {
    }
}
