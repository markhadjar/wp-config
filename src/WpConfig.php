<?php

declare(strict_types=1);

namespace MarkHadjar\WpConfig;

use MarkHadjar\WpConfig\Exception\ConfigKeyWasNotFound;
use MarkHadjar\WpConfig\Exception\ConstantWasAlreadyDefined;

class WpConfig
{
    /** @var array<string, mixed> */
    private array $entries = [];

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
}
