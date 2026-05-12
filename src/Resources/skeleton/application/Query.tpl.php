<?php echo "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace ?>;

/**
 * Query — <?= $class_name ?>.
 *
 * Queries represent a request for data. They never change state.
 * All parameters needed to fetch data go here as readonly properties.
 */
final readonly class <?= $class_name ?>

{
    public function __construct(
        // Add query parameters here:
        // public string $id,
        // public int $page = 1,
        // public int $limit = 20,
    ) {}
}
