<?php echo "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace ?>;

use InvalidArgumentException;

/**
 * Value Object — Identity of <?= $entity_name ?>.
 *
 * Zero framework imports — pure PHP UUID v4 generation.
 * Wraps a UUID string to provide type-safety and domain semantics.
 * Comparison is by value (equals()), never by reference.
 */
final readonly class <?= $class_name ?>

{
    private function __construct(
        private string $value,
    ) {}

    public static function generate(): self
    {
        $data    = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0F) | 0x40); // version 4
        $data[8] = chr((ord($data[8]) & 0x3F) | 0x80); // variant bits

        return new self(vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4)));
    }

    public static function fromString(string $value): self
    {
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $value)) {
            throw new InvalidArgumentException(
                sprintf('"%s" is not a valid UUID for <?= $class_name ?>.', $value)
            );
        }

        return new self(strtolower($value));
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
