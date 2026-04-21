-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-12-2025 a las 19:07:23
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `matriculas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

CREATE TABLE `alumnos` (
  `id` int(11) NOT NULL,
  `apellido_nombre` varchar(150) NOT NULL,
  `dni` varchar(25) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `apoderado` varchar(150) DEFAULT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `correo` varchar(150) DEFAULT NULL,
  `grado_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumnos`
--

INSERT INTO `alumnos` (`id`, `apellido_nombre`, `dni`, `fecha_nacimiento`, `apoderado`, `telefono`, `correo`, `grado_id`) VALUES
(1, 'CALONGOS VASQUEZ, Gerardo Dahel', '93299501', '2023-03-12', 'VASQUEZ CORONADO, Damaris', '963755060', 'vasquezcoronadod@gmail.com', 1),
(2, 'GARRIAZO RIMACHI, Eliseo Jaír', '92965466', '2022-07-07', 'RIMACHI TERRES, Edith Janeth', '956256392', 'edith_rt82@hotmail.com', 1),
(3, 'LLERENA ARAMBULO, Jose Manuel', '93085113', '2022-10-03', 'ARAMBULO MONSERRATE, Yemmi Vanessa', '945597993', 'yemmiarambulo@gmail.com', 1),
(4, 'RUIZ ESPINOZA, Alessia Camille', '92862989', '2022-04-26', 'ESPINOZA MERINO, Ellen Cindy', '941434783', '', 1),
(5, 'SANTOS BERNAZA, Enzo Miguel', '92886452', '2022-05-10', 'BERNAZA ZAVALA, Rocio Lizzett', '945935104', 'rocio_bernaza@hotmail.com', 1),
(6, 'SANTOS BERNAZA, Thiago Angel', '92886470', '2022-05-10', 'BERNAZA ZAVALA, Rocio Lizzett', '945935104', 'rocio_bernaza@hotmail.com', 1),
(7, 'SOCOLA LESCANO, Aitana Jaziel', '92940286', '2022-06-18', 'LESCANO ZUBILETE, Yojana', '910135085', 'yojanalescano@gmail.com', 1),
(8, 'VALLADARES ARROYO, Alessia Valentina', '93088000', '2022-10-05', 'ARROYO SANCHEZ, Giovana Cristina', '947238081', 'criss_2809@hotmail.com', 1),
(9, 'AGUIRRE VASQUEZ, Eithan Gael', '92677375', '2021-12-21', 'VASQUEZ CHAPOÑAN, Katheryn', '910841744', 'katheryn.vasquez.cha@gmail.com', 2),
(10, 'CABREJOS HUERTA, Kaely', '92354298', '2021-05-11', 'HUERTA COBOS, Joselin Sabrina', '994513835', 'jsabrinahc@gmail.com', 2),
(11, 'CARRERA RETUERTO, Carla Avril', '92582167', '2021-10-13', 'RETUERTO RAMIREZ, Carla Yanet', '918594192', 'carlyretuerto@gmail.com', 2),
(12, 'CHANI ORTEGA, Cielo Sayuri', '92383921', '2021-06-01', 'ORTEGA VENTURO, Emilin', '980653320', 'evelynortegaventuro@gmail.com', 2),
(13, 'CHINCHAY BRICEÑO, Ander Jaredth', '92628734', '2021-11-16', 'BRICEÑO VILLANUEVA, Keyko Judith', '913131090', 'jubricevilla@gmail.com', 2),
(14, 'CORTEZ ROSALES, Dylan Jose', '92400193', '2021-06-11', 'ROSALES MONTESINO, Susan Miriam', '985431511', 'estarkecortez@gmail.com', 2),
(15, 'ESPINOZA SAMAN, Gael', '92663538', '2021-12-11', 'SAMAN BOLIVAR, Liliana', '994411401', 'lilianatodocables@gmail.com', 2),
(16, 'FLORES SALAZAR, Mafer Khaleesy', '92608844', '2021-11-02', 'SALAZAR EGOABIL, Kiara Minerva', '913287209', 'kimsalazar464@gmail.com', 2),
(17, 'HUARI GOMEZ, Angelina Emilia', '92608802', '2021-11-02', 'GOMEZ POZO, Katherine del Rocio', '975104283', 'katrocgp@gmail.com', 2),
(18, 'JULCA TAPIA, Thiago Gael', '92349388', '2021-05-08', 'JULCA HUAMANTTICA, Erick Hugo', '912295568', 'erickjulca24@gmail.com', 2),
(19, 'MENDOZA MUR, Edgar Sebastian', '92321326', '2021-04-18', 'MUR MELENDEZ, Josselyn Beatriz', '902511894', 'edgarcatalinasebastian@gmail.com', 2),
(20, 'MIMBELA ALVAREZ, Nicolás Antonio', '92758931', '2022-02-16', 'ALVAREZ CAMPOS, Joanie Cecilia', '914839836', 'jalvarez29191@gmail.com', 2),
(21, 'OJEDA YNFANTE, Khaleesi Nickol', '92572769', '2022-10-07', 'YNFANTE GOMEZ, Mirian Pilar', '984627294', 'mirianinfante69@gmail.com', 2),
(22, 'PRAELLI CASAHUAMAN, Ayanna Rosella', '92465900', '2021-07-26', 'CASAHUAMAN CASTILLO, Claudia Rosali', '952938698', 'praelli3118@gmail.com', 2),
(23, 'SAAVEDRA JARAMILLO, Luana Taisha', '92722929', '2022-01-23', 'JARAMILLO VEGAS, Anyily Ivonne', '956314576', 'anyilyjaramillo@gmail.com', 2),
(24, 'SIFUENTES CADILLO, Alexis Yampier', '92412934', '2021-06-20', 'CADILLO LEIVA, Maruja Delza', '987264581', 'maruja.cadillo8@gmail.com', 2),
(25, 'VALVERDE TRONCOSO, Emily Isabella', '92502918', '2021-08-20', 'VALVERDE SANCHEZ, Jimmy Glicerio', '957061568', 'patriciarios2209@gmail.com', 2),
(26, 'VARGAS ESPINOZA, Said Mathias', '92384917', '2021-06-01', 'ESPINOZA MERINO, Marilia', '951341380', 'marianel18_es@hotmail.com', 2),
(27, 'VILLANUEVA SOLIS, Danny Máverick', '92593418', '2021-10-22', 'SOLIS VENTURA, Andrehina Roxana', '991108577', 'asolisven1886@gmail.com', 2),
(28, 'BAQUERIZO HUAMANI, Dara Abdiel', '91963970', '2020-08-09', 'HUAMANI MEZA, Joysi Caroli', '906554410', 'nickitalo.30201@gmail.com', 3),
(29, 'BARRETO SUAREZ, Jhordy Mateo', '92044056', '2020-10-02', 'SUAREZ PEÑA, Maria Dolores', '994395433', 'suarezpema@gmail.com', 3),
(30, 'CARDEÑA RAMOS, Lucca Wilfredo', '92276844', '2021-03-18', 'RAMOS LOZANO, Ines Manuela', '994368109', 'imaralo23@hotmail.com', 3),
(31, 'CORDOVA YALAN, Isabella Margarita', '91865671', '2020-05-25', 'YALAN SOTELO, Yurico', '956517416', 'sotelo.yurico@hotmail.com', 3),
(32, 'ENRIQUEZ CASTILLO, Thiago Alessandro', '91990888', '2020-08-27', 'ENRIQUEZ ALCCA, Luis Francisco', '923233826', 'luisenriquez2105@gmail.com', 3),
(33, 'GARCIA MARCELO, Daphne Alessia', '92292881', '2021-03-29', 'MARCELO CORTEZ, Nancy', '954913384', 'nany14451@gmail.com', 3),
(34, 'HUARCA GONZALES, César Luis', '91849287', '2020-05-11', 'GONZALES MORENO, Carmen Rosa', '938338525', 'cagonzales2023@pedagogicomariannefrostig.edu.pe', 3),
(35, 'MARIN ROSALES, Maia Samara', '91891301', '2020-06-14', 'ROSALES MONTESINO, Judith Paola', '992386963', 'jrosalesmontesino@gmail.com', 3),
(36, 'MIRANO CARHUAJULCA, Thiago Matteo', '92201641', '2021-01-21', 'CARHUAJULCA MALUQUIS, Maryori', '932374627', 'nicolecarhuajulca0109@gmail.com', 3),
(37, 'RAMOS HUAMAN, Nicolás Adriano', '91846341', '2020-05-09', 'HUAMAN RICRA, Mylene', '', 'mylenehuaman@gmail.com', 3),
(38, 'REQUEJO SENA, Eddy Gael', '92204773', '2021-01-23', 'SENA PAISIG, Yesela', '986893364', 'yeselasena1993@gmail.com', 3),
(39, 'RODRIGO MENDOZA, Leonardo Raíd', '91960494', '2020-08-06', 'MENDOZA NUÑEZ, Icela Noemi', '983799439', 'icelamendozan@gmail.com', 3),
(40, 'SILVA ROJAS, Alex Omar', '92231641', '2021-02-12', 'ROJAS VENTURA, Luz Esperanza', '960158678', 'silvaomar282@gmail.com', 3),
(41, 'YATACO VENTURA, Emilia Victoria', '92230158', '2021-02-12', 'VENTURA ZAFRA, Milena Ofelia', '982088862', '1395mvz@gmail.com', 3),
(42, 'ZAVALA CORONADO, Laura Olinda', '91823093', '2020-04-20', 'CORONADO COCHACHI, Laura', '943690794', 'lauracoronadoley@gmail.com', 3),
(43, 'AGUIRRE VASQUEZ, Liam Stefano', '91268151', '2019-04-06', 'VASQUEZ CHAPOÑAN, Katheryn', '910841744', 'katheryn.vasquez.cha@gmail.com', 4),
(44, 'CALLERGOS CUBAS, Milber Mateo', '81906520', '2020-01-14', 'CUBAS ARCE, Elena', '916514617', 'elenacuar@gmail.com', 4),
(45, 'CONZUELO CASTROMONTE, Sammy Eileen', '91555705', '2019-09-16', 'CASTROMONTE MORENO, Emilia Edilvira', '990737626', 'lconzuelo@hotmail.com', 4),
(46, 'CONZUELO OLORTEGUI, Khiara Valemtina', '91649860', '2019-12-24', 'OLORTEGUI DAMAZO, Rosana Cecilia', '900260978', 'olorteguidamazorosana@gmail.com', 4),
(47, 'HURTADO COTRINA, Zoe Luna', '91754799', '2020-03-02', 'COTRINA SALAZAR, Rene', '957620449', 'renecotsal@gmail.com', 4),
(48, 'MOGOLLON MEDINA, Thiago', '25100000000000', '2019-12-15', 'MOGOLLON MEDINA, Yajaira', '918600682', 'medina777y@icloud.com', 4),
(49, 'MOTTA TOCTO, Elsy Rocio', '91481441', '2019-08-07', 'TOCTO LUCAR, Juana Maribel', '970166469', 'hmottajuez@outlook.com', 4),
(50, 'RAMOS SOTO, Aisha Mia Mikeyla', '91428664', '2019-06-28', 'SOTO RUIZ, Estela Yaqueline', '985025872', 'esyasoru430@gmail.com', 4),
(51, 'REGALADO CORDOVA, Zoe Aithana', '91479558', '2019-08-26', 'CORDOVA ROMAN, Tania Aracely', '987822779', 'taniacrr45@gmail.com', 4),
(52, 'REQUEJO SENA, Alisson Marinette', '91328685', '2019-04-27', 'SENA PAISIG, Yesela', '986893364', 'yeselasena1993@gmail.com', 4),
(53, 'RICOPA VILLANUEVA, Ian Matteo', '91702615', '2020-01-28', 'VILLANUEVA OBISPO, Victoria Maria', '990130427', 'victoria020@hotmail.com', 4),
(54, 'ROJAS ARROYO, Ainhoa Sofia Khalessi', '91317436', '2019-04-22', 'ARROYO COLONIA, Lilia Marlene', '970553741', 'mar442216@gmail.com', 4),
(55, 'SANCHEZ JULCA, Eyal Camilo', '91649048', '2019-12-23', 'SANCHEZ GUIZADO, Luis Alfredo', '983736332', 'aimept06@gmail.com', 4),
(56, 'VALVERDE TRONCOSO, Gael Glicerio', '91609822', '2019-11-26', 'TRONCOSO CORVERA, Isabela Patricia Carolina', '957061568', 'patriciarios2209@gmail.com', 4),
(57, 'VIVIANO VALLEJOS, Sol Luciana', '91434045', '2019-06-27', 'VALLEJOS PALOMINO, Silvia Raquel', '996785681', 'silvia.vallejos@pucp.edu.pe', 4),
(58, 'YUCRA TAPIA, Catalina Alessandra', '91517036', '2019-08-18', 'TAPIA MELENDEZ, Brenda Alisson', '913746691', 'bm2498698@gmail.com', 4),
(59, 'AGUILAR MENDOZA, Kalessi Leonela', '90832784', '2018-06-13', 'MENDOZA NUÑEZ, Maria Estilita', '981992785', 'elita.mn19@gmail.com', 5),
(60, 'BARRENECHEA VALENZUELA, Alessia Guadalupe', '90924665', '2018-08-14', 'BARRENECHEA TAFUR, Helio Vicente', '952902567', 'helio.bar.taf@gmail.com', 5),
(61, 'CEDEÑO, Arianny Marilina', '20200000000000', '2018-09-19', 'SUAREZ ARAUJO, Anderson', '917160229', 'andersonsuarez0306@gmail.com', 5),
(62, 'HUAMANI PEREZ, Alizée Keyla', '90796042', '2018-05-24', 'HUAMANI GONZALES, Elvis David', '963007902', 'jaynko1992@gmail.com', 5),
(63, 'IGNACIO INGA, Alexander Jens', '90997232', '2018-10-01', 'IGNACIO MUÑOZ, Juan Luis', '997686972', 'jil508835@gmail.com', 5),
(64, 'PALACIOS FERNANDEZ, Hansel Alexis', '90757996', '2018-04-26', 'FERNANDEZ RUIZ, Jackeline Fiorella', '981478281', 'jackifernandezruiz@gmail.com', 5),
(65, 'QUISPE PACOMPIA, Thiago Muller', '91221906', '2019-03-09', 'PACOMPIA PACOMPIA, Fanny', '907545840', 'pacompiafanny53@gmail.com', 5),
(66, 'RAMOS CHATE, Elián Jonás', '90800555', '2018-05-30', 'RAMOS CAMPOS, David Wilder', '982544130', 'ramoscamposdavidwilder@gmail.com', 5),
(67, 'SALAZAR BUSTAMANTE, Dyland Kael', '91589274', '2018-12-19', 'BUSTAMANTE RIVERA, Milagros', '922035586', 'milagritos.09.12@gmail.com', 5),
(68, 'TENORIO BURGA, Hanna Alessia', '91082735', '2018-11-20', 'BURGA ALZAMORA, Lady Loredan', '919657505', 'lburgaalzamora@gmail.com', 5),
(69, 'VASQUEZ SALINAS, Pamela Julieta', '91143940', '2018-12-26', 'SALINAS RIOJA, Carmen Ayde', '969680367', 'aydesalinasrioja@gmail.com', 5),
(70, 'VIZARRETA TARAZONA, Dylan Ian Abel', '91215431', '2019-02-19', 'VIZARRETA VEGA, Victor Abel', '934275932', 'vizavega@gmail.com', 5),
(71, 'ANGULO PEÑA, Noah Leao', '90498003', '2017-11-06', 'PEÑA ZUÑIGA, Dayana del Carmen', '977631959', 'aylinangulo59@gmail.com', 6),
(72, 'ARONE TOMATEO, Valentina Cecilia', '90739908', '2018-03-28', 'TOMATEO VELASQUE, Cecilia', '936788377', 'arone2307@gmail.com', 6),
(73, 'CABRERA RAMOS, David Adrian', '90566433', '2018-01-01', 'RAMOS HOYOS, Ena', '933955582', 'enaramosjd@gmail.com', 6),
(74, 'CASTAÑEDA ENRIQUEZ, Alisson Alessandra', '90531571', '2017-12-02', 'ENRIQUEZ CASTILLO, Estela Dionisia', '943766979', 'esdienka0608@hotmail.com', 6),
(75, 'CASTILLO CORDOVA, Daymar de Jesus', '90272982', '2017-06-11', 'CORDOVA NAUCA, Araceli Yanet', '987455842', 'aracelyyanetcordova998@gmail.com', 6),
(76, 'CCECCAÑA SAIRITUPAC, Maria Fernanda', '90711461', '2018-03-24', 'SAIRITUPAC VENTURA, Rhina Grisela', '936532015', 'alexander100986@hotmail.com', 6),
(77, 'CESPEDES GARCIA, Adriano Ghael', '90400964', '2017-09-06', 'GARCIA BERNAL, Alizon Rubi', '902832473', 'operacion123@hotmail.com', 6),
(78, 'COLLANQUI CASTILLO, Enzo Kaled', '90420693', '2017-09-12', 'CASTILLO GAMARRA, Ignacia Hermelinda', '955479889', 'wcollanqui25@gmail.com', 6),
(79, 'COLONIA GOMEZ, Linda Krystal del Cielo Guadalupe', '90173380', '2017-04-14', 'COLONIA ZEVALLOS, Amador Fernando', '993829846', 'psicocolo81@hotmail.com', 6),
(80, 'CUADROS FIGUEROA, Iker Brian', '90553334', '2017-12-12', 'FIGUEROA TORPOCO, Kelly Edilene', '920363942', 'kellyfigueroa24@hotmail.com', 6),
(81, 'DÍAZ RODAS, Joe Luis', '90603791', '2018-01-22', 'RODAS SANCHEZ, Sarita Ruvi', '952258357', 'rodassanchezsarita@gmail.com', 6),
(82, 'DOLORES PAULINO, Henz Elber', '90468926', '2017-10-07', 'PAULINO CAMONES, Luz Liliana', '955150299', 'paulinocamonesliliana@gmail.com', 6),
(83, 'FERNANDEZ AGREDA, Raffaella Luciana', '90700003', '2018-03-14', 'AGREDA AVILA, Claudia Olivia', '933383669', '', 6),
(84, 'GARCIA CARDENAS, Maell Kelvin', '90223605', '2017-05-01', 'CARDENAS ALBUJAR, Nathaly Mercedes', '974521919', 'cnathaly808@gmail.com', 6),
(85, 'GARCIA VALENCIA, Anthuan Nebú', '90624063', '2018-01-21', 'VALENCIA RIVERA, Maryory Rosa', '924932955', '', 6),
(86, 'HURTADO YNFANTE, Brianna Pilar', '90458887', '2017-10-15', 'YNFANTE GOMEZ, Mirian Pilar', '984627294', 'mirianinfante69@gmail.com', 6),
(87, 'MAYO ZAVALA, Anna Lucía', '90386678', '2017-09-01', 'ZAVALA GOMEZ, Estefani Beatriz', '945413911', 'estefanizavalagomez@gmail.com', 6),
(88, 'MOGOLLON MEDINA, Martin David', '25100000000000', '2017-09-07', 'MOGOLLON MEDINA, Yajaira', '918600682', 'medina777y@icloud.com', 6),
(89, 'ORTEGA SARREA, Danitza Vanessa', '90497660', '2017-11-15', 'SARREA BECERRA, Lizeth Vanessa', '938129287', 'sarreabecerravanessa@gmail.com', 6),
(90, 'PEIXOTO NAJARRO, Sasha Karla Valentina', '90470214', '2017-10-22', 'NAJARRO GARCES, Lea', '947197059', 'lenaga28@gmail.com', 6),
(91, 'QUISPE URZULA, Lukas Adrian', '90491908', '2017-10-27', 'URZULA CARPIO, Pierina Felisa', '997729944', 'pierii1527@gmail.com', 6),
(92, 'RUIZ SIGUAS, Diana Luisa', '90269089', '2017-06-13', 'RUIZ SALAZAR, Pedro Desiderio', '975299487', 'emirsiguas77@gmail.com', 6),
(93, 'VERA DIAZ, Brianna Mayte', '90559472', '2017-12-20', 'DIAZ VASQUEZ, Rosmery', '956652480', 'diazrosmery858@gmail.com', 6),
(94, 'ZAVALA CORONADO, Fernanda Estela', '90180318', '2017-04-17', 'CORONADO COCHACHI, Laura', '943690794', 'lauracoronadoley@gmail.com', 6),
(95, 'BERNABE GONZALES, Bryhanna Ruby', '79810539', '2016-08-15', 'GONZALES SALAZAR, Gina Ruby', '999929924', 'ing-ginaruby@up.org.pe', 7),
(96, 'CONTRERAS IGNACIO, Diana Valentina', '79771452', '2016-07-15', 'IGNACIO PARDO, Aydee Carol', '976097965', 'carolay43@hotmail.com', 7),
(97, 'CONZUELO CASTROMONTE, Leandro Livio', '90098847', '2017-02-06', 'CASTROMONTE MORENO, Emilia Edilvira', '990737626', 'lconzuelo@hotmail.com', 7),
(98, 'ESTELA OSORIO, Jeheli Maite', '81664299', '2016-04-12', 'OSORIO URBANO, Brenda Helen', '963246153', 'obrendahelen@gmail.com', 7),
(99, 'GUZMAN RAMIREZ, Joannie Khaleesi', '90074241', '2017-02-04', 'GUZMAN GONZALES, Jorge Alfredo', '982949262', 'jorgexguzman@gmail.com', 7),
(100, 'JIMENEZ GARRIAZO, Luciana Mayte', '90133404', '2017-03-18', 'GARRIAZO QUISPE, Judy Elizabeth', '960410660', 'jadelic_2001@hotmail.com', 7),
(101, 'LOPEZ GARRIAZO, Arisbeth Luna Julieth', '79728641', '2016-06-22', 'GARRIAZO QUISPE, Maria del Pilar', '938141420', 'garriazopilar65@gmail.com', 7),
(102, 'LOZANO SANCHEZ, Zhoemy Carolina', '90026926', '2017-01-09', 'SANCHEZ PONTE, Greis Katty', '947714054', 'greiskse@gmail.com', 7),
(103, 'MENDOZA AROSTE, Logan Alesandro', '79784895', '2016-07-22', 'AROSTE YÑIGO, Nancy Aida', '904722627', '', 7),
(104, 'NAVARRO VILLA, Thiago Eduardo', '90146920', '2017-03-28', 'VILLA GONZALES, Beatriz', '969461825', 'almadeloriente@gmail.com', 7),
(105, 'RAMIREZ MANDUJANO, Yazid Abdiel', '79939357', '2016-11-12', 'MANDUJANO VIVANCO, Haily Esmeralda', '988002558', 'hailyifbestudio@gmail.com', 7),
(106, 'URRESTI LEDESMA, Valentin Nicolás', '90001182', '2016-12-12', 'LEDESMA CABRERA, Samanda Jhasmine', '933713646', 'ledesmasamanda@gmail.com', 7),
(107, 'VIDAL MOSTACERO, Maria Fernanda', '79656129', '2016-05-07', 'MOSTACERO CHIZA, Carmen Rosa', '945474910', 'carmenmostacero1979@hotmail.com', 7),
(108, 'VILCHEZ TAFUR, Fabrizzio Sebastián', '79841561', '2016-08-23', 'TAFUR VILLACORTA, Itala Farraday', '946295970', 'itafa_9er@hotmail.com', 7),
(109, 'YUCRA TAPIA, Jhon Matias', '90215109', '2017-03-26', 'TAPIA MELENDEZ, Brenda Alisson', '913746691', 'bm2498698@gmail.com', 7),
(110, 'BENITES SALAZAR, Valentina Anghely', '79998513', '2016-12-26', 'SALAZAR JAMANCA, Carmen Luz', '934580302', 'salazarjamancacarmen@gmail.com', 8),
(111, 'CADILLO SIFUENTES, Jhosep Adriano', '79731702', '2016-06-20', 'SIFUENTES OSORIO, Yanet', '912586419', 'yanetsifuentes36@gmail.com', 8),
(112, 'LANDA FERNANDEZ, Naeem Aryan', '79671156', '2016-04-20', 'FERNANDEZ TORRES, Lucy Esther', '963373289', 'katzumy_15@hotmail.com', 8),
(113, 'LIPA VASQUEZ, Axel Lionel', '79996378', '2016-12-05', 'VASQUEZ CRUZ, Nixia Mafiori', '969546028', 'alejandro1992461@gmail.com', 8),
(114, 'MEDINA URRIOLA, Mathias Gabriel', '19100000000000', '2015-12-28', 'URRIOLA WELESKE, Mary Leysy', '901687086', 'urriolaweleskem@gmail.com', 8),
(115, 'NOEL CALDERON, Briana Ethel', '79900259', '2016-09-22', 'CALDERON BALDEON, Jackelin', '957138589', 'jaquelinecalderonbaldeon@gmail.com', 8),
(116, 'ÑAHUIS VARGAS, Franco Jhael', '81535074', '2016-05-29', 'VARGAS RENGIFO, Nieves del Pilar', '969368814', 'varenny.13@gmail.com', 8),
(117, 'OLIVARES MACEDO, Edwin Nicolas', '90018258', '2017-01-07', 'OLIVARES ECHEVARRIA, Edwin Edgardo', '928736375', 'familia.olivares.macedo@gmail.com', 8),
(118, 'PADILLA MARTINEZ, Harol Anthony', '79637132', '2016-04-11', 'MARTINEZ RAMIREZ, Rossana', '921878173', 'rossana_08@hotmail.com', 8),
(119, 'PAREDES PAREDES, Darianys Sarai', '8009111', '2017-03-28', 'PAREDES SIFUENTES, Elimar del Valle', '917972867', 'elyparedess87@gmail.com', 8),
(120, 'RIBBECK LIMA, Angelina Salome', '79682810', '2016-04-28', 'RIBBECK HURTADO, Abelardo Ernesto', '950165057', 'jribbecklima@gmail.com', 8),
(121, 'ROQUE ZEGARRA, Derek Dasher', '79827589', '2016-08-06', 'ZEGARRA ALZAMORA, Rosario Judith', '952896934', '', 8),
(122, 'SANCHEZ ROJAS, Joaquín Gabriel', '79949694', '2016-11-18', 'SANCHEZ RAMIREZ, Paul Jordy', '970776910', 'pjsanchezramirez@gmail.com', 8),
(123, 'SARAZU BAYONA, Thiago Ghael', '90090938', '2017-02-20', 'BAYONA RAMIREZ, Rosalia Rebeka', '931189626', 'rebekabayona20@gmail.com', 8),
(124, 'AUCCALLA VILLANUEVA, Gisem Alessandra', '79441896', '2015-12-21', 'VILLANUEVA ZAFRA, Melissa Beatriz', '941936309', '1395mvz@gmail.com', 9),
(125, 'AVILES BUSTAMANTE, Mathias Leonel', '79338944', '2015-10-12', 'AVILES SUDARIO, Robert Said', '933128288', 'robert0407ac@gmail.com', 9),
(126, 'CALLERGOS CUBAS, Jheik Stiven', '81486208', '2015-07-20', 'CUBAS ARCE, Elena', '916514617', 'elenacuar@gmail.com', 9),
(127, 'CARMONA COLONIA, Josuhe Jhamil Jheicob', '79187281', '2015-07-01', 'COLONIA ZEVALLOS, Sarita', '983455733', 'colonia.zevallos@outlook.com', 9),
(128, 'CARRERA RETUERTO, Axel Samir', '79453602', '2015-08-15', 'RETUERTO RAMIREZ, Carla Yanet', '918594192', 'carlyretuerto@gmailcom', 9),
(129, 'CASTILLO ADRIANO, Franco Sthefano', '81515832', '2015-11-16', 'ADRIANO TORRES, Elizabeth del Socorro', '970516584', 'elizabethat_2507@hotmail.com', 9),
(130, 'DAMIAN TORVISCO, Ivana Hillary', '79458916', '2015-12-29', 'TORVISCO USCATA, Marcelina', '989957683', '', 9),
(131, 'DIESTRA LEON, Jadriel Liam', '79430706', '2015-11-18', 'DIESTRA PRINCIPE, Ilario Timoteo', '985969435', 'diestraleong@gmail.com', 9),
(132, 'HERRERA MANTARI, Soledad Maricielo', '79254689', '2015-06-02', 'MANTARI LAUREANO, Esther Filomena', '927221035', 'filomenamantari1978@gmail.com', 9),
(133, 'LOPEZ LOPEZ, Nicolás Josué', '79221324', '2015-07-25', 'LOPEZ CHUMACERO, Doraldina', '983628935', 'doritalopez85@hotmail.com', 9),
(134, 'MARTINEZ VILLANUEVA, Alejandro Valentino', '79606971', '2016-03-29', 'VILLANUEVA OBISPO, Victoria Maria', '964951391', 'victoria020@hotmail.com', 9),
(135, 'MISAJEL PALOMINO, Emir Rider', '79509453', '2016-01-09', 'PALOMINO OROZCO, Gloria Elizabeth', '995990615', 'e.palomino.002@gmail.com', 9),
(136, 'MOTTA TOCTO, Alicia Camila', '79584546', '2016-03-13', 'TOCTO LUCAR, Juana Maribel', '970166469', 'hmottajuez@outlook.com', 9),
(137, 'O’HIGGINS NAJARRO, Carmen Jazmin', '79179663', '2015-07-01', 'NAJARRO GARCES, Lea', '947197059', 'lenaga28@gmail.com', 9),
(138, 'PAREDES PAREDES, Davielys Sofia', '8049246', '2014-11-10', 'PAREDES SIFUENTES, Elimar del Valle', '917972867', 'elyparedess87@gmail.com', 9),
(139, 'PILLACA RAMOS, Alyssa Liv Esmeralda', '79213852', '2015-06-28', 'RAMOS MALLQUI, Flor de Liz', '989436891', 'flor_liz_rm@yahoo.com', 9),
(140, 'RAMOS OLIVEROS, Fabrizio Aquiles', '79147994', '2015-06-04', 'OLIVEROS ALVARADO, Tania Ursula', '933655511', 'jr6073172@gmail.com', 9),
(141, 'BARRENECHEA VALENZUELA, Maria Angela Ivanna', '79161099', '2015-06-15', 'BARRENECHEA TAFUR, Helio Vicente', '952902567', 'helio.bar.taf@gmail.com', 10),
(142, 'CAJUSOL CASTAÑEDA, Thiago Yahel', '79287115', '2015-08-31', 'CASTAÑEDA CARDOZA, Sofia Steefany', '969750265', 'sofia_sc_19@hotmail.com', 10),
(143, 'CALISAYA SERGO, Luciana Berenice Ursula', '79498523', '2016-01-07', 'SERGO CHURATA, Gabriela', '938123856', 'gabrielasergo123@gmail.com', 10),
(144, 'CHANI ORTEGA, Nijash Vidrejan', '79517155', '2016-02-02', 'ORTEGA VENTURO, Emilin', '980653320', 'evelynortegaventuro@gmail.com', 10),
(145, 'COTRINA QUISPE, Thiago Liam', '79213350', '2015-06-26', 'QUISPE HUAMAN, Julia', '985948655', 'juliaquispe526@gmail.com', 10),
(146, 'DOLORES PAULINO, Enzo Clemente', '79462022', '2015-12-17', 'PAULINO CAMONES, Luz Liliana', '955150299', 'paulinocamonesliliana@gmail.com', 10),
(147, 'GARRIAZO RIMACHI, Elías Miguel', '79308266', '2015-09-24', 'RIMACHI TERRES, Edith Janeth', '956256392', 'edith_rt82@hotmail.com', 10),
(148, 'LEON LEZAMA, Liam Sthefano', '79129593', '2015-05-26', 'LEON CALEROS, Romualdo', '965930358', 'romualdo_15168@outlook.es', 10),
(149, 'MARIN ROSALES, Maximiliano Zaid', '79152123', '2015-06-09', 'ROSALES MONTESINO, Judith Paola', '992386963', 'jrosalesmontesino@gmail.com', 10),
(150, 'NAVARRO TELLO, Fabiano Gabriel', '79547303', '2016-02-25', 'NAVARRO VALDIVIEZO, Jhon Guillermo', '997083097', 'johnguillermo90@gmail.com', 10),
(151, 'ORTEGA SARREA, Lizeth Daniela', '79857602', '2016-01-19', 'SARREA BECERRA, Lizeth Vanessa', '938129287', 'sarreabecerravanessa@gmail.com', 10),
(152, 'TORIBIO FUENTES RIVERA, Beckham Daniel', '79145500', '2015-05-21', 'FUENTES RIVERA UGARTE, Ana Maria', '995885823', 'anamariafru11@gmail.com', 10),
(153, 'VELASQUEZ SALINAS, Brianna Isabel', '79447361', '2015-12-16', 'SALINAS RIOJA, Danitza', '994381318', 'dannytza_30@hotmail.com', 10),
(154, 'VILLAVERDE ARROYO, Camila Dayana', '79608549', '2016-03-16', 'ARROYO SANCHEZ, Giovana Cristina', '947238081', 'criss_2809@hotmail.com', 10),
(155, 'ZAVALA ALMONTE, Vittorino Israel', '79632990', '2016-03-28', 'ALMONTE ALVA, Mercedes Alicia', '927462379', 'semacoral@gmail.com', 10),
(156, 'AGREDA RUPAY, Jeanfranco Jesus', '78897315', '2014-12-24', 'RUPAY PAITA, Cyntia Herly', '933383669', 'cyntiarupaypaita@gmail.com', 11),
(157, 'ANICETO VERA, Flavio Antonio Nijash', '78532684', '2014-04-02', 'VERA MORAZANI, Diana Isabel', '976399266', 'ariana_1199@hotmail.com', 11),
(158, 'CORTEZ ROSALES, Thiago Mateo', '78948538', '2015-01-25', 'ROSALES MONTESINO, Susan Miriam', '985431511', 'estarkecortez@gmail.com', 11),
(159, 'DIONICIO ALONZO, Leonel Jose', '78577956', '2014-04-17', 'ALONZO CANCHANYA, Sonia', '983757941', 'edwindionicio11@gmail.com', 11),
(160, 'ESPEZUA CONTRERAS, Diana Nicoll', '78652330', '2014-07-02', 'CONTRERAS MEDINA, Ayde Beatriz', '943025805', 'boydaesp14@gmail.com', 11),
(161, 'ESPINOZA COLONIA, Danna Leonela', '78968437', '2015-01-31', 'COLONIA ZEVALLOS, Jessica Mercedes', '924808335', 'jessdann2731@gmail.com', 11),
(162, 'FERNANDEZ GALARZA, Jazhiel Alisai', '78746636', '2014-07-11', 'GALARZA SULCA, Jasmin Jhosem', '968782129', '', 11),
(163, 'HUAYCOCHEA GUARDAMINO, Luciana Marilú', '78603690', '2014-05-19', 'GUARDAMINO PALOMINO, Lisset Luciana', '981427994', 'lissetguardamino@gmail.com', 11),
(164, 'JESUS CHATE, Maria Fernanda', '79030050', '2015-02-24', 'CHATE SULCA, Rocio Nilda', '936668650', 'rociochatesulca88@gmail.com', 11),
(165, 'LOAYZA DE LA CRUZ, Amiel Adrian', '78777906', '2014-09-11', 'DE LA CRUZ BONIFACIO, Agripina Lucy', '994513422', 'carlohumbertoloayzasolis@gmail.com', 11),
(166, 'LOPEZ GARRIAZO, Brianna Abigail', '78714470', '2014-08-14', 'GARRIAZO QUISPE, Maria del Pilar', '938141420', 'garriazopilar65@gmail.com', 11),
(167, 'MARTINEZ CANCINO, Thiago Mathias', '78616109', '2014-06-08', 'CANCINO ZEDANO, Maribel Alicia', '961262471', 'maribelcancino13@gmail.com', 11),
(168, 'MONRROY STARRIPA, Dominick Jesús', '78963554', '2015-02-02', 'STARRIPA NIETO, Edith Mercedes', '993592990', 'edith_majo07@hotmail.com', 11),
(169, 'VALENZUELA DURAND, Yadira Abigail', '78678492', '2014-07-17', 'DURAND YARASQUI, Janet Teresa', '964096146', 'jady1982@gmail.com', 11),
(170, 'AGREDA ROMERO, Abigail Stephanie', '78593655', '2014-05-15', 'ROMERO DIAZ, Jerly', '958281906', 'jerlyromero27@gmail.com', 12),
(171, 'AGUIRRE VASQUEZ, Mia Angely', '78914132', '2015-01-05', 'VASQUEZ CHAPOÑAN, Katheryn', '910841744', 'katheryn.vasquez.cha@gmail.com', 12),
(172, 'ARMAS MANRIQUE, Damaris Estefany', '78746860', '2014-08-24', 'ARMAS ROSARIO, Ivan Ramiro', '929078145', 'ivan.2382@hotmail.com', 12),
(173, 'CABRERA RAMOS, Jocelyn Jazmin', '79043246', '2015-03-31', 'RAMOS HOYOS, Ena', '933955582', 'enaramosjd@gmail.com', 12),
(174, 'CCECCAÑA SAIRITUPAC, Alexia Gisele', '78772785', '2014-09-20', 'SAIRITUPAC VENTURA, Rhina Grisela', '936532015', 'alexander100986@hotmail.com', 12),
(175, 'GARCIA PALACIOS, Aarón Ferrer', '79039748', '2015-03-23', 'PALACIOS ALVARADO, Islaida Tait', '944681612', 'islaidapalacios@gmail.com', 12),
(176, 'MENDOZA GUEVARA, Keyla Arelí', '78531254', '2014-04-07', 'MENDOZA SEGUIL, Maximo Edgar', '902511894', 'edgarcatalinasebastian@gmail.com', 12),
(177, 'MUR MELENDEZ, Catalina Alexia', '78688815', '2014-07-24', 'MUR MELENDEZ, Josselyn Beatriz', '902511894', 'edgarcatalinasebastian@gmail.com', 12),
(178, 'POSAICO PALACIOS, Axel Benjamin', '78798431', '2014-10-09', 'PALACIOS CALDERON, Debora Lucia', '947106110', 'lucialpc84@gmail.com', 12),
(179, 'QUISPE URZULA, Evans Josué Matheo', '78801328', '2014-10-15', 'URZULA CARPIO, Pierina Felisa', '997729944', 'pierii1527@gmail.com', 12),
(180, 'ROSALES GUTIERREZ, Matias Ricardo', '78977243', '2015-02-09', 'GUTIERREZ RODRIGUEZ, Violeta', '937273944', 'violetagutierrezrodriguez2@gmail.com', 12),
(181, 'REYNOSO CACERES, Xímena Nataly', '78883340', '2014-12-03', 'CACERES CONTRERAS, Natalia', '', '', 12),
(182, 'RUIZ SIGUAS, Jimmy Alexis', '78747511', '2014-08-24', 'RUIZ SALAZAR, Pedro Desiderio', '975299487', 'emirsiguas77@gmail.com', 12),
(183, 'VILLANUEVA SOLIS, Danette Micaela', '78531186', '2014-04-13', 'SOLIS VENTURA, Andrehina Roxana', '991108577', 'asolisven2007@gmail.com', 12),
(184, 'ALVA MURO, Fabianne Nathanielly', '78280611', '2013-09-22', 'MURO PAZ, Maria del Pilar', '980102305', 'maripili123@gmail.com', 13),
(185, 'ALZAMORA WALDE, Jade Mayte', '78360301', '2013-12-10', 'ALZAMORA REVOLLEDO, Telesfor', '902079406', 'carmen.walde@hotmail.com', 13),
(186, 'BRAVO TORVISCO, Anber Angela Maytee', '78417438', '2014-01-22', 'TORVISCO VALENCIA, Ernestina', '967114980', 'torviscomonica@gmail.com', 13),
(187, 'CALLERGOS CUBAS, Alierd Llevermi', '81036194', '2013-11-23', 'CUBAS ARCE, Elena', '916514617', 'elenacuar@gmail.com', 13),
(188, 'CAMACHO FERNANDEZ, Kaori Mahalasaisha', '78400515', '2013-12-20', 'FERNANDEZ MANZANEDO, Elizabeth Mercedes', '923302533', 'luisaliravi@gmail.com', 13),
(189, 'CARDEÑA RAMOS, Chiara Dámaris', '78232254', '2013-08-17', 'RAMOS LOZANO, Ines Manuela', '994368109', 'imaralo23@hotmail.com', 13),
(190, 'COLLANQUI CASTILLO, Bryan Mateo', '78501813', '2014-03-09', 'CASTILLO GAMARRA, Ignacia Hermelinda', '955479889', 'wcollanqui25@gmail.com', 13),
(191, 'CORRALES ZEGARRA, Kahori Nicol', '63347069', '2011-10-31', 'CORRALES CALLA, Hedvin', '937718688', 'corralescallah@gmail.com', 13),
(192, 'GARRIAZO RIMACHI, Danitza Edith', '78436221', '2014-02-04', 'RIMACHI TERRES, Edith Janeth', '956256392', 'edith_rt82@hotmail.com', 13),
(193, 'GONZALES SILVA, Cielo Nayara', '78503652', '2014-03-03', 'SILVA GUEVARA, Fabiola Esther', '936712027', 'fabiolaesthersilvaguevara68@gmail.com', 13),
(194, 'HUAMAN CARDOZO, Leandro Javier', '78129980', '2013-06-02', 'CARDOZO, Deisy Patricia', '924036084', 'deisypcl@gmail.com', 13),
(195, 'INGA ROJAS, Ashley Milagro', '78466339', '2014-01-31', 'ROJAS RIVERA, Jaqueline Luzmila', '999310684', 'ashleyingarojas@gmail.com', 13),
(196, 'LEIVA CABEZAS, Janis Juliette', '78191022', '2013-07-09', 'CABEZAS FLORES, Sintia Simonei', '957341887', 'cintiacabezas@gmail.com', 13),
(197, 'MAYURI VALQUI, Thiago Alejandro', '78359388', '2013-12-02', 'VALQUI ANGELES, Socorro Esperanza', '949958615', 'esperanzavalqui9@gmail.com', 13),
(198, 'MEJIA MOZOMBITE, Wilmer Alexis', '78080275', '2013-04-29', 'MEJIA CARHUAYANO, Wilmer Juan', '932095912', 'wilmermejiacarhuayano@gmail.com', 13),
(199, 'MENDOZA HUERTA, Brithany Gia', '90243556', '2013-08-19', 'MENDOZA SEGUIL, Erick', '947800612', 'emendozaseguil0@gmail.com', 13),
(200, 'MISAJEL PALOMINO, Ariana Sisary', '78541308', '2014-03-31', 'PALOMINO OROZCO, Gloria Elizabeth', '995990615', 'e.palomino.002@gmail.com', 13),
(201, 'MORALES ARAUCANO, Homero Hilario', '78302327', '2013-09-25', 'MORALES CACHA, Hilario', '961787613', 'hilario.riconcitohuaracino@gmail.com', 13),
(202, 'PALOMINO JOO, Dick Oliver', '78296425', '2013-10-18', 'JOO AQUINO, Cecilia Juana', '947220732', 'cecijoo1986@gmail.com', 13),
(203, 'ZAVALA ALMONTE, Anghelo Oswaldo', '78192876', '2013-07-27', 'ZAVALA ÑAHUYS, Oswaldo Israel', '925645348', 'osis6465@gmail.com', 13),
(204, 'ALAYO SARREA, Maria Fernanda Santos', '77778882', '2012-08-25', 'SARREA BECERRA, Lizeth Vanessa', '938129287', 'sarreabecerravanessa@gmail.com', 14),
(205, 'AMPUERO VASQUEZ, Juan Diego', '77992587', '2013-02-08', 'VASQUEZ CHERO, Merly', '946185524', 'ampuerojuliana.123@gmail.com', 14),
(206, 'CABRERA TIMOTEO, Angelo Samir', '77784497', '2012-07-23', 'TIMOTEO GARCIA, Marcela', '947699176', 'marcela1857timoteo@gmail.com', 14),
(207, 'CARMONA COLONIA, Bryanna Ghaela', '77971832', '2013-02-02', 'COLONIA ZEVALLOS, Sarita', '983455733', 'colonia.zevallos@outlook.com', 14),
(208, 'CASTRO BUSTAMANTE, Valentina Luciana', '77861582', '2012-10-06', 'BUSTAMANTE RIVERA, Milagros', '922035586', 'milagritos.09.12@gmail.com', 14),
(209, 'CERRON RAMOS, Fredy Miguel', '77761999', '2012-07-26', 'RAMOS PARIASCA, Mirian Pilar', '992246658', 'pily_mrp6@hotmail.com', 14),
(210, 'COLLANQUI CASTILLO, Rodrigo Gabriel', '77730731', '2012-06-21', 'CASTILLO GAMARRA, Ignacia Hermelinda', '955479889', 'wcollanqui25@gmail.com', 14),
(211, 'ESPINOZA SILUPU, Jairo Joel', '81231861', '2013-01-19', 'SILUPU SOSA, Maria Marcelina', '904657288', 'marcysilupu10@gmail.com', 14),
(212, 'FERNANDEZ MOGOLLON, Ashley Dahiana', '2510000000000', '2013-02-20', 'MOGOLLON MEDINA, Yajaira', '918600682', 'medina777y@icloud.com', 14),
(213, 'FIGUEROA VALVERDE, Ariana Vania', '77834136', '2012-09-18', 'VALVERDE MILLA, Vanessa Martha', '935151520', 'figuerarianavania@gmail.com', 14),
(214, 'MARCA REBAZA, Adriana Micaela', '77720458', '2012-05-29', 'REBAZA GASTAÑADUI, Santos Amalia', '963461183', 'rebazaamelia@gmail.com', 14),
(215, 'MEREJILDO ZAFRA, Ivanna Fernanda', '78057583', '2013-03-25', 'ZAFRA GARAY, Teolita Reyes', '985754981', 'vzafra99@gmail.com', 14),
(216, 'NOEL CALDERON, Joseph Jenko', '77890056', '2012-11-25', 'CALDERON BALDEON, Jackelin', '957138589', 'jaquelinecalderonbaldeon@gmail.com', 14),
(217, 'NORIEGA LAO, Thiago Alberto', '77872622', '2012-11-05', 'LAO GUERRA, Lesly Melisa', '965750898', 'melisalesly@gmail.com', 14),
(218, 'QUIPO GALARZA, Kiara Merary', '77941342', '2012-12-25', 'GALARZA VILLENA, Gaby Deyss', '982695033', 'deyssgv2018@gmail.com', 14),
(219, 'QUIROZ CANTA, Génesis Yoaly', '63571308', '2012-05-10', 'CANTA HERRERA, Angelica Sonia', '935880034', '', 14),
(220, 'RIBBECK LIMA, Fabrizzio Haron', '77866284', '2012-09-19', 'RIBBECK HURTADO, Abelardo Ernesto', '950165057', 'jribbecklima@gmail.com', 14),
(221, 'RODRIGUEZ ZAPATA, Andrea Maribel', '77628363', '2012-04-23', 'ZAPATA CARREÑO, Mirian Maribel', '923154914', 'clever.rodriguez14@gmail.com', 14),
(222, 'RUIZ COTRINA, Carlos Fabiano', '77941262', '2012-12-29', 'RUIZ JARA, Deciderio Prado', '945530543', 'percilacotrina@hotmail.com', 14),
(223, 'RUIZ GOMEZ, Aracelly Briana', '77786608', '2012-09-04', 'GOMEZ CABALLERO, Yohana Delia', '921895240', 'yohaniitah_03_18@hotmail.com', 14),
(224, 'SANCHEZ MORI, Christopher Matthew', '77791886', '2012-08-26', 'SANCHEZ CHAVEZ, Omar Abdul', '989108160', 'omar.sanchez114@gmail.com', 14),
(225, 'SANCHEZ VERA, Thiago Valentín', '78040526', '2013-02-14', 'VERA CAMARINA, Alida Juana', '997539003', '', 14),
(226, 'SOTO ZUMAETA, Valeska', '77634505', '2012-05-01', 'SOTO HIDALGO, Boris', '', '', 14),
(227, 'TIPULA CASTILLO, Cristopher Gabriel', '77857449', '2012-08-29', 'CASTILLO PIZARRO, Jany Dubi', '977656282', 'jenycastillopizarro@gmail.com', 14),
(228, 'TORRES CHAMPA, Silvana Valeria', '77738855', '2012-06-10', 'CHAMPA AVALOS, Monica', '956945422', 'torresrenzo856@gmail.com', 14),
(229, 'VILCHEZ FELIPE, Jhael Mathias', '77317661', '2011-09-13', 'FELIPE MEZA, Kelly Katherine', '961052299', 'kellyjatherine05@gmail.com', 14),
(230, 'VILCHEZ SUAREZ, Luana Aomeh', '77859980', '2012-10-09', 'REYES VALLE, Hiliana Leonor', '982013948', 'suarezpema@gmail.com', 14),
(231, 'ZEGARRA HIDALGO, Richard Adriano', '77935963', '2012-12-22', 'HIDALGO ESPINOZA, Elvia Yvonne', '970066771', 'yhidalgo16@yahoo.es', 14),
(232, 'ALARCON LUGO, Aldo Miguel', '77252207', '2011-08-09', 'LUGO OSTOS, Evila Isabel', '980787118', 'evilalugo1526@gmail.com', 15),
(233, 'ALIAGA MACHUCA, Ariana Valentina', '62798578', '2011-04-14', 'MACHUCA GALLARDO, Erica', '970534270', 'saulo2811@hotmail.com', 15),
(234, 'ANGULO PEÑA, Briana Dayana del Pilar', '77561661', '2011-11-25', 'PEÑA ZUÑIGA, Dayana del Carmen', '977631959', 'aylinangulo59@gmail.com', 15),
(235, 'ARENAS BERNAL, Manuel Gael', '63696069', '2012-01-20', 'BERNAL ALVARADO, Magaly Karina', '964646073', 'Karin00072@hotmail.com', 15),
(236, 'BECERRA MONTALVO, Olenka Rebeca', '77460609', '2011-12-08', 'MONTALVO YNFANTES, Katty Amalia', '921695296', 'kamy_v80@hotmail.com', 15),
(237, 'CAMACHO FERNANDEZ, Rohan Luis Enrique', '77592131', '2012-03-01', 'FERNANDEZ MANZANEDO, Elizabeth Mercedes', '923302533', 'luisaliravi@gmail.com', 15),
(238, 'CARRERA RETUERTO, Jhosep Steven Valentino', '77627862', '2011-11-29', 'RETUERTO RAMIREZ, Carla Yanet', '918594192', 'carlyretuerto@gmailcom', 15),
(239, 'DIONICIO ALONZO, David Alejandro', '77248361', '2011-06-14', 'ALONZO CANCHANYA, Sonia', '983757941', 'edwindionicio11@gmail.com', 15),
(240, 'GUERRERO BERNAL, Angelo Fabian', '74795682', '2010-09-30', 'BERNAL ANAYA, Edith Justina', '928616514', 'angelobernalguerrero2010@gmail.com', 15),
(241, 'IDRUGO OSORIO, Lionel Dario', '77266940', '2011-08-02', 'OSORIO ROJAS, Maribel', '980039684', '', 15),
(242, 'JUIPA JERONIMO, Zhamir Angel', '77609905', '2012-03-21', 'JERONIMO OLIVAS, Teylith Andrea', '922470387', 'ga_2392@outlook.com', 15),
(243, 'MAYTA VALDIVIA, Angel Akiro', '77004811', '2011-05-21', 'VALDIVIA ROJAS, Judith', '990873913', 'vrjudith78@gmail.com', 15),
(244, 'MONRROY STARRIPA, Jherico Facundo', '76896694', '2011-04-20', 'STARRIPA NIETO, Edith Mercedes', '993592990', 'edith_majo07@hotmail.com', 15),
(245, 'MORALES ARAUCANO, Paola Carmen', '77583061', '2012-01-30', 'MORALES CACHA, Hilario', '961787613', 'hilario.riconcitohuaracino@gmail.com', 15),
(246, 'NAVARRO TELLO, Luciana Janeth', '77459125', '2011-12-17', 'NAVARRO VALDIVIEZO, Jhon Guillermo', '997083097', 'johnguillermo90@gmail.com', 15),
(247, 'QUISPE CHUNQUE, Ryan Smith', '77582304', '2012-02-15', 'CHUNQUE DIAZ, Maria Sonia', '923439010', '', 15),
(248, 'RAMOS GARCIA, Mauricio Anderson', '77319043', '2011-09-28', 'GARCIA CARBONEL, Yrene', '938212889', 'yrenegarcia16@gmail.com', 15),
(249, 'SALAS ALMONACID, Nicoll Alessandra', '77260438', '2011-07-27', 'SALAS PINTO, Cesar Hermogenes', '978705932', 'dalmonacid2011@gmail.com', 15),
(250, 'SERNA ASENCIO, Kevin Lucas', '77610026', '2012-03-08', 'ASENCIO SOSA, Hermelinda Margot', '925501460', 'margotasencio1978@gmail.com', 15),
(251, 'SIFUENTES CADILLO, Luiz Sebastian', '77449484', '2011-11-30', 'CADILLO LEIVA, Maruja Delza', '987264581', 'maruja.cadillo8@gmail.com', 15),
(252, 'TARAZONA MATAMORROS, Cristopher Snayder', '79023504', '2011-05-06', 'TARAZONA MATAMORROS, Leiter', '940579762', 'leitertarazonam@gmail.com', 15),
(253, 'VARGAS ESPINOZA, Fernando Mijail', '77264056', '2011-08-28', 'ESPINOZA ANGULO DE VARGAS, Melissa', '951341380', 'melissa.ca_3981@hotmail.com', 15),
(254, 'ARENAS BERNAL, Gianna Sumacc', '63271334', '2011-02-17', 'BERNAL ALVARADO, Magaly Karina', '964646073', 'Karin00072@hotmail.com', 16),
(255, 'CAMPOS MENDOZA, Franco Edu', '74335065', '2010-07-07', 'CAMPOS GONZALES, Daniel', '94539404', 'camposdaniel484@gmail.com', 16),
(256, 'CASTAÑEDA ENRIQUEZ, Leonel Gerard', '76716720', '2011-03-15', 'ENRIQUEZ CASTILLO, Estela Dionisia', '943766979', 'esdienka0608@hotmail.com', 16),
(257, 'ESPIRITU CRUZ, Rey Christopher', '77611560', '2010-10-05', 'ESPIRITU VARGAS, Gregorio Samuel', '982428725', 'fotoestudioluzuriaga@gmail.com', 16),
(258, 'FRANCO ACUÑA, Justin Ian', '76118956', '2011-02-20', 'ACUÑA CUADROS, Melgam Ross', '912981743', 'klnestudionails140824@gmail.com', 16),
(259, 'GRILLO CARMEN, Gabriel Abdiel', '75630945', '2010-11-25', 'CARMEN SOLORZANO, Karla Roxana', '935271378', 'kcarmensolorzano@gmail.com', 16),
(260, 'GUIZADO OSCCO, Angel Oriel', '74668254', '2010-08-20', 'GUIZADO VILLEGAS, Everth', '979641776', 'marcysilupu10@gmail.com', 16),
(261, 'HIDALGO MANIHUARI, Angel Sebastian', '75043209', '2009-04-23', 'MARIN HIDALGO, Antonia', '931294269', '', 16),
(262, 'HUERTO SOTELO, Alexandra', '76115339', '2011-02-10', 'HUAMAN PAYHUA, Imelda', '954518295', '', 16),
(263, 'MAMANI DIAZ, Adela Mercedes Darlin', '76494670', '2011-03-05', 'MAMANI CHOQUE, Rodolfo', '946224515', 'berselladd16@gmail.com', 16),
(264, 'MARQUINA MOGOLLON, Ricardo Dairon', '74335427', '2010-05-19', 'MOGOLLON ESPINOZA, Noemi Angelica', '900129112', 'mogollonnoemi@gmail.com', 16),
(265, 'MENDOZA AROSTE, Daniela Margarita', '62432755', '2010-05-30', 'AROSTE YÑIGO, Nancy Aida', '904722627', '', 16),
(266, 'OSCANOA HUAMALI, Cielo Antonia', '77317239', '2010-09-11', 'OSCANOA TORIBIO, Rolando Arturo', '917314821', 'vero456ger@gmail.com', 16),
(267, 'PEREZ INGA, Rafael Alonso', '74794888', '2010-10-14', 'INGA SANDOVAL, Karen Elizabeth', '967750194', 'kareningasandoval@hotmail.com', 16),
(268, 'QUIROZ PEREZ, Leonel Esthefano', '74511737', '2010-07-19', 'QUIROZ SUAREZ, Maximo', '937317184', 'perez_quispe26@hotmail.com', 16),
(269, 'RAMOS SOTO, Taylor Yoann Jamir', '76118385', '2011-01-30', 'SOTO RUIZ, Estela', '985025872', 'esyasoru430@gmail.com', 16),
(270, 'ROJAS ESPINO, Pierina Alessandra', '74781205', '2010-09-07', 'ROJAS SAMAME, Percy', '957701958', 'projas3170@gmail.com', 16),
(271, 'ROJAS RAMIREZ, Alejandra Xiomara', '62598383', '2010-02-07', 'RAMIREZ MOREY, Gissela', '986204936', 'jorgexguzman@gmail.com', 16),
(272, 'ROQUE ZEGARRA, Jesus Gadiel', '74260956', '2010-05-09', 'ZEGARRA ALZAMORA, Rosario Judith', '906089089', '', 16),
(273, 'RUIZ LEON, Fabricio', '73400865', '2009-05-16', 'LEON JARAMILLO, Esperanza Carmen', '982858403', '982858403esperanza@hotmail.com', 16),
(274, 'VILCAHUAMAN PEREZ, José Abdiel', '75298021', '2010-12-06', 'VILCAHUAMAN ARIAS, Jose Luis', '902891579', 'mariajesusperezcabanillas126@gmail.com', 16),
(275, 'APUMAYTA SIFUENTES, Mariana Fernanda', '77392439', '2009-04-18', 'SIFUENTES OSORIO, Yanet', '912586419', 'yanetsifuentes36@gmail.com', 17),
(276, 'ARONE TOMATEO, Julisa Andrea', '61876450', '2009-06-29', 'TOMATEO VELASQUE, Cecilia', '936788377', 'arone2307@gmail.com', 17),
(277, 'ASENCIO FERNANDEZ, Kristofer Alfonso', '74114457', '2010-03-14', 'FERNANDEZ RUIZ, Jackeline Fiorella', '981478281', 'jackifernandezruiz@gmail.com', 17),
(278, 'BECERRA MEDINA, Brayan Matias', '73848039', '2009-10-18', 'MEDINA SANCHEZ, Aurora', '913011935', 'dra.auroraloiz@hotmail.com', 17),
(279, 'CENTURION CARRASCO, Rady Mihail', '73848170', '2009-10-28', 'CARRASCO URBINA, Marisol Karin', '964008239', 'marisolcarrascourbina45@gmail.com', 17),
(280, 'CHUQUI RISCO, Jadhir Farith', '73563021', '2009-07-20', 'RISCO MORALES, Beatriz', '987081972', 'beatrizriscomorales@gmail.com', 17),
(281, 'CONZUELO MATIENZO, Max Gabriel', '73727586', '2009-09-15', 'CONZUELO ROSARIO, Livio Modesto', '990737626', 'lconzuelo@hotmail.com', 17),
(282, 'LOPEZ HUAMANI, Gabriela Deyanira', '72596174', '2008-11-02', 'HUAMANI MEZA, Joysi Caroli', '906554410', 'nickitalo.30201@gmail.com', 17),
(283, 'PALOMINO JOO, Cecilia Janeth', '73839010', '2009-10-22', 'JOO AQUINO, Cecilia', '947220732', 'cecijoo1986@gmail.com', 17),
(284, 'PEIXOTO NAJARRO, Shanaya Thais', '73719809', '2009-09-04', 'NAJARRO GARCES, Lea', '947197059', 'lenaga28@gmail.com', 17),
(285, 'PENADILLO DEXTRE, Anyelo Rodrigo', '61911975', '2009-11-05', 'DEXTRE NUÑEZ, Daysi Evelyn', '943198542', 'dextredaysi@gmail.com', 17),
(286, 'RIOS GARCIA, Gahel Leonardo', '61735669', '2009-07-29', 'GARCIA RENGIFO, Alice Mariana', '', '', 17),
(287, 'SERNA ASENCIO, Brian Miguel', '79125129', '2009-11-30', 'ASENCIO SOSA, Hermelinda Margot', '925501460', 'margotasencio1978@gmail.com', 17),
(288, 'VELASQUE TORBISCO, Tania Zayuri', '61931924', '2009-11-23', 'TORBISCO RIOS, Ricardina', '936070637', 'velasqueallison@gmail.com', 17),
(289, 'ALIAGA MACHUCA, Saul Leonel', '61516431', '2008-11-22', 'MACHUCA GALLARDO, Erica Manyori', '970534270', 'saulo2811@hotmail.com', 18),
(290, 'CAMPOS MENDOZA, Alvaro Daniel', '61493905', '2008-09-17', 'CAMPOS GONZALES, Daniel', '94539404', 'camposdaniel484@gmail.com', 18),
(291, 'HURTADO YNFANTE, Cielo Yamilet', '77721414', '2008-07-11', 'YNFANTE GOMEZ, Mirian Pilar', '984627294', 'mirianinfante69@gmail.com', 18),
(292, 'MALPARTIDA SARREA, Maria Belen', '70966409', '2007-11-17', 'SARREA BECERRA, Lizeth Vanessa', '938129287', 'sarreabecerravanessa@gmail.com', 18),
(293, 'MALPARTIDA SARREA, Maria Paz', '70966419', '2007-11-17', 'SARREA BECERRA, Lizeth Vanessa', '938129287', 'sarreabecerravanessa@gmail.com', 18),
(294, 'MARQUINA MOGOLLON, Mariana Noemi', '72340694', '2008-09-06', 'MOGOLLON ESPINOZA, Noemi Angelica', '900129112', 'mogollonnoemi@gmail.com', 18),
(295, 'MEDINA URRIOLA, Nicole Valentina', '69875412', '2008-08-21', 'URRIOLA WELESKE, Mary Leysy', '901687086', 'urriolaweleskem@gmail.com', 18),
(296, 'RABANAL VALENCIA, Jerau Santiago', '61526999', '2008-12-17', 'VALENCIA RIVERA, Maryory Rosa', '924932955', '', 18),
(297, 'RAMIREZ MAMANI, Marco Yorshua', '72830183', '2008-03-15', 'RAMIREZ ALCANTARA, Marco', '942913399', 'marco_ra_72@hotmail.com', 18),
(298, 'ROJAS YOVERA, Jose Thiago', '60705335', '2008-09-28', 'ROJAS CELIS, Santos Eladio', '', '', 18),
(299, 'ROSALES RAMOS, Maria Alejandra', '61483050', '2008-10-01', 'ROSALES PEÑA, Rafael Johnny', '992246658', 'julia_rp01@hotmail.com', 18),
(300, 'SGIER ARTEAGA, Hans Henrich', '73296923', '2008-10-30', 'ARTEAGA HERRERA, Gisell Betty', '943190872', 'gizearti@hotmail.com', 18),
(301, 'TERAN EVARISTO, Anyeli Zuleika', '61300473', '2008-05-03', 'EVARISTO RODRIGUEZ, Ena Karina', '914103224', 'enakari04@gmail.com', 18),
(302, 'VELASCO ALVAREZ, Maria Paula', '72836037', '2009-01-14', 'ALVAREZ CAMPOS, Joanie Cecilia', '914839836', 'jalvarez29191@gmail.com', 18),
(303, 'VILLAVERDE ARROYO, Luciana Esthefany', '73278779', '2009-03-18', 'ARROYO SANCHEZ, Giovana Cristina', '947238081', 'criss_2809@hotmail.com', 18);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grados`
--

CREATE TABLE `grados` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grados`
--

INSERT INTO `grados` (`id`, `nombre`) VALUES
(1, 'ET'),
(2, 'I3'),
(3, 'I4'),
(4, 'I5'),
(5, 'P1'),
(6, 'P2'),
(7, 'P3A'),
(8, 'P3B'),
(9, 'P4A'),
(10, 'P4B'),
(11, 'P5A'),
(12, 'P5B'),
(13, 'P6'),
(14, 'S1'),
(15, 'S2'),
(16, 'S3'),
(17, 'S4'),
(18, 'S5'),
(19, 'Otros');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matriculas`
--

CREATE TABLE `matriculas` (
  `id` int(11) NOT NULL,
  `alumno_id` int(11) NOT NULL,
  `grado_id` int(11) NOT NULL,
  `fecha_matricula` date NOT NULL,
  `anio_year` int(4) NOT NULL,
  `estado` enum('activo','retirado','fallecido') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modificar_montos_defecto`
--

CREATE TABLE `modificar_montos_defecto` (
  `id` int(11) NOT NULL,
  `alumno_id` int(11) DEFAULT NULL,
  `grado` varchar(10) DEFAULT NULL,
  `tipo` enum('matricula','pension') NOT NULL,
  `anio` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` int(11) NOT NULL,
  `alumno_id` int(11) NOT NULL,
  `fecha_pago` date DEFAULT NULL,
  `mes_pago` varchar(20) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','pagado','vencido') DEFAULT 'pendiente',
  `observaciones` text DEFAULT NULL,
  `metodo_pago` varchar(50) NOT NULL,
  `detalle` varchar(50) DEFAULT NULL,
  `apoderado` varchar(255) DEFAULT NULL,
  `codigo_operacion` varchar(100) DEFAULT NULL,
  `anio_escolar` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('director','administrador','secretaria') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`) VALUES
(1, 'Juan Carlos Mendoza Ramos', 'juancarlosmendozaramos@gmail.com', '1ee0f1ecb9c0aa31c3af3d26edc6a7ba', 'director');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `modificar_montos_defecto`
--
ALTER TABLE `modificar_montos_defecto`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `alumno_id` (`alumno_id`,`grado`,`tipo`,`anio`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `modificar_montos_defecto`
--
ALTER TABLE `modificar_montos_defecto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
