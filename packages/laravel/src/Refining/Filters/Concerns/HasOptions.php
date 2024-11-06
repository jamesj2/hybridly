<?php

namespace Hybridly\Refining\Filters\Concerns;

use BackedEnum;
use Illuminate\Support\Collection;

trait HasOptions
{
    protected \Closure|Collection|string|array|null $options = null;

    public function options(\Closure|Collection|string|array|null $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions(): array
    {
        if (\is_string($this->options) && is_a($this->options, BackedEnum::class, allow_string: true)) {
            return array_map(fn (\BackedEnum $enum) => $enum->value, $this->options::cases());
        }

        return $this->getOptionsArray()?->pluck('value', 'label')->toArray() ?? [];
    }

    /**
     * Builds a collection of arrays with labels & values for the select filter in tables.
     *
     * @return Collection|null
     */
    public function getOptionsArray(): Collection|null
    {
        $options = $this->evaluate($this->options);

        if ($options === null) {
            return null;
        }

        if (\is_string($options) && is_a($options, BackedEnum::class, allow_string: true)) {
            return collect($options::cases())->mapWithKeys(function (BackedEnum $case, int $key) {
                return [$key => ['value' => $case->value, 'label' => $case->name]];
            });
        }

        // When options is not a collection, make it a collection
        if (!$options instanceof Collection) {
            $options = collect($options);
        }

        // When options already has a two-dimensional array with value & label keys, just return it
        $first = $options->first();
        if (!\is_string($first) && \array_key_exists('value', $first) && \array_key_exists('label', $first)) {
            return $options;
        }

        // When options is a single-dimensional array with numeric keys, map it to a two-dimensional array using the value
        $filtered = $options->filter(fn ($value, $key) => is_numeric($key));
        if ($filtered->count() === $options->count()) {
            return $options->mapWithKeys(function (string|int $item, int|string $key) {
                return [$key => ['value' => $item, 'label' => $item]];
            });
        }

        // When collection has a single-dimensional array with string keys, map it to a two-dimensional array
        return $options->mapWithKeys(function (string|int $item, int|string $key) {
            return [$key => ['value' => $item, 'label' => $key]];
        });
    }
}
