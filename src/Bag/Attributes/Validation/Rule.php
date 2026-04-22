<?php

declare(strict_types=1);

namespace Bag\Attributes\Validation;

use Attribute;
use Bag\Attributes\Attribute as AttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Rule implements AttributeInterface
{
    public string|object $rule;

    public function __construct(
        string $rule,
        mixed ...$arguments,
    ) {
        if (\str_contains($rule, '\\')) {
            $this->rule = new $rule(...$arguments);

            return;
        }

        if (\count($arguments) === 0) {
            $this->rule = $rule;

            return;
        }

        /** @var array<string> $arguments */
        $this->rule = $rule . ':'.\implode(',', $arguments);
    }
}
