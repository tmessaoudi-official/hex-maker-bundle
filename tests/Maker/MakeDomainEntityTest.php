<?php

declare(strict_types=1);

namespace TakiTech\HexMakerBundle\Tests\Maker;

use PHPUnit\Framework\TestCase;
use TakiTech\HexMakerBundle\Maker\MakeDomainEntity;

/**
 * Unit tests for MakeDomainEntity.
 *
 * Scope: These tests cover namespace derivation, class-name sanitization,
 * and field-name parsing — the pure PHP logic inside the Maker class.
 *
 * They do NOT exercise the full MakerBundle Generator pipeline (which requires
 * a Symfony kernel fixture). Integration-level generation is verified manually
 * via `php bin/console make:domain:entity` in a host Symfony app.
 */
final class MakeDomainEntityTest extends TestCase
{
    public function test_command_name_is_correct(): void
    {
        $this->assertSame('make:domain:entity', MakeDomainEntity::getCommandName());
    }

    public function test_command_description_is_not_empty(): void
    {
        $this->assertNotEmpty(MakeDomainEntity::getCommandDescription());
    }

    /**
     * @dataProvider provideClassNameSanitizationCases
     */
    public function test_class_name_sanitization(string $input, string $expected): void
    {
        $maker = new MakeDomainEntity();
        $method = new \ReflectionMethod($maker, 'sanitizeClassName');
        $method->setAccessible(true);

        $this->assertSame($expected, $method->invoke($maker, $input));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideClassNameSanitizationCases(): array
    {
        return [
            'simple name' => ['Order', 'Order'],
            'lowercase' => ['order', 'Order'],
            'with forward slash' => ['App/Order', 'AppOrder'],
            'with backslash' => ['App\\Order', 'AppOrder'],
            'leading slash stripped' => ['/Order', 'Order'],
            'trailing slash stripped' => ['Order/', 'Order'],
            'multi-segment' => ['App/Domain/Order', 'AppDomainOrder'],
        ];
    }

    /**
     * @dataProvider provideFieldNameParsingCases
     */
    public function test_field_name_parsing(string $input, array $expected): void
    {
        $maker = new MakeDomainEntity();
        $method = new \ReflectionMethod($maker, 'parseFieldNames');
        $method->setAccessible(true);

        $this->assertSame($expected, array_values($method->invoke($maker, $input)));
    }

    /**
     * @return array<string, array{string, string[]}>
     */
    public static function provideFieldNameParsingCases(): array
    {
        return [
            'empty string' => ['', []],
            'single field' => ['email', ['email']],
            'multiple fields' => ['email,name,phone', ['email', 'name', 'phone']],
            'fields with spaces' => [' email , name ', ['email', 'name']],
            'trailing comma ignored' => ['email,', ['email']],
            'leading comma ignored' => [',email', ['email']],
        ];
    }

    /**
     * @dataProvider provideNamespaceExtractionCases
     */
    public function test_namespace_extraction(string $fqn, string $expected): void
    {
        $maker = new MakeDomainEntity();
        $method = new \ReflectionMethod($maker, 'extractNamespace');
        $method->setAccessible(true);

        $this->assertSame($expected, $method->invoke($maker, $fqn));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideNamespaceExtractionCases(): array
    {
        return [
            'top-level class' => ['Order', ''],
            'one level deep' => ['App\\Order', 'App'],
            'two levels deep' => ['App\\Domain\\Order', 'App\\Domain'],
            'three levels deep' => ['App\\Domain\\Model\\Order', 'App\\Domain\\Model'],
        ];
    }
}
