<?php

declare(strict_types=1);

namespace TakiTech\HexMakerBundle\Maker;

use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Scaffolds a Domain Event (PHP 8.4 readonly class with timestamp).
 *
 * Usage:
 *   php bin/console make:domain:event OrderCreated
 */
final class MakeDomainEvent extends AbstractMaker
{
    private const SKELETON_PATH = __DIR__ . '/../Resources/skeleton/domain/';

    public static function getCommandName(): string
    {
        return 'make:domain:event';
    }

    public static function getCommandDescription(): string
    {
        return 'Scaffolds a Domain Event as a PHP 8.4 readonly class with occurrence timestamp';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument('event-name', InputArgument::OPTIONAL, 'The event name (e.g. <fg=yellow>OrderCreated</>)')
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command scaffolds a Domain Event.

<info>php %command.full_name% OrderCreated</info>

This creates:
  - <comment>src/Domain/Event/OrderCreated.php</comment>

The generated event is a PHP 8.4 readonly class with an occurrence timestamp.
Generated files are plain PHP — no runtime dependency on this bundle.
HELP
            );
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        // No additional runtime dependencies required
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $eventName = $input->getArgument('event-name')
            ?? $io->ask('Event name (e.g. OrderCreated)', null, static function (?string $name): string {
                if (empty($name)) {
                    throw new \InvalidArgumentException('Event name cannot be empty.');
                }
                return $name;
            });

        $eventName = $this->sanitizeClassName((string) $eventName);

        $eventClassDetails = $generator->createClassNameDetails(
            $eventName,
            'Domain\\Event\\'
        );

        $generator->generateClass(
            $eventClassDetails->getFullName(),
            self::SKELETON_PATH . 'DomainEvent.tpl.php',
            [
                'class_name' => $eventClassDetails->getShortName(),
                'namespace' => $this->extractNamespace($eventClassDetails->getFullName()),
                'event_name' => $eventName,
            ]
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);

        $io->text([
            'Next: dispatch this event from your domain entity or command handler.',
            'Example: <comment>$this->dispatcher->dispatch(new ' . $eventName . '($this->id))</comment>',
        ]);
    }

    private function sanitizeClassName(string $name): string
    {
        $name = trim($name, '/\\');
        $parts = preg_split('/[\/\\\\]/', $name) ?: [$name];

        return implode('', array_map(
            static fn (string $part) => ucfirst($part),
            $parts
        ));
    }

    private function extractNamespace(string $fullyQualifiedName): string
    {
        $parts = explode('\\', $fullyQualifiedName);
        array_pop($parts);

        return implode('\\', $parts);
    }
}
