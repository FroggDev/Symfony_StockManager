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

use App\Entity\Product;
use App\SiteConfig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Frogg <admin@frogg.fr>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{

    /** @var array $allUser store the full list of user */
    private $allProduct;

    /**
     * AuthorRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.name', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $search
     * @param string $numPage
     *
     * @return array
     */
    public function findAddSearch(string $search, string $numPage): array
    {
        // Get the min limit to display
        $limit = ((int)$numPage - 1) * SiteConfig::NBPERPAGE;

        dump($limit);
        dump( SiteConfig::NBPERPAGE);

        //create query
        $query = $this->createQueryBuilder('p')
            ->select('p')
            ->join('p.brands', 'b')
            ->join('p.categories', 'c')
            ->where('p.commonName LIKE :search')
            ->orWhere('p.name LIKE :search')
            ->orWhere('b.name LIKE :search')
            ->orWhere('c.name LIKE :search')
            ->setParameter('search', "%$search%")
            ->setFirstResult($limit)
            //->setMaxResults(SiteConfig::NBPERPAGE)
            ->orderBy('p.name', 'DESC')
            ->getQuery();

        // get products
        $products = $query->getResult();

        // get nb of products
        $nbProducts = 1; //$products->select('COUNT(p)')->getQuery()->getSingleScalarResult();

        return [$nbProducts,$products];

    }
}
