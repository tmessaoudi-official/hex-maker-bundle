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
 * Scaffolds a CQRS Query + QueryHandler + Output DTO.
 *
 * Usage:
 *   php bin/console make:cqrs:query GetOrderById
 */
final class MakeCqrsQuery extends AbstractMaker
{
    private const SKELETON_PATH = __DIR__ . '/../Resources/skeleton/application/';

    public static function getCommandName(): string
    {
        return 'make:cqrs:query';
    }

    public static function getCommandDescription(): string
    {
        return 'Scaffolds a CQRS Query, QueryHandler, and Output DTO (with unit test)';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument('query-name', InputArgument::OPTIONAL, 'The query name (e.g. <fg=yellow>GetOrderById</>)')
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command scaffolds a CQRS Query + QueryHandler + Output DTO.

<info>php %command.full_name% GetOrderById</info>

This creates:
  - <comment>src/Application/Query/GetOrderById/GetOrderByIdQuery.php</comment>
  - <comment>src/Application/Query/GetOrderById/GetOrderByIdQueryHandler.php</comment>
  - <comment>src/Application/DTO/Output/OrderOutput.php</comment>
  - <comment>tests/Unit/Application/Query/GetOrderByIdQueryHandlerTest.php</comment>

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
        $queryName = $input->getArgument('query-name')
            ?? $io->ask('Query name (e.g. GetOrderById)', null, static function (?string $name): string {
                if (empty($name)) {
                    throw new \InvalidArgumentException('Query name cannot be empty.');
                }
                return $name;
            });

        $queryName = $this->sanitizeClassName((string) $queryName);

        $baseName = str_ends_with($queryName, 'Query')
            ? substr($queryName, 0, -5)
            : $queryName;

        $outputDtoName = $io->ask(
            'Output DTO class name?',
            $this->guessOutputDtoName($baseName)
        );
        $outputDtoName = $this->sanitizeClassName((string) ($outputDtoName ?? $this->guessOutputDtoName($baseName)));

        // Generate Query class
        $queryClassDetails = $generator->createClassNameDetails(
            $baseName . 'Query',
            'Application\\Query\\' . $baseName . '\\'
        );

        $generator->generateClass(
            $queryClassDetails->getFullName(),
            self::SKELETON_PATH . 'Query.tpl.php',
            [
                'class_name' => $queryClassDetails->getShortName(),
                'namespace' => $this->extractNamespace($queryClassDetails->getFullName()),
            ]
        );

        // Generate Output DTO
        $dtoClassDetails = $generator->createClassNameDetails(
            $outputDtoName,
            'Application\\DTO\\Output\\'
        );

        $generator->generateClass(
            $dtoClassDetails->getFullName(),
            self::SKELETON_PATH . 'OutputDto.tpl.php',
            [
                'class_name' => $dtoClassDetails->getShortName(),
                'namespace' => $this->extractNamespace($dtoClassDetails->getFullName()),
                'query_name' => $baseName,
            ]
        );

        // Generate QueryHandler class
        $handlerClassDetails = $generator->createClassNameDetails(
            $baseName . 'QueryHandler',
            'Application\\Query\\' . $baseName . '\\'
        );

        $generator->generateClass(
            $handlerClassDetails->getFullName(),
            self::SKELETON_PATH . 'QueryHandler.tpl.php',
            [
                'class_name' => $handlerClassDetails->getShortName(),
                'namespace' => $this->extractNamespace($handlerClassDetails->getFullName()),
                'query_class' => $queryClassDetails->getShortName(),
                'query_namespace' => $queryClassDetails->getFullName(),
                'output_dto_class' => $dtoClassDetails->getShortName(),
                'output_dto_namespace' => $dtoClassDetails->getFullName(),
            ]
        );

        // Generate unit test
        $testClassDetails = $generator->createClassNameDetails(
            $baseName . 'QueryHandlerTest',
            'Unit\\Application\\Query\\'
        );

        $generator->generateClass(
            $testClassDetails->getFullName(),
            self::SKELETON_PATH . 'QueryHandlerTest.tpl.php',
            [
                'class_name' => $testClassDetails->getShortName(),
                'namespace' => $this->extractNamespace($testClassDetails->getFullName()),
                'handler_class' => $handlerClassDetails->getShortName(),
                'handler_namespace' => $handlerClassDetails->getFullName(),
                'query_class' => $queryClassDetails->getShortName(),
                'query_namespace' => $queryClassDetails->getFullName(),
                'output_dto_class' => $dtoClassDetails->getShortName(),
                'output_dto_namespace' => $dtoClassDetails->getFullName(),
            ]
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);

        $io->text([
            sprintf('Next: implement <comment>%s::__invoke()</comment> and populate <comment>%s</comment>.', $handlerClassDetails->getShortName(), $dtoClassDetails->getShortName()),
            'Wire repository/service dependencies via constructor injection.',
        ]);
    }

    private function guessOutputDtoName(string $baseName): string
    {
        // GetOrderById → OrderOutput; ListOrders → OrderListOutput
        $name = preg_replace('/^(Get|Find|Fetch|List|Search)/i', '', $baseName) ?: $baseName;
        return $name . 'Output';
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
