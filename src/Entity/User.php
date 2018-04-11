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

use App\Security\AbstractAdvancedUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Frogg <admin@frogg.fr>
 *
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 *
 * @UniqueEntity(fields={"email"},errorPath="email",message="email already in use")
 *
 * Unique entity:
 * @see https://symfony.com/doc/current/reference/constraints/UniqueEntity.html
 *
 * Validator translation:
 * @see https://symfony.com/doc/current/validation/translations.html
 *
 * Test format des mail
 * @see https://symfony.com/blog/new-in-symfony-4-1-html5-email-validation
 *
 * AdvancedUser
 * https://symfony.com/blog/new-in-symfony-4-1-deprecated-the-advanceduserinterface
 */
class User extends AbstractAdvancedUser
{

    /*################
    # User constants #
    #################*/

    /** @const TOKENVALIDITYTIME Constant for Token validity time in day */
    const TOKENVALIDITYTIME = 1;

    /*########
    # Entity #
    #########*/

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="firstname should not be blank")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="lastname should not be blank")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email(message="email is not valid",checkMX = true,checkHost = true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=100,nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $tokenValidity;

    /**
     * For base64 encode
     * @ORM\Column(type="string",length=64)
     * @Assert\NotBlank(message="password should not be blank")
     */
    private $password;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateInscription;

    /**
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $lastConnexion;

    /**
     * User account state ( 0 = inactive ; 1 = active ; 2 = closed ; 3 = banned ...other state for later )
     * @ORM\Column(type="integer")
     *
     * @see User contants (top of this file)
     */
    protected $status;

    /**
     * When User account is closed or banned
     * @ORM\Column(type="datetime",nullable=true)
     */
    protected $dateClosed;

    /*#############
    # Constructor #
    ##############*/

    /**
     * User constructor.
     */
    public function __construct()
    {
        // Symfony automatically serialize it
        $this->setRoles('ROLE_MEMBER');
        // initialize date creation on User creation
        $this->dateInscription = new \DateTime();
        //by default account is not active and has to be validated by email
        $this->setDisabled();
    }

    /*#########
    # Methods #
    ##########*/

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }


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
     * @return User
     */
    public function setId(int $id): User
    {
        $this->id = $id;

        return $this;
    }


    /**
     * @return string
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }


    /**
     * @param string $firstName
     *
     * @return $this
     */
    public function setFirstName(string $firstName): User
    {
        $this->firstName = $firstName;

        return $this;
    }


    /**
     * @return string
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName(string $lastName): User
    {
        $this->lastName = $lastName;

        return $this;
    }


    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;

        return $this;
    }


    /**
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }


    /**
     * @param string $password
     *
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;

        return $this;
    }


    /**
     * @return \DateTime
     */
    public function getDateInscription(): \DateTime
    {
        return $this->dateInscription;
    }


    /**
     * @param \DateTime $dateInscription
     *
     * @return User
     */
    public function setDateInscription(\DateTime $dateInscription): User
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): ?array
    {
        return $this->roles;
    }

    /**
     * @param string $role
     *
     * @return User
     */
    public function setRoles(string $role): User
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * @param array $roles
     *
     * @return User
     */
    public function setAllRoles(array $roles): User
    {
        $this->roles = $roles;

        return $this;
    }


    /**
     * @param string $role
     *
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles);
    }

    /**
     * @return \DateTime
     */
    public function getLastConnexion(): \DateTime
    {
        return $this->lastConnexion;
    }


    /**
     * @return $this
     */
    public function setLastConnexion()
    {
        $this->lastConnexion = new \DateTime();

        return $this;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     *
     * @return null
     */
    public function eraseCredentials()
    {
        return null;
    }

    /**
     * @return null|string
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @return User
     */
    public function setToken(): AbstractAdvancedUser
    {

        //nothing to do if account is banned or closed
        if ($this->status === $this::CLOSED || $this->status === $this::BANNED || $this->status === $this::DELETED) {
            return $this;
        }

        /*
         * Another way to create the unique token could be
         * User->setToken(bin2hex(random_bytes(100)));
         *
         * If token already exist dosnt reset it to be able to register/recover if both asked at the same time
         */
        if (!$this->token) {
            $this->token = uniqid('', true).uniqid('', true);
        }
        /*
         * set token validity only if account has been validated
         * case if user didnt validated email, can validate later
         */
        if ($this->status === $this::ENABLED) {
            $this->tokenValidity = new \DateTime();
            $this->tokenValidity->modify('+'.$this::TOKENVALIDITYTIME.' day');
        }

        return $this;
    }

    /**
     * @return null|\DateTime
     */
    public function getTokenValidity(): ?\DateTime
    {
        return $this->tokenValidity;
    }

    /**
     * This should not be used in code, has been added only for test
     * @param \DateTime $validity
     *
     * @return User
     */
    public function testToSetTokenValidity(\DateTime $validity): User
    {
        $this->tokenValidity = $validity;

        return $this;
    }

    /**
     * @return bool
     */
    public function isTokenExpired(): bool
    {
        if (null === $this->tokenValidity) {
            return false;
        }

        return $this->tokenValidity < new \DateTime();
    }

    /**
     * @return User
     */
    public function removeToken(): AbstractAdvancedUser
    {
        $this->tokenValidity = null;
        $this->token = null;

        return $this;
    }
}
