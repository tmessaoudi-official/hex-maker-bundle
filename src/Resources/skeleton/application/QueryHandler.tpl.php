<?php echo "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace ?>;

use <?= $query_namespace ?>;
use <?= $output_dto_namespace ?>;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class <?= $class_name ?>

{
    public function __construct(
        // private SomeRepositoryInterface $repository,
    ) {}

    public function __invoke(<?= $query_class ?> $query): <?= $output_dto_class ?>

    {
        // Implement query logic:
        // 1. Fetch data from repository (read model or domain model)
        // 2. Map to Output DTO
        // 3. Return DTO (never return domain entities from query handlers)

        return new <?= $output_dto_class ?>();
    }
}
