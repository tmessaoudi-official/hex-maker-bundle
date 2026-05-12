<?php echo "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace ?>;

<?php if ($entity_namespace): ?>
use <?= $entity_namespace ?>;
<?php endif ?>

/**
 * Command — <?= $class_name ?>.
 *
 * Commands represent intent to change state.
 * They are immutable: use readonly properties.
 * Validation of input format belongs here or in a dedicated validator.
 * Domain invariant validation belongs in the entity.
 */
final readonly class <?= $class_name ?>

{
    public function __construct(
<?php if ($entity_name): ?>
        // public string $<?= lcfirst($entity_name) ?>Id,
<?php endif ?>
        // Add command payload properties here
    ) {}
}
