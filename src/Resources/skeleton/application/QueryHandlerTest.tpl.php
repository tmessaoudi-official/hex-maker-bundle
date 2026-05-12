<?php echo "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace ?>;

use <?= $handler_namespace ?>;
use <?= $query_namespace ?>;
use <?= $output_dto_namespace ?>;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class <?= $class_name ?> extends TestCase
{
    private <?= $handler_class ?> $handler;

    protected function setUp(): void
    {
        // $this->repository = $this->createMock(SomeRepositoryInterface::class);
        $this->handler = new <?= $handler_class ?>(
            // $this->repository
        );
    }

    public function test_it_returns_output_dto(): void
    {
        // Arrange
        $query = new <?= $query_class ?>(
            // Fill query parameters
        );

        // Act
        $result = ($this->handler)($query);

        // Assert
        $this->assertInstanceOf(<?= $output_dto_class ?>::class, $result);
        // Assert specific fields: $this->assertSame('expected', $result->fieldName);
    }

    public function test_it_returns_correct_data_for_known_input(): void
    {
        // Arrange — seed repository mock with known data
        // $entity = ...;
        // $this->repository->method('findById')->willReturn($entity);

        // Act
        // $result = ($this->handler)(new <?= $query_class ?>(...));

        // Assert
        // $this->assertSame('expected', $result->id);
        $this->markTestIncomplete('Implement when repository stub is wired.');
    }
}
