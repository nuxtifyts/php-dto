<?php

namespace Nuxtifyts\PhpDto\Support;

/**
 * @template TKey of array-key
 * @template TValue of mixed
 */
class Collection
{
    /** @var array<TKey, TValue> */
    protected array $items = [];

    /**
     * @param array<TKey, TValue> $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * @param TValue $item
     *
     * @return self<TKey, TValue>
     */
    public function push(mixed $item): self
    {
        $this->items[] = $item;
        return $this;
    }

    /**
     * @param TKey $key
     * @param TValue $value
     *
     * @return self<TKey, TValue>
     */
    public function put(mixed $key, mixed $value): self
    {
        $this->items[$key] = $value;
        return $this;
    }

    /**
     * @param ?callable(TValue $item): bool $callable
     *
     * @return ?TValue
     */
    public function first(?callable $callable = null): mixed
    {
        return is_null($callable)
            ? reset($this->items) ?: null
            : array_find($this->items, $callable);
    }

    public function isNotEmpty(): bool
    {
        return !empty($this->items);
    }

    public function isEmpty(): bool
    {
        return !$this->isNotEmpty();
    }

    /**
     * @param callable(TValue $item): bool $callable
     */
    public function every(callable $callable): bool
    {
        return array_all($this->items, $callable);
    }

    /**
     * @param callable(TValue $item): bool $callable
     */
    public function some(callable $callable): bool
    {
        return array_any($this->items, $callable);
    }
}
