<?php

declare(strict_types=1);

namespace Bag\Pipelines;

use Bag\Attributes\MemoizeUsing;
use Bag\Bag;
use Bag\Internal\Reflection;
use Bag\Pipelines\Pipes\CastInputValues;
use Bag\Pipelines\Pipes\ComputedValues;
use Bag\Pipelines\Pipes\DebugCollection;
use Bag\Pipelines\Pipes\ExtraParameters;
use Bag\Pipelines\Pipes\FillBag;
use Bag\Pipelines\Pipes\FillNulls;
use Bag\Pipelines\Pipes\FillOptionals;
use Bag\Pipelines\Pipes\IsVariadic;
use Bag\Pipelines\Pipes\LaravelRouteParameters;
use Bag\Pipelines\Pipes\MapInput;
use Bag\Pipelines\Pipes\MissingProperties;
use Bag\Pipelines\Pipes\ProcessArguments;
use Bag\Pipelines\Pipes\ProcessParameters;
use Bag\Pipelines\Pipes\StripExtraParameters;
use Bag\Pipelines\Pipes\Transform;
use Bag\Pipelines\Pipes\Validate;
use Bag\Pipelines\Values\BagInput;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use League\Pipeline\Pipeline;

class MemoizePipeline
{
    /**
     * @template T of Bag
     * @param BagInput<T> $input
     * @param string|array<string>|Collection<array-key, string>|null $cacheKeyAttributeNames
     * @return T
     */
    public static function process(BagInput $input, string|array|Collection|null $cacheKeyAttributeNames = null): Bag
    {
        if ($cacheKeyAttributeNames === null) {
            $attribute = Reflection::getAttribute(Reflection::getClass($input->bagClassname), MemoizeUsing::class);
            if ($attribute !== null) {
                $cacheKeyAttributeNames = Reflection::getAttributeInstance($attribute)?->cacheKeyAttributeNames;
                if ($cacheKeyAttributeNames !== null) {
                    $cacheKeyAttributeNames = Collection::wrap($cacheKeyAttributeNames);
                }
            }
        }

        if ($cacheKeyAttributeNames === null) {
            return static::runPipeline($input);
        }

        $cacheKeyAttributeNames = Collection::wrap($cacheKeyAttributeNames);
        if ($input->input->count() === 1 && isset($input->input[0]) && is_array($input->input[0])) {
            $cacheKey = Collection::wrap($input->input[0])->filter(fn ($value, $key) => $cacheKeyAttributeNames->contains($key))->values()->implode(':');
        } else {
            $cacheKey = $input->input->filter(fn ($value, $key) => $cacheKeyAttributeNames->contains($key))->values()->implode(':');
        }

        return Cache::driver('array')->rememberForever($input->bagClassname . ':' . $cacheKey, function () use ($input) {
            return static::runPipeline($input);
        });
    }

    /**
     * @template T of Bag
     * @param BagInput<T> $input
     * @return T
     */
    protected static function runPipeline(BagInput $input): Bag
    {
        $pipeline = new Pipeline(
            null,
            new Transform(),
            new ProcessParameters(),
            new ProcessArguments(),
            new IsVariadic(),
            new MapInput(),
            new LaravelRouteParameters(),
            new FillOptionals(),
            new FillNulls(),
            new MissingProperties(),
            new ExtraParameters(),
            new StripExtraParameters(),
            new Validate(),
            new CastInputValues(),
            new FillBag(),
            new ComputedValues(),
            new DebugCollection(),
        );

        // @phpstan-ignore-next-line property.nonObject
        return $pipeline->process($input)->bag;
    }
}
