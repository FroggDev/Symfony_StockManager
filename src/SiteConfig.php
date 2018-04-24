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
    //const SITEURL = 'https://stock.frogg.fr';
    const SITEURL = 'http://127.0.0.1:8000';

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
    const SITEDATE = '2018';
    /**
     * website charset
     * @const string
     */
    const SITECHARSET = 'UTF-8';
    /**
     * website physical path
     * @const string
     */
    const SITEPATH = 'C:/symfony/Symfony_StockManager/public/';

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
    const COOKIELOCALENAME = 'locale';

    /**
     * Locale Date format
     * @const array
     */
    const DATELOCALE = [
        'fr' => 'd/m/Y',
        'en' => 'm/d/Y',
    ];

    /*#####
    # SQL #
    ######*/

    const SQLCOUNTRY = 'sql/country.sql';
    const SQLSTOCK = 'sql/stock.sql';
    const SQLUSER = 'sql/user.sql';

    /*##########
    # SECURITY #
    ###########*/

    /**
     * name of security mail from
     * @const string
     */
    const SECURITYMAIL = 'support@frogg.fr';

    /**
     * name of security Entity
     * @const string
     */
    const USERENTITY = '\App\Entity\User';

    /**
     * liste of the application roles
     * @const array
     */
    const SECURITYROLES = ['ROLE_MEMBER', 'ROLE_EDITOR', 'ROLE_ADMIN'];

    /**
     * Cookie validity in days
     * @const int
     */
    const COOKIEUSERVALIDITY = 30;

    /**
     * Cookie name
     * @const string
     */
    const COOKIEUSERNAME = 'user';

    /*#########
    # PRODUCT #
    ##########*/

    /**
     * nb product to display per page
     * @const int
     */
    const NBPERPAGE = 2;

    /**
     * Energie Unit list
     * @const array
     */
    const ENERGYUNIT = ['kcal', 'KJ'];

    /**
     * Weight unit list
     * @const array
     */
    const WEIGHTUNIT = ['g'=>1, 'mg'=>1000, 'µg'=>1000000];

    /**
     * where the products images are stored
     * @const string
     */
    const UPLOADPATH = 'upload/product/';

    /**
     * Grade product information
     * @const array
     *
     * TODO TRAD THIS
     */
    const PRODUCTGRADE = [
        0 => [
            'color' => 'green',
            'text' => 'en faible quantité',
        ],
        1 => [
            'color' => 'orange darken-1',
            'text' => 'en quantité modérée',
        ],
        2 => [
            'color' => 'red',
            'text' => 'en quantité élevée',
        ],
    ];


    /*#######
    # SCRAP #
    ########*/

    /**
     * URL to login on the site with products
     * @const string
     */
    const SCRAPLOGINURL = 'https://fr.openfoodfacts.org/cgi/session.pl';

    /**
     * URL to login ID
     * @const string
     */
    const SCRAPUSERID = 'stock@frogg.fr';
    /**
     * URL to login ID INPUT
     * @const string
     */
    const SCRAPINPUTUSERID = 'user_id';

    /**
     * URL to login PASSWORD
     * @const string
     */
    const SCRAPPASSWORD = 'scrapOFF';
    /**
     * URL to login PASSWORD
     * @const string
     */
    const SCRAPINPUTPASSWORD = 'password';

    /**
     * URL to where data are available
     * @const string
     */
    const SCRAPDATAURL = 'https://fr.openfoodfacts.org/cgi/product.pl?type=edit&code=';

    /**
     * URL to where images are available
     * @const string
     */
    const SCRAPIMGURL = 'https://fr.openfoodfacts.org/images/products/';
}
