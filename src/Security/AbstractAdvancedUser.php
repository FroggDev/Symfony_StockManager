<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Frogg <admin@frogg.fr>
 */
abstract class AbstractAdvancedUser implements UserInterface
{

    /*################
    # User constants #
    #################*/

    /** @const INACTIVE Constant for inactive User, register but didn't validate email confirmation */
    const INACTIVE = 0;

    /** @const ACTIVE Constant for registerd User */
    const ACTIVE = 1;

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

        return $this;
    }

    /**
     * get closed date
     *
     * @return \DateTime()
     */
    public function getDateClosed(): \DateTime
    {
        return $this->dateClosed;
    }


    /**
     * set an account inactive, used when create account but set inactive to wait email validation
     *
     * @return AbstractAdvancedUser
     */
    public function setInactive() : AbstractAdvancedUser
    {
        $this->status = $this::INACTIVE;

        return $this;
    }

    /**
     * set account active when user validated account from email link
     *
     * @return AbstractAdvancedUser
     */
    public function setActive(): AbstractAdvancedUser
    {
        $this->status = $this::ACTIVE;

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
        return $this->status === $this::ACTIVE;
    }
}
