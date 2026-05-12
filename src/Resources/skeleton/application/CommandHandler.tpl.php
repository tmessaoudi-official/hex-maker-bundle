<?php echo "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace ?>;

use <?= $command_namespace ?>;
<?php if ($entity_namespace): ?>
use <?= $entity_namespace ?>;
<?php endif ?>
<?php if ($repository_namespace): ?>
use <?= $repository_namespace ?>;
<?php endif ?>
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class <?= $class_name ?>

{
    public function __construct(
<?php if ($entity_name): ?>
        // private <?= $entity_name ?>RepositoryInterface $<?= lcfirst($entity_name) ?>Repository,
<?php endif ?>
    ) {}

    public function __invoke(<?= $command_class ?> $command): <?= $return_type ?>

    {
        // Implement command logic:
        // 1. Load aggregate from repository
        // 2. Call domain method on aggregate
        // 3. Persist aggregate
        // 4. (Optional) dispatch domain events
<?php if ($handler_needs_return): ?>

        // return ...;
<?php endif ?>
    }
}
