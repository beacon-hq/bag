<?php

declare(strict_types=1);

namespace Tests\Fixtures\Values;

use Bag\Attributes\MemoizeUsing;
use Bag\Bag;

#[MemoizeUsing('email')]
readonly class MemoizedBag extends Bag
{
    public function __construct(
        public string $name,
        public int $age,
        public string $email
    ) {
    }
}
