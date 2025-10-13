# Memoization

If you find yourself creating the same value objects multiple times (e.g. related models for an Eloquent collection), you can use the `Bag::memoize()` or `Bag::memoizeUsing()` methods to cache the created value objects in memory.

> [!NOTE]
> Memoization is only done in memory, and will not be persisted e.g. across requests.

## Using `Bag::memoize()`

To use `Bag::memoize()` you must first add the `Bag\Attributes\MemoizeUsing` attribute to your value class:

```php
use Bag\Attributes\MemoizeUsing;
use Bag\Bag;

#[MemoizeUsing('id')]
readonly class MyValue extends Bag
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}
}
```

You may specify one or more properties (as an array) to use as the cache key.

Once you have added the attribute, you can use the `Bag::memoize()` method to create or retrieve a cached instance of the value object:

```php
$value1 = MyValue::memoize(id: 1, name: 'Davey Shafik');
$value2 = MyValue::memoize(id: 1, name: 'Not Davey Shafik');

$value1 === $value2; // true
```

In the above example, the second call to `MyValue::memoize()` returns the same instance as the first call, because the `id` property is used as the cache key.

## Using `Bag::memoizeUsing()`

If you need more control over the cache key, you can use the `Bag::memoizeUsing()` method instead. This method allows you to specify the properties to use as the cache key at the time of calling the method.

This will override the `#[MemoizeUsing]` attribute if it is present.

```php
$value1 = MyValue::memoizeUsing('id', id: 1, name: 'Davey Shafik');
$value2 = MyValue::memoizeUsing(['id'], id: 1, name: 'Not Davey Shafik');

$value1 === $value2; // true
```
