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
 * Scaffolds a CQRS Command + CommandHandler pair.
 *
 * Usage:
 *   php bin/console make:cqrs:command CreateOrder
 */
final class MakeCqrsCommand extends AbstractMaker
{
    private const SKELETON_PATH = __DIR__ . '/../Resources/skeleton/application/';

    public static function getCommandName(): string
    {
        return 'make:cqrs:command';
    }

    public static function getCommandDescription(): string
    {
        return 'Scaffolds a CQRS Command and CommandHandler (with unit test)';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument('command-name', InputArgument::OPTIONAL, 'The command name (e.g. <fg=yellow>CreateOrder</>)')
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command scaffolds a CQRS Command + CommandHandler pair.

<info>php %command.full_name% CreateOrder</info>

This creates:
  - <comment>src/Application/Command/CreateOrder/CreateOrderCommand.php</comment>
  - <comment>src/Application/Command/CreateOrder/CreateOrderCommandHandler.php</comment>
  - <comment>tests/Unit/Application/Command/CreateOrderCommandHandlerTest.php</comment>

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
        $commandName = $input->getArgument('command-name')
            ?? $io->ask('Command name (e.g. CreateOrder)', null, static function (?string $name): string {
                if (empty($name)) {
                    throw new \InvalidArgumentException('Command name cannot be empty.');
                }
                return $name;
            });

        $commandName = $this->sanitizeClassName((string) $commandName);

        // Ensure suffix
        $baseName = str_ends_with($commandName, 'Command')
            ? substr($commandName, 0, -7)
            : $commandName;

        $entityHint = $io->ask(
            'Which entity does this command act on? (leave blank to skip import)',
            ''
        );
        $entityHint = trim((string) ($entityHint ?? ''));

        $returnType = $io->ask('Return type of handler?', 'void');
        $returnType = trim((string) ($returnType ?? 'void'));

        $handlerNeedsReturn = $returnType !== 'void';

        // Generate Command class
        $commandClassDetails = $generator->createClassNameDetails(
            $baseName . 'Command',
            'Application\\Command\\' . $baseName . '\\'
        );

        $generator->generateClass(
            $commandClassDetails->getFullName(),
            self::SKELETON_PATH . 'Command.tpl.php',
            [
                'class_name' => $commandClassDetails->getShortName(),
                'namespace' => $this->extractNamespace($commandClassDetails->getFullName()),
                'entity_name' => $entityHint ?: null,
                'entity_namespace' => $entityHint ? 'App\\Domain\\Model\\' . $entityHint : null,
            ]
        );

        // Generate CommandHandler class
        $handlerClassDetails = $generator->createClassNameDetails(
            $baseName . 'CommandHandler',
            'Application\\Command\\' . $baseName . '\\'
        );

        $generator->generateClass(
            $handlerClassDetails->getFullName(),
            self::SKELETON_PATH . 'CommandHandler.tpl.php',
            [
                'class_name' => $handlerClassDetails->getShortName(),
                'namespace' => $this->extractNamespace($handlerClassDetails->getFullName()),
                'command_class' => $commandClassDetails->getShortName(),
                'command_namespace' => $commandClassDetails->getFullName(),
                'return_type' => $returnType,
                'handler_needs_return' => $handlerNeedsReturn,
                'entity_name' => $entityHint ?: null,
                'entity_namespace' => $entityHint ? 'App\\Domain\\Model\\' . $entityHint : null,
                'repository_namespace' => $entityHint ? 'App\\Domain\\Repository\\' . $entityHint . 'RepositoryInterface' : null,
            ]
        );

        // Generate unit test
        $testClassDetails = $generator->createClassNameDetails(
            $baseName . 'CommandHandlerTest',
            'Unit\\Application\\Command\\'
        );

        $generator->generateClass(
            $testClassDetails->getFullName(),
            self::SKELETON_PATH . 'CommandHandlerTest.tpl.php',
            [
                'class_name' => $testClassDetails->getShortName(),
                'namespace' => $this->extractNamespace($testClassDetails->getFullName()),
                'handler_class' => $handlerClassDetails->getShortName(),
                'handler_namespace' => $handlerClassDetails->getFullName(),
                'command_class' => $commandClassDetails->getShortName(),
                'command_namespace' => $commandClassDetails->getFullName(),
                'entity_name' => $entityHint ?: null,
            ]
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);

        $io->text([
            sprintf('Next: implement the <comment>%s::__invoke()</comment> handler logic.', $handlerClassDetails->getShortName()),
            'Tag the handler with <comment>#[AsMessageHandler]</comment> or register it in services.yaml.',
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
