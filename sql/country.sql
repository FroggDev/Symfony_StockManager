-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 07, 2013 at 08:28 PM
-- Server version: 5.5.28
-- PHP Version: 5.3.10-1ubuntu3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `stockmanager`
--

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE IF NOT EXISTS `country` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `code` int(3) NOT NULL,
  `alpha2` varchar(2) NOT NULL,
  `alpha3` varchar(3) NOT NULL,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alpha2` (`alpha2`),
  UNIQUE KEY `alpha3` (`alpha3`),
  UNIQUE KEY `code_unique` (`code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=242 ;

--
-- Dumping data for table `country`
--
/*!40000 ALTER TABLE `country` DISABLE KEYS */;
INSERT INTO `country` (`id`, `code`, `alpha2`, `alpha3`, `name`) VALUES
(1, 4, 'AF', 'AFG', 'Afghanistan'),
(2, 8, 'AL', 'ALB', 'Albanie'),
(3, 10, 'AQ', 'ATA', 'Antarctique'),
(4, 12, 'DZ', 'DZA', 'Algérie'),
(5, 16, 'AS', 'ASM', 'Samoa Américaines'),
(6, 20, 'AD', 'AND', 'Andorre'),
(7, 24, 'AO', 'AGO', 'Angola'),
(8, 28, 'AG', 'ATG', 'Antigua-et-Barbuda'),
(9, 31, 'AZ', 'AZE', 'Azerbaïdjan'),
(10, 32, 'AR', 'ARG', 'Argentine'),
(11, 36, 'AU', 'AUS', 'Australie'),
(12, 40, 'AT', 'AUT', 'Autriche'),
(13, 44, 'BS', 'BHS', 'Bahamas'),
(14, 48, 'BH', 'BHR', 'Bahreïn'),
(15, 50, 'BD', 'BGD', 'Bangladesh'),
(16, 51, 'AM', 'ARM', 'Arménie'),
(17, 52, 'BB', 'BRB', 'Barbade'),
(18, 56, 'BE', 'BEL', 'Belgique'),
(19, 60, 'BM', 'BMU', 'Bermudes'),
(20, 64, 'BT', 'BTN', 'Bhoutan'),
(21, 68, 'BO', 'BOL', 'Bolivie'),
(22, 70, 'BA', 'BIH', 'Bosnie-Herzégovine'),
(23, 72, 'BW', 'BWA', 'Botswana'),
(24, 74, 'BV', 'BVT', 'Île Bouvet'),
(25, 76, 'BR', 'BRA', 'Brésil'),
(26, 84, 'BZ', 'BLZ', 'Belize'),
(27, 86, 'IO', 'IOT', 'Territoire Britannique de l''Océan Indien'),
(28, 90, 'SB', 'SLB', 'Îles Salomon'),
(29, 92, 'VG', 'VGB', 'Îles Vierges Britanniques'),
(30, 96, 'BN', 'BRN', 'Brunéi Darussalam'),
(31, 100, 'BG', 'BGR', 'Bulgarie'),
(32, 104, 'MM', 'MMR', 'Myanmar'),
(33, 108, 'BI', 'BDI', 'Burundi'),
(34, 112, 'BY', 'BLR', 'Bélarus'),
(35, 116, 'KH', 'KHM', 'Cambodge'),
(36, 120, 'CM', 'CMR', 'Cameroun'),
(37, 124, 'CA', 'CAN', 'Canada'),
(38, 132, 'CV', 'CPV', 'Cap-vert'),
(39, 136, 'KY', 'CYM', 'Îles Caïmanes'),
(40, 140, 'CF', 'CAF', 'République Centrafricaine'),
(41, 144, 'LK', 'LKA', 'Sri Lanka'),
(42, 148, 'TD', 'TCD', 'Tchad'),
(43, 152, 'CL', 'CHL', 'Chili'),
(44, 156, 'CN', 'CHN', 'Chine'),
(45, 158, 'TW', 'TWN', 'Taïwan'),
(46, 162, 'CX', 'CXR', 'Île Christmas'),
(47, 166, 'CC', 'CCK', 'Îles Cocos (Keeling)'),
(48, 170, 'CO', 'COL', 'Colombie'),
(49, 174, 'KM', 'COM', 'Comores'),
(50, 175, 'YT', 'MYT', 'Mayotte'),
(51, 178, 'CG', 'COG', 'République du Congo'),
(52, 180, 'CD', 'COD', 'République Démocratique du Congo'),
(53, 184, 'CK', 'COK', 'Îles Cook'),
(54, 188, 'CR', 'CRI', 'Costa Rica'),
(55, 191, 'HR', 'HRV', 'Croatie'),
(56, 192, 'CU', 'CUB', 'Cuba'),
(57, 196, 'CY', 'CYP', 'Chypre'),
(58, 203, 'CZ', 'CZE', 'République Tchèque'),
(59, 204, 'BJ', 'BEN', 'Bénin'),
(60, 208, 'DK', 'DNK', 'Danemark'),
(61, 212, 'DM', 'DMA', 'Dominique'),
(62, 214, 'DO', 'DOM', 'République Dominicaine'),
(63, 218, 'EC', 'ECU', 'Équateur'),
(64, 222, 'SV', 'SLV', 'El Salvador'),
(65, 226, 'GQ', 'GNQ', 'Guinée Équatoriale'),
(66, 231, 'ET', 'ETH', 'Éthiopie'),
(67, 232, 'ER', 'ERI', 'Érythrée'),
(68, 233, 'EE', 'EST', 'Estonie'),
(69, 234, 'FO', 'FRO', 'Îles Féroé'),
(70, 238, 'FK', 'FLK', 'Îles (malvinas) Falkland'),
(71, 239, 'GS', 'SGS', 'Géorgie du Sud et les Îles Sandwich du Sud'),
(72, 242, 'FJ', 'FJI', 'Fidji'),
(73, 246, 'FI', 'FIN', 'Finlande'),
(74, 248, 'AX', 'ALA', 'Îles Åland'),
(75, 250, 'FR', 'FRA', 'France'),
(76, 254, 'GF', 'GUF', 'Guyane Française'),
(77, 258, 'PF', 'PYF', 'Polynésie Française'),
(78, 260, 'TF', 'ATF', 'Terres Australes Françaises'),
(79, 262, 'DJ', 'DJI', 'Djibouti'),
(80, 266, 'GA', 'GAB', 'Gabon'),
(81, 268, 'GE', 'GEO', 'Géorgie'),
(82, 270, 'GM', 'GMB', 'Gambie'),
(83, 275, 'PS', 'PSE', 'Territoire Palestinien Occupé'),
(84, 276, 'DE', 'DEU', 'Allemagne'),
(85, 288, 'GH', 'GHA', 'Ghana'),
(86, 292, 'GI', 'GIB', 'Gibraltar'),
(87, 296, 'KI', 'KIR', 'Kiribati'),
(88, 300, 'GR', 'GRC', 'Grèce'),
(89, 304, 'GL', 'GRL', 'Groenland'),
(90, 308, 'GD', 'GRD', 'Grenade'),
(91, 312, 'GP', 'GLP', 'Guadeloupe'),
(92, 316, 'GU', 'GUM', 'Guam'),
(93, 320, 'GT', 'GTM', 'Guatemala'),
(94, 324, 'GN', 'GIN', 'Guinée'),
(95, 328, 'GY', 'GUY', 'Guyana'),
(96, 332, 'HT', 'HTI', 'Haïti'),
(97, 334, 'HM', 'HMD', 'Îles Heard et Mcdonald'),
(98, 336, 'VA', 'VAT', 'Saint-Siège (état de la Cité du Vatican)'),
(99, 340, 'HN', 'HND', 'Honduras'),
(100, 344, 'HK', 'HKG', 'Hong-Kong'),
(101, 348, 'HU', 'HUN', 'Hongrie'),
(102, 352, 'IS', 'ISL', 'Islande'),
(103, 356, 'IN', 'IND', 'Inde'),
(104, 360, 'ID', 'IDN', 'Indonésie'),
(105, 364, 'IR', 'IRN', 'République Islamique d''Iran'),
(106, 368, 'IQ', 'IRQ', 'Iraq'),
(107, 372, 'IE', 'IRL', 'Irlande'),
(108, 376, 'IL', 'ISR', 'Israël'),
(109, 380, 'IT', 'ITA', 'Italie'),
(110, 384, 'CI', 'CIV', 'Côte d''Ivoire'),
(111, 388, 'JM', 'JAM', 'Jamaïque'),
(112, 392, 'JP', 'JPN', 'Japon'),
(113, 398, 'KZ', 'KAZ', 'Kazakhstan'),
(114, 400, 'JO', 'JOR', 'Jordanie'),
(115, 404, 'KE', 'KEN', 'Kenya'),
(116, 408, 'KP', 'PRK', 'République Populaire Démocratique de Corée'),
(117, 410, 'KR', 'KOR', 'République de Corée'),
(118, 414, 'KW', 'KWT', 'Koweït'),
(119, 417, 'KG', 'KGZ', 'Kirghizistan'),
(120, 418, 'LA', 'LAO',  'République Démocratique Populaire Lao'),
(121, 422, 'LB', 'LBN', 'Liban'),
(122, 426, 'LS', 'LSO', 'Lesotho'),
(123, 428, 'LV', 'LVA', 'Lettonie'),
(124, 430, 'LR', 'LBR', 'Libéria'),
(125, 434, 'LY', 'LBY', 'Jamahiriya Arabe Libyenne'),
(126, 438, 'LI', 'LIE', 'Liechtenstein'),
(127, 440, 'LT', 'LTU', 'Lituanie'),
(128, 442, 'LU', 'LUX', 'Luxembourg'),
(129, 446, 'MO', 'MAC', 'Macao'),
(130, 450, 'MG', 'MDG', 'Madagascar'),
(131, 454, 'MW', 'MWI', 'Malawi'),
(132, 458, 'MY', 'MYS', 'Malaisie'),
(133, 462, 'MV', 'MDV', 'Maldives'),
(134, 466, 'ML', 'MLI', 'Mali'),
(135, 470, 'MT', 'MLT', 'Malte'),
(136, 474, 'MQ', 'MTQ', 'Martinique'),
(137, 478, 'MR', 'MRT', 'Mauritanie'),
(138, 480, 'MU', 'MUS', 'Maurice'),
(139, 484, 'MX', 'MEX', 'Mexique'),
(140, 492, 'MC', 'MCO', 'Monaco'),
(141, 496, 'MN', 'MNG', 'Mongolie'),
(142, 498, 'MD', 'MDA', 'République de Moldova'),
(143, 500, 'MS', 'MSR', 'Montserrat'),
(144, 504, 'MA', 'MAR', 'Maroc'),
(145, 508, 'MZ', 'MOZ', 'Mozambique'),
(146, 512, 'OM', 'OMN', 'Oman'),
(147, 516, 'NA', 'NAM', 'Namibie'),
(148, 520, 'NR', 'NRU', 'Nauru'),
(149, 524, 'NP', 'NPL', 'Népal'),
(150, 528, 'NL', 'NLD', 'Pays-Bas'),
(151, 530, 'AN', 'ANT', 'Antilles Néerlandaises'),
(152, 533, 'AW', 'ABW', 'Aruba'),
(153, 540, 'NC', 'NCL', 'Nouvelle-Calédonie'),
(154, 548, 'VU', 'VUT', 'Vanuatu'),
(155, 554, 'NZ', 'NZL', 'Nouvelle-Zélande'),
(156, 558, 'NI', 'NIC', 'Nicaragua'),
(157, 562, 'NE', 'NER', 'Niger'),
(158, 566, 'NG', 'NGA', 'Nigéria'),
(159, 570, 'NU', 'NIU', 'Niué'),
(160, 574, 'NF', 'NFK', 'Île Norfolk'),
(161, 578, 'NO', 'NOR', 'Norvège'),
(162, 580, 'MP', 'MNP', 'Îles Mariannes du Nord'),
(163, 581, 'UM', 'UMI', 'Îles Mineures Éloignées des États-Unis'),
(164, 583, 'FM', 'FSM', 'États Fédérés de Micronésie'),
(165, 584, 'MH', 'MHL', 'Îles Marshall'),
(166, 585, 'PW', 'PLW', 'Palaos'),
(167, 586, 'PK', 'PAK', 'Pakistan'),
(168, 591, 'PA', 'PAN', 'Panama'),
(169, 598, 'PG', 'PNG', 'Papouasie-Nouvelle-Guinée'),
(170, 600, 'PY', 'PRY', 'Paraguay'),
(171, 604, 'PE', 'PER', 'Pérou'),
(172, 608, 'PH', 'PHL', 'Philippines'),
(173, 612, 'PN', 'PCN', 'Pitcairn'),
(174, 616, 'PL', 'POL', 'Pologne'),
(175, 620, 'PT', 'PRT', 'Portugal'),
(176, 624, 'GW', 'GNB', 'Guinée-Bissau'),
(177, 626, 'TL', 'TLS', 'Timor-Leste'),
(178, 630, 'PR', 'PRI', 'Porto Rico'),
(179, 634, 'QA', 'QAT', 'Qatar'),
(180, 638, 'RE', 'REU', 'Réunion'),
(181, 642, 'RO', 'ROU', 'Roumanie'),
(182, 643, 'RU', 'RUS', 'Fédération de Russie'),
(183, 646, 'RW', 'RWA', 'Rwanda'),
(184, 654, 'SH', 'SHN', 'Sainte-Hélène'),
(185, 659, 'KN', 'KNA', 'Saint-Kitts-et-Nevis'),
(186, 660, 'AI', 'AIA', 'Anguilla'),
(187, 662, 'LC', 'LCA', 'Sainte-Lucie'),
(188, 666, 'PM', 'SPM', 'Saint-Pierre-et-Miquelon'),
(189, 670, 'VC', 'VCT', 'Saint-Vincent-et-les Grenadines'),
(190, 674, 'SM', 'SMR', 'Saint-Marin'),
(191, 678, 'ST', 'STP', 'Sao Tomé-et-Principe'),
(192, 682, 'SA', 'SAU', 'Arabie Saoudite'),
(193, 686, 'SN', 'SEN', 'Sénégal'),
(194, 690, 'SC', 'SYC', 'Seychelles'),
(195, 694, 'SL', 'SLE', 'Sierra Leone'),
(196, 702, 'SG', 'SGP', 'Singapour'),
(197, 703, 'SK', 'SVK', 'Slovaquie'),
(198, 704, 'VN', 'VNM', 'Viet Nam'),
(199, 705, 'SI', 'SVN', 'Slovénie'),
(200, 706, 'SO', 'SOM' ,'Somalie'),
(201, 710, 'ZA', 'ZAF' ,'Afrique du Sud'),
(202, 716, 'ZW', 'ZWE' ,'Zimbabwe'),
(203, 724, 'ES', 'ESP' ,'Espagne'),
(204, 732, 'EH', 'ESH' ,'Sahara Occidental'),
(205, 736, 'SD', 'SDN' ,'Soudan'),
(206, 740, 'SR', 'SUR' ,'Suriname'),
(207, 744, 'SJ', 'SJM' ,'Svalbard etÎle Jan Mayen'),
(208, 748, 'SZ', 'SWZ' ,'Swaziland'),
(209, 752, 'SE', 'SWE', ',Suède'),
(210, 756, 'CH', 'CHE', 'Suisse'),
(211, 760, 'SY', 'SYR', 'République Arabe Syrienne'),
(212, 762, 'TJ', 'TJK', 'Tadjikistan'),
(213, 764, 'TH', 'THA', 'Thaïlande'),
(214, 768, 'TG', 'TGO', 'Togo'),
(215, 772, 'TK', 'TKL', 'Tokelau'),
(216, 776, 'TO', 'TON', 'Tonga'),
(217, 780, 'TT', 'TTO', 'Trinité-et-Tobago'),
(218, 784, 'AE', 'ARE', 'Émirats Arabes Unis'),
(219, 788, 'TN', 'TUN', 'Tunisie'),
(220, 792, 'TR', 'TUR', 'Turquie'),
(221, 795, 'TM', 'TKM', 'Turkménistan'),
(222, 796, 'TC', 'TCA', 'Îles Turks et Caïques'),
(223, 798, 'TV', 'TUV', 'Tuvalu'),
(224, 800, 'UG', 'UGA', 'Ouganda'),
(225, 804, 'UA', 'UKR', 'Ukraine'),
(226, 807, 'MK', 'MKD', 'L''ex-République Yougoslave de Macédoine'),
(227, 818, 'EG', 'EGY', 'Égypte'),
(228, 826, 'GB', 'GBR', 'Royaume-Uni'),
(229, 833, 'IM', 'IMN', 'Île de Man'),
(230, 834, 'TZ', 'TZA', 'République-Unie de Tanzanie'),
(231, 840, 'US', 'USA', 'États-Unis'),
(232, 850, 'VI', 'VIR', 'Îles Vierges des États-Unis'),
(233, 854, 'BF', 'BFA', 'Burkina Faso'),
(234, 858, 'UY', 'URY', 'Uruguay'),
(235, 860, 'UZ', 'UZB', 'Ouzbékistan'),
(236, 862, 'VE', 'VEN', 'Venezuela'),
(237, 876, 'WF', 'WLF', 'Wallis et Futuna'),
(238, 882, 'WS', 'WSM', 'Samoa'),
(239, 887, 'YE', 'YEM', 'Yémen'),
(240, 891, 'CS', 'SCG', 'Serbie-et-Monténégro'),
(241, 894, 'ZM', 'ZMB', 'Zambie');
/*!40000 ALTER TABLE `country` ENABLE KEYS */;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;