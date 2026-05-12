<?php echo "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace ?>;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

/**
 * API Platform State Provider for <?= $entity_name ?>.
 *
 * Bridges API Platform read operations to CQRS Queries.
 * Inject the MessageBus and dispatch the appropriate query.
 *
 * @implements ProviderInterface<<?= $resource_class ?>>
 */
final readonly class <?= $class_name ?> implements ProviderInterface
{
    public function __construct(
        private MessageBusInterface $queryBus,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // $query = new SomeQuery($uriVariables['id'] ?? null);
        // $envelope = $this->queryBus->dispatch($query);
        // return $envelope->last(HandledStamp::class)?->getResult();

        return null;
    }
}
