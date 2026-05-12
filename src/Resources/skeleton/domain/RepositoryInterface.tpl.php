<?php echo "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace ?>;

use <?= $entity_namespace ?>;
<?php if ($id_namespace): ?>
use <?= $id_namespace ?>;
<?php endif ?>

interface <?= $class_name ?>

{
    public function findById(<?= $id_class ?> $id): ?<?= $entity_name ?>;

    /**
     * @throws \<?= $namespace ?>\.. Use the domain exception (e.g. <?= $entity_name ?>NotFoundException)
     */
    public function getById(<?= $id_class ?> $id): <?= $entity_name ?>;

    public function save(<?= $entity_name ?> $<?= lcfirst($entity_name) ?>): void;

    public function delete(<?= $entity_name ?> $<?= lcfirst($entity_name) ?>): void;

    /**
     * @return <?= $entity_name ?>[]
     */
    public function findAll(): array;
}
