<?php
namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{

    private $allUser;

    /**
     * AuthorRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param bool $forceBase
     * @return array
     */
    public function findAll(bool $forceBase = false): array
    {
        if ($this->allUser || $forceBase) {
            return $this->allUser;
        }

        $this->allUser = $this->createQueryBuilder('u')
            ->orderBy('u.dateInscription', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->allUser;
    }
}
