<?php

declare(strict_types=1);

namespace TakiTech\HexMakerBundle\Tests\Maker;

use PHPUnit\Framework\TestCase;
use TakiTech\HexMakerBundle\Maker\MakeCqrsCommand;
use TakiTech\HexMakerBundle\Maker\MakeCqrsQuery;
use TakiTech\HexMakerBundle\Maker\MakeDomainEvent;

/**
 * Unit tests for CQRS makers.
 *
 * Scope: command metadata (names, descriptions) and class-name helpers.
 * Generator pipeline integration is validated manually.
 */
final class MakeCqrsCommandTest extends TestCase
{
    // --- MakeCqrsCommand ---

    public function test_cqrs_command_name(): void
    {
        $this->assertSame('make:cqrs:command', MakeCqrsCommand::getCommandName());
    }

    public function test_cqrs_command_description_is_not_empty(): void
    {
        $this->assertNotEmpty(MakeCqrsCommand::getCommandDescription());
    }

    /**
     * @dataProvider provideCommandClassNameSanitization
     */
    public function test_command_sanitizes_class_name(string $input, string $expected): void
    {
        $maker = new MakeCqrsCommand();
        $method = new \ReflectionMethod($maker, 'sanitizeClassName');
        $method->setAccessible(true);

        $this->assertSame($expected, $method->invoke($maker, $input));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideCommandClassNameSanitization(): array
    {
        return [
            'already pascal case' => ['CreateOrder', 'CreateOrder'],
            'lowercase' => ['createorder', 'Createorder'],
            'with slash' => ['App/CreateOrder', 'AppCreateOrder'],
        ];
    }

    // --- MakeCqrsQuery ---

    public function test_cqrs_query_name(): void
    {
        $this->assertSame('make:cqrs:query', MakeCqrsQuery::getCommandName());
    }

    public function test_cqrs_query_description_is_not_empty(): void
    {
        $this->assertNotEmpty(MakeCqrsQuery::getCommandDescription());
    }

    /**
     * @dataProvider provideOutputDtoGuessingCases
     */
    public function test_output_dto_name_guessing(string $baseName, string $expected): void
    {
        $maker = new MakeCqrsQuery();
        $method = new \ReflectionMethod($maker, 'guessOutputDtoName');
        $method->setAccessible(true);

        $this->assertSame($expected, $method->invoke($maker, $baseName));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideOutputDtoGuessingCases(): array
    {
        return [
            'Get prefix stripped' => ['GetOrderById', 'OrderByIdOutput'],
            'Find prefix stripped' => ['FindOrder', 'OrderOutput'],
            'List prefix stripped' => ['ListOrders', 'OrdersOutput'],
            'Fetch prefix stripped' => ['FetchInvoice', 'InvoiceOutput'],
            'Search prefix stripped' => ['SearchProducts', 'ProductsOutput'],
            'No prefix' => ['OrderSummary', 'OrderSummaryOutput'],
        ];
    }

    // --- MakeDomainEvent ---

    public function test_domain_event_command_name(): void
    {
        $this->assertSame('make:domain:event', MakeDomainEvent::getCommandName());
    }

    public function test_domain_event_description_is_not_empty(): void
    {
        $this->assertNotEmpty(MakeDomainEvent::getCommandDescription());
    }
}
