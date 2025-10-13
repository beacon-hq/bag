<?php

declare(strict_types=1);

namespace Bag\Concerns;

use Bag\Pipelines\MemoizePipeline;
use Bag\Pipelines\Values\BagInput;
use Illuminate\Support\Collection;

trait WithMemoization
{
    public static function memoize(mixed ...$values): static
    {
        $input = new BagInput(static::class, collect($values));

        return MemoizePipeline::process($input);
    }

    /**
     * @param string|array<string>|Collection<array-key, string> $cacheKeyAttributeNames
     */
    public static function memoizeUsing(string|array|Collection $cacheKeyAttributeNames, mixed ...$values): static
    {
        $input = new BagInput(static::class, collect($values));

        return MemoizePipeline::process($input, $cacheKeyAttributeNames);
    }
}
