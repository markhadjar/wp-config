<?php

declare(strict_types=1);

namespace MarkHadjar\WpConfig\Exception;

use RuntimeException;

final class ConstantWasAlreadyDefined extends RuntimeException implements WpConfigException
{
    public function __construct(string $key)
    {
        parent::__construct(\sprintf('Constant "%s" is already defined.', $key));
    }
}
