<?php

declare(strict_types=1);

namespace MarkHadjar\WpConfig\Exception;

use InvalidArgumentException;

final class DefaultValueTypeWasNotSupported extends InvalidArgumentException implements WpConfigException
{
    public function __construct(string $key, string $type)
    {
        parent::__construct(\sprintf('Default value type for key "%s" is not supported: %s.', $key, $type));
    }
}
