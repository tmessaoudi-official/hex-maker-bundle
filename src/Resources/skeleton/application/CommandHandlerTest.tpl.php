<?php echo "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace ?>;

use <?= $handler_namespace ?>;
use <?= $command_namespace ?>;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
<?php if ($entity_name): ?>
// use App\Domain\Repository\<?= $entity_name ?>RepositoryInterface;
<?php endif ?>

final class <?= $class_name ?> extends TestCase
{
    private <?= $handler_class ?> $handler;

<?php if ($entity_name): ?>
    /** @var <?= $entity_name ?>RepositoryInterface&MockObject */
    // private MockObject $repository;
<?php endif ?>

    protected function setUp(): void
    {
<?php if ($entity_name): ?>
        // $this->repository = $this->createMock(<?= $entity_name ?>RepositoryInterface::class);
        $this->handler = new <?= $handler_class ?>(
            // $this->repository
        );
<?php else: ?>
        $this->handler = new <?= $handler_class ?>();
<?php endif ?>
    }

    public function test_it_handles_the_command_successfully(): void
    {
        // Arrange
        $command = new <?= $command_class ?>(
            // Fill constructor args
        );

        // Act
        ($this->handler)($command);

        // Assert
        // $this->repository->expects($this->once())->method('save')->with(...);
        $this->assertTrue(true); // Replace with real assertions
    }

    public function test_it_throws_when_aggregate_not_found(): void
    {
        // Arrange — simulate a missing aggregate
<?php if ($entity_name): ?>
        // $this->repository->method('getById')->willThrowException(
        //     <?= $entity_name ?>NotFoundException::withId(...)
        // );
<?php endif ?>

        // Assert
        // $this->expectException(<?= $entity_name ?? '\\DomainException' ?>NotFoundException::class);

        // Act
        // ($this->handler)(new <?= $command_class ?>(...));
        $this->markTestIncomplete('Implement when repository stub is wired.');
    }
}
