-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 14-10-2024 a las 17:20:38
-- Versión del servidor: 10.6.18-MariaDB-cll-lve
-- Versión de PHP: 8.1.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `mklogisticsac_facturador`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cji_tipo_afectacion`
--

CREATE TABLE `cji_tipo_afectacion` (
  `AFECT_Codigo` int(11) NOT NULL,
  `AFECT_Numero` varchar(3) NOT NULL,
  `AFECT_Descripcion` varchar(65) NOT NULL,
  `AFECT_DescripcionSmall` varchar(70) DEFAULT NULL,
  `AFECT_FechaRegistro` timestamp NOT NULL DEFAULT current_timestamp(),
  `AFECT_FechaModificacion` datetime NOT NULL,
  `AFECT_FlagEstado` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `cji_tipo_afectacion`
--

INSERT INTO `cji_tipo_afectacion` (`AFECT_Codigo`, `AFECT_Numero`, `AFECT_Descripcion`, `AFECT_DescripcionSmall`, `AFECT_FechaRegistro`, `AFECT_FechaModificacion`, `AFECT_FlagEstado`) VALUES
(1, '1', 'Gravado - Operación Onerosa [10]', '[GRAVADO] - Operación Onerosa', '2019-10-15 19:42:11', '0000-00-00 00:00:00', '1'),
(2, '2', '[GRATUITO] Gravado – Retiro por premio [11]', '[GRATUITO] - Retiro por premio', '2019-10-15 19:46:05', '2019-10-15 14:46:05', '1'),
(3, '3', '[GRATUITO] Gravado – Retiro por donación [12]', '[GRATUITO] - Retiro por donación', '2019-10-15 19:46:22', '2019-10-15 14:46:22', '1'),
(4, '4', '[GRATUITO] Gravado – Retiro [13]', '[GRATUITO] - Retiro', '2019-10-15 19:46:36', '2019-10-15 14:46:36', '1'),
(5, '5', '[GRATUITO] Gravado – Retiro por publicidad [14]', '[GRATUITO] - Retiro por publicidad', '2019-10-15 19:46:47', '2019-10-15 14:46:47', '1'),
(6, '6', '[GRATUITO] Gravado – Bonificaciones [15]', '[GRATUITO] - Bonificaciones', '2019-10-15 19:46:59', '2019-10-15 14:46:59', '1'),
(7, '7', '[GRATUITO] Gravado – Retiro por entrega a trabajadores [16]', '[GRATUITO] - Retiro por entrega a trabajadores', '2019-10-15 19:47:08', '2019-10-15 14:47:08', '1'),
(8, '8', 'Exonerado - Operación Onerosa [20]', '[EXONERADO]', '2019-10-15 19:47:21', '2019-10-15 14:47:21', '1'),
(9, '9', 'Inafecto - Operación Onerosa [30]', '[INAFECTO]', '2019-10-15 19:47:36', '2019-10-15 14:47:36', '1'),
(10, '10', '[GRATUITO] Inafecto – Retiro por Bonificación [31]', '[GRATUITO] Inafecto – Retiro por Bonificación', '2019-10-15 19:48:11', '2019-10-15 14:48:11', '1'),
(11, '11', '[GRATUITO] Inafecto – Retiro [32]', '[GRATUITO] Inafecto – Retiro', '2019-10-15 19:48:22', '2019-10-15 14:48:22', '1'),
(12, '12', '[GRATUITO] Inafecto – Retiro por Muestras Médicas [33]', '[GRATUITO] Inafecto – Retiro por Muestras Médicas', '2019-10-15 19:48:33', '2019-10-15 14:48:33', '1'),
(13, '13', '[GRATUITO] Inafecto - Retiro por Convenio Colectivo [34]', '[GRATUITO] Inafecto - Retiro por Convenio Colectivo', '2019-10-15 19:48:46', '2019-10-15 14:48:46', '1'),
(14, '14', '[GRATUITO] Inafecto – Retiro por premio [35]', '[GRATUITO] Inafecto – Retiro por premio', '2019-10-15 19:48:57', '2019-10-15 14:48:57', '1'),
(15, '15', '[GRATUITO] Inafecto - Retiro por publicidad [36]', '[GRATUITO] Inafecto - Retiro por publicidad', '2019-10-15 19:49:08', '2019-10-15 14:49:08', '1'),
(16, '16', 'Exportación [40]', '[EXPORTACIÓN]', '2019-10-15 19:49:18', '2019-10-15 14:49:18', '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cji_unidadmedida`
--

CREATE TABLE `cji_unidadmedida` (
  `UNDMED_Codigo` int(11) NOT NULL,
  `UNDMED_Descripcion` varchar(250) DEFAULT NULL,
  `UNDMED_Simbolo` varchar(30) DEFAULT NULL,
  `UNDMED_FechaRegistro` timestamp NOT NULL DEFAULT current_timestamp(),
  `UNDMED_FechaModificacion` datetime DEFAULT NULL,
  `UNDMED_FlagEstado` char(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `cji_unidadmedida`
--

INSERT INTO `cji_unidadmedida` (`UNDMED_Codigo`, `UNDMED_Descripcion`, `UNDMED_Simbolo`, `UNDMED_FechaRegistro`, `UNDMED_FechaModificacion`, `UNDMED_FlagEstado`) VALUES
(1, 'UNIDAD', 'NIU', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1'),
(2, 'PAQUETE', 'PK', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1'),
(3, 'KILOGRAMO', 'KGM', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1'),
(4, 'ROLLO', 'RO', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1'),
(5, 'KIT', 'KT', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1'),
(6, 'SACO', 'SA', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1'),
(7, 'CAJA', 'BX', '2017-01-30 04:46:13', NULL, '1'),
(8, 'PAR', 'PR', '2017-03-01 02:32:32', NULL, '1'),
(9, 'METRO CUBICO', 'MTQ', '2017-04-18 06:35:44', NULL, '1'),
(10, 'BOLSA', 'BG', '2017-04-29 07:20:29', NULL, '1'),
(11, 'METRO', 'MTR', '2017-04-29 07:20:51', NULL, '1'),
(12, 'GALON INGLES', 'GLI', '2017-04-29 07:21:02', NULL, '1'),
(13, 'LITRO', 'LTR', '2017-06-23 11:38:48', NULL, '1'),
(14, 'PULGADA', 'INH', '2017-06-23 11:40:11', NULL, '1'),
(15, 'PIES', 'FOT', '2017-06-23 11:43:51', NULL, '1'),
(16, 'PIE CUADRADO', 'FTK', '2017-06-23 11:47:49', NULL, '1'),
(17, 'PIE CUBICO', 'FTQ', '2017-06-23 11:48:16', NULL, '1'),
(18, 'LIBRA', 'LBR', '2017-06-23 11:48:36', NULL, '1'),
(19, 'GRAMO', 'GRM', '2017-06-23 11:50:00', NULL, '1'),
(20, 'PIEZA', 'C62', '2017-07-08 19:42:22', NULL, '1'),
(21, 'CIENTO DE UNIDADES', 'CEN', '2017-07-10 23:03:49', NULL, '1'),
(22, 'JUEGO', 'SET', '2017-07-10 23:18:38', NULL, '1'),
(23, 'LATAS', 'CA', '2017-07-10 23:20:12', NULL, '1'),
(24, 'MILLARES', 'MLL', '2017-07-10 23:22:45', NULL, '1'),
(25, 'METRO CUADRADO', 'MTK', '2017-07-10 23:23:37', NULL, '1'),
(26, 'TONELADAS', 'TNE', '2017-07-10 23:27:18', NULL, '1'),
(27, 'VARILLA', 'Var', '2017-07-10 23:29:18', NULL, '0'),
(28, 'GALON', 'WG', '2019-04-08 05:00:00', NULL, '1'),
(29, 'BOTELLA', 'BO', '2019-04-08 05:00:00', NULL, '1'),
(30, 'DOCENA', 'DZN', '2019-04-16 05:00:00', NULL, '1'),
(31, 'MIL', 'MIL', '2019-04-16 05:00:00', NULL, '1'),
(32, 'SERVICIOS (OTROS)', 'ZZ', '2020-03-05 16:02:21', NULL, '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `nombre_cliente` varchar(255) NOT NULL,
  `num_cliente` char(30) NOT NULL,
  `telefono_cliente` char(30) NOT NULL,
  `email_cliente` varchar(64) NOT NULL,
  `direccion_cliente` varchar(255) NOT NULL,
  `tipo_doc` tinyint(4) NOT NULL,
  `status_cliente` tinyint(4) NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `nombre_cliente`, `num_cliente`, `telefono_cliente`, `email_cliente`, `direccion_cliente`, `tipo_doc`, `status_cliente`, `date_added`) VALUES
(1, 'LAZARO CARHUAVILCA, MARCIAL BASILIDES', '103290312', '986532215', 'p_nuevas@hotmail.com', 'CAL.50 MZA. A11 LOTE. 4 ASOC LAS VEGAS LIMA - LIMA - SANTA ANITA', 0, 1, '2018-02-23 00:40:42'),
(2, 'Juan Ramirez', '10328932832', '981005695', 'jramirez@hotmail.com', 'ATE', 0, 1, '2020-06-05 18:56:15'),
(3, 'Sra Lucha', '', '3518946', 'luchorondon@hotmail.com', 'UrbanizaciÃ³n Tilda Mz L lote 5 Ate (Primer Piso, Preguntar por la Sra Lucha) Viven Inquilinos en los pisos Superiores', 0, 1, '2020-06-05 19:11:23'),
(4, 'CARLOS RONDON GRADOS', '', '989838633', 'ccarlosrondon@gmail.com', 'Calle AvicaciÃ³n 234 Lima (Departamento 104)', 0, 1, '2020-06-05 19:54:38'),
(5, 'Patty Trujillo', '', '993649438', 'paty@hotmail.com', 'UrbanizaciÃ³n Tilda Mz L lote 5 Ate (Segundo piso) Preguntar por Raul Rondon', 0, 1, '2020-06-05 14:36:49'),
(7, 'Gabriel Belleza', '72748697', '979742332', 'josegabriel@gmail.com', 'Av.Las Palmeras', 1, 1, '2024-10-10 16:18:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `currencies`
--

CREATE TABLE `currencies` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `symbol` varchar(255) NOT NULL,
  `precision` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `thousand_separator` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `decimal_separator` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `currencies`
--

INSERT INTO `currencies` (`id`, `name`, `symbol`, `precision`, `thousand_separator`, `decimal_separator`, `code`) VALUES
(1, 'US Dollar', '$', '2', ',', '.', 'USD'),
(2, 'Soles', 'S/', '2', ',', '.', 'PER'),
(3, 'Euro', 'â‚¬', '2', '.', ',', 'EUR'),
(4, 'South African Rand', 'R', '2', '.', ',', 'ZAR'),
(5, 'Danish Krone', 'kr ', '2', '.', ',', 'DKK'),
(6, 'Israeli Shekel', 'NIS ', '2', ',', '.', 'ILS'),
(7, 'Swedish Krona', 'kr ', '2', '.', ',', 'SEK'),
(8, 'Kenyan Shilling', 'KSh ', '2', ',', '.', 'KES'),
(9, 'Canadian Dollar', 'C$', '2', ',', '.', 'CAD'),
(10, 'Philippine Peso', 'P ', '2', ',', '.', 'PHP'),
(11, 'Indian Rupee', 'Rs. ', '2', ',', '.', 'INR'),
(12, 'Australian Dollar', '$', '2', ',', '.', 'AUD'),
(13, 'Singapore Dollar', 'SGD ', '2', ',', '.', 'SGD'),
(14, 'Norske Kroner', 'kr ', '2', '.', ',', 'NOK'),
(15, 'New Zealand Dollar', '$', '2', ',', '.', 'NZD'),
(16, 'Vietnamese Dong', 'VND ', '0', '.', ',', 'VND'),
(17, 'Swiss Franc', 'CHF ', '2', '\'', '.', 'CHF'),
(18, 'Quetzal Guatemalteco', 'Q', '2', ',', '.', 'GTQ'),
(19, 'Malaysian Ringgit', 'RM', '2', ',', '.', 'MYR'),
(20, 'Real Brasile&ntilde;o', 'R$', '2', '.', ',', 'BRL'),
(21, 'Thai Baht', 'THB ', '2', ',', '.', 'THB'),
(22, 'Nigerian Naira', 'NGN ', '2', ',', '.', 'NGN'),
(23, 'Peso Argentino', '$', '2', '.', ',', 'ARS'),
(24, 'Bangladeshi Taka', 'Tk', '2', ',', '.', 'BDT'),
(25, 'United Arab Emirates Dirham', 'DH ', '2', ',', '.', 'AED'),
(26, 'Hong Kong Dollar', '$', '2', ',', '.', 'HKD'),
(27, 'Indonesian Rupiah', 'Rp', '2', ',', '.', 'IDR'),
(28, 'Peso Mexicano', '$', '2', ',', '.', 'MXN'),
(29, 'Egyptian Pound', '&pound;', '2', ',', '.', 'EGP'),
(30, 'Peso Colombiano', '$', '2', '.', ',', 'COP'),
(31, 'West African Franc', 'CFA ', '2', ',', '.', 'XOF'),
(32, 'Chinese Renminbi', 'RMB ', '2', ',', '.', 'CNY');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_factura`
--

CREATE TABLE `detalle_factura` (
  `id_detalle` int(11) NOT NULL,
  `numero_factura` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_venta` double NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `detalle_factura`
--

INSERT INTO `detalle_factura` (`id_detalle`, `numero_factura`, `id_producto`, `cantidad`, `precio_venta`) VALUES
(31, 1, 4, 1, 20),
(32, 2, 2, 1, 20),
(35, 5, 1, 1, 5),
(34, 4, 4, 1, 20),
(33, 3, 2, 1, 20),
(39, 6, 1, 1, 5),
(40, 6, 2, 1, 20),
(41, 7, 2, 1, 20),
(42, 7, 1, 1, 5),
(43, 8, 1, 1, 5),
(44, 9, 1, 1, 5),
(45, 10, 1, 1, 5),
(47, 12, 1, 1, 5),
(48, 13, 1, 1, 5),
(49, 14, 2, 1, 20),
(50, 14, 1, 1, 5),
(51, 15, 1, 1, 5),
(52, 16, 1, 1, 5),
(53, 17, 1, 1, 5),
(54, 18, 1, 1, 5),
(55, 18, 1, 1, 5),
(56, 19, 1, 1, 5),
(57, 20, 2, 1, 20),
(58, 20, 1, 1, 5),
(59, 21, 1, 1, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `id_factura` int(11) NOT NULL,
  `numero_factura` int(11) NOT NULL,
  `fecha_factura` datetime NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_vendedor` int(11) NOT NULL,
  `condiciones` varchar(30) NOT NULL,
  `total_venta` varchar(20) NOT NULL,
  `estado_factura` tinyint(1) NOT NULL,
  `igv` int(11) NOT NULL,
  `tipo_doc` int(11) NOT NULL DEFAULT 0,
  `cod_doc` varchar(10) NOT NULL DEFAULT 'null'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `facturas`
--

INSERT INTO `facturas` (`id_factura`, `numero_factura`, `fecha_factura`, `id_cliente`, `id_vendedor`, `condiciones`, `total_venta`, `estado_factura`, `igv`, `tipo_doc`, `cod_doc`) VALUES
(20, 1, '2021-04-27 15:44:39', 1, 2, '1', '20', 2, 1, 3, '1'),
(21, 2, '2021-04-28 15:44:51', 1, 2, '1', '20', 2, 1, 1, '1'),
(22, 3, '2021-04-29 15:50:28', 1, 2, '1', '20', 0, 1, 3, '2'),
(23, 4, '2021-04-29 16:00:38', 1, 2, '1', '20', 2, 1, 3, '3'),
(24, 5, '2021-04-29 16:00:59', 1, 2, '1', '5', 0, 1, 1, '2'),
(26, 6, '2024-09-25 22:53:55', 2, 1, '1', '25', 1, 1, 1, '3'),
(27, 7, '2024-09-27 23:11:53', 2, 1, '1', '25', 1, 1, 1, '4'),
(28, 8, '2024-09-27 23:12:28', 2, 1, '1', '5', 1, 1, 1, '5'),
(29, 9, '2024-09-27 23:21:08', 2, 1, '1', '5', 1, 1, 2, '1'),
(30, 10, '2024-09-27 23:21:47', 2, 1, '1', '5', 1, 1, 1, '6'),
(31, 11, '2024-09-27 23:22:26', 2, 1, '1', '0', 1, 1, 1, '7'),
(32, 12, '2024-09-27 23:52:18', 2, 1, '1', '5', 1, 1, 2, '2'),
(33, 13, '2024-09-27 23:56:56', 2, 1, '1', '5', 1, 1, 2, '3'),
(34, 14, '2024-09-28 00:00:17', 2, 1, '1', '25', 1, 1, 1, '8'),
(35, 15, '2024-09-28 00:00:39', 2, 1, '1', '5', 1, 1, 1, '9'),
(36, 16, '2024-09-28 00:02:14', 2, 1, '1', '5', 1, 1, 1, '10'),
(37, 17, '2024-09-28 00:03:04', 2, 1, '1', '5', 0, 1, 1, '11'),
(38, 18, '2024-09-28 00:11:21', 2, 1, '1', '10', 2, 1, 2, '4'),
(39, 19, '2024-10-01 18:15:05', 2, 1, '1', '5', 2, 1, 2, '5'),
(40, 20, '2024-10-01 18:57:31', 2, 1, '1', '25', 2, 1, 1, '12'),
(41, 21, '2024-10-02 13:43:50', 2, 1, '1', '5', 1, 1, 1, '13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perfil`
--

CREATE TABLE `perfil` (
  `id_perfil` int(11) NOT NULL,
  `nombre_empresa` varchar(150) NOT NULL,
  `ruc_empresa` varchar(50) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `ciudad` varchar(100) NOT NULL,
  `codigo_postal` varchar(100) NOT NULL,
  `estado` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `email` varchar(64) NOT NULL,
  `impuesto` int(2) NOT NULL,
  `moneda` varchar(6) NOT NULL,
  `logo_url` varchar(255) NOT NULL,
  `igv_total` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `perfil`
--

INSERT INTO `perfil` (`id_perfil`, `nombre_empresa`, `ruc_empresa`, `direccion`, `ciudad`, `codigo_postal`, `estado`, `telefono`, `email`, `impuesto`, `moneda`, `logo_url`, `igv_total`) VALUES
(1, 'Movil Peru', '10104191768', 'Javier Heraud 345 - ATE', 'Lima', '150103', 'Lima', '991317142', 'luchorondon@hotmail.com', 18, 'S/', 'img/1728591655_movilPeru.png', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `id_producto` int(11) NOT NULL,
  `codigo_producto` char(20) NOT NULL,
  `nombre_producto` char(255) NOT NULL,
  `detalle` char(255) NOT NULL,
  `status_producto` tinyint(4) NOT NULL,
  `date_added` datetime NOT NULL,
  `precio_producto` double NOT NULL,
  `precio_costo` double NOT NULL,
  `igv` int(11) NOT NULL,
  `medida` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id_producto`, `codigo_producto`, `nombre_producto`, `detalle`, `status_producto`, `date_added`, `precio_producto`, `precio_costo`, `igv`, `medida`) VALUES
(1, '001', 'Entrega de Pedido TuBodeguita.pe Express', '', 1, '2018-02-23 00:38:33', 5, 1, 16, 0),
(2, '0001', 'Entrega de Pedido TuBodeguita.pe', '', 1, '2020-06-05 17:10:12', 20, 5, 2, 0),
(4, '0032', 'test', '', 1, '2021-04-28 17:04:55', 20, 5, 1, 0),
(5, 'prueba1', 'producto prubea', '', 1, '2021-04-29 18:04:08', 30, 10, 1, 0),
(6, 'prueba2', 'producto prueba', '', 1, '2021-04-29 18:04:30', 30, 10, 2, 0),
(7, 'cargaf', 'asdsad', '', 1, '2021-04-29 18:12:35', 22, 22, 1, 2),
(8, '003', 'Prueba 3', '', 1, '2021-04-29 18:33:03', 20, 25.5, 1, 1),
(9, 'Nuevo23', 'Lapiz a2', 'Hola', 1, '2024-10-01 20:01:52', 25, 15, 1, 2),
(10, 'Nuevo232', 'Caja de lapiz', 'Nuevo', 1, '2024-10-01 20:03:56', 20, 13, 1, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tmp`
--

CREATE TABLE `tmp` (
  `id_tmp` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad_tmp` int(11) NOT NULL,
  `precio_tmp` double(8,2) DEFAULT NULL,
  `session_id` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL COMMENT 'auto incrementing user_id of each user, unique index',
  `firstname` varchar(20) NOT NULL,
  `lastname` varchar(20) NOT NULL,
  `user_name` varchar(64) NOT NULL COMMENT 'user''s name, unique',
  `user_password_hash` varchar(255) NOT NULL COMMENT 'user''s password in salted and hashed format',
  `user_email` varchar(64) NOT NULL COMMENT 'user''s email, unique',
  `date_added` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci COMMENT='user data';

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`user_id`, `firstname`, `lastname`, `user_name`, `user_password_hash`, `user_email`, `date_added`) VALUES
(1, 'Israel', 'Rondon', 'admin', '$2y$10$MPVHzZ2ZPOWmtUUGCq3RXu31OTB.jo7M9LZ7PmPQYmgETSNn19ejO', 'irondon@gmail.com', '2016-05-21 15:06:00'),
(2, 'Freddy', 'Rondon Grados', 'freddy', '$2y$10$n1ftHMVo3OwIwSdQXmgoQeXkuXeRzTKs0XQGyTtrpnLtNBkYfPR8i', 'freddy@hotmail.com', '2020-06-05 18:53:44'),
(3, 'Diego', 'Rondon Almeida', 'diego', '$2y$10$qCZAnwB7Ah1eUtXZTWPf2uqxtTaXnr3xtnavvSQ5ym2B1O52quprW', 'diego@hotmail.com', '2020-06-05 18:54:12');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cji_tipo_afectacion`
--
ALTER TABLE `cji_tipo_afectacion`
  ADD PRIMARY KEY (`AFECT_Codigo`);

--
-- Indices de la tabla `cji_unidadmedida`
--
ALTER TABLE `cji_unidadmedida`
  ADD PRIMARY KEY (`UNDMED_Codigo`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `codigo_producto` (`nombre_cliente`);

--
-- Indices de la tabla `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_factura`
--
ALTER TABLE `detalle_factura`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `numero_cotizacion` (`numero_factura`,`id_producto`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id_factura`),
  ADD UNIQUE KEY `numero_cotizacion` (`numero_factura`);

--
-- Indices de la tabla `perfil`
--
ALTER TABLE `perfil`
  ADD PRIMARY KEY (`id_perfil`);

--
-- Indices de la tabla `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id_producto`),
  ADD UNIQUE KEY `codigo_producto` (`codigo_producto`);

--
-- Indices de la tabla `tmp`
--
ALTER TABLE `tmp`
  ADD PRIMARY KEY (`id_tmp`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_name` (`user_name`),
  ADD UNIQUE KEY `user_email` (`user_email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cji_tipo_afectacion`
--
ALTER TABLE `cji_tipo_afectacion`
  MODIFY `AFECT_Codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `cji_unidadmedida`
--
ALTER TABLE `cji_unidadmedida`
  MODIFY `UNDMED_Codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `detalle_factura`
--
ALTER TABLE `detalle_factura`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id_factura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `perfil`
--
ALTER TABLE `perfil`
  MODIFY `id_perfil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `products`
--
ALTER TABLE `products`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tmp`
--
ALTER TABLE `tmp`
  MODIFY `id_tmp` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'auto incrementing user_id of each user, unique index', AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
