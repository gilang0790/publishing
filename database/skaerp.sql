-- MySQL dump 10.13  Distrib 5.6.23, for Win64 (x86_64)
--
-- Host: localhost    Database: skaerp
-- ------------------------------------------------------
-- Server version	5.5.5-10.1.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `migration`
--

DROP TABLE IF EXISTS `migration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migration`
--

LOCK TABLES `migration` WRITE;
/*!40000 ALTER TABLE `migration` DISABLE KEYS */;
INSERT INTO `migration` VALUES ('m000000_000000_base',1551269299),('m130524_201442_init',1551269303);
/*!40000 ALTER TABLE `migration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ms_city`
--

DROP TABLE IF EXISTS `ms_city`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ms_city` (
  `cityid` int(11) NOT NULL AUTO_INCREMENT,
  `provinceid` int(11) NOT NULL,
  `citycode` varchar(5) DEFAULT NULL,
  `cityname` varchar(50) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`cityid`),
  UNIQUE KEY `uq_city_catname` (`provinceid`,`cityname`),
  KEY `fk_city_province` (`provinceid`),
  KEY `ix_city` (`cityid`,`provinceid`,`cityname`,`status`,`citycode`),
  CONSTRAINT `fk_city_province` FOREIGN KEY (`provinceid`) REFERENCES `ms_province` (`provinceid`)
) ENGINE=InnoDB AUTO_INCREMENT=471 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_city`
--

LOCK TABLES `ms_city` WRITE;
/*!40000 ALTER TABLE `ms_city` DISABLE KEYS */;
INSERT INTO `ms_city` VALUES (1,1,'1101','Sinabung',1),(2,1,'1102','Singkil',1),(3,1,'1103','Tapakutan',1),(4,1,'1104','Kutacane',1),(5,1,'1105','Langsa',1),(6,1,'1106','Takengon',1),(7,1,'1107','Meulaboh',1),(8,1,'1108','Jantoi',1),(9,1,'1109','Sigli',1),(10,1,'1110','Bireuen',1),(11,1,'1111','Lhokseumawe',1),(12,1,'1112','Blangpidie',1),(13,1,'1113','Blangkejeran',1),(14,1,'1114','Karang Baru',1),(15,1,'1115','Suka Makmue',1),(16,1,'1116','Calang',1),(17,1,'1117','Simpang Tiga Redelong',1),(18,1,'1118','Meureudu',1),(19,1,'1171','Banda Aceh',1),(20,1,'1172','Kota Sabang',1),(21,1,'1173','Kota Langsa',1),(22,1,'1174','Kota Lhokseumawe',1),(23,1,'1175','Subulussalam',1),(24,2,'1201','Gunungsitoli',1),(25,2,'1202','Penyabungan',1),(26,2,'1203','Padang Sidempuan',1),(27,2,'1204','Sibolga',1),(28,2,'1205','Tarutung',1),(29,2,'1206','Balige',1),(30,2,'1207','Rantauprapat',1),(31,2,'1208','Kisaran',1),(32,2,'1209','Pematangsiantar',1),(33,2,'1210','Sidikalang',1),(34,2,'1211','Kabanjahe',1),(35,2,'1212','Lubukpakam',1),(36,2,'1213','Stabat',1),(37,2,'1214','Teluk Dalam',1),(38,2,'1215','Dolok Sanggul',1),(39,2,'1216','Salak',1),(40,2,'1217','Pangururan',1),(41,2,'1218','Sei Rampah',1),(42,2,'1219','Lima Puluh',1),(43,2,'1271','Kota Sibolga',1),(44,2,'1272','Tanjung Balai',1),(45,2,'1273','Pematang Siantar',1),(46,2,'1274','Tebingtinggi',1),(47,2,'1275','Medan',1),(48,2,'1276','Binjai',1),(49,2,'1277','Kota Padang Sidempuan',1),(50,3,'1301','Tua Pejat',1),(51,3,'1302','Painan',1),(52,3,'1303','Solok',1),(53,3,'1304','Muaro Sijunjung',1),(54,3,'1305','Batusangkar',1),(55,3,'1306','Pariaman',1),(56,3,'1307','Lubukbasung',1),(57,3,'1308','Payakumbuh',1),(58,3,'1309','Lubuksikaping',1),(59,3,'1310','Padang Aro',1),(60,3,'1311','Pulau Punjung',1),(61,3,'1312','Simpang Empat',1),(62,3,'1371','Padang',1),(63,3,'1372','Kota Solok',1),(64,3,'1373','Sawah Lunto',1),(65,3,'1374','Padang Panjang',1),(66,3,'1375','Bukittinggi',1),(67,3,'1376','Kota Payakumbuh',1),(68,3,'1377','Kota Pariaman',1),(69,4,'1401','Teluk Kuantan',1),(70,4,'1402','Rengat',1),(71,4,'1403','Tembilahan',1),(72,4,'1404','Pangkalan Kerinci',1),(73,4,'1405','Siak Sriindrapura',1),(74,4,'1406','Bangkinang',1),(75,4,'1407','Pasir Pangaraian',1),(76,4,'1408','Bengkalis',1),(77,4,'1409','Ujung Tanjung',1),(78,4,'1471','Pekanbaru',1),(79,4,'1473','Dumai',1),(80,5,'1501','Sungaipenuh',1),(81,5,'1502','Bangko',1),(82,5,'1503','Sarolangun',1),(83,5,'1504','Muara Bulian',1),(84,5,'1505','Sengeti',1),(85,5,'1506','Muara Sabak',1),(86,5,'1507','Kuala Tungkal',1),(87,5,'1508','Muara Tebo',1),(88,5,'1509','Muara Bungo',1),(89,5,'1571','Jambi',1),(90,6,'1601','Baturaja',1),(91,6,'1602','Kayu Agung',1),(92,6,'1603','Muara Enim',1),(93,6,'1604','Lahat',1),(94,6,'1605','Lubuk Linggau',1),(95,6,'1606','Sekayu',1),(96,6,'1607','Banyuasin',1),(97,6,'1608','Muaradua',1),(98,6,'1609','Martapura',1),(99,6,'1610','Indralaya',1),(100,6,'1611','Tebing Tinggi',1),(101,6,'1671','Palembang',1),(102,6,'1672','Prabumulih',1),(103,6,'1673','Pagaralam',1),(104,6,'1674','Lubuklinggau',1),(105,7,'1701','Manna',1),(106,7,'1702','Curup',1),(107,7,'1703','Argamakmur',1),(108,7,'1704','Bintuhan',1),(109,7,'1705','Tais',1),(110,7,'1706','Mukomuko',1),(111,7,'1707','Tubei',1),(112,7,'1708','Kepahiang',1),(113,7,'1771','Bengkulu',1),(114,8,'1801','Liwa',1),(115,8,'1802','Kotaagung',1),(116,8,'1803','Kalianda',1),(117,8,'1804','Sukadana',1),(118,8,'1805','Gunungsugih',1),(119,8,'1806','Kotabumi',1),(120,8,'1807','Blambangan Umpu',1),(121,8,'1808','Menggala',1),(122,8,'1871','Bandar Lampung',1),(123,8,'1872','Metro',1),(124,9,'1901','Sungailiat',1),(125,9,'1902','Tanjungpandan',1),(126,9,'1903','Toboali',1),(127,9,'1904','Koba',1),(128,9,'1905','Mentok',1),(129,9,'1906','Manggar',1),(130,9,'1971','Pangkal Pinang',1),(131,10,'2101','Tanung Balai Karimun',1),(132,10,'2102','Tanjungpinang',1),(133,10,'2103','Ranai',1),(134,10,'2104','Daik Lingga',1),(135,10,'2171','Batam',1),(136,10,'2172','Kota Tanjungpinang',1),(137,11,'3101','Pulau Pramuka Kec. Kep. Seribu Utara',1),(138,11,'3171','Jakarta Selatan',1),(139,11,'3172','Jakarta Timur',1),(140,11,'3173','Jakarta Pusat',1),(141,11,'3174','Puri Kembangan',1),(142,11,'3175','Jakarta Utara',1),(143,12,'3201','Cibinong',1),(144,12,'3202','Sukabumi',1),(145,12,'3203','Cianjur',1),(146,12,'3204','Soreang',1),(147,12,'3205','Garut',1),(148,12,'3206','Tasikmalaya',1),(149,12,'3207','Ciamis',1),(150,12,'3208','Kuningan',1),(151,12,'3209','Sumber',1),(152,12,'3210','Majalengka',1),(153,12,'3211','Sumedang',1),(154,12,'3212','Indramayu',1),(155,12,'3213','Subang',1),(156,12,'3214','Purwakarta',1),(157,12,'3215','Karawang',1),(158,12,'3216','Bekasi',1),(159,12,'3217','Ngamprah',1),(160,12,'3271','Bogor',1),(161,12,'3272','Kota Sukabumi',1),(162,12,'3273','Bandung',1),(163,12,'3274','Cirebon',1),(164,12,'3275','Kota Bekasi',1),(165,12,'3276','Depok',1),(166,12,'3277','Cimahi',1),(167,12,'3278','Kota Tasikmalaya',1),(168,12,'3279','Banjar',1),(169,13,'3301','Cilacap',1),(170,13,'3302','Purwokerto',1),(171,13,'3303','Purbalingga',1),(172,13,'3304','Banjarnegara',1),(173,13,'3305','Kebumen',1),(174,13,'3306','Purworejo',1),(175,13,'3307','Wonosobo',1),(176,13,'3308','Mungkid',1),(177,13,'3309','Boyolali',1),(178,13,'3310','Klaten',1),(179,13,'3311','Sukoharjo',1),(180,13,'3312','Wonogiri',1),(181,13,'3313','Karanganyar',1),(182,13,'3314','Sragen',1),(183,13,'3315','Grobogan',1),(184,13,'3316','Blora',1),(185,13,'3317','Rembang',1),(186,13,'3318','Pati',1),(187,13,'3319','Kudus',1),(188,13,'3320','Jepara',1),(189,13,'3321','Demak',1),(190,13,'3322','Ungaran',1),(191,13,'3323','Temanggung',1),(192,13,'3324','Kendal',1),(193,13,'3325','Batang',1),(194,13,'3326','Kajen',1),(195,13,'3327','Pemalang',1),(196,13,'3328','Slawi',1),(197,13,'3329','Brebes',1),(198,13,'3371','Magelang',1),(199,13,'3372','Surakarta',1),(200,13,'3373','Salatiga',1),(201,13,'3374','Semarang',1),(202,13,'3375','Pekalongan',1),(203,13,'3376','Tegal',1),(204,14,'3401','Wates',1),(205,14,'3402','Bantul',1),(206,14,'3403','Wonosari',1),(207,14,'3404','Sleman',1),(208,14,'3471','Yogyakarta',1),(209,15,'3501','Pacitan',1),(210,15,'3502','Ponorogo',1),(211,15,'3503','Trenggalek',1),(212,15,'3504','Tulungagung',1),(213,15,'3505','Blitar',1),(214,15,'3506','Kediri',1),(215,15,'3507','Kepanjen',1),(216,15,'3508','Lumajang',1),(217,15,'3509','Jember',1),(218,15,'3510','Banyuwangi',1),(219,15,'3511','Bondowoso',1),(220,15,'3512','Situbondo',1),(221,15,'3513','Probolinggo',1),(222,15,'3514','Pasuruan',1),(223,15,'3515','Sidoarjo',1),(224,15,'3516','Mojokerto',1),(225,15,'3517','Jombang',1),(226,15,'3518','Nganjuk',1),(227,15,'3519','Madiun',1),(228,15,'3520','Magetan',1),(229,15,'3521','Ngawi',1),(230,15,'3522','Bonjonegoro',1),(231,15,'3523','Tuban',1),(232,15,'3524','Lamongan',1),(233,15,'3525','Gresik',1),(234,15,'3526','Bangkalan',1),(235,15,'3527','Sampang',1),(236,15,'3528','Pamekasan',1),(237,15,'3529','Sumenep',1),(238,15,'3571','Kota Kediri',1),(239,15,'3572','Kota Blitar',1),(240,15,'3573','Malang',1),(241,15,'3574','Kota Probolinggo',1),(242,15,'3575','Kota Pasuruan',1),(243,15,'3576','Kota Mojokerto',1),(244,15,'3577','Kota Madiun',1),(245,15,'3578','Kota Surabaya',1),(246,15,'3579','Kota Batu',1),(247,16,'3601','Padeglang',1),(248,16,'3602','Rangkasbitung',1),(249,16,'3603','Tigaraksa',1),(250,16,'3604','Serang',1),(251,16,'3671','Tangerang',1),(252,16,'3672','Cilegon',1),(253,17,'5101','Negara',1),(254,17,'5102','Tabanan',1),(255,17,'5103','Badung',1),(256,17,'5104','Gianyar',1),(257,17,'5105','Klungkung',1),(258,17,'5106','Bangli',1),(259,17,'5107','Karangasem',1),(260,17,'5108','Singaraja',1),(261,17,'5171','Denpasar',1),(262,18,'5201','Mataram',1),(263,18,'5202','Praya',1),(264,18,'5203','Selong',1),(265,18,'5204','Sumbawa Besar',1),(266,18,'5205','Dompu',1),(267,18,'5206','Raba',1),(268,18,'5207','Taliwang',1),(269,18,'5271','Kota Mataram',1),(270,18,'5272','Bima',1),(271,19,'5301','Waikabubak',1),(272,19,'5302','Waingapu',1),(273,19,'5303','Kupang',1),(274,19,'5304','Soe',1),(275,19,'5305','Kefamenanu',1),(276,19,'5306','Atambua',1),(277,19,'5307','Kalabhi',1),(278,19,'5308','Lewoleba',1),(279,19,'5309','Larantuka',1),(280,19,'5310','Maumere',1),(281,19,'5311','Ende',1),(282,19,'5312','Bajawa',1),(283,19,'5313','Ruteng',1),(284,19,'5314','Baa',1),(285,19,'5315','Labuan Bajo',1),(286,19,'5316','Tambolaka',1),(287,19,'5317','Waibakul',1),(288,19,'5318','Mbay',1),(289,19,'5371','Kota Kupang',1),(290,20,'6101','Sambas',1),(291,20,'6102','Bengkayang',1),(292,20,'6103','Ngabang',1),(293,20,'6104','Mempawah',1),(294,20,'6105','Batang Tarang',1),(295,20,'6106','Ketapang',1),(296,20,'6107','Sintang',1),(297,20,'6108','Putussibau',1),(298,20,'6109','Sekadau',1),(299,20,'6110','Nanga Pinoh',1),(300,20,'6111','Sukadana',1),(301,20,'6171','Pontianak',1),(302,20,'6172','Singkawang',1),(303,21,'6201','Pankalan Bun',1),(304,21,'6202','Sampit',1),(305,21,'6203','Kuala Kapuas',1),(306,21,'6204','Buntok',1),(307,21,'6205','Muara Taweh',1),(308,21,'6206','Sukamara',1),(309,21,'6207','Nanga Bulik',1),(310,21,'6208','Kuala Pembuang',1),(311,21,'6209','Kasongan',1),(312,21,'6210','Pulang Pisau',1),(313,21,'6211','Kuala Kurun',1),(314,21,'6212','Tamiang',1),(315,21,'6213','Purukcahu',1),(316,21,'6271','Palangkaraya',1),(317,22,'6301','Pelaihari',1),(318,22,'6302','Kotabaru',1),(319,22,'6303','Martapura',1),(320,22,'6304','Marabahan',1),(321,22,'6305','Rantau',1),(322,22,'6306','Kandangan',1),(323,22,'6307','Barabai',1),(324,22,'6308','Amuntai',1),(325,22,'6309','Tanjung',1),(326,22,'6310','Batulicin',1),(327,22,'6311','Paringin',1),(328,22,'6371','Banjarmasin',1),(329,22,'6372','Banjarbaru',1),(330,23,'6401','Tanah Grogot',1),(331,23,'6402','Sendawar',1),(332,23,'6403','Tenggarong',1),(333,23,'6404','Sangatta',1),(334,23,'6405','Tanjungredep',1),(335,23,'6406','Malinau',1),(336,23,'6407','Tanjungselor',1),(337,23,'6408','Nunukan',1),(338,23,'6409','Penajam',1),(339,23,'6471','Balikpapan',1),(340,23,'6472','Samarinda',1),(341,23,'6473','Tarakan',1),(342,23,'6474','Bontang',1),(343,25,'7101','Kotamubagu',1),(344,25,'7102','Tondano',1),(345,25,'7103','Tahuna',1),(346,25,'7104','Melonguane',1),(347,25,'7105','Amurang',1),(348,25,'7106','Airmadidi',1),(349,25,'7107','Boroko',1),(350,25,'7108','Ondong Siau',1),(351,25,'7109','Ratahan',1),(352,25,'7171','Manado',1),(353,25,'7172','Bitung',1),(354,25,'7173','Tomohon',1),(355,25,'7174','Kotamobagu',1),(356,26,'7201','Banggai',1),(357,26,'7202','Luwuk',1),(358,26,'7203','Bungku',1),(359,26,'7204','Poso',1),(360,26,'7205','Donggala',1),(361,26,'7206','Toli-toli',1),(362,26,'7207','Buol',1),(363,26,'7208','Parigi',1),(364,26,'7209','Ampana',1),(365,26,'7271','Palu',1),(366,27,'7301','Bantaeng',1),(367,27,'7302','Bulukumba',1),(369,27,'7304','Jeneponto',1),(370,27,'7305','Takalar',1),(371,27,'7306','Sunggu Minasa',1),(372,27,'7307','Sinjai',1),(373,27,'7308','Maros',1),(374,27,'7309','Pangkajene',1),(375,27,'7310','Barru',1),(376,27,'7311','Watampone',1),(377,27,'7312','Watan Soppeng',1),(378,27,'7313','Sengkang',1),(379,27,'7314','Sidenreng',1),(380,27,'7315','Pinrang',1),(381,27,'7316','Enrekang',1),(382,27,'7317','Palopo',1),(383,27,'7318','Makale',1),(384,27,'7322','Masamba',1),(385,27,'7325','Malili',1),(386,27,'7371','Makassar',1),(387,27,'7372','Pare-pare',1),(389,28,'7401','Bau-Bau',1),(390,28,'7402','Raha',1),(391,28,'7403','Unaaha',1),(392,28,'7404','Kolaka',1),(393,28,'7405','Andolo',1),(394,28,'7406','Rumbia',1),(395,28,'7407','Wangi-Wangi',1),(396,28,'7408','Lasusua',1),(397,28,'7409','Bonegunu',1),(398,28,'7410','Asera',1),(399,28,'7471','Kendari',1),(401,29,'7501','Marisa/Tilamuta',1),(402,29,'7502','Gorontalo',1),(403,29,'7503','Marisa',1),(404,29,'7504','Suwawa',1),(405,29,'7505','Kwandang',1),(406,29,'7571','Kota Gorontalo',1),(407,30,'7601','Majene',1),(408,30,'7602','Polewali',1),(409,30,'7603','Mamasa',1),(410,30,'7604','Mamuju',1),(411,30,'7605','Pasangkayu',1),(412,31,'8101','Saumlaki',1),(413,31,'8102','Tual',1),(414,31,'8103','Masohi',1),(415,31,'8104','Namlea',1),(416,31,'8105','Dobo',1),(417,31,'8106','Dataran Hunipopu',1),(418,31,'8107','Dataran Hunimoa',1),(419,31,'8108','Ambon',1),(420,32,'8201','Ternate',1),(421,32,'8202','Weda',1),(422,32,'8203','Sanana',1),(423,32,'8204','Labuha',1),(424,32,'8205','Tobelo',1),(425,32,'8206','Maba',1),(427,32,'8272','Tidore',1),(428,33,'9101','Fak-Fak',1),(429,33,'9102','Kaimana',1),(430,33,'9103','Rasiei',1),(431,33,'9104','Bintuni',1),(432,33,'9105','Manokwari',1),(433,33,'9106','Teminabuan',1),(434,33,'9107','Sorong',1),(435,33,'9108','Waisai',1),(437,34,'9401','Wamena',1),(438,34,'9402','Jayapura',1),(439,34,'9403','Nabire',1),(440,34,'9404','Serui',1),(441,34,'9405','Biak',1),(442,34,'9406','Enarotali',1),(443,34,'9407','Kotamulia',1),(444,34,'9408','Timika',1),(445,34,'9409','Tanah Merah',1),(446,34,'9410','Kepi',1),(447,34,'9411','Agats',1),(448,34,'9412','Sumohai',1),(449,34,'9413','Oksibil',1),(450,34,'9414','Karubaga',1),(451,34,'9415','Sarmi',1),(452,34,'9416','Waris',1),(453,34,'9417','Botawa',1),(454,34,'9418','Sorendiweri',1),(455,34,'9419','Kota Jayapura',1),(456,4,'9420','Selat Panjang',1),(457,10,'9421','Tanjung Balai Karimun',1),(458,4,'9422','Taluk Kuantan',1),(459,4,'9999','Simpang Padang',1),(461,4,'1000','balam',1),(462,3,'1001','Sungai Sungkai',1),(463,2,'1002','Bakal Batu',1),(464,4,'1003','Duri',1),(465,2,'10005','Parinsoran',1),(466,2,'1006','Parbaba',1),(467,2,'1007','Liat Tondung',1),(468,2,'1008','Berastagi',1),(469,2,'1009','Tanjung Maria',1),(470,2,'1010','Samosir',1);
/*!40000 ALTER TABLE `ms_city` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ms_company`
--

DROP TABLE IF EXISTS `ms_company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ms_company` (
  `companyid` int(11) NOT NULL AUTO_INCREMENT,
  `companyname` varchar(50) DEFAULT NULL,
  `companycode` varchar(10) DEFAULT NULL,
  `address` varchar(250) DEFAULT NULL,
  `cityid` int(11) DEFAULT NULL,
  `zipcode` varchar(10) DEFAULT NULL,
  `phoneno` varchar(50) DEFAULT NULL,
  `webaddress` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `isholding` tinyint(4) DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`companyid`),
  UNIQUE KEY `uq_company_catname` (`companyname`),
  KEY `fk_company_city` (`cityid`),
  KEY `ix_company` (`companyid`,`companyname`,`cityid`,`zipcode`,`phoneno`,`status`,`address`,`webaddress`,`email`,`isholding`),
  CONSTRAINT `fk_company_city` FOREIGN KEY (`cityid`) REFERENCES `ms_city` (`cityid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_company`
--

LOCK TABLES `ms_company` WRITE;
/*!40000 ALTER TABLE `ms_company` DISABLE KEYS */;
INSERT INTO `ms_company` VALUES (2,'PT SARANA KREASI ABADI','SKA','Jl. Raya Grogol No.79, RT.6/RW.1, Grogol, Limo, Kota Depok, Jawa Barat',165,'16514','081398314145','','',0,1);
/*!40000 ALTER TABLE `ms_company` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ms_country`
--

DROP TABLE IF EXISTS `ms_country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ms_country` (
  `countryid` int(11) NOT NULL AUTO_INCREMENT,
  `countrycode` varchar(5) NOT NULL,
  `countryname` varchar(50) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`countryid`),
  UNIQUE KEY `uq_country_concode` (`countrycode`),
  UNIQUE KEY `uq_country_conname` (`countryname`),
  KEY `ix_country` (`countryid`,`countrycode`,`countryname`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=224 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_country`
--

LOCK TABLES `ms_country` WRITE;
/*!40000 ALTER TABLE `ms_country` DISABLE KEYS */;
INSERT INTO `ms_country` VALUES (1,'AD','ANDORRA',1),(2,'AE','UNITED ARAB EMIRATES',1),(3,'AF','AFGHANISTAN',1),(4,'AG','ANTIGUA AND BARBUDA',1),(5,'AI','ANGUILLA',1),(6,'AL','ALBANIA',1),(7,'AM','ARMENIA',1),(8,'AN','NETHERLANDS ANTILLES',1),(9,'AO','ANGOLA',1),(10,'AQ','ANTARCTICA',1),(11,'AR','ARGENTINA',1),(12,'AS','AMERICAN SAMOA',1),(13,'AT','AUSTRIA',1),(14,'AU','AUSTRALIA',1),(15,'AW','ARUBA',1),(16,'AZ','AZERBAIJAN',1),(17,'BA','BOSNIA HERZEGOVINA',1),(18,'BB','BARBADOS',1),(19,'BD','BANGLADESH',1),(20,'BE','BELGIUM',1),(21,'BF','BURKINA FASO',1),(22,'BG','BULGARIA',1),(23,'BH','BAHRAIN',1),(24,'BI','BURUNDI',1),(25,'BJ','BENIN',1),(26,'BM','BERMUDA',1),(27,'BN','BRUNEI',1),(28,'BO','BOLIVIA',1),(29,'BR','BRAZIL',1),(30,'BS','BAHAMAS',1),(31,'BT','BHUTAN',1),(32,'BV','BOUVET ISLAND',1),(33,'BW','BOTSWANA',1),(34,'BY','BELARUS',1),(35,'BZ','BELIZE',1),(36,'CA','CANADA',1),(37,'CC','COCOS ISLANDS',1),(38,'CD','CONGO REPUBLIC',1),(39,'CF','CENTRAL AFRICA',1),(40,'CG','CONGO',1),(41,'CH','SWITZERLAND',1),(42,'CI','COTE D\'IVOIRE',1),(43,'CK','COOK ISLANDS',1),(44,'CL','CHILI',1),(45,'CM','CAMEROON',1),(46,'CN','CHINA',1),(47,'CO','COLOMBIA',1),(48,'CR','COSTA RICA',1),(49,'CU','CUBA',1),(50,'CV','CAPE VERDE',1),(51,'CX','CHRISTMAS ISLAND',1),(52,'CY','CYPRUS',1),(53,'CZ','CHECH REPUBLIC',1),(54,'DE','GERMAN',1),(55,'DJ','DJIBOUTI',1),(56,'DK','DENMARK',1),(57,'DM','DOMINICA',1),(58,'DO','DOMINICAN REPUBLIC',1),(59,'DZ','ALGERIA',1),(60,'EC','ECUADOR',1),(61,'EE','ESTONIA',1),(62,'EG','EGYPT',1),(63,'ER','ERITREA',1),(64,'ES','SPAIN',1),(65,'ET','ETHIOPIA',1),(66,'FI','FINLANDIA',1),(67,'FJ','FIJI ISLANDS',1),(68,'FM','MICRONESIA',1),(69,'FO','FAROE ISLANDS',1),(70,'FR','FRANCE',1),(71,'GA','GABON',1),(72,'GD','GRENADA',1),(73,'GE','GEORGIA',1),(74,'GF','FRENCH GUIANA',1),(75,'GH','GHANA',1),(76,'GI','GIBRALTAR',1),(77,'GL','GREENLAND',1),(78,'GM','GAMBIA',1),(79,'GN','GUINEA',1),(80,'GP','GUADELOUPE',1),(81,'GQ','EQUATORIAL GUINEA',1),(82,'GR','GREECE',1),(83,'GT','GUATEMALA',1),(84,'GU','GUAM',1),(85,'GW','GUINEA-BISSAU',1),(86,'GY','GUYANA',1),(87,'HK','HONGKONGS.A.R.',1),(88,'HN','HONDURAS',1),(89,'HR','CROATIA(HRVATSKA)',1),(90,'HT','HAITI',1),(91,'HU','HUNGARIA',1),(92,'ID','INDONESIA',1),(93,'IE','IRELAND',1),(94,'IL','ISRAEL',1),(95,'IN','INDIA',1),(96,'IO','BRITISH INDIAN OCEAN',1),(97,'IQ','IRAQ',1),(98,'IR','IRAN',1),(99,'IS','ICELAND',1),(100,'IT','ITALIA',1),(101,'JM','JAMAICA',1),(102,'JO','JORDAN',1),(103,'JP','JAPAN',1),(104,'KE','KENYA',1),(105,'KG','KYRGYZSTAN',1),(106,'KH','CAMBODIA',1),(107,'KI','KIRIBATI',1),(108,'KM','COMOROS',1),(109,'KP','NORTH KOREAN',1),(110,'KR','SOUTH KOREA',1),(111,'KW','KUWAIT',1),(112,'KY','CAYMAN ISLANDS',1),(113,'KZ','KAZAKHSTAN',1),(114,'LA','LAOS',1),(115,'LB','LEBANON',1),(116,'LI','LIECHTENSTEIN',1),(117,'LK','SRILANKA',1),(118,'LR','LIBERIA',1),(119,'LS','LESOTHO',1),(120,'LT','LITHUANIA',1),(121,'LU','LUXEMBOURG',1),(122,'LV','LATVIA',1),(123,'LY','LIBYA',1),(124,'MA','MOROCCO',1),(125,'MC','MONACO',1),(126,'MD','REPUBLIC OF MOLDOVA',1),(127,'MG','MADAGASKAR',1),(128,'MH','MARSHALL ISLANDS',1),(129,'MK','REPUBLIC OF MACEDONIA',1),(130,'ML','MALI',1),(131,'MM','MYANMAR',1),(132,'MN','MONGOLIA',1),(133,'MO','MACAUS.A.R.',1),(134,'MQ','MARTINIQUE',1),(135,'MR','MAURITANIA',1),(136,'MS','MONTSERRAT',1),(137,'MT','MALTA',1),(138,'MU','MAURITIUS',1),(139,'MV','MALDIVES',1),(140,'MW','MALAWI',1),(141,'MX','MEXICO',1),(142,'MY','MALAYSIA',1),(143,'MZ','MOZAMBIQUE',1),(144,'NA','NAMIBIA',1),(145,'NC','NEW CALEDONIA',1),(146,'NE','NIGER',1),(147,'NF','NORFOLK ISLAND',1),(148,'NG','NIGERIA',1),(149,'NI','NICARAGUA',1),(150,'NL','NETHERLAND',1),(151,'NO','NORWAY',1),(152,'NP','NEPAL',1),(153,'NR','NAURU',1),(154,'NU','NIUE',1),(155,'NZ','NEW ZEALAND',1),(156,'OM','OMAN',1),(157,'PA','PANAMA',1),(158,'PE','PERU',1),(159,'PF','FRENCH POLYNESIA',1),(160,'PG','PAPUA NEW GUINEA',1),(161,'PH','PHILIPINES',1),(162,'PK','PAKISTAN',1),(163,'PL','POLAND',1),(164,'PN','PITCAIRN ISLAND',1),(165,'PR','PUERTORICO',1),(166,'PT','PORTUGAL',1),(167,'PW','PALAU',1),(168,'PY','PARAGUAY',1),(169,'QA','QATAR',1),(170,'RE','REUNION',1),(171,'RO','ROMANIA',1),(172,'RU','RUSSIA',1),(173,'RW','RWANDA',1),(174,'SA','SAUDIARABIA',1),(175,'SB','SOLOMONISLANDS',1),(176,'SC','SEYCHELLES',1),(177,'SD','SUDAN',1),(178,'SE','SWEDIA',1),(179,'SG','SINGAPORE',1),(180,'SH','SAINTHELENA',1),(181,'SI','SLOVENIA',1),(182,'SK','SLOVAKIA',1),(183,'SL','SIERRALEONE',1),(184,'SM','SANMARINO',1),(185,'SN','SENEGAL',1),(186,'SO','SOMALIA',1),(187,'SR','SURINAME',1),(188,'ST','SAOTOMEANDPRINCIPE',1),(189,'SV','ELSALVADOR',1),(190,'SY','SYRIA',1),(191,'SZ','SWAZILAND',1),(192,'TD','CHAD',1),(193,'TG','TOGO',1),(194,'TH','THAILAND',1),(195,'TJ','TAJIKISTAN',1),(196,'TK','TOKELAU',1),(197,'TM','TURKMENISTAN',1),(198,'TN','TUNISIA',1),(199,'TO','TONGA',1),(200,'TP','TIMORTIMUR',1),(201,'TR','TURKI',1),(202,'TT','TRINIDADANDTOBAGO',1),(203,'TV','TUVALU',1),(204,'TW','TAIWAN',1),(205,'TZ','TANZANIA',1),(206,'UA','UKRAINE',1),(207,'UG','UGANDA',1),(208,'UK','INGGRIS',1),(209,'US','UNITED STATES OF AMERICA',1),(210,'UY','URUGUAY',1),(211,'UZ','UZBEKISTAN',1),(212,'VA','VATICANCITY',1),(213,'VE','VENEZUELA',1),(214,'VN','VIETNAM',1),(215,'VU','VANUATU',1),(216,'WS','SAMOA',1),(217,'YE','YAMAN',1),(218,'YT','MAYOTTE',1),(219,'YU','YUGOSLAVIA',1),(220,'ZA','AFRIKASELATAN',1),(221,'ZM','ZAMBIA',1),(223,'ZW','ZIMBABWE',1);
/*!40000 ALTER TABLE `ms_country` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ms_groupaccess`
--

DROP TABLE IF EXISTS `ms_groupaccess`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ms_groupaccess` (
  `groupaccessid` int(11) NOT NULL AUTO_INCREMENT,
  `groupname` varchar(50) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`groupaccessid`),
  UNIQUE KEY `uq_groupaccess_group` (`groupname`),
  KEY `ix_groupaccess` (`groupaccessid`,`groupname`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_groupaccess`
--

LOCK TABLES `ms_groupaccess` WRITE;
/*!40000 ALTER TABLE `ms_groupaccess` DISABLE KEYS */;
INSERT INTO `ms_groupaccess` VALUES (1,'Guest',1),(2,'Administrators',1),(3,'Gudang SKA',1);
/*!40000 ALTER TABLE `ms_groupaccess` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ms_groupmenu`
--

DROP TABLE IF EXISTS `ms_groupmenu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ms_groupmenu` (
  `groupmenuid` int(11) NOT NULL AUTO_INCREMENT,
  `groupaccessid` int(11) NOT NULL,
  `menuaccessid` int(11) NOT NULL,
  `isread` tinyint(4) NOT NULL DEFAULT '1',
  `iswrite` tinyint(4) NOT NULL DEFAULT '1',
  `ispost` tinyint(4) NOT NULL DEFAULT '1',
  `isreject` tinyint(4) NOT NULL DEFAULT '1',
  `isupload` tinyint(4) NOT NULL DEFAULT '1',
  `isdownload` tinyint(4) NOT NULL DEFAULT '1',
  `ispurge` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`groupmenuid`),
  UNIQUE KEY `uq_groupmenu_gm` (`groupaccessid`,`menuaccessid`),
  KEY `fk_groupmenu_group` (`groupaccessid`),
  KEY `ix_groupmenu` (`groupmenuid`,`groupaccessid`,`menuaccessid`,`isread`,`iswrite`,`ispost`,`isreject`,`isupload`,`isdownload`,`ispurge`),
  KEY `fk_groupmenu_menu` (`menuaccessid`),
  CONSTRAINT `fk_groupmenu_group` FOREIGN KEY (`groupaccessid`) REFERENCES `ms_groupaccess` (`groupaccessid`),
  CONSTRAINT `fk_groupmenu_menu` FOREIGN KEY (`menuaccessid`) REFERENCES `ms_menuaccess` (`menuaccessid`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_groupmenu`
--

LOCK TABLES `ms_groupmenu` WRITE;
/*!40000 ALTER TABLE `ms_groupmenu` DISABLE KEYS */;
INSERT INTO `ms_groupmenu` VALUES (1,2,4,1,1,1,1,1,1,1),(2,2,5,1,1,1,1,1,1,1),(3,2,6,1,1,1,1,1,1,1),(4,2,7,1,1,1,1,1,1,1),(5,2,8,1,1,1,1,1,1,1),(6,2,9,1,1,1,1,1,1,1),(7,2,10,1,1,1,1,1,1,1),(8,2,11,1,1,1,1,1,1,1),(9,2,12,1,1,1,1,1,1,1),(10,2,13,1,1,1,1,1,1,1),(11,2,14,1,1,1,1,1,1,1),(12,2,15,1,1,1,1,1,1,1),(13,2,16,1,1,1,1,1,1,1),(14,2,17,1,1,1,1,1,1,1),(15,2,18,1,1,1,1,1,1,1),(16,2,19,1,1,1,1,1,1,1),(17,2,20,1,1,1,1,1,1,1);
/*!40000 ALTER TABLE `ms_groupmenu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ms_groupmenuauth`
--

DROP TABLE IF EXISTS `ms_groupmenuauth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ms_groupmenuauth` (
  `groupmenuauthid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `groupaccessid` int(10) DEFAULT NULL,
  `menuauthid` int(10) DEFAULT NULL,
  `menuvalueid` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`groupmenuauthid`),
  KEY `ix_groupmenuauth` (`groupmenuauthid`,`groupaccessid`,`menuauthid`,`menuvalueid`),
  KEY `fk_groupmenua_group` (`groupaccessid`),
  KEY `fk_groupmenua_menu` (`menuauthid`),
  CONSTRAINT `fk_groupmenua_group` FOREIGN KEY (`groupaccessid`) REFERENCES `ms_groupaccess` (`groupaccessid`),
  CONSTRAINT `fk_groupmenua_menu` FOREIGN KEY (`menuauthid`) REFERENCES `ms_menuauth` (`menuauthid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_groupmenuauth`
--

LOCK TABLES `ms_groupmenuauth` WRITE;
/*!40000 ALTER TABLE `ms_groupmenuauth` DISABLE KEYS */;
INSERT INTO `ms_groupmenuauth` VALUES (1,2,3,'1');
/*!40000 ALTER TABLE `ms_groupmenuauth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ms_menuaccess`
--

DROP TABLE IF EXISTS `ms_menuaccess`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ms_menuaccess` (
  `menuaccessid` int(11) NOT NULL AUTO_INCREMENT,
  `menuname` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `description` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `menuurl` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `menuicon` varchar(50) DEFAULT NULL,
  `parentid` int(10) DEFAULT NULL,
  `moduleid` int(10) DEFAULT NULL,
  `sortorder` tinyint(4) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`menuaccessid`),
  UNIQUE KEY `uq_menuaccess` (`menuname`),
  KEY `ix_menuaccess` (`menuaccessid`,`menuname`,`description`,`menuurl`,`status`,`menuicon`,`parentid`,`sortorder`,`moduleid`),
  KEY `fk_menuaccess_module` (`moduleid`),
  CONSTRAINT `fk_menuaccess_module` FOREIGN KEY (`moduleid`) REFERENCES `ms_modules` (`moduleid`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_menuaccess`
--

LOCK TABLES `ms_menuaccess` WRITE;
/*!40000 ALTER TABLE `ms_menuaccess` DISABLE KEYS */;
INSERT INTO `ms_menuaccess` VALUES (1,'signup','Sign Up','admin/signup','glyphicon glyphicon-gift',NULL,1,99,0),(2,'login','Login','admin/login','glyphicon glyphicon-log-in',NULL,1,99,0),(3,'logout','Logout','admin/logout','glyphicon glyphicon-log-out',NULL,1,99,0),(4,'admin','Admin','admin/default/index','database',NULL,1,1,1),(5,'user','Master User','admin/user','user-circle',4,1,2,1),(6,'menuaccess','Master Menu','admin/menuaccess','list',4,1,3,1),(7,'groupaccess','Master Group','admin/groupaccess','list-alt',4,1,4,1),(8,'groupmenu','Master Group Menu','admin/groupmenu','list-ol',4,1,5,1),(9,'menuauth','Master Menu Objek','admin/menuauth','object-group',4,1,6,1),(10,'groupmenuauth','Master Group Menu Object','admin/groupmenuauth','object-ungroup',4,1,7,1),(11,'modules','Master Modules','admin/modules','book',4,1,12,1),(12,'usergroup','Master User Group','admin/usergroup','users',4,1,8,1),(13,'workflow','Master Workflow','admin/workflow','arrows-alt',4,1,9,1),(14,'wfstatus','Master Workflow Status','admin/wfstatus','adjust',4,1,10,1),(15,'wfgroup','Master Workflow Group','admin/wfgroup','briefcase',4,1,11,1),(16,'common','Common','common/default/index','archive',NULL,2,1,1),(17,'company','Master Company','common/company','building',16,2,2,1),(18,'plant','Master Plant','common/plant','building',16,2,3,1),(19,'sloc','Master Sloc','common/sloc','building',16,2,4,1),(20,'storagebin','Master Storagebin','common/storagebin','inbox',16,2,5,1);
/*!40000 ALTER TABLE `ms_menuaccess` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ms_menuauth`
--

DROP TABLE IF EXISTS `ms_menuauth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ms_menuauth` (
  `menuauthid` int(10) NOT NULL AUTO_INCREMENT,
  `menuobject` varchar(50) CHARACTER SET latin1 NOT NULL,
  `status` tinyint(3) NOT NULL,
  PRIMARY KEY (`menuauthid`),
  KEY `ix_menuauth` (`menuauthid`,`menuobject`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_menuauth`
--

LOCK TABLES `ms_menuauth` WRITE;
/*!40000 ALTER TABLE `ms_menuauth` DISABLE KEYS */;
INSERT INTO `ms_menuauth` VALUES (1,'sloc',1),(2,'useraccess',1),(3,'company',1);
/*!40000 ALTER TABLE `ms_menuauth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ms_modules`
--

DROP TABLE IF EXISTS `ms_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ms_modules` (
  `moduleid` int(10) NOT NULL AUTO_INCREMENT,
  `modulename` varchar(50) DEFAULT NULL,
  `moduledesc` varchar(150) DEFAULT NULL,
  `moduleicon` varchar(50) DEFAULT NULL,
  `isinstall` tinyint(4) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`moduleid`),
  UNIQUE KEY `uq_modules` (`modulename`),
  KEY `ix_modules` (`moduleid`,`modulename`,`moduledesc`,`moduleicon`,`isinstall`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_modules`
--

LOCK TABLES `ms_modules` WRITE;
/*!40000 ALTER TABLE `ms_modules` DISABLE KEYS */;
INSERT INTO `ms_modules` VALUES (1,'admin','Administrative','administration.png',1,1),(2,'common','Common','common.png',1,1),(3,'accounting','Accounting','accounting.png',1,1),(4,'inventory','Inventory','inventory.png',1,1),(5,'order','Order','order.png',1,1);
/*!40000 ALTER TABLE `ms_modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ms_plant`
--

DROP TABLE IF EXISTS `ms_plant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ms_plant` (
  `plantid` int(11) NOT NULL AUTO_INCREMENT,
  `plantcode` varchar(10) NOT NULL,
  `description` varchar(50) CHARACTER SET latin1 NOT NULL,
  `companyid` int(11) NOT NULL DEFAULT '1',
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`plantid`),
  UNIQUE KEY `uq_plant_code` (`plantcode`),
  KEY `fk_plant_com` (`companyid`),
  CONSTRAINT `fk_plant_com` FOREIGN KEY (`companyid`) REFERENCES `ms_company` (`companyid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_plant`
--

LOCK TABLES `ms_plant` WRITE;
/*!40000 ALTER TABLE `ms_plant` DISABLE KEYS */;
INSERT INTO `ms_plant` VALUES (1,'SKADPK','PT SARANA KREASI ABADI',2,1);
/*!40000 ALTER TABLE `ms_plant` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ms_province`
--

DROP TABLE IF EXISTS `ms_province`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ms_province` (
  `provinceid` int(11) NOT NULL AUTO_INCREMENT,
  `countryid` int(11) NOT NULL,
  `provincecode` varchar(5) DEFAULT NULL,
  `provincename` varchar(100) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`provinceid`),
  UNIQUE KEY `uq_province_counidprovname` (`countryid`,`provincename`),
  KEY `fk_province_country` (`countryid`),
  KEY `ix_province` (`provinceid`,`countryid`,`provincename`,`status`,`provincecode`),
  CONSTRAINT `fk_province_country` FOREIGN KEY (`countryid`) REFERENCES `ms_country` (`countryid`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_province`
--

LOCK TABLES `ms_province` WRITE;
/*!40000 ALTER TABLE `ms_province` DISABLE KEYS */;
INSERT INTO `ms_province` VALUES (1,92,'11','ACEH',1),(2,92,'12','SUMATERA UTARA',1),(3,92,'13','SUMATERA BARAT',1),(4,92,'14','RIAU',1),(5,92,'15','JAMBI',1),(6,92,'16','SUMATERA SELATAN',1),(7,92,'17','BENGKULU',1),(8,92,'18','LAMPUNG',1),(9,92,'19','KEPULAUAN BANGKA BELITUNG',1),(10,92,'21','KEPULAUAN RIAU',1),(11,92,'31','DKI JAKARTA',1),(12,92,'32','JAWA BARAT',1),(13,92,'33','JAWA TENGAH',1),(14,92,'34','DI YOGYAKARTA',1),(15,92,'35','JAWA TIMUR',1),(16,92,'36','BANTEN',1),(17,92,'51','BALI',1),(18,92,'52','NUSA TENGGARA BARAT',1),(19,92,'53','NUSA TENGGARA TIMUR',1),(20,92,'61','KALIMANTAN BARAT',1),(21,92,'62','KALIMANTAN TENGAH',1),(22,92,'63','KALIMANTAN SELATAN',1),(23,92,'64','KALIMANTAN TIMUR',1),(24,92,'65','KALIMANTAN UTARA',1),(25,92,'71','SULAWESI UTARA',1),(26,92,'72','SULAWESI TENGAH',1),(27,92,'73','SULAWESI SELATAN',1),(28,92,'74','SULAWESI TENGGARA',1),(29,92,'75','GORONTALO',1),(30,92,'76','SULAWESI BARAT',1),(31,92,'81','MALUKU',1),(32,92,'82','MALUKU UTARA',1),(33,92,'91','PAPUA BARAT',1),(34,92,'94','PAPUA',1);
/*!40000 ALTER TABLE `ms_province` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ms_sloc`
--

DROP TABLE IF EXISTS `ms_sloc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ms_sloc` (
  `slocid` int(11) NOT NULL AUTO_INCREMENT,
  `plantid` int(11) NOT NULL,
  `sloccode` varchar(20) NOT NULL,
  `description` varchar(50) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`slocid`),
  UNIQUE KEY `uq_sloc` (`sloccode`),
  KEY `ix_sloc` (`slocid`,`plantid`,`sloccode`,`description`,`status`),
  KEY `fk_sloc_plant` (`plantid`),
  CONSTRAINT `fk_sloc_plant` FOREIGN KEY (`plantid`) REFERENCES `ms_plant` (`plantid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_sloc`
--

LOCK TABLES `ms_sloc` WRITE;
/*!40000 ALTER TABLE `ms_sloc` DISABLE KEYS */;
INSERT INTO `ms_sloc` VALUES (1,1,'SKATOKO','TOKO',1);
/*!40000 ALTER TABLE `ms_sloc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ms_storagebin`
--

DROP TABLE IF EXISTS `ms_storagebin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ms_storagebin` (
  `storagebinid` int(10) NOT NULL AUTO_INCREMENT,
  `slocid` int(10) DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  `ismultiproduct` tinyint(4) DEFAULT '1',
  `qtymax` decimal(30,4) DEFAULT '0.0000',
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`storagebinid`),
  UNIQUE KEY `uq_storagebin` (`description`,`slocid`),
  KEY `ix_storagebin` (`storagebinid`,`description`,`status`,`ismultiproduct`),
  KEY `fk_storagebin_sloc` (`slocid`),
  CONSTRAINT `fk_storagebin_sloc` FOREIGN KEY (`slocid`) REFERENCES `ms_sloc` (`slocid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_storagebin`
--

LOCK TABLES `ms_storagebin` WRITE;
/*!40000 ALTER TABLE `ms_storagebin` DISABLE KEYS */;
INSERT INTO `ms_storagebin` VALUES (1,1,'A1',1,0.0000,1);
/*!40000 ALTER TABLE `ms_storagebin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ms_user`
--

DROP TABLE IF EXISTS `ms_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ms_user` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fullName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `authKey` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `passwordHash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`userID`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_user`
--

LOCK TABLES `ms_user` WRITE;
/*!40000 ALTER TABLE `ms_user` DISABLE KEYS */;
INSERT INTO `ms_user` VALUES (1,'administrator','Administrator','uBv4QxooDWLiav-jAEAli8u7NjkVwiSM','$2y$13$SKlajJVjR6WyQRzWpJ8/g.RkKNY7Ct.wq60DP24JA3evE0qPyH.GS','gilang.abcd@gmail.com',1,'2019-02-27 13:08:23','2019-02-28 12:16:18',NULL,1),(3,'gilang','Gilang','v3RKYabcLmvFAHpDXHDtaKypv33hF7Le','$2y$13$8R2WsWRaDnEDbZxluIw9n.CG4zAlmFevJG4D3YA5KFPu1YoiR9Rsa','gilang@karyadigital.com',1,'2019-02-28 12:24:16','2019-02-28 12:33:52',1,1),(4,'fitria','Fitria Desriana','Q8PvJxmk_gJg115vGS5M6OfxNz6HVq9_','$2y$13$Yd9XpdKAzWPMUfU2IzeHqeRJHSXDSR37OHBoZwilsbuQqK.R/PEOm','fitriade0590@gmail.com',1,'2019-02-28 12:29:14','2019-02-28 12:33:43',1,1),(5,'guest','Guest','VqpjvvtpB7BkFX7reGYyd0KjzDz6Ri-R','$2y$13$YEwMmy1.pycHOl9AD61uleXmxvn9SeECwzEF8iXaqGMUqXvIGxJbi','guest@gmail.com',1,'2019-02-28 15:49:25','0000-00-00 00:00:00',1,NULL);
/*!40000 ALTER TABLE `ms_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ms_usergroup`
--

DROP TABLE IF EXISTS `ms_usergroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ms_usergroup` (
  `usergroupid` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `groupaccessid` int(11) NOT NULL,
  PRIMARY KEY (`usergroupid`),
  UNIQUE KEY `uq_usergroup` (`userID`,`groupaccessid`),
  KEY `ix_usergroup` (`usergroupid`,`userID`,`groupaccessid`),
  KEY `fk_usergroup_group` (`groupaccessid`),
  CONSTRAINT `fk_usergroup_group` FOREIGN KEY (`groupaccessid`) REFERENCES `ms_groupaccess` (`groupaccessid`),
  CONSTRAINT `fk_usergroup_user` FOREIGN KEY (`userID`) REFERENCES `ms_user` (`userID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_usergroup`
--

LOCK TABLES `ms_usergroup` WRITE;
/*!40000 ALTER TABLE `ms_usergroup` DISABLE KEYS */;
INSERT INTO `ms_usergroup` VALUES (1,1,2);
/*!40000 ALTER TABLE `ms_usergroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ms_wfgroup`
--

DROP TABLE IF EXISTS `ms_wfgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ms_wfgroup` (
  `wfgroupid` int(11) NOT NULL AUTO_INCREMENT,
  `workflowid` int(11) NOT NULL,
  `groupaccessid` int(11) NOT NULL,
  `wfbefstat` tinyint(4) NOT NULL,
  `wfrecstat` tinyint(4) NOT NULL,
  PRIMARY KEY (`wfgroupid`) USING BTREE,
  UNIQUE KEY `ix_wfgroup_wgb` (`workflowid`,`groupaccessid`,`wfbefstat`) USING BTREE,
  KEY `ix_wfgroup_wfgbr` (`workflowid`,`groupaccessid`,`wfbefstat`,`wfrecstat`),
  KEY `ix_wfgroup_wgr` (`workflowid`,`groupaccessid`,`wfrecstat`),
  KEY `fk_wfgroup_group` (`groupaccessid`),
  CONSTRAINT `fk_wfgroup_group` FOREIGN KEY (`groupaccessid`) REFERENCES `ms_groupaccess` (`groupaccessid`),
  CONSTRAINT `fk_wfgroup_wf` FOREIGN KEY (`workflowid`) REFERENCES `ms_workflow` (`workflowid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_wfgroup`
--

LOCK TABLES `ms_wfgroup` WRITE;
/*!40000 ALTER TABLE `ms_wfgroup` DISABLE KEYS */;
INSERT INTO `ms_wfgroup` VALUES (1,1,2,1,1);
/*!40000 ALTER TABLE `ms_wfgroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ms_wfstatus`
--

DROP TABLE IF EXISTS `ms_wfstatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ms_wfstatus` (
  `wfstatusid` int(11) NOT NULL AUTO_INCREMENT,
  `workflowid` int(11) NOT NULL,
  `wfstat` tinyint(4) NOT NULL,
  `wfstatusname` varchar(50) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`wfstatusid`),
  KEY `fk_wfstatus_workflow` (`workflowid`),
  KEY `ix_wfstatus` (`wfstatusid`,`workflowid`,`wfstat`,`wfstatusname`),
  CONSTRAINT `fk_wfstatus_workflow` FOREIGN KEY (`workflowid`) REFERENCES `ms_workflow` (`workflowid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_wfstatus`
--

LOCK TABLES `ms_wfstatus` WRITE;
/*!40000 ALTER TABLE `ms_wfstatus` DISABLE KEYS */;
INSERT INTO `ms_wfstatus` VALUES (1,2,0,'Not Active');
/*!40000 ALTER TABLE `ms_wfstatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ms_workflow`
--

DROP TABLE IF EXISTS `ms_workflow`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ms_workflow` (
  `workflowid` int(11) NOT NULL AUTO_INCREMENT,
  `wfname` varchar(20) NOT NULL,
  `wfdesc` varchar(50) NOT NULL COMMENT 'wf description',
  `wfminstat` tinyint(4) NOT NULL,
  `wfmaxstat` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`workflowid`),
  UNIQUE KEY `uq_workflow_wfname` (`wfname`),
  KEY `ix_workflow_wfname` (`wfname`),
  KEY `ix_workflow` (`workflowid`,`wfname`,`wfdesc`,`wfminstat`,`wfmaxstat`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ms_workflow`
--

LOCK TABLES `ms_workflow` WRITE;
/*!40000 ALTER TABLE `ms_workflow` DISABLE KEYS */;
INSERT INTO `ms_workflow` VALUES (1,'listbs','List Stock Opname',0,1,1),(2,'appbs','Approve Stock Opname',1,3,1),(3,'insbs','Insert Stock Opname',1,2,1),(4,'rejbs','Reject Stock Opname',1,3,1);
/*!40000 ALTER TABLE `ms_workflow` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-03-05 23:16:48
