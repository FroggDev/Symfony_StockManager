<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * @author Frogg <admin@frogg.fr>
 *
 * @ORM\Entity(repositoryClass="App\Repository\StockProductsRepository")
 */
class StockProducts
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     *
     * @var int
     */
    private $id;

    /**
     * When the product is created
     * @ORM\Column(type="datetime")
     */
    private $dateCreation;

    /**
     * When the product is expired
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $dateExpire;

    /*############
     # RELATIONS #
     ############*/

    /**
     * Many StockProducts have One Stock.
     * @ManyToOne(targetEntity="App\Entity\Stock", inversedBy="stockProducts")
     * @JoinColumn(name="stock_id", referencedColumnName="id")
     */
    private $stock;

    /**
     * Many StockProducts have One Product.
     * @ManyToOne(targetEntity="App\Entity\Product", inversedBy="stockProducts")
     * @JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    /*##############
     # CONSTRUCTOR #
     ##############*/


    public function __construct()
    {
        $this->dateCreation = new \DateTime();
    }

    /*##########
     # METHODS #
     ##########*/

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return StockProducts
     */
    public function setId(int $id): StockProducts
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreation(): \DateTime
    {
        return $this->dateCreation;
    }

    /**
     * @param \DateTime $dateCreation
     *
     * @return StockProducts
     */
    public function setDateCreation($dateCreation): StockProducts
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateExpire(): ?\DateTime
    {
        return $this->dateExpire;
    }

    /**
     * @param \DateTime $dateExpire
     *
     * @return StockProducts
     */
    public function setDateExpire($dateExpire): StockProducts
    {
        $this->dateExpire = $dateExpire;

        return $this;
    }

    /**
     * @return Stock
     */
    public function getStock(): Stock
    {
        return $this->stock;
    }

    /**
     * @param Stock $stock
     *
     * @return StockProducts
     */
    public function setStock($stock): StockProducts
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     *
     * @return StockProducts
     */
    public function setProduct(Product $product): StockProducts
    {
        $this->product = $product;

        return $this;
    }
}
