<?php echo "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace ?>;

<?php if ($id_namespace): ?>
use <?= $id_namespace ?>;
<?php endif ?>

final class <?= $class_name ?> extends \DomainException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function withId(<?= $id_class ?> $id): self
    {
        return new self(
            sprintf('<?= $entity_name ?> with ID "%s" was not found.', $id)
        );
    }

    public static function withMessage(string $message): self
    {
        return new self($message);
    }
}
