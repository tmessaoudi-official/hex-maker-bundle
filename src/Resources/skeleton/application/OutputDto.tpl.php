<?php echo "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace ?>;

/**
 * Output DTO — <?= $class_name ?>.
 *
 * Data Transfer Object returned by <?= $query_name ?>QueryHandler.
 * Use readonly properties for immutability.
 * This is the boundary object between Application and Presentation layers.
 *
 * Anti-pattern: do NOT return domain entities from query handlers.
 * Pro-pattern: add a static fromEntity() factory method for mapping.
 */
final readonly class <?= $class_name ?>

{
    public function __construct(
        // Add fields matching what the query consumer needs:
        // public string $id,
        // public string $name,
        // public \DateTimeImmutable $createdAt,
    ) {}

    // public static function fromDomain(SomeEntity $entity): self
    // {
    //     return new self(
    //         id: (string) $entity->id(),
    //         name: $entity->name()->value(),
    //     );
    // }
}
