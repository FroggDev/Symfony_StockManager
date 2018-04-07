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
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @author Frogg <admin@frogg.fr>
 *
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 *
 * @UniqueEntity(fields={"email"},errorPath="email",message="This email is already in use")
 *
 * @see https://symfony.com/doc/current/reference/constraints/UniqueEntity.html
 */
class User implements AdvancedUserInterface
{

    /*################
    # User constants #
    #################*/

    /** @const INACTIVE Constant for inactive Author, register but didn't validate email confirmation */
    const INACTIVE = 0;
    /** @const ACTIVE Constant for registerd Author */
    const ACTIVE = 1;
    /** @const CLOSED Constant for Author closed account */
    const CLOSED = 2;
    /** @const BANNED Constant for Author banned account */
    const BANNED = 3;
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
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
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
    private $status;

    /**
     * When User account is closed or banned
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $dateClosed;

    /**
     * User has subscribe to the box
     * @ORM\Column(type="boolean",nullable=true)
     */
    private $hasSubscribe;

    /*#########
    # Methods #
    ##########*/

    /**
     * User constructor.
     */
    public function __construct()
    {
        // initialize date creation on User creation
        $this->dateInscription = new \DateTime();
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

     */
    public function eraseCredentials()
    {
        /**
         * @TODO : maybe something to do there ?
         */
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
    public function setToken(): User
    {
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
        if ($this->status !== $this::INACTIVE) {
            $this->tokenValidity = new \DateTime();
        }

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTokenValidity(): \DateTime
    {
        return $this->tokenValidity;
    }

    /**
     * @return bool
     */
    public function isTokenExpired(): bool
    {
        if (null === $this->tokenValidity) {
            return false;
        }
        $now = new \DateTime();

        return $now->diff($this->getTokenValidity())->days > $this::TOKENVALIDITYTIME;
    }

    /**
     * @return User
     */
    public function removeToken(): User
    {
        $this->tokenValidity = null;
        $this->token = null;

        return $this;
    }


    /**
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param integer $status
     *
     * @return User
     */
    public function setStatus($status): User
    {
        $this->status = $status;

        return $this;
    }


    /**
     * @return User
     */
    public function setInactive(): User
    {
        $this->status = $this::INACTIVE;

        return $this;
    }

    /**
     * @return User
     */
    public function setActive(): User
    {
        $this->status = $this::ACTIVE;

        return $this;
    }

    /**
     * @return User
     */
    public function setClosed(): User
    {
        $this->status = $this::CLOSED;
        $this->setDateClosed();

        return $this;
    }

    /**
     * @return User
     */
    public function setBanned(): User
    {
        $this->status = $this::BANNED;
        $this->setDateClosed();

        return $this;
    }

    /**
     * @return bool
     */
    public function isBanned(): bool
    {
        return $this->status === $this::BANNED;
    }

    /**
     * @return bool
     */
    public function isClosed(): bool
    {
        return $this->status === $this::CLOSED;
    }

    /**
     * Checks whether the user is enabled.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a DisabledException and prevent login.
     *
     * @return bool true if the user is enabled, false otherwise
     *
     * @see DisabledException
     */
    public function isEnabled(): bool
    {
        return $this->status === $this::ACTIVE;
    }

    /**
     * @return void
     */
    public function setDateClosed(): void
    {
        $this->dateClosed = new \DateTime();
    }


    /**
     * @return \DateTime()
     */
    public function getDateClosed(): \DateTime
    {
        return $this->dateClosed;
    }

    /**
     * Checks whether the user's account has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw an AccountExpiredException and prevent login.
     *
     * @return bool true if the user's account is non expired, false otherwise
     *
     * @see AccountExpiredException
     */
    public function isAccountNonExpired(): bool
    {
        return true;
    }

    /**
     * Checks whether the user is locked.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a LockedException and prevent login.
     *
     * @return bool true if the user is not locked, false otherwise
     *
     * @see LockedException
     */
    public function isAccountNonLocked(): bool
    {
        return true;
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a CredentialsExpiredException and prevent login.
     *
     * @return bool true if the user's credentials are non expired, false otherwise
     *
     * @see CredentialsExpiredException
     */
    public function isCredentialsNonExpired(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function getHasSubscribe()
    {
        return $this->hasSubscribe;
    }

    /**
     * @param bool $hasSubscribe
     *
     * @return User
     */
    public function setHasSubscribe($hasSubscribe)
    {
        $this->hasSubscribe = $hasSubscribe;

        return $this;
    }
}
