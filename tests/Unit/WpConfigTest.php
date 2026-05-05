<?php

declare(strict_types=1);

namespace MarkHadjar\WpConfig\Tests\Unit;

use MarkHadjar\WpConfig\Exception\ConfigKeyWasNotFound;
use MarkHadjar\WpConfig\Exception\ConstantWasAlreadyDefined;
use MarkHadjar\WpConfig\Exception\DefaultValueTypeWasNotSupported;
use MarkHadjar\WpConfig\WpConfig;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\WithEnvironmentVariable;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(WpConfig::class)]
#[CoversClass(ConfigKeyWasNotFound::class)]
#[CoversClass(ConstantWasAlreadyDefined::class)]
#[CoversClass(DefaultValueTypeWasNotSupported::class)]
final class WpConfigTest extends TestCase
{
    private WpConfig $config;

    protected function setUp(): void
    {
        $this->config = new WpConfig();
    }

    #[Test]
    #[TestDox('It returns itself when setting a key from the environment')]
    public function itReturnsItselfWhenSettingAKeyFromTheEnvironment(): void
    {
        self::assertSame($this->config, $this->config->env('FOO', 'fallback'));
    }

    #[Test]
    #[TestDox('It resolves a boolean value from the environment')]
    #[WithEnvironmentVariable('VALID_BOOL_LOWERCASE_TRUE')]
    public function itResolvesABooleanValueFromTheEnvironment(): void
    {
        $_ENV['VALID_BOOL_LOWERCASE_TRUE'] = 'true';

        $this->config->env('VALID_BOOL_LOWERCASE_TRUE', false);

        self::assertTrue($this->config->get('VALID_BOOL_LOWERCASE_TRUE'));
    }

    #[Test]
    #[TestDox('It resolves a float value from the environment')]
    #[WithEnvironmentVariable('VALID_FLOAT_POSITIVE')]
    public function itResolvesAFloatValueFromTheEnvironment(): void
    {
        $_ENV['VALID_FLOAT_POSITIVE'] = '1.5';

        $this->config->env('VALID_FLOAT_POSITIVE', 0.0);

        self::assertSame(1.5, $this->config->get('VALID_FLOAT_POSITIVE'));
    }

    #[Test]
    #[TestDox('It resolves an integer value from the environment')]
    #[WithEnvironmentVariable('VALID_INT_POSITIVE')]
    public function itResolvesAnIntegerValueFromTheEnvironment(): void
    {
        $_ENV['VALID_INT_POSITIVE'] = '1';

        $this->config->env('VALID_INT_POSITIVE', 0);

        self::assertSame(1, $this->config->get('VALID_INT_POSITIVE'));
    }

    #[Test]
    #[TestDox('It resolves a string value from the environment')]
    #[WithEnvironmentVariable('VALID_STRING_NON_EMPTY')]
    public function itResolvesAStringValueFromTheEnvironment(): void
    {
        $_ENV['VALID_STRING_NON_EMPTY'] = 'abc';

        $this->config->env('VALID_STRING_NON_EMPTY', '');

        self::assertSame('abc', $this->config->get('VALID_STRING_NON_EMPTY'));
    }

    #[Test]
    #[TestDox('It falls back to the default when the key is not in the environment')]
    #[WithEnvironmentVariable('FOO')]
    public function itFallsBackToTheDefaultWhenTheKeyIsNotInTheEnvironment(): void
    {
        $this->config->env('FOO', 'fallback');

        self::assertSame('fallback', $this->config->get('FOO'));
    }

    #[Test]
    #[TestDox('It resolves a value from the environment when the default is null')]
    #[WithEnvironmentVariable('FOO')]
    public function itResolvesAValueFromTheEnvironmentWhenTheDefaultIsNull(): void
    {
        $_ENV['FOO'] = 'bar';

        $this->config->env('FOO');

        self::assertSame('bar', $this->config->get('FOO'));
    }

    #[Test]
    #[TestDox('It stores null when the default is null and the key is not in the environment')]
    #[WithEnvironmentVariable('FOO')]
    public function itStoresNullWhenTheDefaultIsNullAndTheKeyIsNotInTheEnvironment(): void
    {
        $this->config->env('FOO');

        self::assertNull($this->config->get('FOO'));
    }

    #[Test]
    #[TestDox('It throws when the default value type is not supported')]
    public function itThrowsWhenTheDefaultValueTypeIsNotSupported(): void
    {
        $this->expectException(DefaultValueTypeWasNotSupported::class);
        $this->expectExceptionMessage('Default value type for key "FOO" is not supported: array.');

        $this->config->env('FOO', []);
    }

    #[Test]
    #[TestDox('It returns itself when setting a key')]
    public function itReturnsItselfWhenSettingAKey(): void
    {
        self::assertSame($this->config, $this->config->set('WP_CONFIG_TEST_SET_RETURNS_SELF', 'foo'));
    }

    #[Test]
    #[TestDox('It overwrites an existing entry when set is called again with the same key')]
    public function itOverwritesAnExistingEntryWhenSetIsCalledAgainWithTheSameKey(): void
    {
        $this->config->set('WP_CONFIG_TEST_SET_OVERWRITES_SAME_KEY', 'foo');
        $this->config->set('WP_CONFIG_TEST_SET_OVERWRITES_SAME_KEY', 'bar');

        self::assertSame('bar', $this->config->get('WP_CONFIG_TEST_SET_OVERWRITES_SAME_KEY'));
    }

    #[Test]
    #[TestDox('It throws when the key is already a defined PHP constant')]
    public function itThrowsWhenKeyIsAlreadyADefinedPhpConstant(): void
    {
        $this->expectException(ConstantWasAlreadyDefined::class);
        $this->expectExceptionMessage('Constant "PHP_INT_MAX" is already defined.');

        $this->config->set('PHP_INT_MAX', 'foo');
    }

    #[Test]
    #[TestDox('It returns the value for a key that exists')]
    public function itReturnsTheValueForAKeyThatExists(): void
    {
        $this->config->set('WP_CONFIG_TEST_GET_RETURNS_VALUE', 'foo');

        self::assertSame('foo', $this->config->get('WP_CONFIG_TEST_GET_RETURNS_VALUE'));
    }

    #[Test]
    #[TestDox('It throws when the key is not found')]
    public function itThrowsWhenTheKeyIsNotFound(): void
    {
        $this->expectException(ConfigKeyWasNotFound::class);
        $this->expectExceptionMessage('Configuration key "WP_CONFIG_TEST_GET_THROWS_ON_MISSING" is not found.');

        $this->config->get('WP_CONFIG_TEST_GET_THROWS_ON_MISSING');
    }

    #[Test]
    #[TestDox('It returns true for a key that exists')]
    public function itReturnsTrueForAKeyThatExists(): void
    {
        $this->config->set('WP_CONFIG_TEST_HAS_RETURNS_TRUE', 'foo');

        self::assertTrue($this->config->has('WP_CONFIG_TEST_HAS_RETURNS_TRUE'));
    }

    #[Test]
    #[TestDox('It returns false for a key that does not exist')]
    public function itReturnsFalseForAKeyThatDoesNotExist(): void
    {
        $this->config->set('WP_CONFIG_TEST_HAS_RETURNS_TRUE', 'foo');

        self::assertFalse($this->config->has('WP_CONFIG_TEST_HAS_RETURNS_FALSE'));
    }

    #[Test]
    #[TestDox('It defines constants when applied')]
    public function itDefinesConstantsWhenApplied(): void
    {
        $this->config->set('WP_CONFIG_TEST_APPLY_DEFINES_CONSTANTS', 'foo');
        $this->config->apply();

        self::assertSame('foo', \constant('WP_CONFIG_TEST_APPLY_DEFINES_CONSTANTS'));
    }

    #[Test]
    #[TestDox('It throws when applying a constant that was already defined with a different value')]
    public function itThrowsWhenApplyingAConstantAlreadyDefinedWithADifferentValue(): void
    {
        $this->config->set('WP_CONFIG_TEST_APPLY_THROWS_ON_CONFLICT', 'foo');

        \define('WP_CONFIG_TEST_APPLY_THROWS_ON_CONFLICT', 'bar');

        $this->expectException(ConstantWasAlreadyDefined::class);
        $this->expectExceptionMessage('Constant "WP_CONFIG_TEST_APPLY_THROWS_ON_CONFLICT" is already defined.');

        $this->config->apply();
    }

    #[Test]
    #[TestDox('It skips a constant that was already defined with the same value')]
    public function itSkipsAConstantAlreadyDefinedWithTheSameValue(): void
    {
        $this->config->set('WP_CONFIG_TEST_APPLY_SKIPS_SAME_VALUE', 'foo');

        \define('WP_CONFIG_TEST_APPLY_SKIPS_SAME_VALUE', 'foo');

        $this->config->apply();

        self::assertSame('foo', \constant('WP_CONFIG_TEST_APPLY_SKIPS_SAME_VALUE'));
    }

    #[Test]
    #[TestDox('It does not define any constant when applying fails on a later entry')]
    public function itDoesNotDefineAnyConstantWhenApplyingFailsOnALaterEntry(): void
    {
        $this->config->set('WP_CONFIG_TEST_APPLY_ATOMICITY_OK', 'foo');
        $this->config->set('WP_CONFIG_TEST_APPLY_ATOMICITY_CONFLICT', 'foo');

        \define('WP_CONFIG_TEST_APPLY_ATOMICITY_CONFLICT', 'bar');

        $this->expectException(ConstantWasAlreadyDefined::class);

        try {
            $this->config->apply();
        } finally {
            self::assertFalse(\defined('WP_CONFIG_TEST_APPLY_ATOMICITY_OK'));
        }
    }
}
