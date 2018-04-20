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
use App\Entity\Stock;
use App\Entity\StockProducts;
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
class StockProductsRepository extends ServiceEntityRepository
{
    /**
     * constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, StockProducts::class);
    }

    /**
     * @param string $order
     * @param int $id stock id
     * @return array
     */
    public function findByGroupedProduct(Stock $stock,int $numPage = 1,string $order = 'sp.dateExpire'): array
    {
        //SELECT COUNT(product_id) FROM stock_products where stock_id=1 GROUP BY product_id ORDER BY date_expire DESC

        // Get the min limit to display
        $limit = ($numPage - 1) * SiteConfig::NBPERPAGE;

        return $this->createQueryBuilder('sp')
            ->select('sp,count(sp.product)')
            ->where('sp.stock = '.$stock->getId())
            ->join('sp.product', 'p')
            ->groupBy('sp.product')
            ->orderBy($order, 'DESC')
            ->setFirstResult($limit)
            ->setMaxResults( $limit + SiteConfig::NBPERPAGE )
            ->getQuery()
            ->getResult();
    }
}
