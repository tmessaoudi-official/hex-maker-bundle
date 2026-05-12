<?php echo "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace ?>;

/**
 * Value Object — <?= ucfirst($field_name) ?> of <?= $entity_name ?>.
 *
 * Value Objects are immutable and compared by value, not by identity.
 * Add validation of domain invariants in the constructor.
 */
final readonly class <?= $class_name ?>

{
    public function __construct(
        private string $value,
    ) {
        // Uncomment and adapt validation below:
        // if (empty(trim($value))) {
        //     throw new \InvalidArgumentException('<?= ucfirst($field_name) ?> cannot be empty.');
        // }
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
