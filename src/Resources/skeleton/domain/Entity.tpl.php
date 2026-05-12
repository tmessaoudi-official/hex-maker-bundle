<?php echo "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace ?>;

<?php if ($generate_id): ?>
use <?= $namespace ?>\<?= $id_class ?>;
<?php endif ?>
<?php if (!empty($extra_vos)): ?>
<?php foreach ($extra_vos as $vo): ?>
use <?= $namespace ?>\<?= ucfirst($vo) ?>;
<?php endforeach ?>
<?php endif ?>

final class <?= $class_name ?>

{
    public function __construct(
        private readonly <?= $id_class ?> $id,
<?php foreach ($extra_vos as $vo): ?>
        // private readonly <?= ucfirst($vo) ?> $<?= lcfirst($vo) ?>,
<?php endforeach ?>
    ) {}

    public function id(): <?= $id_class ?>

    {
        return $this->id;
    }

    // Add domain behaviour below.
    // Keep entities behaviour-rich and state-focused.
    // Avoid anemic domain model: validate invariants here, not in handlers.
}
