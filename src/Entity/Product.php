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
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Frogg <admin@frogg.fr>
 *
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 *
 * @UniqueEntity(fields={"barcode"},errorPath="barcode",message="barcode already in database")
 *
 * Unique entity:
 * @see https://symfony.com/doc/current/reference/constraints/UniqueEntity.html
 *
 * Validator translation:
 * @see https://symfony.com/doc/current/validation/translations.html
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="integer", unique=true)
     * @Assert\Length(max=13,maxMessage="barcode is too long")
     * @Assert\NotBlank(message="barcode should not be blank")
     *
     * @var int
     */
    private $barcode;

    /**
     * @ORM\Column(type="string", length=100,nullable=true)
     *
     * @var string
     */
    private $embCode;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\Length(max=150,maxMessage="product name is too long")
     * @Assert\NotBlank(message="product name should not be blank")
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\Length(max=150,maxMessage="product common name is too long")
     *
     * @var string
     */
    private $commonName;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $dateCreation;

    /**
     * @ORM\Column(type="string", length=100,nullable=true)
     *
     * @var string
     */
    private $quantity;

    /**
     * @ORM\Column(type="string", length=100,nullable=true)
     *
     * @var string
     */
    private $picture;

    /**
     * Labels, certifications, award
     * @var
     */
    private $certifications;

    /**
     * @ORM\Column(type="string", length=150,nullable=true)
     *
     * @var string
     */
    private $producerPage;

    /*############
     # RELATIONS #
     ############*/

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User",inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userCreation;

    /**
     * Contries where sold
     * @ManyToMany(targetEntity="App\Entity\Country")
     * @JoinTable(name="products_countries",
     *      joinColumns={@JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="country_id", referencedColumnName="id")}
     *      )
     */
    private $countries;

    /*####################
     # RELATIONS PRODUCT #
     ####################*/

    /**
     * Ingredients origin
     * @ManyToMany(targetEntity="App\Entity\Product\Origin")
     * @JoinTable(name="products_origins",
     *      joinColumns={@JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="origin_id", referencedColumnName="id")}
     *      )
     */
    private $origins;

    /**
     * Manufacturing or processing place
     * @ManyToMany(targetEntity="App\Entity\Product\Place")
     * @JoinTable(name="products_places",
     *      joinColumns={@JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="place_id", referencedColumnName="id")}
     *      )
     */
    private $places;

    /**
     * Product packaging
     * @ManyToMany(targetEntity="App\Entity\Product\Packaging")
     * @JoinTable(name="products_packagings",
     *      joinColumns={@JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="packaging_id", referencedColumnName="id")}
     *      )
     */
    private $packagings;


    /**
     * Product categories
     * @ManyToMany(targetEntity="App\Entity\Product\Category")
     * @JoinTable(name="products_categories",
     *      joinColumns={@JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="category_id", referencedColumnName="id")}
     *      )
     */
    private $categories;

    /**
     * Product brands
     * @ManyToMany(targetEntity="App\Entity\Product\Brand")
     * @JoinTable(name="products_brands",
     *      joinColumns={@JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="brand_id", referencedColumnName="id")}
     *      )
     */
    private $brands;

    /**
     * Substances or products causing allergies or intolerances
     * @ManyToMany(targetEntity="App\Entity\Product\Alergy")
     * @JoinTable(name="products_alergies",
     *      joinColumns={@JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="alergy_id", referencedColumnName="id")}
     *      )
     */
    private $alergies;

    /**
     * Substances traces
     * @ManyToMany(targetEntity="App\Entity\Product\Trace")
     * @JoinTable(name="products_traces",
     *      joinColumns={@JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="trace_id", referencedColumnName="id")}
     *      )
     */
    private $traces;

    /**
     * Additive
     * @ManyToMany(targetEntity="App\Entity\Product\Additive")
     * @JoinTable(name="products_additives",
     *      joinColumns={@JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="additive_id", referencedColumnName="id")}
     *      )
     */
    private $additives;

    /*##############
     # INGREDIENTS #
     ##############*/

    /**
     * @ORM\Column(type="string", length=100,nullable=true)
     *
     * @var string
     */
    private $ingredientPicture;

    /**
     * @ORM\Column(type="string", length=100,nullable=true)
     *
     * @var string
     */
    private $ingredients;

    /*############
     # NUTRITION #
     ############*/

    /**
     * @ORM\Column(type="string", length=100,nullable=true)
     *
     * @var string
     */
    private $nutritionPicture;

    /**
     * @ORM\Column(type="string", length=1, columnDefinition="CHAR(1)",nullable=true)
     *
     * @var string (char)
     */
    private $nutriscore;

    /**
     * @ORM\Column(type="string", length=100,nullable=true)
     *
     * @var string
     */
    private $servingSize;

    /**
     * @ORM\Column(type="float",nullable=true)
     *
     * @var float
     */
    private $energy;
    /**
     * @ORM\Column(type="string", length=2, columnDefinition="CHAR(2)",nullable=true)
     *
     * @var string
     */
    private $energyUnit;

    /**
     * @ORM\Column(type="float",nullable=true)
     *
     * @var float
     */
    private $levelFat;
    /**
     * @ORM\Column(type="string", length=2, columnDefinition="CHAR(2)",nullable=true)
     *
     * @var string
     */
    private $levelFatUnit;

    /**
     * @ORM\Column(type="float",nullable=true)
     *
     * @var float
     */
    private $levelSaturedFat;
    /**
     * @ORM\Column(type="string", length=2, columnDefinition="CHAR(2)",nullable=true)
     *
     * @var string
     */
    private $levelSaturedUnit;

    /**
     * @ORM\Column(type="float",nullable=true)
     *
     * @var float
     */
    private $levelCarbohydrate;
    /**
     * @ORM\Column(type="string", length=2, columnDefinition="CHAR(2)",nullable=true)
     *
     * @var string
     */
    private $levelCarbohydrateUnit;

    /**
     * @ORM\Column(type="float",nullable=true)
     *
     * @var float
     */
    private $levelSugar;
    /**
     * @ORM\Column(type="string", length=2, columnDefinition="CHAR(2)",nullable=true)
     *
     * @var string
     */
    private $levelSugarUnit;

    /**
     * @ORM\Column(type="float",nullable=true)
     *
     * @var float
     */
    private $levelDietaryFiber;
    /**
     * @ORM\Column(type="string", length=2, columnDefinition="CHAR(2)",nullable=true)
     *
     * @var string
     */
    private $levelDietaryFiberUnit;

    /**
     * @ORM\Column(type="float",nullable=true)
     *
     * @var float
     */
    private $levelProteins;
    /**
     * @ORM\Column(type="string", length=2, columnDefinition="CHAR(2)",nullable=true)
     *
     * @var string
     */
    private $levelProteinsUnit;

    /**
     * @ORM\Column(type="float",nullable=true)
     *
     * @var float
     */
    private $levelSalt;
    /**
     * @ORM\Column(type="string", length=2, columnDefinition="CHAR(2)",nullable=true)
     *
     * @var string
     */
    private $levelSaltUnit;

    /**
     * @ORM\Column(type="float",nullable=true)
     *
     * @var float
     */
    private $levelSodium;
    /**
     * @ORM\Column(type="string", length=2, columnDefinition="CHAR(2)",nullable=true)
     *
     * @var string
     */
    private $levelSodiumUnit;

    /**
     * @ORM\Column(type="float",nullable=true)
     *
     * @var float
     */
    private $levelAlcohol;

    /*########
     # OTHER #
     ########*/

    /** @var float */
    private $footprint;
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=2, columnDefinition="CHAR(2)",nullable=true)
     */
    private $footprintUnit;
}