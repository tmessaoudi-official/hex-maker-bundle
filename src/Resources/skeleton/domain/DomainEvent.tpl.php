<?php echo "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace ?>;

/**
 * Domain Event — <?= $event_name ?>.
 *
 * Represents something that has happened in the domain.
 * Dispatch from aggregate roots or command handlers after state changes.
 * Immutable: all properties are readonly.
 */
final readonly class <?= $class_name ?>

{
    public \DateTimeImmutable $occurredAt;

    public function __construct(
        // Add event payload properties here:
        // public string $aggregateId,
    ) {
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function eventName(): string
    {
        return '<?= $event_name ?>';
    }
}
