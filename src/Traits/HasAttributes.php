<?php

namespace Sevaske\PayfortApi\Traits;

use Sevaske\PayfortApi\Exceptions\ReadOnlyAttributesException;
use Sevaske\PayfortApi\Exceptions\UndefinedAttributeException;

trait HasAttributes
{
    /**
     * Internal storage for dynamic attributes.
     */
    protected array $attributes = [];

    /**
     * Determines if the attributes are read-only.
     */
    protected bool $readOnlyAttributes = false;

    /**
     * Magic method to retrieve the value of a dynamic attribute.
     *
     * @param  string  $name  The name of the attribute.
     * @return mixed|null The value of the attribute or null if not set.
     */
    public function __get(string $name)
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * Magic method to set the value of a dynamic attribute.
     *
     * @param  string  $name  The name of the attribute.
     * @param  mixed  $value  The value to assign to the attribute.
     *
     * @throws ReadOnlyAttributesException
     */
    public function __set(string $name, mixed $value)
    {
        if ($this->readOnlyAttributes) {
            throw new ReadOnlyAttributesException;
        }

        $this->attributes[$name] = $value;
    }

    /**
     * Magic method to check if a dynamic attribute is set.
     *
     * @param  string  $name  The name of the attribute.
     * @return bool True if the attribute is set, false otherwise.
     */
    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    /**
     * Magic method to unset a dynamic attribute.
     *
     * @param  string  $name  The name of the attribute to unset.
     */
    public function __unset(string $name): void
    {
        unset($this->attributes[$name]);
    }

    /**
     * Retrieves an attribute value.
     *
     * @param string $key The attribute key.
     *
     * @return mixed The attribute value.
     * @throws UndefinedAttributeException
     */
    protected function getAttribute(string $key): mixed
    {
        if (! isset($this->attributes[$key])) {
            throw (new UndefinedAttributeException)->withContext([
                'key' => $key,
            ]);
        }

        return $this->attributes[$key];
    }

    /**
     * Retrieves an attribute value or null if undefined.
     *
     * @param string $key The attribute key.
     *
     * @return mixed|null The attribute value or null if not found.
     */
    protected function getOptionalAttribute(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Retrieves an integer attribute value.
     *
     * @param string $key The attribute key.
     *
     * @return int|null The integer value or null if not found.
     * @throws UndefinedAttributeException
     */
    protected function getAttributeAsInt(string $key): ?int
    {
        $value = $this->getAttribute($key);

        if ($value === null) {
            return null;
        }

        return (int) $value;
    }
}
