<?php

declare(strict_types=1);

namespace MarkHadjar\WpConfig;

use MarkHadjar\Env\Env;
use MarkHadjar\Env\Exception\ValueCouldNotBeCast;
use MarkHadjar\WpConfig\Exception\ConfigKeyWasNotFound;
use MarkHadjar\WpConfig\Exception\ConstantWasAlreadyDefined;
use MarkHadjar\WpConfig\Exception\DefaultValueTypeWasNotSupported;

class WpConfig
{
    /** @var array<string, mixed> */
    private array $entries = [];

    /**
     * @throws ConstantWasAlreadyDefined
     * @throws DefaultValueTypeWasNotSupported
     * @throws ValueCouldNotBeCast
     */
    public function env(string $key, mixed $default = null): self
    {
        return $this->set($key, $this->resolve($key, $default));
    }

    /**
     * @throws ConstantWasAlreadyDefined
     */
    public function set(string $key, mixed $value): self
    {
        if (\defined($key)) {
            throw new ConstantWasAlreadyDefined($key);
        }

        $this->entries[$key] = $value;

        return $this;
    }

    /**
     * @throws ConfigKeyWasNotFound
     */
    public function get(string $key): mixed
    {
        if (!$this->has($key)) {
            throw new ConfigKeyWasNotFound($key);
        }

        return $this->entries[$key];
    }

    public function has(string $key): bool
    {
        return \array_key_exists($key, $this->entries);
    }

    /**
     * @throws ConstantWasAlreadyDefined
     */
    public function apply(): void
    {
        foreach ($this->entries as $key => $value) {
            if (\defined($key) && \constant($key) !== $value) {
                throw new ConstantWasAlreadyDefined($key);
            }
        }

        foreach ($this->entries as $key => $value) {
            if (\defined($key)) {
                continue;
            }

            \define($key, $value);
        }
    }

    /**
     * @throws DefaultValueTypeWasNotSupported
     * @throws ValueCouldNotBeCast
     */
    private function resolve(string $key, mixed $default): mixed
    {
        return match (true) {
            \is_bool($default) => Env::getBool($key, $default),
            \is_float($default) => Env::getFloat($key, $default),
            \is_int($default) => Env::getInt($key, $default),
            \is_string($default) => Env::getString($key, $default),
            $default === null => Env::get($key),
            default => throw new DefaultValueTypeWasNotSupported($key, \get_debug_type($default)),
        };
    }
}
