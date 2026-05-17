<?php echo "<?php\n" ?>

declare(strict_types=1);

use <?= $policy_fqn ?>;

// ─── In-memory fakes ─────────────────────────────────────────────────────────
//
// Implement your Domain repository ports here as simple in-memory arrays.
// Zero Symfony. Zero Doctrine. Zero database.
//
// final class InMemory<?= $entity_name ?>Repository implements <?= $entity_name ?>RepositoryInterface
// {
//     public function __construct(private array $items = []) {}
//     public function save(<?= $entity_name ?> $item): void { $this->items[] = $item; }
//     public function countByRole(...): int { return count(array_filter(...)); }
// }

// ─── Tests ───────────────────────────────────────────────────────────────────

describe('<?= $class_name ?>', function (): void {
    beforeEach(function (): void {
        $this->policy = new <?= $class_name ?>();
        // $this->repo = new InMemory<?= $entity_name ?>Repository();
        $this->now = new \DateTimeImmutable();
    });

    it('TODO: describe your first rule', function (): void {
        // Arrange: set up domain objects directly — no factories, no fixtures
        // Act:     call $this->policy->check(...)
        // Assert:  expect the result
        expect(true)->toBeTrue(); // replace this
    });
});
