<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Service\Security;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Frogg <admin@frogg.fr>
 */
abstract class AbstractAdvancedUser implements UserInterface
{

    /*################
    # User constants #
    #################*/

    /** @const DELETED Constant for deleted User */
    const DELETED = -1;

    /** @const DISABLED Constant for inactive User, register but didn't validate email confirmation */
    const DISABLED = 0;

    /** @const ENABLED Constant for registerd User */
    const ENABLED = 1;

    /** @const CLOSED Constant for User closed account */
    const CLOSED = 2;

    /** @const BANNED Constant for User banned account */
    const BANNED = 3;


    /*###########
    # Variables #
    ############*/

    /** @var \DateTime */
    protected $dateClosed;

    /** @var int $status */
    protected $status;

    /*###########
    # Abstracts #
    ###########*/

    /**
     * @return AbstractAdvancedUser
     */
    abstract public function setToken() : AbstractAdvancedUser;

    /**
     * @return AbstractAdvancedUser
     */
    abstract public function removeToken() : AbstractAdvancedUser;

    /*###############
    # Getter/Setter #
    ################*/

    /**
     * Set date when account is closed
     *
     * @return AbstractAdvancedUser
     */
    public function setDateClosed(): AbstractAdvancedUser
    {
        $this->dateClosed = new \DateTime();
        $this->removeToken();

        return $this;
    }

    /**
     * get closed date
     *
     * @return null|\DateTime()
     */
    public function getDateClosed(): ?\DateTime
    {
        return $this->dateClosed;
    }


    /**
     * set an account inactive, used when create account but set inactive to wait email validation
     *
     * @return AbstractAdvancedUser
     */
    public function setDisabled() : AbstractAdvancedUser
    {
        $this->status = $this::DISABLED;
        $this->setToken();

        return $this;
    }

    /**
     * set account active when user validated account from email link
     *
     * @return AbstractAdvancedUser
     */
    public function setEnabled(): AbstractAdvancedUser
    {
        $this->status = $this::ENABLED;
        $this->removeToken();

        return $this;
    }

    /**
     * set account closed
     *
     * @return AbstractAdvancedUser
     */
    public function setClosed(): AbstractAdvancedUser
    {
        $this->status = $this::CLOSED;
        $this->setDateClosed();


        return $this;
    }

    /**
     * set account banned
     *
     * @return AbstractAdvancedUser
     */
    public function setBanned(): AbstractAdvancedUser
    {
        $this->status = $this::BANNED;
        $this->setDateClosed();

        return $this;
    }

    /**
     * set account deleted
     *
     * @return AbstractAdvancedUser
     */
    public function setDeleted(): AbstractAdvancedUser
    {
        $this->status = $this::DELETED;
        $this->setDateClosed();

        return $this;
    }

    /*########
    # Checks #
    ##########*/

    /**
     * Check if account is banned
     *
     * @return bool
     */
    public function isBanned(): bool
    {
        return $this->status === $this::BANNED;
    }

    /**
     * check if account is closed
     *
     * @return bool
     */
    public function isClosed(): bool
    {
        return $this->status === $this::CLOSED;
    }

    /**
     * Check if the account is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->status === $this::ENABLED;
    }

    /**
     * Check if the account is disabled.
     *
     * @return bool
     */
    public function isDisabled(): bool
    {
        return $this->status === $this::DISABLED;
    }


    /**
     * Check if the account is deleted.
     *
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->status === $this::DELETED;
    }
}
