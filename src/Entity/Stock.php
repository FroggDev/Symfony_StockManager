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
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;

/**
 * @author Frogg <admin@frogg.fr>
 *
 * @ORM\Entity()
 */
class Stock
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     *
     * @var int
     */
    private $id;

    /*############
     # RELATIONS #
     ############*/

    /**
     * @OneToOne(targetEntity="App\Entity\User", mappedBy="stock")
     * @var User
     */
    private $user;

    /**
     * One Stock has Many StockProducts.
     * @OneToMany(targetEntity="App\Entity\StockProducts", mappedBy="stock")
     * @var StockProducts
     */
    private $stockProducts;

    /*################
     # GETTER/SETTER #
     ################*/


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Stock
     */
    public function setId(int $id): Stock
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return Stock
     */
    public function setUser(User $user): Stock
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return StockProducts
     */
    public function getStockProducts(): StockProducts
    {
        return $this->stockProducts;
    }

    /**
     * @param StockProducts $stockProducts
     * @return Stock
     */
    public function setStockProducts(StockProducts $stockProducts): Stock
    {
        $this->stockProducts = $stockProducts;

        return $this;
    }
}
