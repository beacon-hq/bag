<?php

declare(strict_types=1);

use Bag\Attributes\MemoizeUsing;
use Bag\Pipelines\MemoizePipeline;
use Bag\Pipelines\Values\BagInput;
use Tests\Fixtures\Values\MemoizedBag;
use Tests\Fixtures\Values\TestBag;

covers(BagInput::class, MemoizePipeline::class, MemoizeUsing::class);

test('it memoizes bags using cache key', function () {
    $input = new BagInput(TestBag::class, collect([
        'name' => 'Davey Shafik',
        'age' => 40,
        'email' => 'davey@php.net'
    ]));

    $bag = MemoizePipeline::process($input, 'email');

    $input = new BagInput(TestBag::class, collect([
        'name' => 'Not Davey Shafik',
        'age' => 40,
        'email' => 'davey@php.net'
    ]));

    $bag2 = MemoizePipeline::process($input, 'email');

    expect($bag)
        ->toBeInstanceOf(TestBag::class)
        ->toBe($bag2);
});

test('it memoizes bags using attribute', function () {
    $input = new BagInput(MemoizedBag::class, collect([
        'name' => 'Davey Shafik',
        'age' => 40,
        'email' => 'davey@php.net'
    ]));

    $bag = MemoizePipeline::process($input);

    $input = new BagInput(MemoizedBag::class, collect([
        'name' => 'Not Davey Shafik',
        'age' => 40,
        'email' => 'davey@php.net'
    ]));

    $bag2 = MemoizePipeline::process($input);

    expect($bag)
        ->toBeInstanceOf(MemoizedBag::class)
        ->toBe($bag2);
});

test('it does not memoize bags without attribute', function () {
    $input = new BagInput(TestBag::class, collect([
        'name' => 'Davey Shafik',
        'age' => 40,
        'email' => 'davey@php.net'
    ]));

    $bag = MemoizePipeline::process($input);

    $input = new BagInput(TestBag::class, collect([
        'name' => 'Not Davey Shafik',
        'age' => 40,
        'email' => 'davey@php.net'
    ]));

    $bag2 = MemoizePipeline::process($input);

    expect($bag)
        ->toBeInstanceOf(TestBag::class)
        ->not->toBe($bag2);
});
