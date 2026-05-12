<?php echo "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace ?>;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use App\Domain\Model\<?= $entity_name ?>;
use App\Domain\Model\<?= $entity_name ?>Id;
use App\Domain\Repository\<?= $entity_name ?>RepositoryInterface;
use App\Domain\Exception\<?= $entity_name ?>NotFoundException;

final class <?= $class_name ?> implements <?= $entity_name ?>RepositoryInterface
{
    /** @var EntityRepository<<?= $entity_name ?>> */
    private EntityRepository $repository;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
        $this->repository = $entityManager->getRepository(<?= $entity_name ?>::class);
    }

    public function findById(<?= $entity_name ?>Id $id): ?<?= $entity_name ?>

    {
        return $this->repository->find((string) $id);
    }

    public function getById(<?= $entity_name ?>Id $id): <?= $entity_name ?>

    {
        $entity = $this->findById($id);

        if (null === $entity) {
            throw <?= $entity_name ?>NotFoundException::withId($id);
        }

        return $entity;
    }

    public function save(<?= $entity_name ?> $<?= lcfirst($entity_name) ?>): void
    {
        $this->entityManager->persist($<?= lcfirst($entity_name) ?>);
        $this->entityManager->flush();
    }

    public function delete(<?= $entity_name ?> $<?= lcfirst($entity_name) ?>): void
    {
        $this->entityManager->remove($<?= lcfirst($entity_name) ?>);
        $this->entityManager->flush();
    }

    /**
     * @return <?= $entity_name ?>[]
     */
    public function findAll(): array
    {
        return $this->repository->findAll();
    }
}
