<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App;

/**
 * @author Frogg <admin@frogg.fr>
 */
class SiteConfig
{
    /*########
    # GLOBAL #
    #########*/

    /**
     * website name
     * @const string
     */
    const SITENAME = 'StockManager';

    /**
     * website url
     * @const string
     */
    const SITEURL = 'https://stock.frogg.fr';

    /**
     * website copyright
     * @const string
     */
    const SITECOPYRIGHT = 'Frogg.fr';
    /**
     * website author
     * @const string
     */
    const SITEAUTHOR = 'Frogg, admin@frogg.fr';
    /**
     * website date (in footer)
     * @const string
     */
    const SITEDATE = "2018";
    /**
     * website charset
     * @const string
     */
    const SITECHARSET = "UTF-8";

    /*##########
    # SECURITY #
    ###########*/

    /**
     * name of security mail from
     * @const string
     */
    const SECURITYMAIL = 'stock@frogg.fr';

    /**
     * name of security Entity
     * @const string
     */
    const USERENTITY = "\App\Entity\User";


    /**
     * liste of the application roles
     * @const array
     */
    const SECURITYROLES = ["ROLE_MEMBER", "ROLE_EDITOR", "ROLE_ADMIN"];

    /*#########
    # PRODUCT #
    ##########*/

    /**
     * nb product to display per page
     * @const int
     */
    const NBPERPAGE = 10;

    /*########
    # LOCALE #
    #########*/

    /**
     * Cookie validity in days
     * @const int
     */
    const COOKIELOCALEVALIDITY = 30;

    /**
     * Cookie name
     * @const string
     */
    const COOKIELOCALENAME = "locale";
}
