<?php echo "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace ?>;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

/**
 * API Platform State Processor for <?= $entity_name ?>.
 *
 * Bridges API Platform write operations to CQRS Commands.
 * Inject the MessageBus and dispatch the appropriate command.
 *
 * @implements ProcessorInterface<<?= $resource_class ?>, <?= $resource_class ?>>
 */
final readonly class <?= $class_name ?> implements ProcessorInterface
{
    public function __construct(
        private MessageBusInterface $commandBus,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        // $command = new SomeCommand(...);
        // $envelope = $this->commandBus->dispatch($command);
        // $result = $envelope->last(HandledStamp::class)?->getResult();

        return $data;
    }
}
