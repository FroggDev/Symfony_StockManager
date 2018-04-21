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
     * @param int $stockId
     * @param int $numPage
     * @param string $order
     *
     * @return array
     */
    public function findByGroupedProduct(int $stockId, int $numPage = 1, string $order = 'sp.dateExpire'): array
    {
        //SELECT COUNT(product_id) FROM stock_products where stock_id=1 GROUP BY product_id ORDER BY date_expire DESC

        // check order direction
        $direction = 'DESC';
        if('p.name'===$order){
            $direction = 'ASC';
        }

        // Get the min limit to display
        $limit = ($numPage - 1) * SiteConfig::NBPERPAGE;

        return $this->createQueryBuilder('sp')
            ->select('sp,count(sp.product)')
            ->where('sp.stock = ' . $stockId)
            ->join('sp.product', 'p')
            ->groupBy('sp.product')
            ->orderBy($order, $direction)
            ->setFirstResult($limit)
            ->setMaxResults($limit + SiteConfig::NBPERPAGE)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    public function findExpires(int $productId, int $stockId)
    {
        return $this->createQueryBuilder('sp')
            ->select('sp')
            ->where('sp.product = ' . $productId)
            ->andWhere('sp.stock = ' . $stockId)
            ->orderBy('sp.dateExpire', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $stockId
     * @param int $numPage
     * @param string $order
     *
     * @return array
     */
    public function findForSearch(int $stockId, int $numPage = 1, string $order = 'sp.dateExpire'): array
    {
        //SELECT COUNT(product_id) FROM stock_products where stock_id=1 GROUP BY product_id ORDER BY date_expire DESC

        // check order direction
        $direction = 'DESC';
        if('p.name'===$order){
            $direction = 'ASC';
        }

        // Get the min limit to display
        $limit = ($numPage - 1) * SiteConfig::NBPERPAGE;

        return $this->createQueryBuilder('sp')
            ->select('sp,count(sp.product)')
            ->where('sp.stock = ' . $stockId)
            ->join('sp.product', 'p')
            ->groupBy('sp.product')
            ->orderBy($order, $direction)
            ->setFirstResult($limit)
            ->setMaxResults($limit + SiteConfig::NBPERPAGE)
            ->getQuery()
            ->getResult();
    }

}
