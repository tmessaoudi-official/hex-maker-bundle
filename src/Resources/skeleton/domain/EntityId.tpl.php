<?php echo "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace ?>;

use Symfony\Component\Uid\Uuid;

/**
 * Value Object — Identity of <?= $entity_name ?>.
 *
 * Wraps a UUID to provide type-safety and domain semantics.
 * Comparison is by value (equals()), never by reference.
 */
final readonly class <?= $class_name ?>

{
    private function __construct(
        private string $value,
    ) {}

    public static function generate(): self
    {
        return new self((string) Uuid::v7());
    }

    public static function fromString(string $value): self
    {
        if (!Uuid::isValid($value)) {
            throw new \InvalidArgumentException(
                sprintf('"%s" is not a valid UUID for <?= $class_name ?>.', $value)
            );
        }

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
