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
 * Scaffolds a Hexagonal Architecture Domain Policy (Service) with its test skeleton.
 *
 * A Domain Policy encapsulates complex business rules that span beyond a single aggregate.
 * It depends only on Domain ports (repository interfaces) — never on infrastructure.
 *
 * Usage:
 *   php bin/console make:domain:service UserPromotion
 *   → generates: src/Domain/Service/UserPromotionPolicy.php
 *               tests/Unit/Domain/Service/UserPromotionPolicyTest.php
 */
final class MakeDomainService extends AbstractMaker
{
    private const SKELETON_PATH = __DIR__ . '/../Resources/skeleton/domain/';

    public static function getCommandName(): string
    {
        return 'make:domain:service';
    }

    public static function getCommandDescription(): string
    {
        return 'Scaffolds a Domain Policy (service) with a test skeleton showing zero-framework patterns';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument(
                'service-name',
                InputArgument::OPTIONAL,
                'The name of the domain service, without the "Policy" suffix (e.g. <fg=yellow>UserPromotion</> → UserPromotionPolicy)',
            )
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command scaffolds a Domain Policy with its unit test.

<info>php %command.full_name% UserPromotion</info>

This creates:
  - <comment>src/Domain/Service/UserPromotionPolicy.php</comment>   — pure PHP, zero framework imports
  - <comment>tests/Unit/Domain/Service/UserPromotionPolicyTest.php</comment> — in-memory fake pattern

Generated files enforce the Hexagonal Architecture rule:
  <comment>No import from Symfony\, Doctrine\, or ApiPlatform\ inside src/Domain/</comment>
HELP
            );
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        // No additional runtime dependencies required
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $rawName = $input->getArgument('service-name')
            ?? $io->ask('Service name without suffix (e.g. UserPromotion)', null, static function (?string $v): string {
                if (empty($v)) {
                    throw new \InvalidArgumentException('Name cannot be empty.');
                }
                return $v;
            });

        $baseName  = rtrim((string) $rawName, 'Policy');
        $className = $baseName . 'Policy';

        $policyDetails = $generator->createClassNameDetails($className, 'Domain\\Service\\');
        $testDetails   = $generator->createClassNameDetails($className . 'Test', 'tests\\Unit\\Domain\\Service\\');

        $policyFqn = $policyDetails->getFullName();
        $testFqn   = $testDetails->getFullName();

        $generator->generateClass(
            $policyFqn,
            self::SKELETON_PATH . 'DomainPolicy.tpl.php',
            [
                'class_name' => $policyDetails->getShortName(),
                'namespace'  => $this->extractNamespace($policyFqn),
            ],
        );

        $generator->generateClass(
            $testFqn,
            self::SKELETON_PATH . 'DomainPolicyTest.tpl.php',
            [
                'class_name'  => $policyDetails->getShortName(),
                'policy_fqn'  => $policyFqn,
                'entity_name' => $baseName,
            ],
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);

        $io->text([
            'Next steps:',
            sprintf('  1. Define your rules in <comment>%s</comment>', $policyFqn),
            sprintf('  2. Write in-memory fakes in <comment>%s</comment>', $testFqn),
            '  3. Verify: <comment>grep -rn \'use Symfony\\\|use Doctrine\\\' src/Domain/</comment> must return nothing',
        ]);
    }

    private function extractNamespace(string $fqn): string
    {
        $parts = explode('\\', $fqn);
        array_pop($parts);
        return implode('\\', $parts);
    }
}
