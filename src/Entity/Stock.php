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
     */
    private $user;

    /**
     * One Stock has Many StockProducts.
     * @OneToMany(targetEntity="App\Entity\StockProducts", mappedBy="stock")
     */
    private $stockProducts;

}