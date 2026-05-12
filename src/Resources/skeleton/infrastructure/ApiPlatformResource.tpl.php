<?php echo "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace ?>;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;

#[ApiResource(
    operations: [
        new GetCollection(provider: <?= $entity_name ?>CollectionProvider::class),
        new Get(provider: <?= $entity_name ?>ItemProvider::class),
        new Post(processor: Create<?= $entity_name ?>Processor::class),
        new Put(processor: Update<?= $entity_name ?>Processor::class),
        new Delete(processor: Delete<?= $entity_name ?>Processor::class),
    ]
)]
final class <?= $class_name ?>

{
    public function __construct(
        public readonly string $id = '',
        // Map entity properties to API representation here
    ) {}

    public static function fromDomain(object $entity): self
    {
        return new self(
            id: (string) $entity->id(),
            // Map other fields
        );
    }
}
