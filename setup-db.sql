-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: database:3306
-- Generation Time: Sep 29, 2026 at 06:03 PM
-- Server version: 10.6.25-MariaDB-ubu2204
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Behoof`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_banner`
--

CREATE TABLE `tbl_banner` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `sub_title` varchar(200) DEFAULT NULL,
  `banner_link` varchar(400) NOT NULL,
  `link_text` varchar(200) DEFAULT NULL,
  `image` varchar(300) NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `position` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_blog`
--

CREATE TABLE `tbl_blog` (
  `id` int(11) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `image` varchar(300) NOT NULL,
  `short_description` varchar(500) DEFAULT NULL,
  `description` mediumtext NOT NULL,
  `meta_title` varchar(255) NOT NULL,
  `meta_keyword` varchar(500) NOT NULL,
  `meta_desc` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `custom_date` date NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_blog_category`
--

CREATE TABLE `tbl_blog_category` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_keyword` varchar(255) DEFAULT NULL,
  `meta_desc` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_config`
--

CREATE TABLE `tbl_config` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `instagram` varchar(500) DEFAULT NULL,
  `youtube` varchar(255) DEFAULT NULL,
  `tiktok` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `email_1` varchar(255) DEFAULT NULL,
  `email_2` varchar(255) DEFAULT NULL,
  `phone_1` varchar(20) DEFAULT NULL,
  `phone_2` varchar(20) DEFAULT NULL,
  `google_map` mediumtext DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `thousand_separator` varchar(10) NOT NULL DEFAULT ',',
  `decimal_separator` varchar(10) NOT NULL DEFAULT '.',
  `tax` int(11) DEFAULT NULL,
  `date_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_config`
--

INSERT INTO `tbl_config` (`id`, `name`, `logo`, `facebook`, `twitter`, `linkedin`, `instagram`, `youtube`, `tiktok`, `address`, `email_1`, `email_2`, `phone_1`, `phone_2`, `google_map`, `currency`, `thousand_separator`, `decimal_separator`, `tax`, `date_time`) VALUES
(1, 'Behoof', '', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '', '', '', '', 'INR-₹', ',', '.', 0, '2021-12-11 14:27:59');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_enquiry`
--

CREATE TABLE `tbl_enquiry` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `message` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_gallery`
--

CREATE TABLE `tbl_gallery` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `image` varchar(300) NOT NULL,
  `position` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_location`
--

CREATE TABLE `tbl_location` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_location`
--

INSERT INTO `tbl_location` (`id`, `title`, `url`, `data`, `status`, `created_at`) VALUES
(1, 'Albania', 'albania', '{\"primary_commercial_hub\": \"Tirana\", \"major_sub_locations\": [\"Durrës\", \"Vlorë\", \"Shkodër\", \"Elbasan\", \"Korçë\", \"Fier\", \"Berat\", \"Gjirokastër\"], \"industrial_hubs\": [\"Elbasan\", \"Fier\", \"Laç\", \"Kuçovë\", \"Ballsh\"], \"trade_and_logistics_hubs\": [\"Port of Durrës\", \"Port of Vlorë\", \"Tirana International Airport\", \"Port of Sarandë\", \"Kapshticë Border Crossing\"]}', 1, '2024-08-30 12:48:24'),
(2, 'Armenia', 'armenia', '{\"primary_commercial_hub\": \"Yerevan\", \"major_sub_locations\": [\"Gyumri\", \"Vanadzor\", \"Vagharshapat\", \"Hrazdan\", \"Kapan\"], \"industrial_hubs\": [\"Yerevan\", \"Vanadzor\", \"Gyumri\", \"Alaverdi\", \"Abovyan\"], \"trade_and_logistics_hubs\": [\"Zvartnots International Airport\", \"Yerevan Railway Station\", \"Port of Ayrum\", \"Bagratashen Border Crossing\", \"Meghri Border Crossing\"]}', 1, '2024-08-30 12:48:24'),
(3, 'Austria', 'austria', '{\"primary_commercial_hub\": \"Vienna\", \"major_sub_locations\": [\"Graz\", \"Linz\", \"Salzburg\", \"Innsbruck\", \"Klagenfurt\", \"St. Pölten\", \"Wels\", \"Villach\"], \"industrial_hubs\": [\"Linz\", \"Graz\", \"Steyr\", \"Leoben\", \"Wels\", \"Kapfenberg\"], \"trade_and_logistics_hubs\": [\"Port of Vienna\", \"Vienna International Airport\", \"Linz Airport\", \"Salzburg Airport\", \"Innsbruck Airport\", \"Wels\"]}', 1, '2024-08-30 12:48:24'),
(4, 'Azerbaijan', 'azerbaijan', '{\"primary_commercial_hub\": \"Baku\", \"major_sub_locations\": [\"Ganja\", \"Sumqayit\", \"Mingachevir\", \"Lankaran\", \"Shaki\"], \"industrial_hubs\": [\"Sumqayit\", \"Ganja\", \"Mingachevir\", \"Neftchala Industrial District\"], \"trade_and_logistics_hubs\": [\"Port of Baku\", \"Heydar Aliyev International Airport\", \"Baku-Tbilisi-Kars railway terminal\"]}', 1, '2024-08-30 12:48:24'),
(5, 'Belarus', 'belarus', '{\"primary_commercial_hub\": \"Minsk\", \"major_sub_locations\": [\"Brest\", \"Gomel\", \"Grodno\", \"Mogilev\", \"Vitebsk\"], \"industrial_hubs\": [\"Minsk\", \"Gomel\", \"Mogilev\", \"Vitebsk\", \"Novopolotsk\", \"Soligorsk\"], \"trade_and_logistics_hubs\": [\"Minsk National Airport\", \"Brest\", \"Minsk\", \"Gomel\"]}', 1, '2024-08-30 12:48:24'),
(6, 'Belgium', 'belgium', '{\"primary_commercial_hub\": \"Brussels\", \"major_sub_locations\": [\"Antwerp\", \"Ghent\", \"Liège\", \"Charleroi\", \"Bruges\", \"Leuven\", \"Namur\", \"Hasselt\"], \"industrial_hubs\": [\"Port of Antwerp-Bruges\", \"Ghent industrial zone\", \"Liège Science Park\", \"Genk industrial area\", \"Charleroi industrial basin\"], \"trade_and_logistics_hubs\": [\"Port of Antwerp-Bruges\", \"Port of Zeebrugge\", \"Liège Airport\", \"Brussels Airport\", \"Brussels-South railway station\"]}', 1, '2024-08-30 12:48:24'),
(7, 'Bulgaria', 'bulgaria', '{\"primary_commercial_hub\": \"Sofia\", \"major_sub_locations\": [\"Plovdiv\", \"Varna\", \"Burgas\", \"Ruse\", \"Stara Zagora\", \"Pleven\", \"Veliko Tarnovo\"], \"industrial_hubs\": [\"Plovdiv\", \"Stara Zagora\", \"Ruse\", \"Burgas\", \"Devnya\", \"Dimitrovgrad\"], \"trade_and_logistics_hubs\": [\"Port of Varna\", \"Port of Burgas\", \"Sofia Airport\", \"Plovdiv Airport\", \"Ruse Danube Port\", \"Kapitan Andreevo Border Crossing\"]}', 1, '2024-08-30 12:48:24'),
(8, 'Croatia', 'croatia', '{\"primary_commercial_hub\": \"Zagreb\", \"major_sub_locations\": [\"Split\", \"Rijeka\", \"Osijek\", \"Zadar\", \"Pula\", \"Dubrovnik\"], \"industrial_hubs\": [\"Zagreb\", \"Rijeka\", \"Osijek\", \"Sisak\", \"Varaždin\"], \"trade_and_logistics_hubs\": [\"Port of Rijeka\", \"Port of Split\", \"Port of Ploče\", \"Zagreb Airport\", \"Rijeka Gateway\"]}', 1, '2024-08-30 12:48:24'),
(9, 'Cyprus', 'cyprus', '{\"primary_commercial_hub\": \"Nicosia\", \"major_sub_locations\": [\"Limassol\", \"Larnaca\", \"Paphos\", \"Famagusta\", \"Kyrenia\"], \"industrial_hubs\": [\"Limassol Industrial Estate\", \"Larnaca Industrial Area\", \"Nicosia Industrial Area\", \"Aradippou Industrial Area\"], \"trade_and_logistics_hubs\": [\"Port of Limassol\", \"Larnaca International Airport\", \"Paphos International Airport\", \"Port of Larnaca\"]}', 1, '2024-08-30 12:48:24'),
(10, 'Czechia', 'czechia', '{\"primary_commercial_hub\": \"Prague\", \"major_sub_locations\": [\"Brno\", \"Ostrava\", \"Plzeň\", \"Olomouc\", \"Liberec\", \"České Budějovice\", \"Hradec Králové\", \"Pardubice\"], \"industrial_hubs\": [\"Mladá Boleslav\", \"Ostrava\", \"Plzeň\", \"Brno\", \"Kopřivnice\", \"Třinec\"], \"trade_and_logistics_hubs\": [\"Port of Mělník\", \"Václav Havel Airport Prague\", \"Brno–Tuřany Airport\", \"Ostrava Airport\", \"Pardubice\"]}', 1, '2024-08-30 12:48:24'),
(11, 'Denmark', 'denmark', '{\"primary_commercial_hub\": \"Copenhagen\", \"major_sub_locations\": [\"Aarhus\", \"Odense\", \"Aalborg\", \"Esbjerg\", \"Kolding\"], \"industrial_hubs\": [\"Aarhus\", \"Odense\", \"Aalborg\", \"Esbjerg\", \"Fredericia\"], \"trade_and_logistics_hubs\": [\"Port of Copenhagen\", \"Port of Aarhus\", \"Port of Esbjerg\", \"Port of Aalborg\", \"Copenhagen Airport\"]}', 1, '2024-08-30 12:48:24'),
(12, 'Estonia', 'estonia', '{\"primary_commercial_hub\": \"Tallinn\", \"major_sub_locations\": [\"Tartu\", \"Narva\", \"Pärnu\", \"Kohtla-Järve\", \"Viljandi\"], \"industrial_hubs\": [\"Ida-Viru County\", \"Sillamäe\", \"Kohtla-Järve\", \"Tartu\"], \"trade_and_logistics_hubs\": [\"Port of Tallinn\", \"Muuga Harbour\", \"Port of Sillamäe\", \"Port of Pärnu\", \"Tallinn Airport\", \"Tartu Airport\"]}', 1, '2024-08-30 12:48:24'),
(13, 'Finland', 'finland', '{\"primary_commercial_hub\": \"Helsinki\", \"major_sub_locations\": [\"Espoo\", \"Tampere\", \"Turku\", \"Oulu\", \"Jyväskylä\", \"Kuopio\", \"Lahti\", \"Vaasa\"], \"industrial_hubs\": [\"Tampere\", \"Turku\", \"Oulu\", \"Rauma\", \"Pori\", \"Kotka\", \"Imatra\"], \"trade_and_logistics_hubs\": [\"Port of Helsinki\", \"Port of HaminaKotka\", \"Port of Turku\", \"Port of Rauma\", \"Helsinki Airport\", \"Port of Oulu\"]}', 1, '2024-08-30 12:48:24'),
(14, 'France', 'france', '{\"primary_commercial_hub\": \"Paris\", \"major_sub_locations\": [\"Lyon\", \"Marseille\", \"Lille\", \"Toulouse\", \"Bordeaux\", \"Nantes\", \"Strasbourg\", \"Nice\"], \"industrial_hubs\": [\"Toulouse\", \"Lyon\", \"Grenoble\", \"Dunkerque\", \"Le Havre\", \"Saint-Nazaire\", \"Fos-sur-Mer\"], \"trade_and_logistics_hubs\": [\"Port of Marseille-Fos\", \"Port of Le Havre\", \"Port of Dunkirk\", \"Port of Nantes-Saint-Nazaire\", \"Paris-Charles de Gaulle Airport\", \"Lyon-Saint Exupéry Airport\", \"Eurotunnel terminal at Coquelles\"]}', 1, '2024-08-30 12:48:24'),
(15, 'Georgia', 'georgia', '{\"primary_commercial_hub\": \"Tbilisi\", \"major_sub_locations\": [\"Batumi\", \"Kutaisi\", \"Rustavi\", \"Zugdidi\", \"Gori\"], \"industrial_hubs\": [\"Rustavi\", \"Kutaisi\", \"Zestafoni\", \"Poti\"], \"trade_and_logistics_hubs\": [\"Port of Poti\", \"Port of Batumi\", \"Tbilisi International Airport\", \"Tbilisi\"]}', 1, '2024-08-30 12:48:24'),
(16, 'Germany', 'germany', '{\"primary_commercial_hub\": \"Berlin\", \"major_sub_locations\": [\"Hamburg\", \"Munich\", \"Frankfurt am Main\", \"Cologne\", \"Stuttgart\", \"Düsseldorf\", \"Leipzig\", \"Dresden\"], \"industrial_hubs\": [\"Ruhrgebiet\", \"Stuttgart\", \"Wolfsburg\", \"Ingolstadt\", \"Ludwigshafen am Rhein\", \"Leuna\", \"Dresden\"], \"trade_and_logistics_hubs\": [\"Port of Hamburg\", \"Port of Bremerhaven\", \"Frankfurt Airport\", \"Leipzig/Halle Airport\", \"Duisburg Intermodal Terminal\"]}', 1, '2024-08-30 12:48:24'),
(17, 'Greece', 'greece', '{\"primary_commercial_hub\": \"Athens\", \"major_sub_locations\": [\"Thessaloniki\", \"Patras\", \"Heraklion\", \"Larissa\", \"Volos\", \"Ioannina\", \"Kavala\", \"Rhodes\"], \"industrial_hubs\": [\"Thessaloniki\", \"Volos\", \"Patras\", \"Larissa\", \"Piraeus\", \"Oinofyta\"], \"trade_and_logistics_hubs\": [\"Port of Piraeus\", \"Port of Thessaloniki\", \"Port of Patras\", \"Port of Heraklion\", \"Athens International Airport\", \"Thessaloniki Airport\"]}', 1, '2024-08-30 12:48:24'),
(18, 'Hungary', 'hungary', '{\"primary_commercial_hub\": \"Budapest\", \"major_sub_locations\": [\"Debrecen\", \"Szeged\", \"Miskolc\", \"Pécs\", \"Győr\", \"Kecskemét\", \"Nyíregyháza\", \"Székesfehérvár\"], \"industrial_hubs\": [\"Győr\", \"Kecskemét\", \"Debrecen\", \"Miskolc\", \"Székesfehérvár\", \"Tatabánya\"], \"trade_and_logistics_hubs\": [\"Budapest Ferenc Liszt International Airport\", \"Budapest Intermodal Logistics Centre\", \"Port of Csepel\", \"Záhony\"]}', 1, '2024-08-30 12:48:24'),
(19, 'Iceland', 'iceland', '{\"primary_commercial_hub\": \"Reykjavík\", \"major_sub_locations\": [\"Kópavogur\", \"Hafnarfjörður\", \"Akureyri\", \"Reykjanesbær\", \"Selfoss\", \"Ísafjörður\", \"Egilsstaðir\"], \"industrial_hubs\": [\"Grundartangi\", \"Norðurál industrial area\", \"Reykjavík\", \"Akureyri\"], \"trade_and_logistics_hubs\": [\"Reykjavík Harbour\", \"Port of Grundartangi\", \"Akureyri Harbour\", \"Keflavík International Airport\", \"Reykjavík Domestic Airport\"]}', 1, '2024-08-30 12:48:24'),
(20, 'Ireland', 'ireland', '{\"primary_commercial_hub\": \"Dublin\", \"major_sub_locations\": [\"Cork\", \"Limerick\", \"Galway\", \"Waterford\", \"Kilkenny\", \"Drogheda\"], \"industrial_hubs\": [\"Cork\", \"Limerick\", \"Galway\", \"Waterford\", \"Shannon\"], \"trade_and_logistics_hubs\": [\"Port of Dublin\", \"Port of Cork\", \"Port of Waterford\", \"Port of Foynes\", \"Dublin Airport\", \"Shannon Airport\"]}', 1, '2024-08-30 12:48:24'),
(21, 'Italy', 'italy', '{\"primary_commercial_hub\": \"Milan\", \"major_sub_locations\": [\"Rome\", \"Naples\", \"Turin\", \"Florence\", \"Bologna\", \"Venice\", \"Genoa\", \"Palermo\", \"Bari\"], \"industrial_hubs\": [\"Turin\", \"Milan\", \"Bologna\", \"Genoa\", \"Brescia\", \"Modena\", \"Taranto\"], \"trade_and_logistics_hubs\": [\"Port of Genoa\", \"Port of Trieste\", \"Port of Gioia Tauro\", \"Port of Livorno\", \"Port of Venice\", \"Milan Malpensa Airport\", \"Bologna Guglielmo Marconi Airport\"]}', 1, '2024-08-30 12:48:24'),
(22, 'Kazakhstan', 'kazakhstan', '{\"primary_commercial_hub\": \"Almaty\", \"major_sub_locations\": [\"Astana\", \"Shymkent\", \"Karaganda\", \"Aktobe\", \"Atyrau\", \"Pavlodar\", \"Kostanay\", \"Oskemen\"], \"industrial_hubs\": [\"Karaganda\", \"Pavlodar\", \"Temirtau\", \"Atyrau\", \"Aktobe\", \"Shymkent\"], \"trade_and_logistics_hubs\": [\"Port of Aktau\", \"Port of Kuryk\", \"Almaty International Airport\", \"Nursultan Nazarbayev International Airport\", \"Khorgos Gateway\"]}', 1, '2024-08-30 12:48:24'),
(23, 'Latvia', 'latvia', '{\"primary_commercial_hub\": \"Riga\", \"major_sub_locations\": [\"Daugavpils\", \"Liepāja\", \"Jelgava\", \"Jūrmala\", \"Ventspils\", \"Rēzekne\", \"Valmiera\"], \"industrial_hubs\": [\"Riga\", \"Liepāja\", \"Daugavpils\", \"Jelgava\", \"Ventspils\"], \"trade_and_logistics_hubs\": [\"Port of Riga\", \"Port of Ventspils\", \"Port of Liepāja\", \"Riga International Airport\", \"Daugavpils\"]}', 1, '2024-08-30 12:48:24'),
(24, 'Liechtenstein', 'liechtenstein', '{\"primary_commercial_hub\": \"Vaduz\", \"major_sub_locations\": [\"Schaan\", \"Balzers\", \"Triesen\", \"Eschen\", \"Mauren\"], \"industrial_hubs\": [\"Schaan\", \"Balzers\", \"Eschen\"], \"trade_and_logistics_hubs\": [\"Schaan-Vaduz railway station\", \"Liechtenstein Bus Center\"]}', 1, '2024-08-30 12:48:24'),
(25, 'Lithuania', 'lithuania', '{\"primary_commercial_hub\": \"Vilnius\", \"major_sub_locations\": [\"Kaunas\", \"Klaipėda\", \"Šiauliai\", \"Panevėžys\", \"Alytus\"], \"industrial_hubs\": [\"Kaunas\", \"Klaipėda\", \"Šiauliai\", \"Panevėžys\", \"Jonava\", \"Mažeikiai\"], \"trade_and_logistics_hubs\": [\"Port of Klaipėda\", \"Vilnius International Airport\", \"Kaunas Airport\", \"Kaunas Intermodal Terminal\", \"Šeštokai Railway Station\"]}', 1, '2024-08-30 12:48:24'),
(26, 'Luxembourg', 'luxembourg', '{\"primary_commercial_hub\": \"Luxembourg City\", \"major_sub_locations\": [\"Esch-sur-Alzette\", \"Differdange\", \"Dudelange\", \"Ettelbruck\", \"Diekirch\"], \"industrial_hubs\": [\"Esch-Belval\", \"Differdange\", \"Dudelange\", \"Wiltz\"], \"trade_and_logistics_hubs\": [\"Luxembourg Findel Airport\", \"Port of Mertert\", \"Bettembourg-Dudelange intermodal terminal\"]}', 1, '2024-08-30 12:48:24'),
(27, 'Netherlands', 'netherlands', '{\"primary_commercial_hub\": \"Amsterdam\", \"major_sub_locations\": [\"Rotterdam\", \"The Hague\", \"Utrecht\", \"Eindhoven\", \"Groningen\", \"Maastricht\"], \"industrial_hubs\": [\"Rotterdam\", \"Eindhoven\", \"Chemelot Industrial Park\", \"Moerdijk\", \"Pernis\"], \"trade_and_logistics_hubs\": [\"Port of Rotterdam\", \"Port of Amsterdam\", \"Amsterdam Airport Schiphol\", \"Port of Moerdijk\", \"Venlo\"]}', 1, '2024-08-30 12:48:24'),
(28, 'Norway', 'norway', '{\"primary_commercial_hub\": \"Oslo\", \"major_sub_locations\": [\"Bergen\", \"Trondheim\", \"Stavanger\", \"Tromsø\", \"Kristiansand\"], \"industrial_hubs\": [\"Stavanger\", \"Grenland\", \"Ålesund\", \"Mo i Rana\", \"Kongsberg\"], \"trade_and_logistics_hubs\": [\"Port of Oslo\", \"Port of Bergen\", \"Port of Stavanger\", \"Port of Narvik\", \"Oslo Airport\"]}', 1, '2024-08-30 12:48:24'),
(29, 'Poland', 'poland', '{\"primary_commercial_hub\": \"Warsaw\", \"major_sub_locations\": [\"Kraków\", \"Łódź\", \"Wrocław\", \"Poznań\", \"Gdańsk\", \"Katowice\", \"Lublin\", \"Szczecin\"], \"industrial_hubs\": [\"Upper Silesian Industrial Region\", \"Łódź\", \"Poznań\", \"Wrocław\", \"Płock\", \"Tarnobrzeg Special Economic Zone\"], \"trade_and_logistics_hubs\": [\"Port of Gdańsk\", \"Port of Gdynia\", \"Warsaw Chopin Airport\", \"Łódź Special Economic Zone\", \"Małaszewicze Logistics Hub\"]}', 1, '2024-08-30 12:48:24'),
(30, 'Portugal', 'portugal', '{\"primary_commercial_hub\": \"Lisbon\", \"major_sub_locations\": [\"Porto\", \"Coimbra\", \"Braga\", \"Faro\", \"Évora\", \"Funchal\", \"Ponta Delgada\"], \"industrial_hubs\": [\"Sines\", \"Setúbal\", \"Aveiro\", \"Leiria\", \"Braga\", \"Marinha Grande\"], \"trade_and_logistics_hubs\": [\"Port of Lisbon\", \"Port of Leixões\", \"Port of Sines\", \"Port of Setúbal\", \"Port of Aveiro\", \"Port of Funchal\", \"Humberto Delgado Airport\", \"Francisco Sá Carneiro Airport\"]}', 1, '2024-08-30 12:48:24'),
(31, 'Romania', 'romania', '{\"primary_commercial_hub\": \"Bucharest\", \"major_sub_locations\": [\"Cluj-Napoca\", \"Timișoara\", \"Iași\", \"Constanța\", \"Brașov\", \"Craiova\"], \"industrial_hubs\": [\"Pitești\", \"Mioveni\", \"Galați\", \"Ploiești\", \"Arad\", \"Craiova\"], \"trade_and_logistics_hubs\": [\"Port of Constanța\", \"Henri Coandă International Airport\", \"Port of Galați\", \"Bucharest\"]}', 1, '2024-08-30 12:48:24'),
(32, 'Russia', 'russia', '{\"primary_commercial_hub\": \"Moscow\", \"major_sub_locations\": [\"Saint Petersburg\", \"Novosibirsk\", \"Yekaterinburg\", \"Kazan\", \"Nizhny Novgorod\", \"Vladivostok\"], \"industrial_hubs\": [\"Nizhny Tagil\", \"Magnitogorsk\", \"Chelyabinsk\", \"Tolyatti\", \"Naberezhnye Chelny\", \"Norilsk\"], \"trade_and_logistics_hubs\": [\"Port of Saint Petersburg\", \"Port of Novorossiysk\", \"Port of Vladivostok\", \"Port of Murmansk\", \"Sheremetyevo International Airport\", \"Vostochny Cosmodrome\"]}', 1, '2024-08-30 12:48:24'),
(33, 'Serbia', 'serbia', '{\"primary_commercial_hub\": \"Belgrade\", \"major_sub_locations\": [\"Novi Sad\", \"Niš\", \"Kragujevac\", \"Subotica\", \"Čačak\", \"Novi Pazar\"], \"industrial_hubs\": [\"Kragujevac\", \"Pančevo\", \"Novi Sad\", \"Subotica\", \"Niš\", \"Bor\"], \"trade_and_logistics_hubs\": [\"Port of Belgrade\", \"Belgrade Nikola Tesla Airport\", \"Port of Novi Sad\", \"Niš Constantine the Great Airport\", \"Subotica\"]}', 1, '2024-08-30 12:48:24'),
(34, 'Slovakia', 'slovakia', '{\"primary_commercial_hub\": \"Bratislava\", \"major_sub_locations\": [\"Košice\", \"Prešov\", \"Žilina\", \"Nitra\", \"Trnava\", \"Banská Bystrica\", \"Trenčín\"], \"industrial_hubs\": [\"Bratislava\", \"Košice\", \"Žilina\", \"Trnava\", \"Nitra\", \"Martin\"], \"trade_and_logistics_hubs\": [\"Bratislava Airport\", \"Košice International Airport\", \"Port of Bratislava\", \"Žilina\", \"Dunajská Streda\"]}', 1, '2024-08-30 12:48:24'),
(35, 'Slovenia', 'slovenia', '{\"primary_commercial_hub\": \"Ljubljana\", \"major_sub_locations\": [\"Maribor\", \"Celje\", \"Kranj\", \"Koper\", \"Novo Mesto\", \"Nova Gorica\", \"Murska Sobota\"], \"industrial_hubs\": [\"Maribor\", \"Celje\", \"Velenje\", \"Kranj\", \"Novo Mesto\", \"Ravne na Koroškem\"], \"trade_and_logistics_hubs\": [\"Port of Koper\", \"Ljubljana Jože Pučnik Airport\", \"Ljubljana\", \"Maribor\"]}', 1, '2024-08-30 12:48:24'),
(36, 'Spain', 'spain', '{\"primary_commercial_hub\": \"Madrid\", \"major_sub_locations\": [\"Barcelona\", \"Valencia\", \"Seville\", \"Bilbao\", \"Málaga\", \"Zaragoza\"], \"industrial_hubs\": [\"Barcelona\", \"Bilbao\", \"Valencia\", \"Zaragoza\", \"Vigo\", \"Valladolid\"], \"trade_and_logistics_hubs\": [\"Port of Barcelona\", \"Port of Valencia\", \"Port of Algeciras\", \"Port of Bilbao\", \"Adolfo Suárez Madrid–Barajas Airport\", \"Zaragoza Logistics Platform (PLAZA)\"]}', 1, '2024-08-30 12:48:24'),
(37, 'Sweden', 'sweden', '{\"primary_commercial_hub\": \"Stockholm\", \"major_sub_locations\": [\"Gothenburg\", \"Malmö\", \"Uppsala\", \"Linköping\", \"Örebro\", \"Västerås\", \"Umeå\", \"Luleå\"], \"industrial_hubs\": [\"Gothenburg\", \"Malmö\", \"Linköping\", \"Västerås\", \"Kiruna\", \"Luleå\", \"Sandviken\"], \"trade_and_logistics_hubs\": [\"Port of Gothenburg\", \"Port of Malmö\", \"Port of Stockholm\", \"Stockholm Arlanda Airport\", \"Port of Trelleborg\", \"Port of Luleå\"]}', 1, '2024-08-30 12:48:24'),
(38, 'Switzerland', 'switzerland', '{\"primary_commercial_hub\": \"Zürich\", \"major_sub_locations\": [\"Geneva\", \"Basel\", \"Bern\", \"Lausanne\", \"Lucerne\", \"Lugano\", \"St. Gallen\", \"Winterthur\"], \"industrial_hubs\": [\"Basel\", \"Zürich\", \"Winterthur\", \"Biel/Bienne\", \"La Chaux-de-Fonds\", \"Schaffhausen\"], \"trade_and_logistics_hubs\": [\"Port of Basel\", \"Zurich Airport\", \"Geneva Airport\", \"EuroAirport Basel Mulhouse Freiburg\", \"Gotthard Base Tunnel\"]}', 1, '2024-08-30 12:48:24'),
(39, 'Turkey', 'turkey', '{\"primary_commercial_hub\": \"Istanbul\", \"major_sub_locations\": [\"Ankara\", \"İzmir\", \"Bursa\", \"Antalya\", \"Adana\", \"Gaziantep\", \"Konya\", \"Mersin\"], \"industrial_hubs\": [\"Gebze\", \"Bursa\", \"İzmir\", \"Gaziantep\", \"Kocaeli\", \"Kayseri\", \"Konya\"], \"trade_and_logistics_hubs\": [\"Port of Ambarlı\", \"Port of Mersin\", \"Port of İzmir\", \"Port of İskenderun\", \"Istanbul Airport\", \"Ankara Esenboğa Airport\", \"Kapıkule Border Gate\"]}', 1, '2024-08-30 12:48:24'),
(40, 'United Kingdom', 'united-kingdom', '{\"primary_commercial_hub\": \"London\", \"major_sub_locations\": [\"Manchester\", \"Birmingham\", \"Glasgow\", \"Leeds\", \"Liverpool\", \"Bristol\", \"Edinburgh\", \"Cardiff\", \"Belfast\"], \"industrial_hubs\": [\"Birmingham\", \"Coventry\", \"Sheffield\", \"Teesside\", \"Newcastle upon Tyne\", \"Aberdeen\", \"Port Talbot\"], \"trade_and_logistics_hubs\": [\"Port of Felixstowe\", \"Port of London\", \"Port of Southampton\", \"Port of Dover\", \"Port of Liverpool\", \"Heathrow Airport\", \"East Midlands Airport\"]}', 1, '2024-08-30 12:48:24'),
(41, 'UK', 'uk', '{\"primary_commercial_hub\": \"London\", \"major_sub_locations\": [\"Birmingham\", \"Manchester\", \"Leeds\", \"Glasgow\", \"Edinburgh\", \"Bristol\", \"Cardiff\", \"Liverpool\", \"Belfast\"], \"industrial_hubs\": [\"Birmingham\", \"Sheffield\", \"Coventry\", \"Newcastle upon Tyne\", \"Teesside\", \"Aberdeen\"], \"trade_and_logistics_hubs\": [\"Port of Felixstowe\", \"Port of Southampton\", \"Port of Dover\", \"Port of Liverpool\", \"Heathrow Airport\", \"East Midlands Airport\", \"Port of Belfast\"]}', 1, '2024-08-30 12:48:24'),
(42, 'Afghanistan', 'afghanistan', '{\"primary_commercial_hub\": \"Kabul\", \"major_sub_locations\": [\"Kandahar\", \"Herat\", \"Mazar-i-Sharif\", \"Jalalabad\", \"Kunduz\"], \"industrial_hubs\": [\"Kabul\", \"Herat\", \"Mazar-i-Sharif\", \"Kandahar\"], \"trade_and_logistics_hubs\": [\"Kabul International Airport\", \"Hairatan Port\", \"Islam Qala Border Crossing\", \"Torkham Border Crossing\", \"Spin Boldak Border Crossing\"]}', 1, '2024-08-30 12:48:24'),
(43, 'Bahrain', 'bahrain', '{\"primary_commercial_hub\": \"Manama\", \"major_sub_locations\": [\"Muharraq\", \"Riffa\", \"Isa Town\", \"Hamad Town\", \"Sitra\"], \"industrial_hubs\": [\"Hidd Industrial Area\", \"Sitra Industrial Area\", \"Bahrain International Investment Park\", \"Bahrain International Industrial Park\"], \"trade_and_logistics_hubs\": [\"Khalifa Bin Salman Port\", \"Bahrain International Airport\", \"Bahrain Logistics Zone\"]}', 1, '2024-08-30 12:48:24'),
(44, 'Bangladesh', 'bangladesh', '{\"primary_commercial_hub\": \"Dhaka\", \"major_sub_locations\": [\"Chattogram\", \"Khulna\", \"Rajshahi\", \"Sylhet\", \"Barishal\", \"Rangpur\", \"Cumilla\", \"Mymensingh\"], \"industrial_hubs\": [\"Narayanganj\", \"Gazipur\", \"Ashulia\", \"Savar\", \"Chattogram\", \"Khulna\"], \"trade_and_logistics_hubs\": [\"Port of Chattogram\", \"Mongla Port\", \"Payra Port\", \"Dhaka International Airport\", \"Kamalapur Railway Station\", \"Benapole Land Port\"]}', 1, '2024-08-30 12:48:24'),
(45, 'Bhutan', 'bhutan', '{\"primary_commercial_hub\": \"Thimphu\", \"major_sub_locations\": [\"Phuntsholing\", \"Paro\", \"Gelephu\", \"Samdrup Jongkhar\", \"Punakha\", \"Wangdue Phodrang\"], \"industrial_hubs\": [\"Phuntsholing\", \"Gelephu\", \"Pasakha Industrial Estate\"], \"trade_and_logistics_hubs\": [\"Phuntsholing\", \"Gelephu\", \"Samdrup Jongkhar\", \"Paro International Airport\"]}', 1, '2024-08-30 12:48:24'),
(46, 'Brunei', 'brunei', '{\"primary_commercial_hub\": \"Bandar Seri Begawan\", \"major_sub_locations\": [\"Kuala Belait\", \"Seria\", \"Tutong\", \"Bangar\"], \"industrial_hubs\": [\"Seria\", \"Lumut\", \"Sungai Liang\"], \"trade_and_logistics_hubs\": [\"Port of Muara\", \"Brunei International Airport\", \"Kuala Belait\"]}', 1, '2024-08-30 12:48:24'),
(47, 'Cambodia', 'cambodia', '{\"primary_commercial_hub\": \"Phnom Penh\", \"major_sub_locations\": [\"Siem Reap\", \"Battambang\", \"Sihanoukville\", \"Kampong Cham\", \"Kampot\"], \"industrial_hubs\": [\"Phnom Penh Special Economic Zone\", \"Sihanoukville Special Economic Zone\", \"Manhattan Special Economic Zone\", \"Poipet Special Economic Zone\", \"Kampong Speu\"], \"trade_and_logistics_hubs\": [\"Port of Sihanoukville\", \"Phnom Penh Autonomous Port\", \"Techo International Airport\", \"Poipet\", \"Bavet\"]}', 1, '2024-08-30 12:48:24'),
(48, 'China', 'china', '{\"primary_commercial_hub\": \"Shanghai\", \"major_sub_locations\": [\"Beijing\", \"Guangzhou\", \"Shenzhen\", \"Chengdu\", \"Wuhan\", \"Hangzhou\", \"Nanjing\", \"Chongqing\"], \"industrial_hubs\": [\"Suzhou\", \"Dongguan\", \"Foshan\", \"Wuxi\", \"Qingdao\", \"Tianjin\", \"Wuhan\"], \"trade_and_logistics_hubs\": [\"Port of Shanghai\", \"Port of Ningbo-Zhoushan\", \"Port of Shenzhen\", \"Port of Guangzhou\", \"Port of Qingdao\", \"Port of Tianjin\", \"Beijing Capital International Airport\"]}', 1, '2024-08-30 12:48:24'),
(49, 'India', 'india', '{\"primary_commercial_hub\": \"Mumbai\", \"major_sub_locations\": [\"Delhi\", \"Bengaluru\", \"Chennai\", \"Kolkata\", \"Hyderabad\", \"Ahmedabad\", \"Pune\", \"Jaipur\", \"Lucknow\", \"Kochi\"], \"industrial_hubs\": [\"Jamshedpur\", \"Bhilai\", \"Rourkela\", \"Surat\", \"Ludhiana\", \"Tiruppur\", \"Pithampur\", \"Noida\"], \"trade_and_logistics_hubs\": [\"Jawaharlal Nehru Port\", \"Mumbai Port\", \"Mundra Port\", \"Chennai Port\", \"Deendayal Port\", \"Kolkata Dock System\", \"Indira Gandhi International Airport\", \"Chhatrapati Shivaji Maharaj International Airport\"]}', 1, '2024-08-30 12:48:24'),
(50, 'Indonesia', 'indonesia', '{\"primary_commercial_hub\": \"Jakarta\", \"major_sub_locations\": [\"Surabaya\", \"Bandung\", \"Medan\", \"Semarang\", \"Makassar\", \"Yogyakarta\", \"Denpasar\", \"Palembang\"], \"industrial_hubs\": [\"Cikarang\", \"Karawang\", \"Batam\", \"Surabaya\", \"Tangerang\"], \"trade_and_logistics_hubs\": [\"Tanjung Priok Port\", \"Port of Tanjung Perak\", \"Soekarno–Hatta International Airport\", \"Kuala Tanjung Port\", \"Belawan Port\", \"Port of Makassar\"]}', 1, '2024-08-30 12:48:24'),
(51, 'Iran', 'iran', '{\"primary_commercial_hub\": \"Tehran\", \"major_sub_locations\": [\"Mashhad\", \"Isfahan\", \"Shiraz\", \"Tabriz\", \"Ahvaz\", \"Qom\", \"Kermanshah\", \"Rasht\", \"Yazd\", \"Bandar Abbas\"], \"industrial_hubs\": [\"Arak\", \"Tabriz\", \"Isfahan\", \"Ahvaz\", \"Bandar Abbas\", \"Asaluyeh\", \"Mahshahr\"], \"trade_and_logistics_hubs\": [\"Port of Bandar Abbas\", \"Port of Imam Khomeini\", \"Port of Bushehr\", \"Port of Chabahar\", \"Imam Khomeini International Airport\", \"Shahid Rajaee Port\"]}', 1, '2024-08-30 12:48:24'),
(52, 'Iraq', 'iraq', '{\"primary_commercial_hub\": \"Baghdad\", \"major_sub_locations\": [\"Baghdad\", \"Basra\", \"Mosul\", \"Erbil\", \"Sulaymaniyah\", \"Najaf\", \"Karbala\", \"Kirkuk\", \"Diwaniyah\", \"Nasiriyah\"], \"industrial_hubs\": [\"Basra\", \"Kirkuk\", \"Baiji\", \"Erbil\", \"Sulaymaniyah\", \"Khor Al Zubair\"], \"trade_and_logistics_hubs\": [\"Port of Umm Qasr\", \"Port of Khor Al Zubair\", \"Baghdad International Airport\", \"Erbil International Airport\", \"Ibrahim Khalil Border Crossing\", \"Trebil Border Crossing\"]}', 1, '2024-08-30 12:48:24'),
(53, 'Israel', 'israel', '{\"primary_commercial_hub\": \"Tel Aviv\", \"major_sub_locations\": [\"Jerusalem\", \"Haifa\", \"Beersheba\", \"Eilat\", \"Netanya\", \"Ashdod\"], \"industrial_hubs\": [\"Haifa Bay\", \"Ashdod\", \"Ramat Hovav\", \"Migdal HaEmek\", \"Kiryat Gat\"], \"trade_and_logistics_hubs\": [\"Port of Haifa\", \"Port of Ashdod\", \"Port of Eilat\", \"Ben Gurion Airport\", \"Haifa Bay\"]}', 1, '2024-08-30 12:48:24'),
(54, 'Japan', 'japan', '{\"primary_commercial_hub\": \"Tokyo\", \"major_sub_locations\": [\"Osaka\", \"Nagoya\", \"Yokohama\", \"Sapporo\", \"Fukuoka\", \"Sendai\", \"Hiroshima\", \"Kyoto\"], \"industrial_hubs\": [\"Keihin Industrial Zone\", \"Chūkyō Industrial Zone\", \"Hanshin Industrial Zone\", \"Kitakyushu Industrial Zone\", \"Setouchi Industrial Region\"], \"trade_and_logistics_hubs\": [\"Port of Tokyo\", \"Port of Yokohama\", \"Port of Nagoya\", \"Port of Osaka\", \"Port of Kobe\", \"Narita International Airport\", \"Kansai International Airport\"]}', 1, '2024-08-30 12:48:24'),
(55, 'Jordan', 'jordan', '{\"primary_commercial_hub\": \"Amman\", \"major_sub_locations\": [\"Zarqa\", \"Irbid\", \"Aqaba\", \"Madaba\", \"Mafraq\"], \"industrial_hubs\": [\"Sahab Industrial Estate\", \"Al Hassan Industrial Estate\", \"King Abdullah II Industrial Estate\", \"Aqaba International Industrial Estate\"], \"trade_and_logistics_hubs\": [\"Port of Aqaba\", \"Queen Alia International Airport\", \"Aqaba Railway Corporation\"]}', 1, '2024-08-30 12:48:24'),
(56, 'Kuwait', 'kuwait', '{\"primary_commercial_hub\": \"Kuwait City\", \"major_sub_locations\": [\"Hawalli\", \"Salmiya\", \"Farwaniya\", \"Jahra\", \"Ahmadi\", \"Mubarak Al-Kabeer\"], \"industrial_hubs\": [\"Shuaiba Industrial Area\", \"Shuwaikh Industrial Area\", \"Mina Abdullah Industrial Area\", \"Mina Al-Ahmadi Industrial Area\"], \"trade_and_logistics_hubs\": [\"Port of Shuwaikh\", \"Port of Shuaiba\", \"Mina Al-Ahmadi Port\", \"Kuwait International Airport\"]}', 1, '2024-08-30 12:48:24'),
(57, 'Kyrgyzstan', 'kyrgyzstan', '{\"primary_commercial_hub\": \"Bishkek\", \"major_sub_locations\": [\"Osh\", \"Jalal-Abad\", \"Karakol\", \"Tokmok\", \"Naryn\"], \"industrial_hubs\": [\"Bishkek\", \"Osh\", \"Kara-Balta\", \"Tokmok\", \"Kadamjay\"], \"trade_and_logistics_hubs\": [\"Manas International Airport\", \"Osh International Airport\", \"Irkeshtam Pass\", \"Torugart Pass\", \"Bishkek\"]}', 1, '2024-08-30 12:48:24'),
(58, 'Maldives', 'maldives', '{\"primary_commercial_hub\": \"Malé\", \"major_sub_locations\": [\"Hulhumalé\", \"Addu City\", \"Fuvahmulah\", \"Kulhudhuffushi\", \"Thinadhoo\"], \"industrial_hubs\": [\"Thilafushi\", \"Hulhumalé\"], \"trade_and_logistics_hubs\": [\"Malé Commercial Harbour\", \"Velana International Airport\", \"Thilafushi Port\", \"Gan International Airport\"]}', 1, '2024-08-30 12:48:24'),
(59, 'Nepal', 'nepal', '{\"primary_commercial_hub\": \"Kathmandu\", \"major_sub_locations\": [\"Pokhara\", \"Lalitpur\", \"Bhaktapur\", \"Biratnagar\", \"Birgunj\", \"Bharatpur\", \"Butwal\", \"Nepalgunj\", \"Dhangadhi\", \"Dharan\"], \"industrial_hubs\": [\"Biratnagar\", \"Birgunj\", \"Hetauda\", \"Bhairahawa\", \"Butwal\", \"Simara\"], \"trade_and_logistics_hubs\": [\"Birgunj Dry Port\", \"Birgunj\", \"Tribhuvan International Airport\", \"Kakarbhitta\", \"Rasuwagadhi\"]}', 1, '2024-08-30 12:48:24'),
(60, 'Oman', 'oman', '{\"primary_commercial_hub\": \"Muscat\", \"major_sub_locations\": [\"Salalah\", \"Sohar\", \"Nizwa\", \"Sur\", \"Duqm\"], \"industrial_hubs\": [\"Sohar Industrial Port\", \"Duqm Special Economic Zone\", \"Salalah Free Zone\", \"Rusayl Industrial Estate\", \"Nizwa Industrial Estate\"], \"trade_and_logistics_hubs\": [\"Port of Sohar\", \"Port of Salalah\", \"Port of Duqm\", \"Muscat International Airport\", \"Sohar Freezone\"]}', 1, '2024-08-30 12:48:24'),
(61, 'Palestine', 'palestine', '{\"primary_commercial_hub\": \"Ramallah\", \"major_sub_locations\": [\"Gaza City\", \"Hebron\", \"Nablus\", \"Jenin\", \"Bethlehem\", \"Jericho\", \"Tulkarm\", \"Qalqilya\"], \"industrial_hubs\": [\"Hebron\", \"Nablus\", \"Gaza City\", \"Jenin Industrial Estate\", \"Bethlehem\"], \"trade_and_logistics_hubs\": [\"Karni Crossing\", \"Allenby Bridge Crossing\", \"Rafah Crossing\", \"Port of Gaza\"]}', 1, '2024-08-30 12:48:24'),
(62, 'Philippines', 'philippines', '{\"primary_commercial_hub\": \"Metro Manila\", \"major_sub_locations\": [\"Cebu City\", \"Davao City\", \"Iloilo City\", \"Cagayan de Oro\", \"Baguio\"], \"industrial_hubs\": [\"Laguna Technopark\", \"Cavite Economic Zone\", \"Clark Freeport Zone\", \"Subic Bay Freeport Zone\", \"Mactan Economic Zone\"], \"trade_and_logistics_hubs\": [\"Port of Manila\", \"Ninoy Aquino International Airport\", \"Port of Cebu\", \"Mactan-Cebu International Airport\", \"Subic Bay International Container Terminal\"]}', 1, '2024-08-30 12:48:24'),
(63, 'Pakistan', 'pakistan', '{\"primary_commercial_hub\": \"Karachi\", \"major_sub_locations\": [\"Lahore\", \"Islamabad\", \"Rawalpindi\", \"Faisalabad\", \"Multan\", \"Peshawar\", \"Quetta\", \"Hyderabad\", \"Sialkot\", \"Gujranwala\"], \"industrial_hubs\": [\"Karachi\", \"Faisalabad\", \"Sialkot\", \"Gujranwala\", \"Hub Industrial Trading Estate\", \"Hattar Industrial Estate\", \"Port Qasim Industrial Zone\"], \"trade_and_logistics_hubs\": [\"Port of Karachi\", \"Port Qasim\", \"Gwadar Port\", \"Jinnah International Airport\", \"Allama Iqbal International Airport\", \"Dry Port Lahore\", \"Sost Dry Port\"]}', 1, '2024-08-30 12:48:24'),
(64, 'Qatar', 'qatar', '{\"primary_commercial_hub\": \"Doha\", \"major_sub_locations\": [\"Al Rayyan\", \"Al Wakrah\", \"Al Khor\", \"Lusail\", \"Dukhan\"], \"industrial_hubs\": [\"Ras Laffan Industrial City\", \"Mesaieed Industrial City\", \"Dukhan\", \"Umm Alhoul Special Economic Zone\"], \"trade_and_logistics_hubs\": [\"Hamad Port\", \"Hamad International Airport\", \"Ras Laffan Port\", \"Mesaieed Port\"]}', 1, '2024-08-30 12:48:24'),
(65, 'Saudi Arabia', 'saudi-arabia', '{\"primary_commercial_hub\": \"Riyadh\", \"major_sub_locations\": [\"Jeddah\", \"Mecca\", \"Medina\", \"Dammam\", \"Khobar\", \"Taif\", \"Abha\", \"Tabuk\", \"Jubail\", \"Yanbu\"], \"industrial_hubs\": [\"Jubail Industrial City\", \"Yanbu Industrial City\", \"Ras Al-Khair Industrial City\", \"King Abdullah Economic City\", \"Modon Industrial City in Riyadh\", \"Jeddah Industrial City\"], \"trade_and_logistics_hubs\": [\"Jeddah Islamic Port\", \"King Abdulaziz Port\", \"King Fahd Industrial Port in Jubail\", \"King Fahd Industrial Port in Yanbu\", \"King Abdulaziz International Airport\", \"King Khalid International Airport\", \"King Fahd International Airport\", \"King Abdullah Port\"]}', 1, '2024-08-30 12:48:24'),
(66, 'Singapore', 'singapore', '{\"primary_commercial_hub\": \"Downtown Core\", \"major_sub_locations\": [\"Orchard\", \"Marina Bay\", \"Jurong East\", \"Woodlands\", \"Tampines\"], \"industrial_hubs\": [\"Jurong Island\", \"Jurong Industrial Estate\", \"Tuas\", \"Woodlands Industrial Park\", \"Changi Business Park\"], \"trade_and_logistics_hubs\": [\"Port of Singapore\", \"Singapore Changi Airport\", \"Tuas Port\", \"Pasir Panjang Terminal\", \"Singapore Airport Logistics Park\"]}', 1, '2024-08-30 12:48:24'),
(67, 'South Korea', 'south-korea', '{\"primary_commercial_hub\": \"Seoul\", \"major_sub_locations\": [\"Busan\", \"Incheon\", \"Daegu\", \"Daejeon\", \"Gwangju\", \"Ulsan\", \"Suwon\", \"Jeju City\"], \"industrial_hubs\": [\"Ulsan\", \"Pohang\", \"Gumi\", \"Changwon\", \"Yeosu\", \"Hwaseong\"], \"trade_and_logistics_hubs\": [\"Port of Busan\", \"Port of Incheon\", \"Incheon International Airport\", \"Port of Gwangyang\", \"Port of Ulsan\", \"Busan New Port\"]}', 1, '2024-08-30 12:48:24'),
(68, 'Sri Lanka', 'sri-lanka', '{\"primary_commercial_hub\": \"Colombo\", \"major_sub_locations\": [\"Kandy\", \"Galle\", \"Jaffna\", \"Negombo\", \"Anuradhapura\", \"Trincomalee\"], \"industrial_hubs\": [\"Katunayake Export Processing Zone\", \"Biyagama Export Processing Zone\", \"Koggala Export Processing Zone\", \"Seethawaka Export Processing Zone\"], \"trade_and_logistics_hubs\": [\"Port of Colombo\", \"Bandaranaike International Airport\", \"Hambantota International Port\", \"Port of Trincomalee\"]}', 1, '2024-08-30 12:48:24'),
(69, 'Taiwan', 'taiwan', '{\"primary_commercial_hub\": \"Taipei\", \"major_sub_locations\": [\"New Taipei City\", \"Taichung\", \"Tainan\", \"Kaohsiung\", \"Hsinchu\"], \"industrial_hubs\": [\"Hsinchu Science Park\", \"Central Taiwan Science Park\", \"Southern Taiwan Science Park\", \"Taichung Industrial Park\", \"Kaohsiung Linhai Industrial Park\"], \"trade_and_logistics_hubs\": [\"Port of Kaohsiung\", \"Port of Keelung\", \"Port of Taichung\", \"Taiwan Taoyuan International Airport\", \"Kaohsiung International Airport\"]}', 1, '2024-08-30 12:48:24'),
(70, 'Tajikistan', 'tajikistan', '{\"primary_commercial_hub\": \"Dushanbe\", \"major_sub_locations\": [\"Khujand\", \"Bokhtar\", \"Kulob\", \"Istaravshan\", \"Khorugh\"], \"industrial_hubs\": [\"Dushanbe\", \"Khujand\", \"Tursunzoda\", \"Yovon\"], \"trade_and_logistics_hubs\": [\"Dushanbe International Airport\", \"Khujand International Airport\", \"Kulma Pass\"]}', 1, '2024-08-30 12:48:24'),
(71, 'Thailand', 'thailand', '{\"primary_commercial_hub\": \"Bangkok\", \"major_sub_locations\": [\"Chiang Mai\", \"Phuket\", \"Pattaya\", \"Nakhon Ratchasima\", \"Khon Kaen\", \"Hat Yai\"], \"industrial_hubs\": [\"Eastern Economic Corridor\", \"Rayong\", \"Chonburi\", \"Samut Prakan\", \"Ayutthaya\"], \"trade_and_logistics_hubs\": [\"Port of Laem Chabang\", \"Port of Bangkok\", \"Suvarnabhumi Airport\", \"Don Mueang International Airport\", \"Lat Krabang Inland Container Depot\"]}', 1, '2024-08-30 12:48:24'),
(72, 'Turkmenistan', 'turkmenistan', '{\"primary_commercial_hub\": \"Ashgabat\", \"major_sub_locations\": [\"Türkmenabat\", \"Mary\", \"Balkanabat\", \"Daşoguz\", \"Turkmenbashi\"], \"industrial_hubs\": [\"Turkmenbashi\", \"Mary\", \"Türkmenabat\", \"Balkanabat\"], \"trade_and_logistics_hubs\": [\"Turkmenbashi International Seaport\", \"Ashgabat International Airport\", \"Türkmenabat\"]}', 1, '2024-08-30 12:48:24'),
(73, 'United Arab Emirates', 'united-arab-emirates', '{\"primary_commercial_hub\": \"Dubai\", \"major_sub_locations\": [\"Abu Dhabi\", \"Sharjah\", \"Ajman\", \"Al Ain\", \"Ras Al Khaimah\", \"Fujairah\"], \"industrial_hubs\": [\"Jebel Ali Industrial Area\", \"Khalifa Industrial Zone Abu Dhabi\", \"Sharjah Industrial Areas\", \"Hamriyah Free Zone\", \"Ras Al Khaimah Economic Zone\"], \"trade_and_logistics_hubs\": [\"Jebel Ali Port\", \"Port of Khalifa\", \"Port of Fujairah\", \"Sharjah International Airport\", \"Dubai International Airport\", \"Al Maktoum International Airport\"]}', 1, '2024-08-30 12:48:24'),
(74, 'UAE', 'uae', '{\"primary_commercial_hub\": \"Dubai\", \"major_sub_locations\": [\"Abu Dhabi\", \"Sharjah\", \"Ajman\", \"Al Ain\", \"Ras Al Khaimah\", \"Fujairah\"], \"industrial_hubs\": [\"Jebel Ali Industrial Area\", \"Khalifa Industrial Zone Abu Dhabi (KIZAD)\", \"Mussafah Industrial Area\", \"Sharjah Industrial Areas\", \"Ras Al Khaimah Economic Zone (RAKEZ)\", \"Fujairah Free Zone\"], \"trade_and_logistics_hubs\": [\"Jebel Ali Port\", \"Port of Khalifa\", \"Port of Fujairah\", \"Zayed International Airport\", \"Dubai International Airport\", \"Dubai South\"]}', 1, '2024-08-30 12:48:24'),
(75, 'Uzbekistan', 'uzbekistan', '{\"primary_commercial_hub\": \"Tashkent\", \"major_sub_locations\": [\"Samarkand\", \"Bukhara\", \"Namangan\", \"Andijan\", \"Fergana\", \"Nukus\", \"Urgench\", \"Qarshi\", \"Termez\"], \"industrial_hubs\": [\"Navoi\", \"Almalyk\", \"Angren\", \"Bekabad\", \"Chirchiq\", \"Fergana\", \"Jizzakh\"], \"trade_and_logistics_hubs\": [\"Tashkent International Airport\", \"Navoi International Airport\", \"Port of Termez\", \"Angren Logistics Centre\", \"Dostyk railway station\"]}', 1, '2024-08-30 12:48:24'),
(76, 'Vietnam', 'vietnam', '{\"primary_commercial_hub\": \"Ho Chi Minh City\", \"major_sub_locations\": [\"Hanoi\", \"Da Nang\", \"Hai Phong\", \"Can Tho\", \"Nha Trang\", \"Vung Tau\"], \"industrial_hubs\": [\"Binh Duong\", \"Dong Nai\", \"Bac Ninh\", \"Hai Phong\", \"Bac Giang\"], \"trade_and_logistics_hubs\": [\"Port of Ho Chi Minh City\", \"Cai Mep–Thi Vai Port\", \"Port of Hai Phong\", \"Noi Bai International Airport\", \"Tan Son Nhat International Airport\", \"Da Nang International Airport\"]}', 1, '2024-08-30 12:48:24'),
(77, 'Cameroon', 'cameroon', '{\"primary_commercial_hub\": \"Douala\", \"major_sub_locations\": [\"Yaoundé\", \"Bamenda\", \"Garoua\", \"Maroua\", \"Bafoussam\", \"Buea\", \"Limbe\", \"Kribi\"], \"industrial_hubs\": [\"Douala\", \"Edéa\", \"Bafoussam\", \"Garoua\"], \"trade_and_logistics_hubs\": [\"Port of Douala\", \"Port of Kribi\", \"Douala International Airport\", \"Yaoundé-Nsimalen International Airport\", \"Garoua International Airport\"]}', 1, '2024-08-30 12:48:24'),
(78, 'Botswana', 'botswana', '{\"primary_commercial_hub\": \"Gaborone\", \"major_sub_locations\": [\"Francistown\", \"Maun\", \"Molepolole\", \"Serowe\", \"Selebi-Phikwe\"], \"industrial_hubs\": [\"Gaborone\", \"Francistown\", \"Selebi-Phikwe\", \"Jwaneng\", \"Orapa\"], \"trade_and_logistics_hubs\": [\"Sir Seretse Khama International Airport\", \"Francistown\", \"Maun\", \"Kazungula\", \"Gaborone\"]}', 1, '2024-08-30 12:48:24'),
(79, 'Egypt', 'egypt', '{\"primary_commercial_hub\": \"Cairo\", \"major_sub_locations\": [\"Alexandria\", \"Giza\", \"Port Said\", \"Suez\", \"Mansoura\", \"Tanta\", \"Asyut\", \"Luxor\", \"Aswan\"], \"industrial_hubs\": [\"10th of Ramadan City\", \"6th of October City\", \"Sadat City\", \"Borg El Arab\", \"Suez\", \"Damietta\"], \"trade_and_logistics_hubs\": [\"Port of Alexandria\", \"Port Said Port\", \"Port of Damietta\", \"Ain Sokhna Port\", \"Cairo International Airport\", \"Suez Canal\"]}', 1, '2024-08-30 12:48:24'),
(80, 'Ethiopia', 'ethiopia', '{\"primary_commercial_hub\": \"Addis Ababa\", \"major_sub_locations\": [\"Dire Dawa\", \"Mekelle\", \"Hawassa\", \"Bahir Dar\", \"Adama\", \"Jimma\", \"Gondar\"], \"industrial_hubs\": [\"Hawassa Industrial Park\", \"Bole Lemi Industrial Park\", \"Eastern Industrial Park\", \"Adama Industrial Park\", \"Dire Dawa Industrial Park\"], \"trade_and_logistics_hubs\": [\"Port of Djibouti\", \"Addis Ababa Bole International Airport\", \"Dire Dawa\", \"Modjo Dry Port\", \"Adama\"]}', 1, '2024-08-30 12:48:24'),
(81, 'Ghana', 'ghana', '{\"primary_commercial_hub\": \"Accra\", \"major_sub_locations\": [\"Kumasi\", \"Tamale\", \"Sekondi-Takoradi\", \"Cape Coast\", \"Tema\", \"Koforidua\", \"Sunyani\", \"Ho\"], \"industrial_hubs\": [\"Tema\", \"Sekondi-Takoradi\", \"Kumasi\", \"Obuasi\", \"Tarkwa\"], \"trade_and_logistics_hubs\": [\"Port of Tema\", \"Port of Takoradi\", \"Kotoka International Airport\", \"Tamale International Airport\", \"Boankra Integrated Logistics Terminal\"]}', 1, '2024-08-30 12:48:24'),
(82, 'Guinea', 'guinea', '{\"primary_commercial_hub\": \"Conakry\", \"major_sub_locations\": [\"Kankan\", \"Labé\", \"Kindia\", \"Nzérékoré\", \"Boké\", \"Mamou\", \"Faranah\", \"Guéckédou\"], \"industrial_hubs\": [\"Kamsar\", \"Fria\", \"Sangaredi\"], \"trade_and_logistics_hubs\": [\"Port of Conakry\", \"Port of Kamsar\", \"Conakry International Airport\", \"Sangaredi\"]}', 1, '2024-08-30 12:48:24'),
(83, 'Kenya', 'kenya', '{\"primary_commercial_hub\": \"Nairobi\", \"major_sub_locations\": [\"Mombasa\", \"Kisumu\", \"Nakuru\", \"Eldoret\", \"Thika\", \"Garissa\"], \"industrial_hubs\": [\"Athi River\", \"Thika\", \"Nairobi\", \"Mombasa\", \"Kisumu\"], \"trade_and_logistics_hubs\": [\"Port of Mombasa\", \"Jomo Kenyatta International Airport\", \"Kisumu Port\", \"Nairobi Inland Container Depot\", \"Moi International Airport\"]}', 1, '2024-08-30 12:48:24'),
(84, 'Madagascar', 'madagascar', '{\"primary_commercial_hub\": \"Antananarivo\", \"major_sub_locations\": [\"Toamasina\", \"Antsirabe\", \"Mahajanga\", \"Fianarantsoa\", \"Toliara\", \"Antsiranana\"], \"industrial_hubs\": [\"Antananarivo\", \"Antsirabe\", \"Toamasina\"], \"trade_and_logistics_hubs\": [\"Port of Toamasina\", \"Ivato International Airport\", \"Port of Mahajanga\", \"Port of Toliara\"]}', 1, '2024-08-30 12:48:24'),
(85, 'Mauritius', 'mauritius', '{\"primary_commercial_hub\": \"Port Louis\", \"major_sub_locations\": [\"Curepipe\", \"Quatre Bornes\", \"Vacoas-Phoenix\", \"Beau Bassin-Rose Hill\", \"Mahébourg\"], \"industrial_hubs\": [\"Plaine Lauzun\", \"Coromandel Industrial Zone\", \"Riche Terre Industrial Zone\", \"Phoenix Industrial Park\"], \"trade_and_logistics_hubs\": [\"Port of Port Louis\", \"Sir Seewoosagur Ramgoolam International Airport\", \"Riche Terre Container Terminal\"]}', 1, '2024-08-30 12:48:24'),
(86, 'Morocco', 'morocco', '{\"primary_commercial_hub\": \"Casablanca\", \"major_sub_locations\": [\"Rabat\", \"Marrakech\", \"Fès\", \"Tangier\", \"Agadir\", \"Meknes\", \"Oujda\"], \"industrial_hubs\": [\"Casablanca\", \"Tangier\", \"Kenitra\", \"Jorf Lasfar\", \"Safi\"], \"trade_and_logistics_hubs\": [\"Port of Casablanca\", \"Tanger Med\", \"Port of Jorf Lasfar\", \"Port of Agadir\", \"Mohammed V International Airport\"]}', 1, '2024-08-30 12:48:24'),
(87, 'Mozambique', 'mozambique', '{\"primary_commercial_hub\": \"Maputo\", \"major_sub_locations\": [\"Matola\", \"Beira\", \"Nampula\", \"Nacala\", \"Pemba\", \"Tete\", \"Quelimane\", \"Chimoio\", \"Xai-Xai\", \"Inhambane\"], \"industrial_hubs\": [\"Matola Industrial Zone\", \"Mozal Aluminium Smelter\", \"Beira Industrial Zone\", \"Nacala Industrial Zone\", \"Tete coal-mining region\"], \"trade_and_logistics_hubs\": [\"Port of Maputo\", \"Port of Beira\", \"Port of Nacala\", \"Port of Pemba\", \"Maputo International Airport\", \"Beira Airport\", \"Ressano Garcia border crossing\", \"Machipanda border crossing\"]}', 1, '2024-08-30 12:48:24'),
(88, 'Namibia', 'namibia', '{\"primary_commercial_hub\": \"Windhoek\", \"major_sub_locations\": [\"Walvis Bay\", \"Swakopmund\", \"Oshakati\", \"Rundu\", \"Ondangwa\", \"Keetmanshoop\"], \"industrial_hubs\": [\"Windhoek\", \"Walvis Bay\", \"Oshakati\"], \"trade_and_logistics_hubs\": [\"Port of Walvis Bay\", \"Port of Lüderitz\", \"Hosea Kutako International Airport\", \"Walvis Bay Corridor\"]}', 1, '2024-08-30 12:48:24'),
(89, 'Nigeria', 'nigeria', '{\"primary_commercial_hub\": \"Lagos\", \"major_sub_locations\": [\"Abuja\", \"Kano\", \"Port Harcourt\", \"Ibadan\", \"Kaduna\", \"Enugu\"], \"industrial_hubs\": [\"Lagos\", \"Kano\", \"Aba\", \"Onitsha\", \"Nnewi\", \"Agbara Industrial Estate\"], \"trade_and_logistics_hubs\": [\"Apapa Port\", \"Tin Can Island Port\", \"Murtala Muhammed International Airport\", \"Port Harcourt Port\", \"Onne Port\", \"Nnamdi Azikiwe International Airport\"]}', 1, '2024-08-30 12:48:24'),
(90, 'Senegal', 'senegal', '{\"primary_commercial_hub\": \"Dakar\", \"major_sub_locations\": [\"Thiès\", \"Saint-Louis\", \"Kaolack\", \"Ziguinchor\", \"Touba\"], \"industrial_hubs\": [\"Dakar\", \"Thiès\", \"Kaolack\", \"Diamniadio\"], \"trade_and_logistics_hubs\": [\"Port of Dakar\", \"Blaise Diagne International Airport\", \"Dakar\", \"Kaolack\"]}', 1, '2024-08-30 12:48:24'),
(91, 'Seychelles', 'seychelles', '{\"primary_commercial_hub\": \"Victoria\", \"major_sub_locations\": [\"Anse Royale\", \"Beau Vallon\", \"Anse Boileau\", \"Takamaka\"], \"industrial_hubs\": [\"Providence Industrial Estate\"], \"trade_and_logistics_hubs\": [\"Port Victoria\", \"Seychelles International Airport\"]}', 1, '2024-08-30 12:48:24'),
(92, 'Sierra Leone', 'sierra-leone', '{\"primary_commercial_hub\": \"Freetown\", \"major_sub_locations\": [\"Bo\", \"Kenema\", \"Makeni\", \"Koidu\", \"Port Loko\"], \"industrial_hubs\": [\"Freetown\", \"Mabole Industrial Estate\", \"Pepel\"], \"trade_and_logistics_hubs\": [\"Queen Elizabeth II Quay\", \"Freetown International Airport\", \"Pepel Port\"]}', 1, '2024-08-30 12:48:24'),
(93, 'South Africa', 'south-africa', '{\"primary_commercial_hub\": \"Johannesburg\", \"major_sub_locations\": [\"Cape Town\", \"Durban\", \"Pretoria\", \"Gqeberha\", \"Bloemfontein\", \"Polokwane\", \"Mbombela\", \"Kimberley\"], \"industrial_hubs\": [\"Vanderbijlpark\", \"Sasolburg\", \"Secunda\", \"eMalahleni\", \"Newcastle\", \"Richards Bay\", \"Saldanha Bay\"], \"trade_and_logistics_hubs\": [\"Port of Durban\", \"Port of Cape Town\", \"Port of Ngqura\", \"Port of Richards Bay\", \"O. R. Tambo International Airport\", \"Cape Town International Airport\", \"King Shaka International Airport\", \"City Deep Freight Terminal\"]}', 1, '2024-08-30 12:48:24'),
(94, 'Sudan', 'sudan', '{\"primary_commercial_hub\": \"Khartoum\", \"major_sub_locations\": [\"Omdurman\", \"Port Sudan\", \"Kassala\", \"El Obeid\", \"Wad Madani\", \"Nyala\"], \"industrial_hubs\": [\"Khartoum\", \"Port Sudan\", \"Atbara\", \"Kosti\"], \"trade_and_logistics_hubs\": [\"Port Sudan\", \"Khartoum International Airport\", \"Atbara\", \"Kosti\"]}', 1, '2024-08-30 12:48:24'),
(95, 'Tanzania', 'tanzania', '{\"primary_commercial_hub\": \"Dar es Salaam\", \"major_sub_locations\": [\"Dodoma\", \"Arusha\", \"Mwanza\", \"Mbeya\", \"Morogoro\", \"Zanzibar City\"], \"industrial_hubs\": [\"Dar es Salaam\", \"Mwanza\", \"Tanga\", \"Morogoro\"], \"trade_and_logistics_hubs\": [\"Port of Dar es Salaam\", \"Port of Tanga\", \"Port of Mtwara\", \"Julius Nyerere International Airport\", \"Kilimanjaro International Airport\"]}', 1, '2024-08-30 12:48:24'),
(96, 'Uganda', 'uganda', '{\"primary_commercial_hub\": \"Kampala\", \"major_sub_locations\": [\"Entebbe\", \"Jinja\", \"Mbarara\", \"Gulu\", \"Mbale\", \"Fort Portal\"], \"industrial_hubs\": [\"Namanve Industrial Park\", \"Luzira Industrial Area\", \"Jinja\", \"Mbarara\"], \"trade_and_logistics_hubs\": [\"Port Bell\", \"Entebbe International Airport\", \"Malaba Border Post\", \"Busia Border Post\", \"Kampala\"]}', 1, '2024-08-30 12:48:24'),
(97, 'Zambia', 'zambia', '{\"primary_commercial_hub\": \"Lusaka\", \"major_sub_locations\": [\"Kitwe\", \"Ndola\", \"Livingstone\", \"Kabwe\", \"Chipata\", \"Kasama\"], \"industrial_hubs\": [\"Kitwe\", \"Ndola\", \"Chingola\", \"Mufulira\", \"Lusaka\"], \"trade_and_logistics_hubs\": [\"Lusaka\", \"Ndola\", \"Kitwe\", \"Livingstone\", \"Kenneth Kaunda International Airport\", \"Port of Dar es Salaam\"]}', 1, '2024-08-30 12:48:24'),
(98, 'Zimbabwe', 'zimbabwe', '{\"primary_commercial_hub\": \"Harare\", \"major_sub_locations\": [\"Bulawayo\", \"Mutare\", \"Gweru\", \"Kwekwe\", \"Masvingo\", \"Chinhoyi\", \"Victoria Falls\"], \"industrial_hubs\": [\"Harare\", \"Bulawayo\", \"Kwekwe\", \"Gweru\", \"Redcliff\", \"Hwange\"], \"trade_and_logistics_hubs\": [\"Port of Beira corridor\", \"Robert Gabriel Mugabe International Airport\", \"Joshua Mqabuko Nkomo International Airport\", \"Beitbridge Border Post\", \"Victoria Falls Border Post\"]}', 1, '2024-08-30 12:48:24'),
(99, 'Antigua and Barbuda', 'antigua-and-barbuda', '{\"primary_commercial_hub\": \"St. John\'s\", \"major_sub_locations\": [\"Codrington\", \"All Saints\", \"Liberta\", \"English Harbour\"], \"industrial_hubs\": [\"St. John\'s\", \"Coolidge\"], \"trade_and_logistics_hubs\": [\"Port of St. John\'s\", \"V. C. Bird International Airport\", \"Codrington Airport\"]}', 1, '2024-08-30 12:48:24'),
(100, 'Bahamas', 'bahamas', '{\"primary_commercial_hub\": \"Nassau\", \"major_sub_locations\": [\"Freeport\", \"Marsh Harbour\", \"George Town\", \"Alice Town\"], \"industrial_hubs\": [\"Freeport\"], \"trade_and_logistics_hubs\": [\"Port of Nassau\", \"Freeport Harbour\", \"Lynden Pindling International Airport\"]}', 1, '2024-08-30 12:48:24'),
(101, 'Barbados', 'barbados', '{\"primary_commercial_hub\": \"Bridgetown\", \"major_sub_locations\": [\"Bridgetown\", \"Speightstown\", \"Oistins\", \"Holetown\", \"The Crane\"], \"industrial_hubs\": [\"Wildey\", \"Spring Garden Industrial Estate\", \"Newton Industrial Park\"], \"trade_and_logistics_hubs\": [\"Port of Bridgetown\", \"Grantley Adams International Airport\"]}', 1, '2024-08-30 12:48:24'),
(102, 'Belize', 'belize', '{\"primary_commercial_hub\": \"Belize City\", \"major_sub_locations\": [\"Belmopan\", \"San Ignacio\", \"Orange Walk Town\", \"Corozal Town\", \"Dangriga\", \"Punta Gorda\", \"San Pedro Town\"], \"industrial_hubs\": [\"Belize City\", \"Orange Walk Town\", \"Dangriga\"], \"trade_and_logistics_hubs\": [\"Port of Belize\", \"Philip S. W. Goldson International Airport\", \"Belize City\", \"Big Creek Port\", \"Puerto Cortés\"]}', 1, '2024-08-30 12:48:24');
INSERT INTO `tbl_location` (`id`, `title`, `url`, `data`, `status`, `created_at`) VALUES
(103, 'Canada', 'canada', '{\"primary_commercial_hub\": \"Toronto\", \"major_sub_locations\": [\"Montreal\", \"Vancouver\", \"Calgary\", \"Edmonton\", \"Ottawa\", \"Winnipeg\", \"Quebec City\", \"Halifax\"], \"industrial_hubs\": [\"Hamilton\", \"Windsor\", \"Sarnia\", \"Sudbury\", \"Saskatoon\", \"Lloydminster\"], \"trade_and_logistics_hubs\": [\"Port of Vancouver\", \"Port of Montreal\", \"Port of Prince Rupert\", \"Port of Halifax\", \"Toronto Pearson International Airport\", \"Port of Saint John\", \"Port of Québec\"]}', 1, '2024-08-30 12:48:24'),
(104, 'Costa Rica', 'costa-rica', '{\"primary_commercial_hub\": \"San José\", \"major_sub_locations\": [\"Alajuela\", \"Cartago\", \"Heredia\", \"Liberia\", \"Puntarenas\"], \"industrial_hubs\": [\"Coyol Free Zone\", \"El Barreal Free Zone\", \"Cartago Industrial Park\", \"Zona Franca La Lima\"], \"trade_and_logistics_hubs\": [\"Juan Santamaría International Airport\", \"Port of Limón\", \"Port of Caldera\", \"Puerto Moín\"]}', 1, '2024-08-30 12:48:24'),
(105, 'Cuba', 'cuba', '{\"primary_commercial_hub\": \"Havana\", \"major_sub_locations\": [\"Santiago de Cuba\", \"Camagüey\", \"Holguín\", \"Santa Clara\", \"Cienfuegos\", \"Matanzas\"], \"industrial_hubs\": [\"Havana\", \"Santiago de Cuba\", \"Cienfuegos\", \"Nuevitas\", \"Moa\"], \"trade_and_logistics_hubs\": [\"Port of Havana\", \"Port of Santiago de Cuba\", \"Port of Mariel\", \"Port of Cienfuegos\", \"José Martí International Airport\"]}', 1, '2024-08-30 12:48:24'),
(106, 'Guatemala', 'guatemala', '{\"primary_commercial_hub\": \"Guatemala City\", \"major_sub_locations\": [\"Quetzaltenango\", \"Escuintla\", \"Puerto Barrios\", \"Antigua Guatemala\", \"Cobán\"], \"industrial_hubs\": [\"Guatemala City\", \"Escuintla\", \"Puerto Barrios\"], \"trade_and_logistics_hubs\": [\"Puerto Quetzal\", \"Puerto Barrios\", \"La Aurora International Airport\", \"Santo Tomás de Castilla\"]}', 1, '2024-08-30 12:48:24'),
(107, 'Jamaica', 'jamaica', '{\"primary_commercial_hub\": \"Kingston\", \"major_sub_locations\": [\"Montego Bay\", \"Spanish Town\", \"Portmore\", \"Mandeville\", \"Ocho Rios\", \"May Pen\"], \"industrial_hubs\": [\"Kingston\", \"Montego Bay\", \"Spanish Town\", \"Port Esquivel\"], \"trade_and_logistics_hubs\": [\"Port of Kingston\", \"Kingston Free Zone\", \"Norman Manley International Airport\", \"Sangster International Airport\", \"Port of Montego Bay\"]}', 1, '2024-08-30 12:48:24'),
(108, 'Mexico', 'mexico', '{\"primary_commercial_hub\": \"Mexico City\", \"major_sub_locations\": [\"Guadalajara\", \"Monterrey\", \"Puebla\", \"Tijuana\", \"León\", \"Mérida\", \"Querétaro\", \"Toluca\"], \"industrial_hubs\": [\"Monterrey\", \"Saltillo\", \"Querétaro\", \"Puebla\", \"Ciudad Juárez\", \"Tijuana\", \"León\", \"Silao\"], \"trade_and_logistics_hubs\": [\"Port of Manzanillo\", \"Port of Lázaro Cárdenas\", \"Port of Veracruz\", \"Port of Altamira\", \"Mexico City International Airport\", \"Monterrey International Airport\", \"Ciudad Juárez\"]}', 1, '2024-08-30 12:48:24'),
(109, 'Panama', 'panama', '{\"primary_commercial_hub\": \"Panama City\", \"major_sub_locations\": [\"Colón\", \"David\", \"Santiago de Veraguas\", \"Chitré\", \"La Chorrera\"], \"industrial_hubs\": [\"Panama City\", \"Colón Free Zone\", \"Panamá Pacífico\", \"Tocumen International Airport Logistics Zone\"], \"trade_and_logistics_hubs\": [\"Port of Balboa\", \"Port of Cristóbal\", \"Manzanillo International Terminal\", \"Port of Colón Container Terminal\", \"Tocumen International Airport\", \"Panama Canal\"]}', 1, '2024-08-30 12:48:24'),
(110, 'Trinidad and Tobago', 'trinidad-and-tobago', '{\"primary_commercial_hub\": \"Port of Spain\", \"major_sub_locations\": [\"San Fernando\", \"Chaguanas\", \"Arima\", \"Scarborough\", \"Point Fortin\"], \"industrial_hubs\": [\"Point Lisas\", \"La Brea\", \"Point Fortin\", \"Pointe-à-Pierre\"], \"trade_and_logistics_hubs\": [\"Port of Port of Spain\", \"Port of Point Lisas\", \"Port of Scarborough\", \"Piarco International Airport\"]}', 1, '2024-08-30 12:48:24'),
(111, 'United States of America', 'united-states-of-america', '{\"primary_commercial_hub\": \"New York City\", \"major_sub_locations\": [\"Los Angeles\", \"Chicago\", \"Houston\", \"Phoenix\", \"Philadelphia\", \"San Antonio\", \"San Diego\", \"Dallas\", \"San Jose\", \"Austin\"], \"industrial_hubs\": [\"Detroit\", \"Pittsburgh\", \"Cleveland\", \"Houston\", \"Los Angeles\", \"Chicago\", \"Seattle\", \"Greenville-Spartanburg\"], \"trade_and_logistics_hubs\": [\"Port of Los Angeles\", \"Port of Long Beach\", \"Port of New York and New Jersey\", \"Port of Houston\", \"Port of Savannah\", \"Chicago O\'Hare International Airport\", \"Memphis International Airport\", \"Port of Seattle\"]}', 1, '2024-08-30 12:48:24'),
(112, 'USA', 'usa', '{\"primary_commercial_hub\": \"New York City\", \"major_sub_locations\": [\"Los Angeles\", \"Chicago\", \"Houston\", \"Dallas–Fort Worth\", \"San Francisco Bay Area\", \"Atlanta\", \"Miami\", \"Seattle\", \"Washington, D.C.\", \"Boston\"], \"industrial_hubs\": [\"Detroit\", \"Pittsburgh\", \"Cleveland\", \"Houston\", \"Los Angeles\", \"Chicago\", \"Seattle\", \"Greenville–Spartanburg\"], \"trade_and_logistics_hubs\": [\"Port of Los Angeles\", \"Port of Long Beach\", \"Port of New York and New Jersey\", \"Port of Houston\", \"Port of Savannah\", \"Port of Seattle\", \"Port of Charleston\", \"Chicago O’Hare International Airport\", \"Memphis International Airport\"]}', 1, '2024-08-30 12:48:24'),
(113, 'Argentina', 'argentina', '{\"primary_commercial_hub\": \"Buenos Aires\", \"major_sub_locations\": [\"Córdoba\", \"Rosario\", \"Mendoza\", \"San Miguel de Tucumán\", \"Mar del Plata\", \"La Plata\", \"Salta\", \"Neuquén\"], \"industrial_hubs\": [\"Greater Buenos Aires\", \"Córdoba\", \"Rosario\", \"Campana\", \"San Nicolás de los Arroyos\", \"Bahía Blanca\", \"Ushuaia\"], \"trade_and_logistics_hubs\": [\"Port of Buenos Aires\", \"Port of Rosario\", \"Port of Bahía Blanca\", \"Port of Quequén\", \"Ezeiza International Airport\", \"Córdoba International Airport\", \"Paso de los Libres–Uruguaiana border crossing\"]}', 1, '2024-08-30 12:48:24'),
(114, 'Brazil', 'brazil', '{\"primary_commercial_hub\": \"São Paulo\", \"major_sub_locations\": [\"Rio de Janeiro\", \"Brasília\", \"Belo Horizonte\", \"Salvador\", \"Curitiba\", \"Porto Alegre\", \"Recife\", \"Fortaleza\", \"Manaus\", \"Belém\"], \"industrial_hubs\": [\"ABC Paulista\", \"Campinas\", \"São José dos Campos\", \"Manaus\", \"Camaçari\", \"Joinville\", \"Betim\", \"Suape\"], \"trade_and_logistics_hubs\": [\"Port of Santos\", \"Port of Paranaguá\", \"Port of Rio de Janeiro\", \"Port of Itajaí\", \"Port of Suape\", \"Viracopos International Airport\", \"São Paulo–Guarulhos International Airport\", \"Port of Manaus\"]}', 1, '2024-08-30 12:48:24'),
(115, 'Chile', 'chile', '{\"primary_commercial_hub\": \"Santiago\", \"major_sub_locations\": [\"Valparaíso\", \"Viña del Mar\", \"Concepción\", \"Antofagasta\", \"La Serena\", \"Puerto Montt\", \"Temuco\"], \"industrial_hubs\": [\"Santiago\", \"Concepción\", \"Antofagasta\", \"Talcahuano\", \"Calama\"], \"trade_and_logistics_hubs\": [\"Port of Valparaíso\", \"Port of San Antonio\", \"Port of Antofagasta\", \"Port of Iquique\", \"Arturo Merino Benítez International Airport\"]}', 1, '2024-08-30 12:48:24'),
(116, 'Colombia', 'colombia', '{\"primary_commercial_hub\": \"Bogotá\", \"major_sub_locations\": [\"Medellín\", \"Cali\", \"Barranquilla\", \"Cartagena\", \"Bucaramanga\", \"Pereira\", \"Manizales\", \"Cúcuta\", \"Santa Marta\", \"Buenaventura\"], \"industrial_hubs\": [\"Medellín\", \"Cali\", \"Barranquilla\", \"Cartagena\", \"Bucaramanga\", \"Manizales\", \"Yumbo\", \"Itagüí\", \"Mamonal\"], \"trade_and_logistics_hubs\": [\"Port of Buenaventura\", \"Port of Cartagena\", \"Port of Barranquilla\", \"Port of Santa Marta\", \"El Dorado International Airport\", \"José María Córdova International Airport\", \"Cali–Yumbo logistics corridor\", \"Cúcuta\"]}', 1, '2024-08-30 12:48:24'),
(117, 'Ecuador', 'ecuador', '{\"primary_commercial_hub\": \"Guayaquil\", \"major_sub_locations\": [\"Quito\", \"Cuenca\", \"Santo Domingo\", \"Machala\", \"Manta\", \"Ambato\", \"Loja\"], \"industrial_hubs\": [\"Guayaquil\", \"Quito\", \"Cuenca\", \"Ambato\", \"Manta\"], \"trade_and_logistics_hubs\": [\"Port of Guayaquil\", \"Port of Posorja\", \"Port of Manta\", \"Mariscal Sucre International Airport\", \"José Joaquín de Olmedo International Airport\"]}', 1, '2024-08-30 12:48:24'),
(118, 'Guyana', 'guyana', '{\"primary_commercial_hub\": \"Georgetown\", \"major_sub_locations\": [\"Linden\", \"New Amsterdam\", \"Bartica\", \"Lethem\", \"Anna Regina\"], \"industrial_hubs\": [\"Linden\", \"Georgetown\", \"Skeldon\"], \"trade_and_logistics_hubs\": [\"Port of Georgetown\", \"Cheddi Jagan International Airport\", \"Eugene F. Correia International Airport\", \"Lethem\"]}', 1, '2024-08-30 12:48:24'),
(119, 'Paraguay', 'paraguay', '{\"primary_commercial_hub\": \"Asunción\", \"major_sub_locations\": [\"Ciudad del Este\", \"Encarnación\", \"San Lorenzo\", \"Pedro Juan Caballero\", \"Concepción\"], \"industrial_hubs\": [\"Villeta\", \"Limpio\", \"Ciudad del Este\", \"Encarnación\"], \"trade_and_logistics_hubs\": [\"Port of Asunción\", \"Port of Villeta\", \"Ciudad del Este\", \"Silvio Pettirossi International Airport\", \"Encarnación\"]}', 1, '2024-08-30 12:48:24'),
(120, 'Peru', 'peru', '{\"primary_commercial_hub\": \"Lima\", \"major_sub_locations\": [\"Arequipa\", \"Trujillo\", \"Chiclayo\", \"Piura\", \"Cusco\", \"Iquitos\"], \"industrial_hubs\": [\"Callao\", \"Arequipa\", \"Trujillo\", \"Chimbote\", \"Ilo\"], \"trade_and_logistics_hubs\": [\"Port of Callao\", \"Jorge Chávez International Airport\", \"Port of Paita\", \"Port of Matarani\", \"Port of Ilo\"]}', 1, '2024-08-30 12:48:24'),
(121, 'Uruguay', 'uruguay', '{\"primary_commercial_hub\": \"Montevideo\", \"major_sub_locations\": [\"Montevideo\", \"Salto\", \"Paysandú\", \"Maldonado\", \"Colonia del Sacramento\", \"Rivera\"], \"industrial_hubs\": [\"Montevideo\", \"Paysandú\", \"Fray Bentos\", \"Nueva Palmira\"], \"trade_and_logistics_hubs\": [\"Port of Montevideo\", \"Port of Nueva Palmira\", \"Carrasco International Airport\", \"Rivera–Santana do Livramento\"]}', 1, '2024-08-30 12:48:24'),
(122, 'Venezuela', 'venezuela', '{\"primary_commercial_hub\": \"Caracas\", \"major_sub_locations\": [\"Maracaibo\", \"Valencia\", \"Barquisimeto\", \"Maracay\", \"Ciudad Guayana\", \"Maturín\"], \"industrial_hubs\": [\"Valencia\", \"Puerto Ordaz\", \"Maracaibo\", \"Barquisimeto\", \"Punto Fijo\"], \"trade_and_logistics_hubs\": [\"Port of La Guaira\", \"Port of Puerto Cabello\", \"Port of Maracaibo\", \"Simón Bolívar International Airport\", \"Arturo Michelena International Airport\"]}', 1, '2024-08-30 12:48:24'),
(123, 'Australia', 'australia', '{\"primary_commercial_hub\": \"Sydney\", \"major_sub_locations\": [\"Melbourne\", \"Brisbane\", \"Perth\", \"Adelaide\", \"Canberra\", \"Hobart\", \"Darwin\"], \"industrial_hubs\": [\"Melbourne\", \"Western Sydney\", \"Gladstone\", \"Newcastle\", \"Geelong\", \"Kwinana Industrial Area\", \"Whyalla\"], \"trade_and_logistics_hubs\": [\"Port of Melbourne\", \"Port of Sydney\", \"Port of Brisbane\", \"Port of Fremantle\", \"Port of Adelaide\", \"Port of Newcastle\", \"Sydney Airport\", \"Melbourne Airport\"]}', 1, '2024-08-30 12:48:24'),
(124, 'Fiji', 'fiji', '{\"primary_commercial_hub\": \"Suva\", \"major_sub_locations\": [\"Nadi\", \"Lautoka\", \"Labasa\", \"Ba\", \"Savusavu\"], \"industrial_hubs\": [\"Lautoka\", \"Ba\", \"Suva\"], \"trade_and_logistics_hubs\": [\"Port of Suva\", \"Port of Lautoka\", \"Nadi International Airport\", \"Labasa Airport\"]}', 1, '2024-08-30 12:48:24'),
(125, 'New Zealand', 'new-zealand', '{\"primary_commercial_hub\": \"Auckland\", \"major_sub_locations\": [\"Wellington\", \"Christchurch\", \"Hamilton\", \"Tauranga\", \"Dunedin\", \"Queenstown\"], \"industrial_hubs\": [\"Auckland\", \"Hamilton\", \"Tauranga\", \"Lower Hutt\", \"Christchurch\"], \"trade_and_logistics_hubs\": [\"Port of Auckland\", \"Port of Tauranga\", \"CentrePort Wellington\", \"Lyttelton Port\", \"Christchurch Airport\", \"Auckland Airport\"]}', 1, '2024-08-30 12:48:24'),
(126, 'Papua New Guinea', 'papua-new-guinea', '{\"primary_commercial_hub\": \"Port Moresby\", \"major_sub_locations\": [\"Lae\", \"Mount Hagen\", \"Madang\", \"Rabaul\", \"Goroka\", \"Kokopo\"], \"industrial_hubs\": [\"Lae\", \"Port Moresby\", \"Kimbe\", \"Mount Hagen\"], \"trade_and_logistics_hubs\": [\"Port of Port Moresby\", \"Port of Lae\", \"Jacksons International Airport\", \"Rabaul Port\", \"Madang Port\"]}', 1, '2024-08-30 12:48:24'),
(127, 'Micronesia', 'micronesia', '{\"primary_commercial_hub\": \"Palikir\", \"major_sub_locations\": [\"Kolonia\", \"Weno\", \"Tofol\", \"Colonia\"], \"industrial_hubs\": [\"Weno\"], \"trade_and_logistics_hubs\": [\"Port of Pohnpei\", \"Port of Weno\", \"Pohnpei International Airport\", \"Chuuk International Airport\"]}', 1, '2024-08-30 12:48:24'),
(128, 'Toronto', 'toronto', '{\"primary_commercial_hub\": \"Downtown Toronto\", \"major_sub_locations\": [\"North York\", \"Scarborough\", \"Etobicoke\", \"York\", \"East York\"], \"industrial_hubs\": [\"South Etobicoke\", \"North York\", \"Scarborough\"], \"trade_and_logistics_hubs\": [\"Port of Toronto\", \"Toronto Pearson International Airport\", \"Toronto Union Station\", \"Toronto Intermodal Terminal\"]}', 1, '2024-08-30 12:48:24'),
(129, 'Vancouver', 'vancouver', '{\"primary_commercial_hub\": \"Downtown Vancouver\", \"major_sub_locations\": [\"West End\", \"Kitsilano\", \"Mount Pleasant\", \"Commercial Drive\", \"Downtown Vancouver\"], \"industrial_hubs\": [\"Port of Vancouver South Shore\", \"North Vancouver industrial area\", \"Tilbury Industrial Park\"], \"trade_and_logistics_hubs\": [\"Port of Vancouver\", \"Vancouver International Airport\", \"Centerm Container Terminal\", \"Deltaport\"]}', 1, '2024-08-30 12:48:24'),
(130, 'Ottawa', 'ottawa', '{\"primary_commercial_hub\": \"Downtown Ottawa\", \"major_sub_locations\": [\"Centretown\", \"ByWard Market\", \"Kanata\", \"Nepean\", \"Orléans\", \"Barrhaven\", \"Gatineau\"], \"industrial_hubs\": [\"Kanata North Business Park\", \"Ottawa South Industrial Park\", \"Colonnade Road Business Park\"], \"trade_and_logistics_hubs\": [\"Ottawa Macdonald–Cartier International Airport\", \"Ottawa Intermodal Terminal\", \"Port of Ottawa\"]}', 1, '2024-08-30 12:48:24'),
(131, 'New York', 'new-york', '{\"primary_commercial_hub\": \"Midtown Manhattan\", \"major_sub_locations\": [\"Manhattan\", \"Brooklyn\", \"Queens\", \"The Bronx\", \"Staten Island\"], \"industrial_hubs\": [\"Brooklyn Navy Yard\", \"Hunts Point\"], \"trade_and_logistics_hubs\": [\"Port of New York and New Jersey\", \"John F. Kennedy International Airport\", \"LaGuardia Airport\", \"Howland Hook Marine Terminal\"]}', 1, '2024-08-30 12:48:24'),
(132, 'Chicago', 'chicago', '{\"primary_commercial_hub\": \"The Loop\", \"major_sub_locations\": [\"The Loop\", \"River North\", \"West Loop\", \"Hyde Park\", \"Lincoln Park\", \"Evanston\"], \"industrial_hubs\": [\"Calumet Industrial Corridor\", \"Stockyards Industrial Corridor\", \"Pullman\"], \"trade_and_logistics_hubs\": [\"Port of Chicago\", \"O\'Hare International Airport\", \"Chicago Midway International Airport\", \"Chicago Union Station\"]}', 1, '2024-08-30 12:48:24'),
(133, 'San Francisco', 'san-francisco', '{\"primary_commercial_hub\": \"Financial District\", \"major_sub_locations\": [\"SoMa\", \"Union Square\", \"Chinatown\", \"Mission District\", \"Richmond District\", \"Sunset District\"], \"industrial_hubs\": [\"Bayview–Hunters Point\", \"India Basin\"], \"trade_and_logistics_hubs\": [\"Port of San Francisco\", \"San Francisco International Airport\", \"Pier 80\"]}', 1, '2024-08-30 12:48:24'),
(134, 'Washington', 'washington', '{\"primary_commercial_hub\": \"Seattle\", \"major_sub_locations\": [\"Spokane\", \"Tacoma\", \"Vancouver\", \"Bellevue\", \"Everett\", \"Bellingham\", \"Olympia\", \"Yakima\", \"Tri-Cities\"], \"industrial_hubs\": [\"Seattle\", \"Tacoma\", \"Everett\", \"Vancouver\", \"Spokane\", \"Tri-Cities\"], \"trade_and_logistics_hubs\": [\"Port of Seattle\", \"Port of Tacoma\", \"Seattle–Tacoma International Airport\", \"Port of Vancouver USA\", \"Spokane International Airport\"]}', 1, '2024-08-30 12:48:24'),
(135, 'Los Angeles', 'los-angeles', '{\"primary_commercial_hub\": \"Downtown Los Angeles\", \"major_sub_locations\": [\"Hollywood\", \"Westwood\", \"Koreatown\", \"San Pedro\", \"West Los Angeles\"], \"industrial_hubs\": [\"Vernon\", \"Commerce\", \"Boyle Heights\", \"Harbor Gateway\"], \"trade_and_logistics_hubs\": [\"Port of Los Angeles\", \"Port of Long Beach\", \"Los Angeles International Airport\", \"Los Angeles Union Station\"]}', 1, '2024-08-30 12:48:24'),
(136, 'Florida', 'florida', '{\"primary_commercial_hub\": \"Miami\", \"major_sub_locations\": [\"Jacksonville\", \"Tampa\", \"Orlando\", \"Tallahassee\", \"Fort Lauderdale\", \"St. Petersburg\", \"West Palm Beach\"], \"industrial_hubs\": [\"Tampa\", \"Jacksonville\", \"Miami\", \"Orlando\", \"Cape Canaveral\"], \"trade_and_logistics_hubs\": [\"PortMiami\", \"Port Tampa Bay\", \"JAXPORT\", \"Port Everglades\", \"Port Canaveral\", \"Miami International Airport\", \"Orlando International Airport\"]}', 1, '2024-08-30 12:48:24'),
(137, 'California', 'california', '{\"primary_commercial_hub\": \"Los Angeles\", \"major_sub_locations\": [\"San Francisco\", \"San Diego\", \"San Jose\", \"Sacramento\", \"Oakland\", \"Fresno\", \"Long Beach\", \"Anaheim\", \"Riverside\", \"Santa Barbara\"], \"industrial_hubs\": [\"Silicon Valley\", \"Inland Empire\", \"Los Angeles\", \"Long Beach\", \"Fresno\", \"Bakersfield\", \"Stockton\"], \"trade_and_logistics_hubs\": [\"Port of Los Angeles\", \"Port of Long Beach\", \"Port of Oakland\", \"Port of San Diego\", \"Los Angeles International Airport\", \"Ontario International Airport\", \"Port of Stockton\", \"Port of Hueneme\"]}', 1, '2024-08-30 12:48:24'),
(138, 'Ohio', 'ohio', '{\"primary_commercial_hub\": \"Columbus\", \"major_sub_locations\": [\"Cleveland\", \"Cincinnati\", \"Toledo\", \"Akron\", \"Dayton\", \"Youngstown\"], \"industrial_hubs\": [\"Cleveland\", \"Toledo\", \"Akron\", \"Dayton\", \"Youngstown\"], \"trade_and_logistics_hubs\": [\"Port of Cleveland\", \"Port of Toledo\", \"John Glenn Columbus International Airport\", \"Rickenbacker International Airport\", \"Cincinnati/Northern Kentucky International Airport\"]}', 1, '2024-08-30 12:48:24'),
(139, 'New Mexico', 'new-mexico', '{\"primary_commercial_hub\": \"Albuquerque\", \"major_sub_locations\": [\"Santa Fe\", \"Las Cruces\", \"Roswell\", \"Farmington\", \"Clovis\", \"Hobbs\"], \"industrial_hubs\": [\"Los Alamos\", \"Carlsbad\", \"Hobbs\", \"Farmington\"], \"trade_and_logistics_hubs\": [\"Albuquerque International Sunport\", \"Santa Teresa Port of Entry\", \"Port of Entry at Columbus\", \"BNSF Railway Albuquerque Intermodal Facility\"]}', 1, '2024-08-30 12:48:24'),
(140, 'New Jersey', 'new-jersey', '{\"primary_commercial_hub\": \"Newark\", \"major_sub_locations\": [\"Jersey City\", \"Paterson\", \"Elizabeth\", \"Edison\", \"Trenton\", \"Atlantic City\", \"Camden\"], \"industrial_hubs\": [\"Newark\", \"Elizabeth\", \"New Brunswick\", \"Edison\", \"Camden\"], \"trade_and_logistics_hubs\": [\"Port Newark–Elizabeth Marine Terminal\", \"Newark Liberty International Airport\", \"Port of Camden\", \"Trenton-Mercer Airport\"]}', 1, '2024-08-30 12:48:24'),
(141, 'Texas', 'texas', '{\"primary_commercial_hub\": \"Houston\", \"major_sub_locations\": [\"Dallas\", \"Austin\", \"San Antonio\", \"Fort Worth\", \"El Paso\", \"Corpus Christi\", \"Lubbock\"], \"industrial_hubs\": [\"Houston Ship Channel\", \"Dallas–Fort Worth\", \"San Antonio\", \"Corpus Christi\", \"Beaumont–Port Arthur\", \"Midland–Odessa\"], \"trade_and_logistics_hubs\": [\"Port of Houston\", \"Dallas Fort Worth International Airport\", \"Port of Corpus Christi\", \"Port of Beaumont\", \"Laredo\"]}', 1, '2024-08-30 12:48:24'),
(142, 'Utah', 'utah', '{\"primary_commercial_hub\": \"Salt Lake City\", \"major_sub_locations\": [\"West Valley City\", \"Provo\", \"Ogden\", \"St. George\", \"Logan\"], \"industrial_hubs\": [\"Salt Lake City\", \"Ogden\", \"Provo\", \"Tooele\"], \"trade_and_logistics_hubs\": [\"Salt Lake City International Airport\", \"Port of Entry at Wendover\", \"Union Pacific intermodal terminal, Salt Lake City\"]}', 1, '2024-08-30 12:48:24'),
(143, 'Virginia', 'virginia', '{\"primary_commercial_hub\": \"Northern Virginia\", \"major_sub_locations\": [\"Richmond\", \"Virginia Beach\", \"Norfolk\", \"Chesapeake\", \"Newport News\", \"Roanoke\", \"Charlottesville\", \"Lynchburg\"], \"industrial_hubs\": [\"Hampton Roads\", \"Newport News\", \"Roanoke\", \"Lynchburg\", \"Danville\", \"Martinsville\"], \"trade_and_logistics_hubs\": [\"Port of Virginia\", \"Norfolk International Terminals\", \"Portsmouth Marine Terminal\", \"Washington Dulles International Airport\", \"Richmond Marine Terminal\", \"Norfolk International Airport\"]}', 1, '2024-08-30 12:48:24'),
(144, 'London', 'london', '{\"primary_commercial_hub\": \"City of London\", \"major_sub_locations\": [\"West End\", \"Canary Wharf\", \"South Bank\", \"Camden\", \"Croydon\"], \"industrial_hubs\": [\"Park Royal\", \"Thames Gateway\"], \"trade_and_logistics_hubs\": [\"Port of Tilbury\", \"London Gateway\", \"Heathrow Airport\", \"London City Airport\"]}', 1, '2024-08-30 12:48:24'),
(145, 'Manchester', 'manchester', '{\"primary_commercial_hub\": \"Manchester city centre\", \"major_sub_locations\": [\"Salford\", \"Trafford\", \"Stockport\", \"Oldham\", \"Rochdale\", \"Bolton\", \"Bury\", \"Wigan\"], \"industrial_hubs\": [\"Trafford Park\", \"Manchester Science Park\", \"Roundthorn Industrial Estate\", \"Broadheath Industrial Estate\"], \"trade_and_logistics_hubs\": [\"Manchester Airport\", \"Port Salford\", \"Trafford Park\", \"Manchester Piccadilly railway station\"]}', 1, '2024-08-30 12:48:24'),
(146, 'Birmingham', 'birmingham', '{\"primary_commercial_hub\": \"Birmingham City Centre\", \"major_sub_locations\": [\"Edgbaston\", \"Jewellery Quarter\", \"Sutton Coldfield\", \"Solihull\", \"West Bromwich\"], \"industrial_hubs\": [\"Birmingham Business Park\", \"Minworth Industrial Estate\", \"Tyseley Industrial Estate\", \"Witton\"], \"trade_and_logistics_hubs\": [\"Birmingham Airport\", \"Birmingham New Street station\", \"Birmingham Intermodal Freight Terminal\", \"Port of Birmingham\"]}', 1, '2024-08-30 12:48:24'),
(147, 'Bristol', 'bristol', '{\"primary_commercial_hub\": \"Bristol city centre\", \"major_sub_locations\": [\"Clifton\", \"Redland\", \"Bedminster\", \"Southville\", \"Bristol city centre\"], \"industrial_hubs\": [\"Avonmouth\", \"Severnside\", \"Bristol and Bath Science Park\"], \"trade_and_logistics_hubs\": [\"Port of Bristol\", \"Bristol Port at Avonmouth\", \"Bristol Airport\", \"Bristol Temple Meads railway station\"]}', 1, '2024-08-30 12:48:24'),
(148, 'Liverpool', 'liverpool', '{\"primary_commercial_hub\": \"Liverpool City Centre\", \"major_sub_locations\": [\"Liverpool City Centre\", \"Ropewalks\", \"Baltic Triangle\", \"Knowledge Quarter\", \"Liverpool Waterfront\", \"Anfield\", \"Everton\", \"Aigburth\", \"Allerton\", \"Wavertree\"], \"industrial_hubs\": [\"Liverpool John Lennon Airport Enterprise Park\", \"Liverpool International Business Park\", \"Speke\", \"Knowsley Industrial Park\"], \"trade_and_logistics_hubs\": [\"Port of Liverpool\", \"Liverpool John Lennon Airport\", \"Liverpool International Business Park\", \"Knowsley Industrial Park\"]}', 1, '2024-08-30 12:48:24'),
(149, 'Glasgow', 'glasgow', '{\"primary_commercial_hub\": \"Glasgow City Centre\", \"major_sub_locations\": [\"West End\", \"East End\", \"Southside\", \"Finnieston\", \"Merchant City\", \"Govan\", \"Partick\"], \"industrial_hubs\": [\"Hillington Industrial Estate\", \"Queenslie Industrial Estate\", \"Port Dundas\", \"Clydebank\"], \"trade_and_logistics_hubs\": [\"Port of Glasgow\", \"Glasgow Airport\", \"Glasgow Central Station\", \"Glasgow Queen Street Station\"]}', 1, '2024-08-30 12:48:24'),
(150, 'Sydney', 'sydney', '{\"primary_commercial_hub\": \"Sydney CBD\", \"major_sub_locations\": [\"Parramatta\", \"North Sydney\", \"Chatswood\", \"Bondi Junction\", \"Liverpool\"], \"industrial_hubs\": [\"Smithfield-Wetherill Park\", \"Eastern Creek\", \"Alexandria\"], \"trade_and_logistics_hubs\": [\"Port Botany\", \"Sydney Airport\", \"Port of Sydney\", \"Moorebank Intermodal Terminal\"]}', 1, '2024-08-30 12:48:24'),
(151, 'Melbourne', 'melbourne', '{\"primary_commercial_hub\": \"Melbourne CBD\", \"major_sub_locations\": [\"Docklands\", \"Southbank\", \"Richmond\", \"Footscray\", \"St Kilda\", \"Dandenong\", \"Frankston\", \"Geelong\"], \"industrial_hubs\": [\"Dandenong South\", \"Campbellfield\", \"Laverton North\", \"Altona North\", \"Somerton\", \"Brooklyn\"], \"trade_and_logistics_hubs\": [\"Port of Melbourne\", \"Melbourne Airport\", \"Dynon railway yards\", \"Appleton Dock\", \"West Swanson Dock\", \"Port of Geelong\"]}', 1, '2024-08-30 12:48:24'),
(152, 'Adelaide', 'adelaide', '{\"primary_commercial_hub\": \"Adelaide Central Business District\", \"major_sub_locations\": [\"North Adelaide\", \"Glenelg\", \"Norwood\", \"Port Adelaide\", \"Unley\"], \"industrial_hubs\": [\"Wingfield\", \"Lonsdale\", \"Edinburgh Parks\", \"Osborne\"], \"trade_and_logistics_hubs\": [\"Port Adelaide\", \"Adelaide Airport\", \"Intermodal Facility at Dry Creek\"]}', 1, '2024-08-30 12:48:24'),
(153, 'Perth', 'perth', '{\"primary_commercial_hub\": \"Perth CBD\", \"major_sub_locations\": [\"Northbridge\", \"Fremantle\", \"Joondalup\", \"Subiaco\", \"Victoria Park\", \"Rockingham\"], \"industrial_hubs\": [\"Kwinana Industrial Area\", \"Welshpool\", \"Malaga\", \"Osborne Park\"], \"trade_and_logistics_hubs\": [\"Port of Fremantle\", \"Perth Airport\", \"Kewdale Freight Terminal\", \"Forrestfield Intermodal Terminal\"]}', 1, '2024-08-30 12:48:24'),
(154, 'Brisbane', 'brisbane', '{\"primary_commercial_hub\": \"Brisbane CBD\", \"major_sub_locations\": [\"South Brisbane\", \"Fortitude Valley\", \"New Farm\", \"Indooroopilly\", \"Chermside\", \"Logan City\", \"Ipswich\"], \"industrial_hubs\": [\"Pinkenba\", \"Eagle Farm\", \"Rocklea\", \"Wacol\", \"Acacia Ridge\"], \"trade_and_logistics_hubs\": [\"Port of Brisbane\", \"Brisbane Airport\", \"Acacia Ridge Intermodal Terminal\"]}', 1, '2024-08-30 12:48:24'),
(155, 'Canberra', 'canberra', '{\"primary_commercial_hub\": \"Canberra City Centre\", \"major_sub_locations\": [\"Belconnen\", \"Woden\", \"Tuggeranong\", \"Gungahlin\", \"Queanbeyan\"], \"industrial_hubs\": [\"Fyshwick\", \"Hume\"], \"trade_and_logistics_hubs\": [\"Canberra Airport\", \"Fyshwick\", \"Hume\"]}', 1, '2024-08-30 12:48:24'),
(156, 'Dubai', 'dubai', '{\"primary_commercial_hub\": \"Downtown Dubai\", \"major_sub_locations\": [\"Deira\", \"Bur Dubai\", \"Business Bay\", \"Dubai Marina\", \"Jumeirah\"], \"industrial_hubs\": [\"Jebel Ali Industrial Area\", \"Al Quoz Industrial Area\", \"Ras Al Khor Industrial Area\", \"Dubai Investment Park\"], \"trade_and_logistics_hubs\": [\"Jebel Ali Port\", \"Dubai International Airport\", \"Al Maktoum International Airport\", \"Dubai Cargo Village\"]}', 1, '2024-08-30 12:48:24'),
(157, 'Abu Dhabi', 'abu-dhabi', '{\"primary_commercial_hub\": \"Abu Dhabi City\", \"major_sub_locations\": [\"Al Reem Island\", \"Khalifa City\", \"Mohammed Bin Zayed City\", \"Mussafah\", \"Al Ain\"], \"industrial_hubs\": [\"Mussafah Industrial Area\", \"Khalifa Industrial Zone Abu Dhabi (KEZAD)\", \"Ruwais Industrial City\"], \"trade_and_logistics_hubs\": [\"Zayed Port\", \"Zayed International Airport\", \"Khalifa Port\", \"Abu Dhabi International Airport\"]}', 1, '2024-08-30 12:48:24'),
(158, 'Sharjah', 'sharjah', '{\"primary_commercial_hub\": \"Al Majaz\", \"major_sub_locations\": [\"Al Nahda\", \"Al Qasimia\", \"Al Khan\", \"Muweilah\", \"Khor Fakkan\"], \"industrial_hubs\": [\"Sharjah Industrial Areas\", \"Hamriyah Free Zone\", \"Sharjah Airport International Free Zone\"], \"trade_and_logistics_hubs\": [\"Port of Khalid\", \"Port of Hamriyah\", \"Sharjah International Airport\", \"Sharjah Airport International Free Zone\"]}', 1, '2024-08-30 12:48:24'),
(159, 'Ajman', 'ajman', '{\"primary_commercial_hub\": \"Ajman City Centre\", \"major_sub_locations\": [\"Al Nuaimiya\", \"Al Rashidiya\", \"Al Jurf\", \"Al Mowaihat\", \"Al Hamidiya\"], \"industrial_hubs\": [\"Ajman Industrial Area\", \"Al Jurf Industrial Area\"], \"trade_and_logistics_hubs\": [\"Ajman Port\", \"Ajman Free Zone\"]}', 1, '2024-08-30 12:48:24'),
(160, 'Riyadh', 'riyadh', '{\"primary_commercial_hub\": \"Olaya\", \"major_sub_locations\": [\"Al Malaz\", \"Al Olaya\", \"Diplomatic Quarter\", \"King Abdullah Financial District\", \"Diriyah\"], \"industrial_hubs\": [\"Riyadh Second Industrial City\", \"Riyadh Third Industrial City\"], \"trade_and_logistics_hubs\": [\"King Khalid International Airport\", \"Riyadh Dry Port\", \"Riyadh Railway Station\"]}', 1, '2024-08-30 12:48:24'),
(161, 'Jeddah', 'jeddah', '{\"primary_commercial_hub\": \"Al-Balad\", \"major_sub_locations\": [\"Al-Hamra\", \"Al-Rawdah\", \"Al-Safa\", \"Obhur\"], \"industrial_hubs\": [\"Jeddah Industrial City\", \"Al-Khomrah Industrial Area\"], \"trade_and_logistics_hubs\": [\"Jeddah Islamic Port\", \"King Abdulaziz International Airport\"]}', 1, '2024-08-30 12:48:24'),
(162, 'Dammam', 'dammam', '{\"primary_commercial_hub\": \"Dammam\", \"major_sub_locations\": [\"Al Khobar\", \"Dhahran\", \"Qatif\", \"Ras Tanura\"], \"industrial_hubs\": [\"Dammam 1st Industrial City\", \"Dammam 2nd Industrial City\", \"Jubail Industrial City\"], \"trade_and_logistics_hubs\": [\"King Abdulaziz Port\", \"King Fahd International Airport\", \"Dammam Railway Station\"]}', 1, '2024-08-30 12:48:24'),
(163, 'Medina', 'medina', '{\"primary_commercial_hub\": \"Medina\", \"major_sub_locations\": [\"Central Medina\", \"Quba\", \"Al-Awali\", \"Al-Aziziyah\"], \"industrial_hubs\": [\"Al-Madinah Industrial City\"], \"trade_and_logistics_hubs\": [\"Prince Mohammad bin Abdulaziz International Airport\", \"Medina Railway Station\"]}', 1, '2024-08-30 12:48:24'),
(164, 'Mecca', 'mecca', '{\"primary_commercial_hub\": \"Mecca\", \"major_sub_locations\": [\"Al Haram\", \"Al Aziziyah\", \"Al Shawqiyyah\", \"Al Awali\", \"Mina\", \"Arafat\"], \"industrial_hubs\": [\"Mecca Industrial City\"], \"trade_and_logistics_hubs\": [\"Makkah Al Mukarramah Bus Station\", \"Makkah Train Station\"]}', 1, '2024-08-30 12:48:24');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_media`
--

CREATE TABLE `tbl_media` (
  `id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `type` enum('image','audio','video','pdf','csv','archive') NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_member`
--

CREATE TABLE `tbl_member` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` mediumtext DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_keyword` varchar(255) DEFAULT NULL,
  `meta_desc` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_newsletter`
--

CREATE TABLE `tbl_newsletter` (
  `id` int(11) NOT NULL,
  `email` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_page`
--

CREATE TABLE `tbl_page` (
  `id` int(11) NOT NULL,
  `title` varchar(300) NOT NULL,
  `url` varchar(300) NOT NULL,
  `meta_title` varchar(300) NOT NULL,
  `meta_keyword` varchar(500) NOT NULL,
  `meta_desc` varchar(700) NOT NULL,
  `image` varchar(500) NOT NULL,
  `description` mediumtext NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_page`
--

INSERT INTO `tbl_page` (`id`, `title`, `url`, `meta_title`, `meta_keyword`, `meta_desc`, `image`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'home', 'home', '', '', '', '', '', 1, '2024-07-05 13:57:14', '2026-09-29 18:02:40'),
(2, 'About Us', 'about', 'About Us', '', '', '', '', 1, '2024-07-05 09:55:36', '2026-09-29 18:02:40'),
(3, 'Contact Us', 'contact', 'Contact Us', '', '', '', '', 1, '2024-07-05 14:01:20', '2026-09-29 18:02:40');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_product`
--

CREATE TABLE `tbl_product` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `title` varchar(255) NOT NULL,
  `heading` varchar(255) DEFAULT NULL,
  `meta_title` varchar(255) NOT NULL,
  `meta_keyword` varchar(255) DEFAULT NULL,
  `meta_desc` varchar(255) DEFAULT NULL,
  `category` varchar(500) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `short_description` varchar(600) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `gallery` text DEFAULT NULL,
  `feature` tinyint(4) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `price` decimal(10,2) NOT NULL,
  `discounted_price` decimal(10,2) DEFAULT NULL,
  `faqs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`faqs`)),
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_product_category`
--

CREATE TABLE `tbl_product_category` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `parent_id` char(36) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `heading` varchar(255) DEFAULT NULL,
  `service_heading` varchar(255) DEFAULT NULL,
  `service_meta_title` varchar(255) DEFAULT NULL,
  `service_meta_keyword` varchar(500) DEFAULT NULL,
  `service_meta_desc` varchar(255) DEFAULT NULL,
  `service_description` mediumtext DEFAULT NULL,
  `faqs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`faqs`)),
  `service_faqs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`service_faqs`)),
  `icon` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `banner` varchar(255) DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `description` mediumtext DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_keyword` varchar(500) DEFAULT NULL,
  `meta_desc` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `discounted_price` decimal(10,2) DEFAULT NULL,
  `unit` varchar(3) NOT NULL DEFAULT 'C62',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_rate_limit`
--

CREATE TABLE `tbl_rate_limit` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `first_attempt` int(11) NOT NULL,
  `last_attempt` int(11) NOT NULL,
  `blocked_until` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_testimonial`
--

CREATE TABLE `tbl_testimonial` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` mediumtext NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `user_id` char(36) NOT NULL DEFAULT uuid(),
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `address_1` varchar(255) DEFAULT NULL,
  `address_2` varchar(255) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `last_login` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `is_email_verified` tinyint(1) DEFAULT 0,
  `email_token` char(36) DEFAULT uuid(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`user_id`, `username`, `email`, `password_hash`, `first_name`, `last_name`, `address_1`, `address_2`, `city`, `state`, `zip_code`, `country`, `phone_number`, `last_login`, `is_admin`, `is_email_verified`, `email_token`, `created_at`) VALUES
('3d9d006b-a7a0-11ee-91e7-1070fd55ff90', 'admin', '', '$2y$10$V25Xm8Qr8TZmGWyKT0Yy3uoRZYxn/IFUaUs0PHUTyQhgStNySYSh.', NULL, NULL, '', '', '', '', '', '', '', '2024-07-13 12:04:01', 1, 1, '9c35eacafd6f0e73b0630913f22283e710bd', '2023-12-31 05:48:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_banner`
--
ALTER TABLE `tbl_banner`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_blog`
--
ALTER TABLE `tbl_blog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_blog_category`
--
ALTER TABLE `tbl_blog_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_config`
--
ALTER TABLE `tbl_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_enquiry`
--
ALTER TABLE `tbl_enquiry`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_gallery`
--
ALTER TABLE `tbl_gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_location`
--
ALTER TABLE `tbl_location`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `url` (`url`);

--
-- Indexes for table `tbl_media`
--
ALTER TABLE `tbl_media`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_member`
--
ALTER TABLE `tbl_member`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `url` (`url`);

--
-- Indexes for table `tbl_newsletter`
--
ALTER TABLE `tbl_newsletter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_page`
--
ALTER TABLE `tbl_page`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `url` (`url`);

--
-- Indexes for table `tbl_product`
--
ALTER TABLE `tbl_product`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `url` (`url`);

--
-- Indexes for table `tbl_product_category`
--
ALTER TABLE `tbl_product_category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `URL` (`url`);

--
-- Indexes for table `tbl_rate_limit`
--
ALTER TABLE `tbl_rate_limit`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ip_address` (`ip_address`),
  ADD KEY `blocked_until` (`blocked_until`),
  ADD KEY `last_attempt` (`last_attempt`);

--
-- Indexes for table `tbl_testimonial`
--
ALTER TABLE `tbl_testimonial`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email_token` (`email_token`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_banner`
--
ALTER TABLE `tbl_banner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_blog`
--
ALTER TABLE `tbl_blog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_blog_category`
--
ALTER TABLE `tbl_blog_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_config`
--
ALTER TABLE `tbl_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_enquiry`
--
ALTER TABLE `tbl_enquiry`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_gallery`
--
ALTER TABLE `tbl_gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_location`
--
ALTER TABLE `tbl_location`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- AUTO_INCREMENT for table `tbl_media`
--
ALTER TABLE `tbl_media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_member`
--
ALTER TABLE `tbl_member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_newsletter`
--
ALTER TABLE `tbl_newsletter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_page`
--
ALTER TABLE `tbl_page`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_rate_limit`
--
ALTER TABLE `tbl_rate_limit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_testimonial`
--
ALTER TABLE `tbl_testimonial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
