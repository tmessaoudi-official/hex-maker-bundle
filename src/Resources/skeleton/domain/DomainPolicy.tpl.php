<?php echo "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace ?>;

/**
 * <?= $class_name ?> — pure domain policy / decision service.
 *
 * RULES FOR THIS FILE:
 *   - Zero imports from Symfony\, Doctrine\, or any infrastructure layer.
 *   - If you need to query data, inject a Domain port (repository interface) as a
 *     METHOD parameter — not in the constructor. This keeps the policy stateless.
 *   - Return an EligibilityResult (or similar VO) rather than throwing exceptions
 *     or returning plain booleans — callers need to know WHY, not just whether.
 *
 * @example
 *   $result = $policy->check($entity, $repository, new \DateTimeImmutable());
 *   if (!$result->isEligible()) {
 *       throw new \DomainException($result->reason());
 *   }
 */
final class <?= $class_name ?>

{
    public function check(
        // Inject domain models and ports only.
        // Example: MyEntity $entity, MyRepositoryInterface $repository, \DateTimeImmutable $now
    ): bool {
        // Implement your domain rules here.
        // Each rule should return early with a descriptive reason when it fails.
        // The last statement returns true (eligible).
        return true;
    }
}
