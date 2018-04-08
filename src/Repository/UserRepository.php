<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Frogg <admin@frogg.fr>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{

    /** @var array $allUser store the full list of user */
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
     *
     * @return array
     */
    public function findAll(bool $forceBase = false): array
    {
        if ($this->allUser && $forceBase === false) {
            return $this->allUser;
        }

        $this->allUser = $this->createQueryBuilder('u')
            ->orderBy('u.dateInscription', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->allUser;
    }
}
