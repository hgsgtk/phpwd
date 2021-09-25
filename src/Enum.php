<?php

declare(strict_types=1);

namespace Phpwd;

use Phpwd\Exceptions\BadMethodCallException;
use Phpwd\Exceptions\InvalidArgumentException;

trait Enum
{
    abstract protected static function enum(): array;

    protected string|int $value;

    final public function __construct($value)
    {
        if (!in_array($value, static::enum(), true)) {
            throw new InvalidArgumentException("{$value} is not found in " . static::class . '.');
        }

        $this->value = $value;
    }

    protected function getValue(): int|string
    {
        return $this->value;
    }

    public function equals(self $that): bool
    {
        return (
            $this->getValue() === $that->getValue() &&
            get_called_class() === get_class($that)
        );
    }

    public static function __callStatic(string $method, array $args)
    {
        $key = $method;
        if (is_null($value = self::valueOf($key))) {
            throw new InvalidArgumentException("{$key} is not found in " . static::class . '.');
        }

        return new self($value);
    }

    private static function valueOf(string $key)
    {
        $enum = static::enum();
        return $enum[$key] ?? null;
    }

    public function __set(string $key, $value)
    {
        throw new BadMethodCallException('All setter is forbidden');
    }
}
