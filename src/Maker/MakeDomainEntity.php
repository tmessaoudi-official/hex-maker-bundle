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
 * Scaffolds a Hexagonal Architecture domain entity with optional value objects,
 * repository interface, and domain exception.
 *
 * Usage:
 *   php bin/console make:domain:entity Order
 */
final class MakeDomainEntity extends AbstractMaker
{
    private const SKELETON_PATH = __DIR__ . '/../Resources/skeleton/domain/';

    public static function getCommandName(): string
    {
        return 'make:domain:entity';
    }

    public static function getCommandDescription(): string
    {
        return 'Scaffolds a domain entity with optional ID value object, repository interface, and domain exception';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument('entity-name', InputArgument::OPTIONAL, 'The name of the domain entity (e.g. <fg=yellow>Order</>)')
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command scaffolds a Hexagonal Architecture domain entity.

<info>php %command.full_name% Order</info>

This creates:
  - <comment>src/Domain/Model/Order.php</comment>
  - <comment>src/Domain/Model/OrderId.php</comment> (optional)
  - <comment>src/Domain/Repository/OrderRepositoryInterface.php</comment> (optional)
  - <comment>src/Domain/Exception/OrderNotFoundException.php</comment> (optional)

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
        $entityName = $input->getArgument('entity-name')
            ?? $io->ask('Entity name (e.g. Order)', null, static function (?string $name): string {
                if (empty($name)) {
                    throw new \InvalidArgumentException('Entity name cannot be empty.');
                }
                return $name;
            });

        $entityName = $this->sanitizeClassName((string) $entityName);

        // Ask for ID value object
        $generateId = $io->confirm(sprintf('Add value object for ID (<comment>%sId</comment>)?', $entityName), true);

        // Ask for extra value objects
        $extraVoInput = $io->ask(
            'Additional value objects? Enter comma-separated field names (e.g. <comment>email,name</comment>), or leave blank',
            ''
        );
        $extraVoNames = $this->parseFieldNames((string) ($extraVoInput ?? ''));

        // Ask for repository interface
        $generateRepository = $io->confirm(sprintf('Add repository interface (<comment>%sRepositoryInterface</comment>)?', $entityName), true);

        // Ask for domain exception
        $generateException = $io->confirm(sprintf('Add domain exception (<comment>%sNotFoundException</comment>)?', $entityName), true);

        $idClass = $generateId ? $entityName . 'Id' : 'string';

        // Generate main entity
        $entityClassDetails = $generator->createClassNameDetails(
            $entityName,
            'Domain\\Model\\'
        );

        $generator->generateClass(
            $entityClassDetails->getFullName(),
            self::SKELETON_PATH . 'Entity.tpl.php',
            [
                'class_name' => $entityClassDetails->getShortName(),
                'namespace' => $entityClassDetails->getFullName() === $entityClassDetails->getShortName()
                    ? 'App\\Domain\\Model'
                    : $this->extractNamespace($entityClassDetails->getFullName()),
                'id_class' => $idClass,
                'generate_id' => $generateId,
                'extra_vos' => $extraVoNames,
            ]
        );

        // Generate ID value object
        if ($generateId) {
            $idClassDetails = $generator->createClassNameDetails(
                $entityName . 'Id',
                'Domain\\Model\\'
            );

            $generator->generateClass(
                $idClassDetails->getFullName(),
                self::SKELETON_PATH . 'EntityId.tpl.php',
                [
                    'class_name' => $idClassDetails->getShortName(),
                    'namespace' => $this->extractNamespace($idClassDetails->getFullName()),
                    'entity_name' => $entityName,
                ]
            );
        }

        // Generate extra value objects
        foreach ($extraVoNames as $fieldName) {
            $voClassDetails = $generator->createClassNameDetails(
                ucfirst($fieldName),
                'Domain\\Model\\'
            );

            $generator->generateClass(
                $voClassDetails->getFullName(),
                self::SKELETON_PATH . 'ValueObject.tpl.php',
                [
                    'class_name' => $voClassDetails->getShortName(),
                    'namespace' => $this->extractNamespace($voClassDetails->getFullName()),
                    'field_name' => $fieldName,
                    'entity_name' => $entityName,
                ]
            );
        }

        // Generate repository interface
        if ($generateRepository) {
            $repositoryClassDetails = $generator->createClassNameDetails(
                $entityName . 'RepositoryInterface',
                'Domain\\Repository\\'
            );

            $generator->generateClass(
                $repositoryClassDetails->getFullName(),
                self::SKELETON_PATH . 'RepositoryInterface.tpl.php',
                [
                    'class_name' => $repositoryClassDetails->getShortName(),
                    'namespace' => $this->extractNamespace($repositoryClassDetails->getFullName()),
                    'entity_name' => $entityName,
                    'entity_namespace' => 'App\\Domain\\Model\\' . $entityName,
                    'id_class' => $idClass,
                    'id_namespace' => $generateId ? 'App\\Domain\\Model\\' . $entityName . 'Id' : null,
                ]
            );
        }

        // Generate domain exception
        if ($generateException) {
            $exceptionClassDetails = $generator->createClassNameDetails(
                $entityName . 'NotFoundException',
                'Domain\\Exception\\'
            );

            $generator->generateClass(
                $exceptionClassDetails->getFullName(),
                self::SKELETON_PATH . 'EntityNotFoundException.tpl.php',
                [
                    'class_name' => $exceptionClassDetails->getShortName(),
                    'namespace' => $this->extractNamespace($exceptionClassDetails->getFullName()),
                    'entity_name' => $entityName,
                    'id_class' => $idClass,
                    'id_namespace' => $generateId ? 'App\\Domain\\Model\\' . $entityName . 'Id' : null,
                ]
            );
        }

        $generator->writeChanges();

        $this->writeSuccessMessage($io);

        $io->text([
            'Next: edit the generated files to add your domain logic.',
            sprintf('Add your infrastructure repository implementing <comment>%sRepositoryInterface</comment>.', $entityName),
        ]);
    }

    private function sanitizeClassName(string $name): string
    {
        // Strip leading/trailing slashes, convert separators to PascalCase fragments
        $name = trim($name, '/\\');
        $parts = preg_split('/[\/\\\\]/', $name) ?: [$name];

        return implode('', array_map(
            static fn (string $part) => ucfirst($part),
            $parts
        ));
    }

    /**
     * @return string[]
     */
    private function parseFieldNames(string $input): array
    {
        if (trim($input) === '') {
            return [];
        }

        return array_filter(
            array_map(
                static fn (string $f) => trim($f),
                explode(',', $input)
            ),
            static fn (string $f) => $f !== ''
        );
    }

    private function extractNamespace(string $fullyQualifiedName): string
    {
        $parts = explode('\\', $fullyQualifiedName);
        array_pop($parts); // remove class name

        return implode('\\', $parts);
    }
}
