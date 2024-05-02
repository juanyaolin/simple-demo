<?php

if (!function_exists('data_get')) {
    /**
     * Get an item from an array or object using "dot" notation.
     */
    function data_get(
        mixed $target,
        array|string|int|null $key,
        mixed $default = null
    ): mixed {
        if (is_null($target) || is_null($key)) {
            return $target;
        }

        $key = is_array($key) ? $key : explode('.', $key);

        foreach ($key as $part) {
            if (is_null($part)) {
                return $target;
            }

            if (is_array($target) && array_key_exists($part, $target)) {
                $target = $target[$part];
            } else {
                return $default;
            }
        }

        return $target;
    }
}

if (function_exists('value')) {
    /**
     * Return the default value of the given value.
     */
    function value(mixed $value, mixed ...$args): mixed
    {
        return $value instanceof Closure ? $value(...$args) : $value;
    }
}
