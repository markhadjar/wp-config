<?php

declare(strict_types=1);

namespace MarkHadjar\WpConfig\Exception;

use OutOfBoundsException;

final class ConfigKeyWasNotFound extends OutOfBoundsException implements WpConfigException
{
    public function __construct(string $key)
    {
        parent::__construct(\sprintf('Configuration key "%s" is not found.', $key));
    }
}
