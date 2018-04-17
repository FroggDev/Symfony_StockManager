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
     * @ORM\Column(type="bigint")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="bigint", unique=true)
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
    private $user;

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
     * Labels, certifications, award
     * @ManyToMany(targetEntity="App\Entity\Product\Certification",cascade={"persist"})
     * @JoinTable(name="products_certifications",
     *      joinColumns={@JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="certification_id", referencedColumnName="id")}
     *      )
     */
    private $certifications;

    /**
     * Ingredients origin
     * @ManyToMany(targetEntity="App\Entity\Product\Origin",cascade={"persist"})
     * @JoinTable(name="products_origins",
     *      joinColumns={@JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="origin_id", referencedColumnName="id")}
     *      )
     */
    private $origins;

    /**
     * Manufacturing or processing place
     * @ManyToMany(targetEntity="App\Entity\Product\Place",cascade={"persist"})
     * @JoinTable(name="products_places",
     *      joinColumns={@JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="place_id", referencedColumnName="id")}
     *      )
     */
    private $places;

    /**
     * Product packaging
     * @ManyToMany(targetEntity="App\Entity\Product\Packaging",cascade={"persist"})
     * @JoinTable(name="products_packagings",
     *      joinColumns={@JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="packaging_id", referencedColumnName="id")}
     *      )
     */
    private $packagings;


    /**
     * Product categories
     * @ManyToMany(targetEntity="App\Entity\Product\Category",cascade={"persist"})
     * @JoinTable(name="products_categories",
     *      joinColumns={@JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="category_id", referencedColumnName="id")}
     *      )
     */
    private $categories;

    /**
     * Product brands
     * @ManyToMany(targetEntity="App\Entity\Product\Brand",cascade={"persist"})
     * @JoinTable(name="products_brands",
     *      joinColumns={@JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="brand_id", referencedColumnName="id")}
     *      )
     */
    private $brands;

    /**
     * Substances or products causing allergies or intolerances
     * @ManyToMany(targetEntity="App\Entity\Product\Alergy",cascade={"persist"})
     * @JoinTable(name="products_alergies",
     *      joinColumns={@JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="alergy_id", referencedColumnName="id")}
     *      )
     */
    private $alergies;

    /**
     * Substances traces
     * @ManyToMany(targetEntity="App\Entity\Product\Trace",cascade={"persist"})
     * @JoinTable(name="products_traces",
     *      joinColumns={@JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="trace_id", referencedColumnName="id")}
     *      )
     */
    private $traces;

    /**
     * Additive
     * @ManyToMany(targetEntity="App\Entity\Product\Additive",cascade={"persist"})
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
     * @ORM\Column(type="string", length=2, columnDefinition="CHAR(5)",nullable=true)
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
    private $levelSaturedFatUnit;

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

    /**
     * @ORM\Column(type="float",nullable=true)
     *
     * @var float
     */
    private $levelSilica;
    /**
     * @ORM\Column(type="string", length=2, columnDefinition="CHAR(2)",nullable=true)
     *
     * @var string
     */
    private $levelSilicaUnit;

    /**
     * @ORM\Column(type="float",nullable=true)
     *
     * @var float
     */
    private $levelBicarbonate;
    /**
     * @ORM\Column(type="string", length=2, columnDefinition="CHAR(2)",nullable=true)
     *
     * @var string
     */
    private $levelBicarbonateUnit;

    /**
     * @ORM\Column(type="float",nullable=true)
     *
     * @var float
     */
    private $levelPotassium;
    /**
     * @ORM\Column(type="string", length=2, columnDefinition="CHAR(2)",nullable=true)
     *
     * @var string
     */
    private $levelPotassiumUnit;

    /**
     * @ORM\Column(type="float",nullable=true)
     *
     * @var float
     */
    private $levelChloride;
    /**
     * @ORM\Column(type="string", length=2, columnDefinition="CHAR(2)",nullable=true)
     *
     * @var string
     */
    private $levelChlorideUnit;

    /**
     * @ORM\Column(type="float",nullable=true)
     *
     * @var float
     */
    private $levelCalcium;
    /**
     * @ORM\Column(type="string", length=2, columnDefinition="CHAR(2)",nullable=true)
     *
     * @var string
     */
    private $levelCalciumUnit;

    /**
     * @ORM\Column(type="float",nullable=true)
     *
     * @var float
     */
    private $levelMagnesium;
    /**
     * @ORM\Column(type="string", length=2, columnDefinition="CHAR(2)",nullable=true)
     *
     * @var string
     */
    private $levelMagnesiumUnit;

    /**
     * @ORM\Column(type="float",nullable=true)
     *
     * @var float
     */
    private $levelNitrates;
    /**
     * @ORM\Column(type="string", length=2, columnDefinition="CHAR(2)",nullable=true)
     *
     * @var string
     */
    private $levelNitratesUnit;

    /**
     * @ORM\Column(type="float",nullable=true)
     *
     * @var float
     */
    private $levelSulfates;
    /**
     * @ORM\Column(type="string", length=2, columnDefinition="CHAR(2)",nullable=true)
     *
     * @var string
     */
    private $levelSulfatesUnit;


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


    /*##############
     # CONSTRUCTOR #
     ##############*/


    /**
     * Product constructor.
     * @param null|int $barcode
     */
    public function __construct(?int $barcode=null)
    {
        $this->dateCreation = new \DateTime();

        if($barcode){
            $this->barcode = $barcode;
        }
    }


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
     * @return Product
     */
    public function setId(int $id): Product
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getBarcode(): int
    {
        return $this->barcode;
    }

    /**
     * @param int $barcode
     * @return Product
     */
    public function setBarcode(int $barcode): Product
    {
        $this->barcode = $barcode;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmbCode(): string
    {
        return $this->embCode;
    }

    /**
     * @param string $embCode
     * @return Product
     */
    public function setEmbCode(string $embCode): Product
    {
        $this->embCode = $embCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Product
     */
    public function setName(string $name): Product
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getCommonName(): string
    {
        return $this->commonName;
    }

    /**
     * @param string $commonName
     * @return Product
     */
    public function setCommonName(string $commonName): Product
    {
        $this->commonName = $commonName;
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
     * @return Product
     */
    public function setDateCreation(\DateTime $dateCreation): Product
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    /**
     * @return string
     */
    public function getQuantity(): string
    {
        return $this->quantity;
    }

    /**
     * @param string $quantity
     * @return Product
     */
    public function setQuantity(string $quantity): Product
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return string
     */
    public function getPicture(): string
    {
        return $this->picture;
    }

    /**
     * @param string $picture
     * @return Product
     */
    public function setPicture(string $picture): Product
    {
        $this->picture = $picture;
        return $this;
    }

    /**
     * @return string
     */
    public function getProducerPage(): string
    {
        return $this->producerPage;
    }

    /**
     * @param string $producerPage
     * @return Product
     */
    public function setProducerPage(string $producerPage): Product
    {
        $this->producerPage = $producerPage;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     * @return Product
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCountries()
    {
        return $this->countries;
    }

    /**
     * @param mixed $countries
     * @return Product
     */
    public function setCountries($countries)
    {
        $this->countries = $countries;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCertifications()
    {
        return $this->certifications;
    }

    /**
     * @param mixed $certifications
     * @return Product
     */
    public function setCertifications($certifications)
    {
        $this->certifications = $certifications;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrigins()
    {
        return $this->origins;
    }

    /**
     * @param mixed $origins
     * @return Product
     */
    public function setOrigins($origins)
    {
        $this->origins = $origins;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlaces()
    {
        return $this->places;
    }

    /**
     * @param mixed $places
     * @return Product
     */
    public function setPlaces($places)
    {
        $this->places = $places;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPackagings()
    {
        return $this->packagings;
    }

    /**
     * @param mixed $packagings
     * @return Product
     */
    public function setPackagings($packagings)
    {
        $this->packagings = $packagings;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param mixed $categories
     * @return Product
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBrands()
    {
        return $this->brands;
    }

    /**
     * @param mixed $brands
     * @return Product
     */
    public function setBrands($brands)
    {
        $this->brands = $brands;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAlergies()
    {
        return $this->alergies;
    }

    /**
     * @param mixed $alergies
     * @return Product
     */
    public function setAlergies($alergies)
    {
        $this->alergies = $alergies;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTraces()
    {
        return $this->traces;
    }

    /**
     * @param mixed $traces
     * @return Product
     */
    public function setTraces($traces)
    {
        $this->traces = $traces;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAdditives()
    {
        return $this->additives;
    }

    /**
     * @param mixed $additives
     * @return Product
     */
    public function setAdditives($additives)
    {
        $this->additives = $additives;
        return $this;
    }

    /**
     * @return string
     */
    public function getIngredientPicture(): string
    {
        return $this->ingredientPicture;
    }

    /**
     * @param string $ingredientPicture
     * @return Product
     */
    public function setIngredientPicture(string $ingredientPicture): Product
    {
        $this->ingredientPicture = $ingredientPicture;
        return $this;
    }

    /**
     * @return string
     */
    public function getIngredients(): string
    {
        return $this->ingredients;
    }

    /**
     * @param string $ingredients
     * @return Product
     */
    public function setIngredients(string $ingredients): Product
    {
        $this->ingredients = $ingredients;
        return $this;
    }

    /**
     * @return string
     */
    public function getNutritionPicture(): string
    {
        return $this->nutritionPicture;
    }

    /**
     * @param string $nutritionPicture
     * @return Product
     */
    public function setNutritionPicture(string $nutritionPicture): Product
    {
        $this->nutritionPicture = $nutritionPicture;
        return $this;
    }

    /**
     * @return string
     */
    public function getNutriscore(): string
    {
        return $this->nutriscore;
    }

    /**
     * @param string $nutriscore
     * @return Product
     */
    public function setNutriscore(string $nutriscore): Product
    {
        $this->nutriscore = $nutriscore;
        return $this;
    }

    /**
     * @return string
     */
    public function getServingSize(): string
    {
        return $this->servingSize;
    }

    /**
     * @param string $servingSize
     * @return Product
     */
    public function setServingSize(string $servingSize): Product
    {
        $this->servingSize = $servingSize;
        return $this;
    }

    /**
     * @return float
     */
    public function getEnergy(): float
    {
        return $this->energy;
    }

    /**
     * @param float $energy
     * @return Product
     */
    public function setEnergy(float $energy): Product
    {
        $this->energy = $energy;
        return $this;
    }

    /**
     * @return string
     */
    public function getEnergyUnit(): string
    {
        return $this->energyUnit;
    }

    /**
     * @param string $energyUnit
     * @return Product
     */
    public function setEnergyUnit(string $energyUnit): Product
    {
        $this->energyUnit = $energyUnit;
        return $this;
    }

    /**
     * @return float
     */
    public function getLevelFat(): float
    {
        return $this->levelFat;
    }

    /**
     * @param float $levelFat
     * @return Product
     */
    public function setLevelFat(float $levelFat): Product
    {
        $this->levelFat = $levelFat;
        return $this;
    }

    /**
     * @return string
     */
    public function getLevelFatUnit(): string
    {
        return $this->levelFatUnit;
    }

    /**
     * @param string $levelFatUnit
     * @return Product
     */
    public function setLevelFatUnit(string $levelFatUnit): Product
    {
        $this->levelFatUnit = $levelFatUnit;
        return $this;
    }

    /**
     * @return float
     */
    public function getLevelSaturedFat(): float
    {
        return $this->levelSaturedFat;
    }

    /**
     * @param float $levelSaturedFat
     * @return Product
     */
    public function setLevelSaturedFat(float $levelSaturedFat): Product
    {
        $this->levelSaturedFat = $levelSaturedFat;
        return $this;
    }

    /**
     * @return string
     */
    public function getLevelSaturedFatUnit(): string
    {
        return $this->levelSaturedFatUnit;
    }

    /**
     * @param string $levelSaturedFatUnit
     * @return Product
     */
    public function setLevelSaturedFatUnit(string $levelSaturedFatUnit): Product
    {
        $this->levelSaturedFatUnit = $levelSaturedFatUnit;
        return $this;
    }

    /**
     * @return float
     */
    public function getLevelCarbohydrate(): float
    {
        return $this->levelCarbohydrate;
    }

    /**
     * @param float $levelCarbohydrate
     * @return Product
     */
    public function setLevelCarbohydrate(float $levelCarbohydrate): Product
    {
        $this->levelCarbohydrate = $levelCarbohydrate;
        return $this;
    }

    /**
     * @return string
     */
    public function getLevelCarbohydrateUnit(): string
    {
        return $this->levelCarbohydrateUnit;
    }

    /**
     * @param string $levelCarbohydrateUnit
     * @return Product
     */
    public function setLevelCarbohydrateUnit(string $levelCarbohydrateUnit): Product
    {
        $this->levelCarbohydrateUnit = $levelCarbohydrateUnit;
        return $this;
    }

    /**
     * @return float
     */
    public function getLevelSugar(): float
    {
        return $this->levelSugar;
    }

    /**
     * @param float $levelSugar
     * @return Product
     */
    public function setLevelSugar(float $levelSugar): Product
    {
        $this->levelSugar = $levelSugar;
        return $this;
    }

    /**
     * @return string
     */
    public function getLevelSugarUnit(): string
    {
        return $this->levelSugarUnit;
    }

    /**
     * @param string $levelSugarUnit
     * @return Product
     */
    public function setLevelSugarUnit(string $levelSugarUnit): Product
    {
        $this->levelSugarUnit = $levelSugarUnit;
        return $this;
    }

    /**
     * @return float
     */
    public function getLevelDietaryFiber(): float
    {
        return $this->levelDietaryFiber;
    }

    /**
     * @param float $levelDietaryFiber
     * @return Product
     */
    public function setLevelDietaryFiber(float $levelDietaryFiber): Product
    {
        $this->levelDietaryFiber = $levelDietaryFiber;
        return $this;
    }

    /**
     * @return string
     */
    public function getLevelDietaryFiberUnit(): string
    {
        return $this->levelDietaryFiberUnit;
    }

    /**
     * @param string $levelDietaryFiberUnit
     * @return Product
     */
    public function setLevelDietaryFiberUnit(string $levelDietaryFiberUnit): Product
    {
        $this->levelDietaryFiberUnit = $levelDietaryFiberUnit;
        return $this;
    }

    /**
     * @return float
     */
    public function getLevelProteins(): float
    {
        return $this->levelProteins;
    }

    /**
     * @param float $levelProteins
     * @return Product
     */
    public function setLevelProteins(float $levelProteins): Product
    {
        $this->levelProteins = $levelProteins;
        return $this;
    }

    /**
     * @return string
     */
    public function getLevelProteinsUnit(): string
    {
        return $this->levelProteinsUnit;
    }

    /**
     * @param string $levelProteinsUnit
     * @return Product
     */
    public function setLevelProteinsUnit(string $levelProteinsUnit): Product
    {
        $this->levelProteinsUnit = $levelProteinsUnit;
        return $this;
    }

    /**
     * @return float
     */
    public function getLevelSalt(): float
    {
        return $this->levelSalt;
    }

    /**
     * @param float $levelSalt
     * @return Product
     */
    public function setLevelSalt(float $levelSalt): Product
    {
        $this->levelSalt = $levelSalt;
        return $this;
    }

    /**
     * @return string
     */
    public function getLevelSaltUnit(): string
    {
        return $this->levelSaltUnit;
    }

    /**
     * @param string $levelSaltUnit
     * @return Product
     */
    public function setLevelSaltUnit(string $levelSaltUnit): Product
    {
        $this->levelSaltUnit = $levelSaltUnit;
        return $this;
    }

    /**
     * @return float
     */
    public function getLevelSodium(): float
    {
        return $this->levelSodium;
    }

    /**
     * @param float $levelSodium
     * @return Product
     */
    public function setLevelSodium(float $levelSodium): Product
    {
        $this->levelSodium = $levelSodium;
        return $this;
    }

    /**
     * @return string
     */
    public function getLevelSodiumUnit(): string
    {
        return $this->levelSodiumUnit;
    }

    /**
     * @param string $levelSodiumUnit
     * @return Product
     */
    public function setLevelSodiumUnit(string $levelSodiumUnit): Product
    {
        $this->levelSodiumUnit = $levelSodiumUnit;
        return $this;
    }

    /**
     * @return float
     */
    public function getLevelAlcohol(): float
    {
        return $this->levelAlcohol;
    }

    /**
     * @param float $levelAlcohol
     * @return Product
     */
    public function setLevelAlcohol(float $levelAlcohol): Product
    {
        $this->levelAlcohol = $levelAlcohol;
        return $this;
    }

    /**
     * @return float
     */
    public function getLevelSilica(): float
    {
        return $this->levelSilica;
    }

    /**
     * @param float $levelSilica
     * @return Product
     */
    public function setLevelSilica(float $levelSilica): Product
    {
        $this->levelSilica = $levelSilica;
        return $this;
    }

    /**
     * @return string
     */
    public function getLevelSilicaUnit(): string
    {
        return $this->levelSilicaUnit;
    }

    /**
     * @param string $levelSilicaUnit
     * @return Product
     */
    public function setLevelSilicaUnit(string $levelSilicaUnit): Product
    {
        $this->levelSilicaUnit = $levelSilicaUnit;
        return $this;
    }

    /**
     * @return float
     */
    public function getLevelBicarbonate(): float
    {
        return $this->levelBicarbonate;
    }

    /**
     * @param float $levelBicarbonate
     * @return Product
     */
    public function setLevelBicarbonate(float $levelBicarbonate): Product
    {
        $this->levelBicarbonate = $levelBicarbonate;
        return $this;
    }

    /**
     * @return string
     */
    public function getLevelBicarbonateUnit(): string
    {
        return $this->levelBicarbonateUnit;
    }

    /**
     * @param string $levelBicarbonateUnit
     * @return Product
     */
    public function setLevelBicarbonateUnit(string $levelBicarbonateUnit): Product
    {
        $this->levelBicarbonateUnit = $levelBicarbonateUnit;
        return $this;
    }

    /**
     * @return float
     */
    public function getLevelPotassium(): float
    {
        return $this->levelPotassium;
    }

    /**
     * @param float $levelPotassium
     * @return Product
     */
    public function setLevelPotassium(float $levelPotassium): Product
    {
        $this->levelPotassium = $levelPotassium;
        return $this;
    }

    /**
     * @return string
     */
    public function getLevelPotassiumUnit(): string
    {
        return $this->levelPotassiumUnit;
    }

    /**
     * @param string $levelPotassiumUnit
     * @return Product
     */
    public function setLevelPotassiumUnit(string $levelPotassiumUnit): Product
    {
        $this->levelPotassiumUnit = $levelPotassiumUnit;
        return $this;
    }

    /**
     * @return float
     */
    public function getLevelChloride(): float
    {
        return $this->levelChloride;
    }

    /**
     * @param float $levelChloride
     * @return Product
     */
    public function setLevelChloride(float $levelChloride): Product
    {
        $this->levelChloride = $levelChloride;
        return $this;
    }

    /**
     * @return string
     */
    public function getLevelChlorideUnit(): string
    {
        return $this->levelChlorideUnit;
    }

    /**
     * @param string $levelChlorideUnit
     * @return Product
     */
    public function setLevelChlorideUnit(string $levelChlorideUnit): Product
    {
        $this->levelChlorideUnit = $levelChlorideUnit;
        return $this;
    }

    /**
     * @return float
     */
    public function getLevelCalcium(): float
    {
        return $this->levelCalcium;
    }

    /**
     * @param float $levelCalcium
     * @return Product
     */
    public function setLevelCalcium(float $levelCalcium): Product
    {
        $this->levelCalcium = $levelCalcium;
        return $this;
    }

    /**
     * @return string
     */
    public function getLevelCalciumUnit(): string
    {
        return $this->levelCalciumUnit;
    }

    /**
     * @param string $levelCalciumUnit
     * @return Product
     */
    public function setLevelCalciumUnit(string $levelCalciumUnit): Product
    {
        $this->levelCalciumUnit = $levelCalciumUnit;
        return $this;
    }

    /**
     * @return float
     */
    public function getLevelMagnesium(): float
    {
        return $this->levelMagnesium;
    }

    /**
     * @param float $levelMagnesium
     * @return Product
     */
    public function setLevelMagnesium(float $levelMagnesium): Product
    {
        $this->levelMagnesium = $levelMagnesium;
        return $this;
    }

    /**
     * @return string
     */
    public function getLevelMagnesiumUnit(): string
    {
        return $this->levelMagnesiumUnit;
    }

    /**
     * @param string $levelMagnesiumUnit
     * @return Product
     */
    public function setLevelMagnesiumUnit(string $levelMagnesiumUnit): Product
    {
        $this->levelMagnesiumUnit = $levelMagnesiumUnit;
        return $this;
    }

    /**
     * @return float
     */
    public function getLevelNitrates(): float
    {
        return $this->levelNitrates;
    }

    /**
     * @param float $levelNitrates
     * @return Product
     */
    public function setLevelNitrates(float $levelNitrates): Product
    {
        $this->levelNitrates = $levelNitrates;
        return $this;
    }

    /**
     * @return string
     */
    public function getLevelNitratesUnit(): string
    {
        return $this->levelNitratesUnit;
    }

    /**
     * @param string $levelNitratesUnit
     * @return Product
     */
    public function setLevelNitratesUnit(string $levelNitratesUnit): Product
    {
        $this->levelNitratesUnit = $levelNitratesUnit;
        return $this;
    }

    /**
     * @return float
     */
    public function getLevelSulfates(): float
    {
        return $this->levelSulfates;
    }

    /**
     * @param float $levelSulfates
     * @return Product
     */
    public function setLevelSulfates(float $levelSulfates): Product
    {
        $this->levelSulfates = $levelSulfates;
        return $this;
    }

    /**
     * @return string
     */
    public function getLevelSulfatesUnit(): string
    {
        return $this->levelSulfatesUnit;
    }

    /**
     * @param string $levelSulfatesUnit
     * @return Product
     */
    public function setLevelSulfatesUnit(string $levelSulfatesUnit): Product
    {
        $this->levelSulfatesUnit = $levelSulfatesUnit;
        return $this;
    }

    /**
     * @return float
     */
    public function getFootprint(): float
    {
        return $this->footprint;
    }

    /**
     * @param float $footprint
     * @return Product
     */
    public function setFootprint(float $footprint): Product
    {
        $this->footprint = $footprint;
        return $this;
    }

    /**
     * @return string
     */
    public function getFootprintUnit(): string
    {
        return $this->footprintUnit;
    }

    /**
     * @param string $footprintUnit
     * @return Product
     */
    public function setFootprintUnit(string $footprintUnit): Product
    {
        $this->footprintUnit = $footprintUnit;
        return $this;
    }
}