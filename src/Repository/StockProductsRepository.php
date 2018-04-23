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
use App\Entity\StockProducts;
use App\SiteConfig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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
     * @param int      $productId
     * @param int      $stockId
     * @param int|null $inDay
     *
     * @return array
     */
    public function findDateExpires(int $productId, int $stockId, ?int $inDay = null)
    {
        $orderDirection = $this->getOrderDirection('sp.dateExpire');

        //prepare query
        $query = $this->createQueryBuilder('sp')
            ->select('sp')
            ->where('sp.product = '.$productId)
            ->andWhere('sp.stock = '.$stockId)
            ->orderBy($orderDirection['order'], $orderDirection['direction']);

        return $this->applyFilter($query, $inDay)->getQuery()->getResult();
    }

    /**
     * @param int    $stockId
     * @param int    $numPage
     * @param string $order
     *
     * @return array
     */
    /*
    public function findByGroupedProduct(int $stockId, int $numPage = 1, string $order = '0'): array
    {
        //SELECT COUNT(product_id) FROM stock_products where stock_id=1 GROUP BY product_id ORDER BY date_expire DESC

        $orderDirection = $this->getOrderDirection($order);

        $products = $this->createQueryBuilder('sp')
            ->select('sp,count(sp.product)')
            ->where('sp.stock = '.$stockId)
            ->join('sp.product', 'p')
            ->groupBy('sp.product')
            ->orderBy($orderDirection['order'], $orderDirection['direction'])
            ->getQuery()
            ->getResult();

        $nbExpired = $this->getNbProductExpire($products);

        return [
            count($products),
            array_slice(
                $products,
                $this->getLimit($numPage),
                SiteConfig::NBPERPAGE
            ),
            $nbExpired,
        ];
    }
    */

    /**
     * @param int         $stockId
     * @param null|string $inDay
     * @param int         $numPage
     * @param string      $order
     *
     * @return array
     *
     * TODO CHANGE ARGUMENTS AS AN OBJECT WITH DEFAULT VALUES
     *
     * @see http://doctrine-orm.readthedocs.io/en/latest/reference/dql-doctrine-query-language.html#id3
     */
    public function findList(int $stockId, ?string $inDay, int $numPage = 1, string $order = '0', int $productId = null, string $search = null, bool $fullList = false)
    {
        $orderDirection = $this->getOrderDirection($order);

        /**
         * TODO ADD  min(sp.dateExpire); / max(sp.dateCreation)
         * to have the good order date
         */
        $query = $this->createQueryBuilder('sp')
            ->select('sp,count(sp.product)')
            ->Where('sp.stock = '.$stockId)
            ->join('sp.product', 'p')
            ->groupBy('sp.product')
            ->orderBy($orderDirection['order'], $orderDirection['direction']);

        if (null!==$productId) {
            $query->andWhere('sp.product = '.$productId);
        }

        if (null!==$search && ""!==$search) {
            $query
                ->join('p.brands', 'b')
                //->join('p.categories', 'c')
                ->where('p.commonName LIKE :search')
                ->orWhere('p.name LIKE :search')
                ->orWhere('b.name LIKE :search')
                //->orWhere('c.name LIKE :search')
                ->setParameter('search', "%$search%");
        }

        $products = $this->applyFilter($query, $inDay)->getQuery()->getResult();

        $nbExpired = $this->getNbProductExpire($products);

        return [
            count($products),
            $fullList ? $products :array_slice($products, $this->getLimit($numPage), SiteConfig::NBPERPAGE),
            $nbExpired,
        ];
    }

    /*###########
     # PRIVATES #
     ###########*/

    /**
     * @param string $order
     *
     * @return array
     */
    private function getOrderDirection(string $order) : array
    {
        // get the selected request order
        switch ($order) {
            case '2':
                $order = 'sp.dateCreation';
                $direction = 'DESC';
                break;
            case '3':
                $order = 'p.name';
                $direction = 'ASC';
                break;
            default:
                $order = 'sp.dateExpire';
                $direction = 'ASC';
        }


        return ['order'=>$order,'direction'=>$direction];
    }

    /**
     * @param int $numPage
     *
     * @return int
     */
    private function getLimit(int $numPage)
    {
        // Get the min limit to display
        return ($numPage - 1) * SiteConfig::NBPERPAGE;
    }

    /**
     * @param array $expired
     *
     * @return int
     */
    private function getNbProductExpire(array $expired)
    {
        //init nb expired
        $nbExpired = 0;

        // add each expired in array
        foreach ($expired as $v) {
            $nbExpired += $v[1];
        }

        return $nbExpired;
    }

    /**
     * @param QueryBuilder $query
     * @param string|null  $inDay
     *
     * @return QueryBuilder
     */
    private function applyFilter(QueryBuilder $query, ?string $inDay): QueryBuilder
    {
        switch ($inDay) {
            case '0':
                $query
                    ->andWhere('DATE_DIFF(sp.dateExpire,CURRENT_DATE()) < 0')
                    ->andWhere('sp.dateExpire IS NOT NULL');
                break;
            case '3':
                $query
                    ->andWhere('DATE_DIFF(sp.dateExpire,CURRENT_DATE()) >= 0')
                    ->andWhere('DATE_DIFF(sp.dateExpire,CURRENT_DATE()) <= 3')
                    ->andWhere('sp.dateExpire IS NOT NULL');
                break;
            case '7':
                $query
                    ->andWhere('DATE_DIFF( sp.dateExpire,CURRENT_DATE()) > 3')
                    ->andWhere('DATE_DIFF( sp.dateExpire,CURRENT_DATE()) <= 7')
                    ->andWhere('sp.dateExpire IS NOT NULL');
                break;
        }

        return $query;
    }
}
