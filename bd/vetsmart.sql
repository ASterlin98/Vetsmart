-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-10-2025 a las 03:15:53
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `vetsmart`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_verificar_permiso` (IN `p_usuario_id` INT, IN `p_permiso_nombre` VARCHAR(100), OUT `p_tiene_permiso` BOOLEAN)   BEGIN
    DECLARE v_count INT DEFAULT 0;
    
    SELECT COUNT(*) INTO v_count
    FROM usuarios u
    INNER JOIN rol_permisos rp ON u.role_id = rp.role_id
    INNER JOIN permisos p ON rp.permiso_id = p.id
    WHERE u.id = p_usuario_id 
    AND p.nombre = p_permiso_nombre
    AND p.activo = 1
    AND u.estado = 1;
    
    SET p_tiene_permiso = IF(v_count > 0, TRUE, FALSE);
END$$

--
-- Funciones
--
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_obtener_permisos_usuario` (`p_usuario_id` INT) RETURNS TEXT CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC READS SQL DATA BEGIN
    DECLARE v_permisos TEXT DEFAULT '';
    
    SELECT GROUP_CONCAT(p.nombre ORDER BY p.nombre SEPARATOR ',') INTO v_permisos
    FROM usuarios u
    INNER JOIN rol_permisos rp ON u.role_id = rp.role_id
    INNER JOIN permisos p ON rp.permiso_id = p.id
    WHERE u.id = p_usuario_id 
    AND p.activo = 1
    AND u.estado = 1;
    
    RETURN IFNULL(v_permisos, '');
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `accion` varchar(255) NOT NULL,
  `entidad` varchar(100) DEFAULT NULL,
  `entidad_id` int(11) DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `creado_en` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bloqueos`
--

CREATE TABLE `bloqueos` (
  `id` int(11) NOT NULL,
  `empleado_id` int(11) DEFAULT NULL,
  `inicio` datetime NOT NULL,
  `fin` datetime NOT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `creado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

CREATE TABLE `citas` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `mascota_id` int(11) NOT NULL,
  `empleado_id` int(11) DEFAULT NULL,
  `servicio_id` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `duracion_min` int(11) NOT NULL DEFAULT 30,
  `estado` enum('pendiente','confirmada','cancelada','completada','no_show') DEFAULT 'pendiente',
  `notas` text DEFAULT NULL,
  `creado_por` int(11) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `citas`
--

INSERT INTO `citas` (`id`, `cliente_id`, `mascota_id`, `empleado_id`, `servicio_id`, `fecha`, `duracion_min`, `estado`, `notas`, `creado_por`, `creado_en`) VALUES
(8, 18, 1, 3, 1, '2025-09-08 09:12:00', 30, 'confirmada', 'asdas', 3, '2025-09-20 00:07:40'),
(9, 18, 1, 3, 1, '2025-09-24 10:10:00', 30, 'confirmada', 'daws', 3, '2025-09-20 00:10:26'),
(35, 18, 1, 3, 1, '2025-09-23 09:00:00', 30, 'confirmada', '', 3, '2025-09-24 01:19:43'),
(37, 18, 1, 3, 1, '2025-09-26 00:00:00', 30, 'confirmada', '', 3, '2025-09-25 00:18:21'),
(40, 18, 1, 25, 1, '2025-09-29 09:00:00', 30, 'confirmada', '', 3, '2025-09-25 00:54:58'),
(41, 20, 2, 25, 2, '2025-09-11 09:00:00', 30, 'confirmada', '', 3, '2025-10-02 00:51:34'),
(42, 20, 2, 3, 2, '2025-10-16 09:00:00', 34, 'confirmada', '', 3, '2025-10-03 23:32:21'),
(43, 28, 6, 25, 2, '2025-10-13 09:00:00', 34, 'confirmada', '', 25, '2025-10-03 23:59:10'),
(44, 20, 2, 25, 1, '2025-10-06 09:00:00', 30, 'confirmada', '', 25, '2025-10-04 01:33:45'),
(45, 18, 1, 25, 1, '2025-10-07 09:00:00', 30, 'confirmada', '', 25, '2025-10-04 01:34:05'),
(46, 28, 6, 25, 1, '2025-10-15 09:00:00', 30, 'confirmada', '', 25, '2025-10-04 01:35:01'),
(47, 20, 3, 3, 1, '2025-10-14 09:00:00', 30, 'confirmada', '', 3, '2025-10-10 01:09:29'),
(48, 28, 6, 3, 2, '2025-10-15 09:00:00', 60, 'confirmada', '', 3, '2025-10-10 02:12:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente_detalles`
--

CREATE TABLE `cliente_detalles` (
  `id` int(11) NOT NULL,
  `idusu` int(11) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cliente_detalles`
--

INSERT INTO `cliente_detalles` (`id`, `idusu`, `telefono`, `direccion`, `ciudad`, `fecha_registro`) VALUES
(3, 9, '3144928505', NULL, NULL, '2025-09-09 02:50:02'),
(8, 18, '654321596', 'casa pin pin', 'Bogota', '2025-09-12 23:25:35'),
(9, 20, '3144928505', 'Cr 7 #6-67', 'Cundinamarca', '2025-10-01 00:19:46'),
(10, 21, '3124285749', 'Pollo rico 32', 'Bogota', '2025-10-01 00:28:22'),
(12, 28, '75896485', 'Cr 2 # 3 - 4', 'Bogota', '2025-10-04 00:22:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `config`
--

CREATE TABLE `config` (
  `id` int(11) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `config`
--

INSERT INTO `config` (`id`, `clave`, `valor`, `actualizado_en`) VALUES
(1, 'site_name', 'VetSmart', '2025-10-11 01:11:45'),
(2, 'contact_email', 'contacto@vetsmart.com', '2025-10-08 23:45:03'),
(3, 'maintenance_mode', '1', '2025-10-09 00:20:50'),
(4, 'records_per_page', '15', '2025-10-08 23:45:03'),
(25, 'company_address', 'Tu Dirección Aquí', '2025-10-11 01:28:35'),
(26, 'company_phone', '123-456-7890', '2025-10-11 01:28:35'),
(27, 'company_logo_url', 'assets/img/logo.png', '2025-10-11 01:28:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consultas`
--

CREATE TABLE `consultas` (
  `id` int(11) NOT NULL,
  `mascota_id` int(11) NOT NULL,
  `empleado_id` int(11) DEFAULT NULL,
  `motivo` text DEFAULT NULL,
  `examen` text DEFAULT NULL,
  `diagnostico` text DEFAULT NULL,
  `tratamiento` text DEFAULT NULL,
  `recomendaciones` text DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `creado_por` int(11) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `consultas`
--

INSERT INTO `consultas` (`id`, `mascota_id`, `empleado_id`, `motivo`, `examen`, `diagnostico`, `tratamiento`, `recomendaciones`, `notas`, `creado_por`, `creado_en`) VALUES
(1, 1, 3, 'diarrea', 'asdasd', 'diarrea cronica', 'diarrea SS', 'asdasd', 'asd', 3, '2025-09-16 22:59:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `emp_det`
--

CREATE TABLE `emp_det` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `especialidad` varchar(100) DEFAULT NULL,
  `salario` decimal(10,2) DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `emp_det`
--

INSERT INTO `emp_det` (`id`, `usuario_id`, `especialidad`, `salario`, `fecha_ingreso`, `activo`) VALUES
(4, 9, 'Veterinario', 3500000.00, '2023-09-01', 1),
(5, 5, 'Peluquero', 1200000.00, '2024-01-15', 1),
(6, 4, 'Recepcionista', 1200000.00, '2024-03-01', 1),
(7, 3, 'Administrador', 2500000.00, '2022-06-01', 1),
(10, 25, 'Operaciones12', 3000000.00, '2025-10-02', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_citas`
--

CREATE TABLE `historial_citas` (
  `id` int(11) NOT NULL,
  `cita_id` int(11) NOT NULL,
  `cambiado_por` int(11) DEFAULT NULL,
  `antes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`antes`)),
  `despues` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`despues`)),
  `razon` varchar(255) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios_semana`
--

CREATE TABLE `horarios_semana` (
  `id` int(11) NOT NULL,
  `empleado_id` int(11) NOT NULL,
  `dia` varchar(15) NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `horarios_semana`
--

INSERT INTO `horarios_semana` (`id`, `empleado_id`, `dia`, `hora_inicio`, `hora_fin`) VALUES
(1, 3, 'Jueves', '08:00:00', '17:00:00'),
(3, 3, 'Sábado', '06:30:00', '14:30:00'),
(5, 3, 'Lunes', '08:00:00', '17:00:00'),
(6, 3, 'Martes', '08:00:00', '17:00:00'),
(7, 25, 'Lunes', '08:00:00', '17:00:00'),
(8, 25, 'Martes', '08:00:00', '17:00:00'),
(9, 3, 'Miércoles', '08:00:00', '17:21:00'),
(10, 25, 'Miércoles', '08:00:00', '17:33:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login_intentos`
--

CREATE TABLE `login_intentos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `intentos` int(11) DEFAULT 1,
  `ultima_fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs_actividad`
--

CREATE TABLE `logs_actividad` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `accion` varchar(255) NOT NULL,
  `entidad` varchar(100) DEFAULT NULL,
  `entidad_id` int(11) DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `ip` varchar(45) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `logs_actividad`
--

INSERT INTO `logs_actividad` (`id`, `usuario_id`, `accion`, `entidad`, `entidad_id`, `meta`, `ip`, `creado_en`) VALUES
(1, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 255, '{\"role_id\": 3, \"permiso_id\": 9}', NULL, '2025-09-12 00:33:21'),
(2, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 256, '{\"role_id\": 3, \"permiso_id\": 8}', NULL, '2025-09-12 00:33:21'),
(3, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 257, '{\"role_id\": 3, \"permiso_id\": 5}', NULL, '2025-09-12 00:33:21'),
(4, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 258, '{\"role_id\": 3, \"permiso_id\": 6}', NULL, '2025-09-12 00:33:21'),
(5, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 259, '{\"role_id\": 3, \"permiso_id\": 4}', NULL, '2025-09-12 00:33:21'),
(6, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 260, '{\"role_id\": 3, \"permiso_id\": 12}', NULL, '2025-09-12 00:33:21'),
(7, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 261, '{\"role_id\": 3, \"permiso_id\": 15}', NULL, '2025-09-12 00:33:21'),
(8, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 262, '{\"role_id\": 3, \"permiso_id\": 13}', NULL, '2025-09-12 00:33:21'),
(9, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 263, '{\"role_id\": 3, \"permiso_id\": 11}', NULL, '2025-09-12 00:33:21'),
(10, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 264, '{\"role_id\": 3, \"permiso_id\": 1}', NULL, '2025-09-12 00:33:21'),
(11, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 265, '{\"role_id\": 3, \"permiso_id\": 76}', NULL, '2025-09-12 00:33:21'),
(12, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 266, '{\"role_id\": 3, \"permiso_id\": 80}', NULL, '2025-09-12 00:33:21'),
(13, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 267, '{\"role_id\": 3, \"permiso_id\": 79}', NULL, '2025-09-12 00:33:21'),
(14, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 268, '{\"role_id\": 3, \"permiso_id\": 75}', NULL, '2025-09-12 00:33:21'),
(15, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 269, '{\"role_id\": 3, \"permiso_id\": 19}', NULL, '2025-09-12 00:33:21'),
(16, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 270, '{\"role_id\": 3, \"permiso_id\": 20}', NULL, '2025-09-12 00:33:21'),
(17, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 271, '{\"role_id\": 3, \"permiso_id\": 18}', NULL, '2025-09-12 00:33:21'),
(18, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 272, '{\"role_id\": 3, \"permiso_id\": 66}', NULL, '2025-09-12 00:33:21'),
(19, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 273, '{\"role_id\": 3, \"permiso_id\": 65}', NULL, '2025-09-12 00:33:21'),
(20, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 274, '{\"role_id\": 3, \"permiso_id\": 32}', NULL, '2025-09-12 00:33:21'),
(21, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 128, '{\"role_id\": 2, \"permiso_id\": 4}', NULL, '2025-09-12 23:35:46'),
(22, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 129, '{\"role_id\": 2, \"permiso_id\": 5}', NULL, '2025-09-12 23:35:46'),
(23, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 130, '{\"role_id\": 2, \"permiso_id\": 6}', NULL, '2025-09-12 23:35:46'),
(24, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 131, '{\"role_id\": 2, \"permiso_id\": 7}', NULL, '2025-09-12 23:35:46'),
(25, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 132, '{\"role_id\": 2, \"permiso_id\": 8}', NULL, '2025-09-12 23:35:46'),
(26, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 133, '{\"role_id\": 2, \"permiso_id\": 9}', NULL, '2025-09-12 23:35:46'),
(27, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 134, '{\"role_id\": 2, \"permiso_id\": 10}', NULL, '2025-09-12 23:35:46'),
(28, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 135, '{\"role_id\": 2, \"permiso_id\": 11}', NULL, '2025-09-12 23:35:46'),
(29, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 136, '{\"role_id\": 2, \"permiso_id\": 12}', NULL, '2025-09-12 23:35:46'),
(30, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 137, '{\"role_id\": 2, \"permiso_id\": 13}', NULL, '2025-09-12 23:35:46'),
(31, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 138, '{\"role_id\": 2, \"permiso_id\": 14}', NULL, '2025-09-12 23:35:46'),
(32, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 139, '{\"role_id\": 2, \"permiso_id\": 15}', NULL, '2025-09-12 23:35:46'),
(33, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 140, '{\"role_id\": 2, \"permiso_id\": 16}', NULL, '2025-09-12 23:35:46'),
(34, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 141, '{\"role_id\": 2, \"permiso_id\": 17}', NULL, '2025-09-12 23:35:46'),
(35, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 142, '{\"role_id\": 2, \"permiso_id\": 52}', NULL, '2025-09-12 23:35:46'),
(36, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 143, '{\"role_id\": 2, \"permiso_id\": 53}', NULL, '2025-09-12 23:35:46'),
(37, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 144, '{\"role_id\": 2, \"permiso_id\": 54}', NULL, '2025-09-12 23:35:46'),
(38, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 145, '{\"role_id\": 2, \"permiso_id\": 55}', NULL, '2025-09-12 23:35:46'),
(39, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 146, '{\"role_id\": 2, \"permiso_id\": 56}', NULL, '2025-09-12 23:35:46'),
(40, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 147, '{\"role_id\": 2, \"permiso_id\": 57}', NULL, '2025-09-12 23:35:46'),
(41, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 148, '{\"role_id\": 2, \"permiso_id\": 58}', NULL, '2025-09-12 23:35:46'),
(42, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 149, '{\"role_id\": 2, \"permiso_id\": 1}', NULL, '2025-09-12 23:35:46'),
(43, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 150, '{\"role_id\": 2, \"permiso_id\": 2}', NULL, '2025-09-12 23:35:46'),
(44, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 151, '{\"role_id\": 2, \"permiso_id\": 3}', NULL, '2025-09-12 23:35:46'),
(45, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 152, '{\"role_id\": 2, \"permiso_id\": 25}', NULL, '2025-09-12 23:35:46'),
(46, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 153, '{\"role_id\": 2, \"permiso_id\": 26}', NULL, '2025-09-12 23:35:46'),
(47, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 154, '{\"role_id\": 2, \"permiso_id\": 27}', NULL, '2025-09-12 23:35:46'),
(48, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 155, '{\"role_id\": 2, \"permiso_id\": 28}', NULL, '2025-09-12 23:35:46'),
(49, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 156, '{\"role_id\": 2, \"permiso_id\": 29}', NULL, '2025-09-12 23:35:46'),
(50, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 157, '{\"role_id\": 2, \"permiso_id\": 30}', NULL, '2025-09-12 23:35:46'),
(51, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 158, '{\"role_id\": 2, \"permiso_id\": 31}', NULL, '2025-09-12 23:35:46'),
(52, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 159, '{\"role_id\": 2, \"permiso_id\": 75}', NULL, '2025-09-12 23:35:46'),
(53, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 160, '{\"role_id\": 2, \"permiso_id\": 76}', NULL, '2025-09-12 23:35:46'),
(54, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 161, '{\"role_id\": 2, \"permiso_id\": 77}', NULL, '2025-09-12 23:35:46'),
(55, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 162, '{\"role_id\": 2, \"permiso_id\": 78}', NULL, '2025-09-12 23:35:46'),
(56, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 163, '{\"role_id\": 2, \"permiso_id\": 79}', NULL, '2025-09-12 23:35:46'),
(57, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 164, '{\"role_id\": 2, \"permiso_id\": 80}', NULL, '2025-09-12 23:35:46'),
(58, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 165, '{\"role_id\": 2, \"permiso_id\": 38}', NULL, '2025-09-12 23:35:46'),
(59, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 166, '{\"role_id\": 2, \"permiso_id\": 39}', NULL, '2025-09-12 23:35:46'),
(60, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 167, '{\"role_id\": 2, \"permiso_id\": 40}', NULL, '2025-09-12 23:35:46'),
(61, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 168, '{\"role_id\": 2, \"permiso_id\": 41}', NULL, '2025-09-12 23:35:46'),
(62, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 169, '{\"role_id\": 2, \"permiso_id\": 42}', NULL, '2025-09-12 23:35:46'),
(63, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 170, '{\"role_id\": 2, \"permiso_id\": 43}', NULL, '2025-09-12 23:35:46'),
(64, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 171, '{\"role_id\": 2, \"permiso_id\": 44}', NULL, '2025-09-12 23:35:46'),
(65, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 172, '{\"role_id\": 2, \"permiso_id\": 70}', NULL, '2025-09-12 23:35:46'),
(66, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 173, '{\"role_id\": 2, \"permiso_id\": 71}', NULL, '2025-09-12 23:35:46'),
(67, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 174, '{\"role_id\": 2, \"permiso_id\": 72}', NULL, '2025-09-12 23:35:46'),
(68, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 175, '{\"role_id\": 2, \"permiso_id\": 73}', NULL, '2025-09-12 23:35:46'),
(69, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 176, '{\"role_id\": 2, \"permiso_id\": 74}', NULL, '2025-09-12 23:35:46'),
(70, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 177, '{\"role_id\": 2, \"permiso_id\": 18}', NULL, '2025-09-12 23:35:46'),
(71, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 178, '{\"role_id\": 2, \"permiso_id\": 19}', NULL, '2025-09-12 23:35:46'),
(72, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 179, '{\"role_id\": 2, \"permiso_id\": 20}', NULL, '2025-09-12 23:35:46'),
(73, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 180, '{\"role_id\": 2, \"permiso_id\": 21}', NULL, '2025-09-12 23:35:46'),
(74, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 181, '{\"role_id\": 2, \"permiso_id\": 22}', NULL, '2025-09-12 23:35:46'),
(75, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 182, '{\"role_id\": 2, \"permiso_id\": 23}', NULL, '2025-09-12 23:35:46'),
(76, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 183, '{\"role_id\": 2, \"permiso_id\": 24}', NULL, '2025-09-12 23:35:46'),
(77, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 184, '{\"role_id\": 2, \"permiso_id\": 65}', NULL, '2025-09-12 23:35:46'),
(78, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 185, '{\"role_id\": 2, \"permiso_id\": 66}', NULL, '2025-09-12 23:35:46'),
(79, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 186, '{\"role_id\": 2, \"permiso_id\": 67}', NULL, '2025-09-12 23:35:46'),
(80, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 187, '{\"role_id\": 2, \"permiso_id\": 68}', NULL, '2025-09-12 23:35:46'),
(81, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 188, '{\"role_id\": 2, \"permiso_id\": 69}', NULL, '2025-09-12 23:35:46'),
(82, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 189, '{\"role_id\": 2, \"permiso_id\": 45}', NULL, '2025-09-12 23:35:46'),
(83, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 190, '{\"role_id\": 2, \"permiso_id\": 46}', NULL, '2025-09-12 23:35:46'),
(84, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 191, '{\"role_id\": 2, \"permiso_id\": 47}', NULL, '2025-09-12 23:35:46'),
(85, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 192, '{\"role_id\": 2, \"permiso_id\": 48}', NULL, '2025-09-12 23:35:46'),
(86, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 193, '{\"role_id\": 2, \"permiso_id\": 49}', NULL, '2025-09-12 23:35:46'),
(87, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 194, '{\"role_id\": 2, \"permiso_id\": 50}', NULL, '2025-09-12 23:35:46'),
(88, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 195, '{\"role_id\": 2, \"permiso_id\": 51}', NULL, '2025-09-12 23:35:46'),
(89, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 196, '{\"role_id\": 2, \"permiso_id\": 32}', NULL, '2025-09-12 23:35:46'),
(90, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 197, '{\"role_id\": 2, \"permiso_id\": 33}', NULL, '2025-09-12 23:35:46'),
(91, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 198, '{\"role_id\": 2, \"permiso_id\": 34}', NULL, '2025-09-12 23:35:46'),
(92, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 199, '{\"role_id\": 2, \"permiso_id\": 35}', NULL, '2025-09-12 23:35:46'),
(93, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 200, '{\"role_id\": 2, \"permiso_id\": 36}', NULL, '2025-09-12 23:35:46'),
(94, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 201, '{\"role_id\": 2, \"permiso_id\": 37}', NULL, '2025-09-12 23:35:46'),
(95, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 338, '{\"role_id\": 2, \"permiso_id\": 11}', NULL, '2025-09-12 23:35:46'),
(96, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 339, '{\"role_id\": 2, \"permiso_id\": 15}', NULL, '2025-09-12 23:35:46'),
(97, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 340, '{\"role_id\": 2, \"permiso_id\": 16}', NULL, '2025-09-12 23:35:46'),
(98, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 341, '{\"role_id\": 2, \"permiso_id\": 17}', NULL, '2025-09-12 23:35:46'),
(99, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 342, '{\"role_id\": 2, \"permiso_id\": 18}', NULL, '2025-09-12 23:35:46'),
(100, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 343, '{\"role_id\": 2, \"permiso_id\": 22}', NULL, '2025-09-12 23:35:46'),
(101, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 344, '{\"role_id\": 2, \"permiso_id\": 23}', NULL, '2025-09-12 23:35:46'),
(102, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 345, '{\"role_id\": 2, \"permiso_id\": 24}', NULL, '2025-09-12 23:35:46'),
(103, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 338, '{\"role_id\": 2, \"permiso_id\": 11}', NULL, '2025-09-12 23:36:58'),
(104, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 339, '{\"role_id\": 2, \"permiso_id\": 15}', NULL, '2025-09-12 23:36:58'),
(105, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 340, '{\"role_id\": 2, \"permiso_id\": 16}', NULL, '2025-09-12 23:36:58'),
(106, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 341, '{\"role_id\": 2, \"permiso_id\": 17}', NULL, '2025-09-12 23:36:58'),
(107, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 342, '{\"role_id\": 2, \"permiso_id\": 18}', NULL, '2025-09-12 23:36:58'),
(108, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 343, '{\"role_id\": 2, \"permiso_id\": 22}', NULL, '2025-09-12 23:36:58'),
(109, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 344, '{\"role_id\": 2, \"permiso_id\": 23}', NULL, '2025-09-12 23:36:58'),
(110, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 345, '{\"role_id\": 2, \"permiso_id\": 24}', NULL, '2025-09-12 23:36:58'),
(111, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 346, '{\"role_id\": 2, \"permiso_id\": 11}', NULL, '2025-09-12 23:36:58'),
(112, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 347, '{\"role_id\": 2, \"permiso_id\": 15}', NULL, '2025-09-12 23:36:58'),
(113, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 348, '{\"role_id\": 2, \"permiso_id\": 16}', NULL, '2025-09-12 23:36:58'),
(114, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 349, '{\"role_id\": 2, \"permiso_id\": 17}', NULL, '2025-09-12 23:36:58'),
(115, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 350, '{\"role_id\": 2, \"permiso_id\": 18}', NULL, '2025-09-12 23:36:58'),
(116, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 351, '{\"role_id\": 2, \"permiso_id\": 22}', NULL, '2025-09-12 23:36:58'),
(117, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 352, '{\"role_id\": 2, \"permiso_id\": 23}', NULL, '2025-09-12 23:36:58'),
(118, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 353, '{\"role_id\": 2, \"permiso_id\": 24}', NULL, '2025-09-12 23:36:58'),
(119, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 354, '{\"role_id\": 3, \"permiso_id\": 4}', NULL, '2025-10-10 00:53:16'),
(120, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 355, '{\"role_id\": 3, \"permiso_id\": 5}', NULL, '2025-10-10 00:53:16'),
(121, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 286, '{\"role_id\": 4, \"permiso_id\": 9}', NULL, '2025-10-10 01:06:07'),
(122, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 287, '{\"role_id\": 4, \"permiso_id\": 6}', NULL, '2025-10-10 01:06:07'),
(123, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 288, '{\"role_id\": 4, \"permiso_id\": 4}', NULL, '2025-10-10 01:06:07'),
(124, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 289, '{\"role_id\": 4, \"permiso_id\": 15}', NULL, '2025-10-10 01:06:07'),
(125, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 290, '{\"role_id\": 4, \"permiso_id\": 16}', NULL, '2025-10-10 01:06:07'),
(126, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 291, '{\"role_id\": 4, \"permiso_id\": 11}', NULL, '2025-10-10 01:06:07'),
(127, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 292, '{\"role_id\": 4, \"permiso_id\": 2}', NULL, '2025-10-10 01:06:07'),
(128, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 293, '{\"role_id\": 4, \"permiso_id\": 1}', NULL, '2025-10-10 01:06:07'),
(129, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 294, '{\"role_id\": 4, \"permiso_id\": 39}', NULL, '2025-10-10 01:06:07'),
(130, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 295, '{\"role_id\": 4, \"permiso_id\": 40}', NULL, '2025-10-10 01:06:07'),
(131, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 296, '{\"role_id\": 4, \"permiso_id\": 44}', NULL, '2025-10-10 01:06:07'),
(132, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 297, '{\"role_id\": 4, \"permiso_id\": 43}', NULL, '2025-10-10 01:06:07'),
(133, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 298, '{\"role_id\": 4, \"permiso_id\": 42}', NULL, '2025-10-10 01:06:07'),
(134, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 299, '{\"role_id\": 4, \"permiso_id\": 38}', NULL, '2025-10-10 01:06:07'),
(135, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 300, '{\"role_id\": 4, \"permiso_id\": 74}', NULL, '2025-10-10 01:06:07'),
(136, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 301, '{\"role_id\": 4, \"permiso_id\": 70}', NULL, '2025-10-10 01:06:07'),
(137, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 302, '{\"role_id\": 4, \"permiso_id\": 24}', NULL, '2025-10-10 01:06:07'),
(138, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 303, '{\"role_id\": 4, \"permiso_id\": 20}', NULL, '2025-10-10 01:06:07'),
(139, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 304, '{\"role_id\": 4, \"permiso_id\": 23}', NULL, '2025-10-10 01:06:07'),
(140, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 305, '{\"role_id\": 4, \"permiso_id\": 22}', NULL, '2025-10-10 01:06:07'),
(141, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 306, '{\"role_id\": 4, \"permiso_id\": 18}', NULL, '2025-10-10 01:06:07'),
(142, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 307, '{\"role_id\": 4, \"permiso_id\": 49}', NULL, '2025-10-10 01:06:07'),
(143, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 308, '{\"role_id\": 4, \"permiso_id\": 45}', NULL, '2025-10-10 01:06:07'),
(144, NULL, 'PERMISO_ELIMINADO', 'rol_permisos', 309, '{\"role_id\": 4, \"permiso_id\": 32}', NULL, '2025-10-10 01:06:07'),
(145, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 356, '{\"role_id\": 4, \"permiso_id\": 11}', NULL, '2025-10-10 01:06:07'),
(146, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 357, '{\"role_id\": 4, \"permiso_id\": 15}', NULL, '2025-10-10 01:06:07'),
(147, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 358, '{\"role_id\": 4, \"permiso_id\": 16}', NULL, '2025-10-10 01:06:07'),
(148, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 359, '{\"role_id\": 4, \"permiso_id\": 1}', NULL, '2025-10-10 01:06:07'),
(149, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 360, '{\"role_id\": 4, \"permiso_id\": 2}', NULL, '2025-10-10 01:06:07'),
(150, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 361, '{\"role_id\": 4, \"permiso_id\": 38}', NULL, '2025-10-10 01:06:07'),
(151, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 362, '{\"role_id\": 4, \"permiso_id\": 39}', NULL, '2025-10-10 01:06:07'),
(152, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 363, '{\"role_id\": 4, \"permiso_id\": 40}', NULL, '2025-10-10 01:06:07'),
(153, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 364, '{\"role_id\": 4, \"permiso_id\": 42}', NULL, '2025-10-10 01:06:07'),
(154, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 365, '{\"role_id\": 4, \"permiso_id\": 43}', NULL, '2025-10-10 01:06:07'),
(155, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 366, '{\"role_id\": 4, \"permiso_id\": 44}', NULL, '2025-10-10 01:06:07'),
(156, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 367, '{\"role_id\": 4, \"permiso_id\": 70}', NULL, '2025-10-10 01:06:07'),
(157, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 368, '{\"role_id\": 4, \"permiso_id\": 74}', NULL, '2025-10-10 01:06:07'),
(158, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 369, '{\"role_id\": 4, \"permiso_id\": 18}', NULL, '2025-10-10 01:06:07'),
(159, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 370, '{\"role_id\": 4, \"permiso_id\": 20}', NULL, '2025-10-10 01:06:07'),
(160, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 371, '{\"role_id\": 4, \"permiso_id\": 22}', NULL, '2025-10-10 01:06:07'),
(161, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 372, '{\"role_id\": 4, \"permiso_id\": 23}', NULL, '2025-10-10 01:06:07'),
(162, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 373, '{\"role_id\": 4, \"permiso_id\": 24}', NULL, '2025-10-10 01:06:07'),
(163, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 374, '{\"role_id\": 4, \"permiso_id\": 45}', NULL, '2025-10-10 01:06:07'),
(164, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 375, '{\"role_id\": 4, \"permiso_id\": 49}', NULL, '2025-10-10 01:06:07'),
(165, NULL, 'PERMISO_AGREGADO', 'rol_permisos', 376, '{\"role_id\": 4, \"permiso_id\": 32}', NULL, '2025-10-10 01:06:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mascotas`
--

CREATE TABLE `mascotas` (
  `id` int(11) NOT NULL,
  `dueño_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `especie` varchar(50) DEFAULT NULL,
  `raza` varchar(100) DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `peso` decimal(6,2) DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `mascotas`
--

INSERT INTO `mascotas` (`id`, `dueño_id`, `nombre`, `especie`, `raza`, `edad`, `peso`, `notas`, `foto`, `creado_en`) VALUES
(1, 18, 'Tommy', 'Perro', 'Bulldog', 2, 45.00, NULL, 'uploads/mascotas/mascota_1_1758770429.png', '2025-09-12 23:33:54'),
(2, 20, 'Hanibal Lecter', 'Gato', 'Criollo', 5, 6.00, '212324\r\n', NULL, '2025-10-01 01:07:53'),
(3, 20, 'Winnie', 'Ave', 'criolla', 2, 1.00, 'qwe', NULL, '2025-10-01 01:15:36'),
(6, 28, 'Puppy', 'Roedor', 'Roedora', 3, 1.00, 'Mera Rata', NULL, '2025-10-04 00:23:29'),
(7, 28, 'Noah', 'Gato', 'Persa', 3, 3.00, 'Gata castrada', NULL, '2025-10-04 21:54:34');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE `modulos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `modulos`
--

INSERT INTO `modulos` (`id`, `nombre`) VALUES
(1, 'citas'),
(2, 'clientes'),
(3, 'mascotas'),
(4, 'servicios'),
(5, 'empleados'),
(6, 'roles'),
(7, 'config'),
(8, 'reportes'),
(9, 'archivos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notas_mascotas`
--

CREATE TABLE `notas_mascotas` (
  `id` int(11) NOT NULL,
  `mascota_id` int(11) NOT NULL,
  `veterinario_id` int(11) NOT NULL,
  `nota` text NOT NULL,
  `creado_en` datetime DEFAULT current_timestamp(),
  `actualizado_en` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notas_mascotas`
--

INSERT INTO `notas_mascotas` (`id`, `mascota_id`, `veterinario_id`, `nota`, `creado_en`, `actualizado_en`) VALUES
(4, 1, 3, 'El estado de la mascota se encuentra perfectamente', '2025-09-23 19:41:45', '2025-09-24 21:54:59'),
(6, 2, 3, 'Esta es la prueba para notas', '2025-10-01 19:55:52', '2025-10-01 19:55:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `tipo` enum('email','sms','push') DEFAULT NULL,
  `destino` varchar(255) DEFAULT NULL,
  `asunto` varchar(255) DEFAULT NULL,
  `cuerpo` text DEFAULT NULL,
  `estado` enum('pendiente','enviado','fallido') DEFAULT 'pendiente',
  `intentos` int(11) DEFAULT 0,
  `programado_en` datetime DEFAULT NULL,
  `enviado_en` datetime DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `notificacion_vista` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Indica si la notificación fue vista'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perfil`
--

CREATE TABLE `perfil` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `nacimiento` date DEFAULT NULL,
  `genero` enum('m','f','o') DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `notas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id` int(11) NOT NULL,
  `modulo` varchar(50) NOT NULL COMMENT 'Módulo al que pertenece el permiso',
  `nombre` varchar(100) NOT NULL COMMENT 'Nombre único del permiso (formato: modulo.accion)',
  `descripcion` varchar(255) DEFAULT NULL COMMENT 'Descripción legible del permiso',
  `accion` varchar(50) NOT NULL COMMENT 'Tipo de acción (ver, crear, editar, eliminar, etc)',
  `orden` int(11) DEFAULT 0 COMMENT 'Orden de visualización',
  `activo` tinyint(1) DEFAULT 1 COMMENT 'Estado del permiso',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla maestra de permisos del sistema';

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id`, `modulo`, `nombre`, `descripcion`, `accion`, `orden`, `activo`, `creado_en`, `actualizado_en`) VALUES
(1, 'dashboard', 'dashboard.ver', 'Ver panel de control principal', 'ver', 1, 1, '2025-09-11 23:57:03', NULL),
(2, 'dashboard', 'dashboard.estadisticas', 'Ver estadísticas completas del negocio', 'estadisticas', 2, 1, '2025-09-11 23:57:03', NULL),
(3, 'dashboard', 'dashboard.widgets', 'Personalizar widgets del dashboard', 'widgets', 3, 1, '2025-09-11 23:57:03', NULL),
(4, 'citas', 'citas.ver', 'Ver listado de citas', 'ver', 10, 1, '2025-09-11 23:57:03', NULL),
(5, 'citas', 'citas.crear', 'Crear nuevas citas', 'crear', 11, 1, '2025-09-11 23:57:03', NULL),
(6, 'citas', 'citas.editar', 'Editar citas existentes', 'editar', 12, 1, '2025-09-11 23:57:03', NULL),
(7, 'citas', 'citas.eliminar', 'Eliminar/cancelar citas', 'eliminar', 13, 1, '2025-09-11 23:57:03', NULL),
(9, 'citas', 'citas.calendario', 'Ver calendario de citas', 'calendario', 15, 1, '2025-09-11 23:57:03', NULL),
(11, 'clientes', 'clientes.ver', 'Ver listado de clientes', 'ver', 20, 1, '2025-09-11 23:57:03', NULL),
(12, 'clientes', 'clientes.crear', 'Registrar nuevos clientes', 'crear', 21, 1, '2025-09-11 23:57:03', NULL),
(13, 'clientes', 'clientes.editar', 'Editar información de clientes', 'editar', 22, 1, '2025-09-11 23:57:03', NULL),
(14, 'clientes', 'clientes.eliminar', 'Eliminar clientes del sistema', 'eliminar', 23, 1, '2025-09-11 23:57:03', NULL),
(15, 'clientes', 'clientes.detalle', 'Ver información detallada de clientes', 'detalle', 24, 1, '2025-09-11 23:57:03', NULL),
(16, 'clientes', 'clientes.historial', 'Ver historial de visitas del cliente', 'historial', 25, 1, '2025-09-11 23:57:03', NULL),
(17, 'clientes', 'clientes.exportar', 'Exportar datos de clientes', 'exportar', 26, 1, '2025-09-11 23:57:03', NULL),
(18, 'mascotas', 'mascotas.ver', 'Ver listado de mascotas', 'ver', 30, 1, '2025-09-11 23:57:03', NULL),
(19, 'mascotas', 'mascotas.crear', 'Registrar nuevas mascotas', 'crear', 31, 1, '2025-09-11 23:57:03', NULL),
(20, 'mascotas', 'mascotas.editar', 'Editar información de mascotas', 'editar', 32, 1, '2025-09-11 23:57:03', NULL),
(21, 'mascotas', 'mascotas.eliminar', 'Eliminar mascotas del sistema', 'eliminar', 33, 1, '2025-09-11 23:57:03', NULL),
(22, 'mascotas', 'mascotas.historial', 'Ver historial médico de mascotas', 'historial', 34, 1, '2025-09-11 23:57:03', NULL),
(23, 'mascotas', 'mascotas.fotos', 'Gestionar fotos de mascotas', 'fotos', 35, 1, '2025-09-11 23:57:03', NULL),
(24, 'mascotas', 'mascotas.documentos', 'Ver/subir documentos de mascotas', 'documentos', 36, 1, '2025-09-11 23:57:03', NULL),
(25, 'empleados', 'empleados.ver', 'Ver listado de empleados', 'ver', 40, 1, '2025-09-11 23:57:03', NULL),
(26, 'empleados', 'empleados.crear', 'Registrar nuevos empleados', 'crear', 41, 1, '2025-09-11 23:57:03', NULL),
(27, 'empleados', 'empleados.editar', 'Editar información de empleados', 'editar', 42, 1, '2025-09-11 23:57:03', NULL),
(28, 'empleados', 'empleados.eliminar', 'Eliminar empleados del sistema', 'eliminar', 43, 1, '2025-09-11 23:57:03', NULL),
(29, 'empleados', 'empleados.horarios', 'Gestionar horarios de empleados', 'horarios', 44, 1, '2025-09-11 23:57:03', NULL),
(30, 'empleados', 'empleados.salarios', 'Ver/editar información salarial', 'salarios', 45, 1, '2025-09-11 23:57:03', NULL),
(31, 'empleados', 'empleados.permisos', 'Gestionar permisos individuales', 'permisos', 46, 1, '2025-09-11 23:57:03', NULL),
(32, 'servicios', 'servicios.ver', 'Ver catálogo de servicios', 'ver', 50, 1, '2025-09-11 23:57:03', NULL),
(33, 'servicios', 'servicios.crear', 'Crear nuevos servicios', 'crear', 51, 1, '2025-09-11 23:57:03', NULL),
(34, 'servicios', 'servicios.editar', 'Editar servicios existentes', 'editar', 52, 1, '2025-09-11 23:57:03', NULL),
(35, 'servicios', 'servicios.eliminar', 'Eliminar servicios', 'eliminar', 53, 1, '2025-09-11 23:57:03', NULL),
(36, 'servicios', 'servicios.precios', 'Gestionar precios de servicios', 'precios', 54, 1, '2025-09-11 23:57:03', NULL),
(37, 'servicios', 'servicios.activar', 'Activar/desactivar servicios', 'activar', 55, 1, '2025-09-11 23:57:03', NULL),
(38, 'historial', 'historial.ver', 'Ver historial médico', 'ver', 60, 1, '2025-09-11 23:57:03', NULL),
(39, 'historial', 'historial.crear', 'Crear registros médicos', 'crear', 61, 1, '2025-09-11 23:57:03', NULL),
(40, 'historial', 'historial.editar', 'Editar registros médicos', 'editar', 62, 1, '2025-09-11 23:57:03', NULL),
(41, 'historial', 'historial.eliminar', 'Eliminar registros médicos', 'eliminar', 63, 1, '2025-09-11 23:57:03', NULL),
(42, 'historial', 'historial.recetas', 'Gestionar recetas médicas', 'recetas', 64, 1, '2025-09-11 23:57:03', NULL),
(43, 'historial', 'historial.laboratorio', 'Gestionar resultados de laboratorio', 'laboratorio', 65, 1, '2025-09-11 23:57:03', NULL),
(44, 'historial', 'historial.imprimir', 'Imprimir historiales médicos', 'imprimir', 66, 1, '2025-09-11 23:57:03', NULL),
(45, 'reportes', 'reportes.ver', 'Ver reportes básicos', 'ver', 70, 1, '2025-09-11 23:57:03', NULL),
(46, 'reportes', 'reportes.crear', 'Generar nuevos reportes', 'crear', 71, 1, '2025-09-11 23:57:03', NULL),
(47, 'reportes', 'reportes.financieros', 'Ver reportes financieros', 'financieros', 72, 1, '2025-09-11 23:57:03', NULL),
(48, 'reportes', 'reportes.ventas', 'Ver reportes de ventas', 'ventas', 73, 1, '2025-09-11 23:57:03', NULL),
(49, 'reportes', 'reportes.clientes', 'Ver reportes de clientes', 'clientes', 74, 1, '2025-09-11 23:57:03', NULL),
(50, 'reportes', 'reportes.empleados', 'Ver reportes de empleados', 'empleados', 75, 1, '2025-09-11 23:57:03', NULL),
(51, 'reportes', 'reportes.exportar', 'Exportar reportes (PDF/Excel)', 'exportar', 76, 1, '2025-09-11 23:57:03', NULL),
(52, 'config', 'config.ver', 'Ver configuración del sistema', 'ver', 80, 1, '2025-09-11 23:57:03', NULL),
(53, 'config', 'config.general', 'Modificar configuración general', 'general', 81, 1, '2025-09-11 23:57:03', NULL),
(54, 'config', 'config.empresa', 'Editar datos de la empresa', 'empresa', 82, 1, '2025-09-11 23:57:03', NULL),
(55, 'config', 'config.notificaciones', 'Configurar notificaciones', 'notificaciones', 83, 1, '2025-09-11 23:57:03', NULL),
(56, 'config', 'config.backup', 'Realizar copias de seguridad', 'backup', 84, 1, '2025-09-11 23:57:03', NULL),
(57, 'config', 'config.restore', 'Restaurar copias de seguridad', 'restore', 85, 1, '2025-09-11 23:57:03', NULL),
(58, 'config', 'config.logs', 'Ver logs del sistema', 'logs', 86, 1, '2025-09-11 23:57:03', NULL),
(59, 'roles', 'roles.ver', 'Ver roles del sistema', 'ver', 90, 1, '2025-09-11 23:57:03', NULL),
(60, 'roles', 'roles.crear', 'Crear nuevos roles', 'crear', 91, 1, '2025-09-11 23:57:03', NULL),
(61, 'roles', 'roles.editar', 'Editar roles existentes', 'editar', 92, 1, '2025-09-11 23:57:03', NULL),
(62, 'roles', 'roles.eliminar', 'Eliminar roles', 'eliminar', 93, 1, '2025-09-11 23:57:03', NULL),
(63, 'roles', 'roles.permisos', 'Gestionar permisos de roles', 'permisos', 94, 1, '2025-09-11 23:57:03', NULL),
(64, 'roles', 'roles.usuarios', 'Asignar usuarios a roles', 'usuarios', 95, 1, '2025-09-11 23:57:03', NULL),
(65, 'notificaciones', 'notificaciones.ver', 'Ver notificaciones', 'ver', 100, 1, '2025-09-11 23:57:03', NULL),
(66, 'notificaciones', 'notificaciones.enviar', 'Enviar notificaciones', 'enviar', 101, 1, '2025-09-11 23:57:03', NULL),
(67, 'notificaciones', 'notificaciones.email', 'Enviar emails masivos', 'email', 102, 1, '2025-09-11 23:57:03', NULL),
(68, 'notificaciones', 'notificaciones.sms', 'Enviar SMS', 'sms', 103, 1, '2025-09-11 23:57:03', NULL),
(69, 'notificaciones', 'notificaciones.programar', 'Programar notificaciones', 'programar', 104, 1, '2025-09-11 23:57:03', NULL),
(70, 'inventario', 'inventario.ver', 'Ver inventario', 'ver', 110, 1, '2025-09-11 23:57:03', NULL),
(71, 'inventario', 'inventario.crear', 'Agregar productos al inventario', 'crear', 111, 1, '2025-09-11 23:57:03', NULL),
(72, 'inventario', 'inventario.editar', 'Editar productos del inventario', 'editar', 112, 1, '2025-09-11 23:57:03', NULL),
(73, 'inventario', 'inventario.eliminar', 'Eliminar productos del inventario', 'eliminar', 113, 1, '2025-09-11 23:57:03', NULL),
(74, 'inventario', 'inventario.movimientos', 'Registrar movimientos de inventario', 'movimientos', 114, 1, '2025-09-11 23:57:03', NULL),
(75, 'facturacion', 'facturacion.ver', 'Ver facturas', 'ver', 120, 1, '2025-09-11 23:57:03', NULL),
(76, 'facturacion', 'facturacion.crear', 'Crear facturas', 'crear', 121, 1, '2025-09-11 23:57:03', NULL),
(77, 'facturacion', 'facturacion.editar', 'Editar facturas', 'editar', 122, 1, '2025-09-11 23:57:03', NULL),
(78, 'facturacion', 'facturacion.anular', 'Anular facturas', 'anular', 123, 1, '2025-09-11 23:57:03', NULL),
(79, 'facturacion', 'facturacion.pagos', 'Registrar pagos', 'pagos', 124, 1, '2025-09-11 23:57:03', NULL),
(80, 'facturacion', 'facturacion.imprimir', 'Imprimir facturas', 'imprimir', 125, 1, '2025-09-11 23:57:03', NULL),
(81, 'peluqueria', 'Peluqueria.ver', 'Ver calendario de peluqueria', 'ver', 1, 1, '2025-10-11 22:29:45', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`, `creado_en`) VALUES
(1, 'super_admin', 'Acceso total al sistema', '2025-09-07 03:31:08'),
(2, 'admin', 'Administrador de la clínica', '2025-09-07 03:31:08'),
(3, 'recepcionista', 'Gestión de citas y clientes', '2025-09-07 03:31:08'),
(4, 'veterinario', 'Profesional veterinario', '2025-09-07 03:31:08'),
(5, 'peluquero', 'Profesional de peluquería', '2025-09-07 03:31:08'),
(6, 'cliente', 'Cliente/propietario', '2025-09-07 03:31:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `rol_permisos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `rol_permisos` (
`role_id` int(11)
,`permiso_id` int(11)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `duracion_min` int(11) NOT NULL DEFAULT 30,
  `activo` tinyint(1) DEFAULT 1,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id`, `nombre`, `descripcion`, `precio`, `duracion_min`, `activo`, `creado_en`) VALUES
(1, 'Peluqueria', 'Hacer baño a la mascota', 45000.00, 30, 1, '2025-09-13 00:40:17'),
(2, 'Bañado y Cortes', 'Con jabon, secadora y olores', 67000.00, 60, 1, '2025-10-01 02:29:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes`
--

CREATE TABLE `solicitudes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo` enum('permiso','vacaciones','incapacidad') NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `motivo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitudes`
--

INSERT INTO `solicitudes` (`id`, `usuario_id`, `tipo`, `fecha_inicio`, `fecha_fin`, `motivo`) VALUES
(3, 4, 'vacaciones', '2025-10-03', '2025-10-27', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL COMMENT 'ID del usuario que crea el ticket',
  `asignado_a` int(11) DEFAULT NULL COMMENT 'ID del usuario de soporte asignado',
  `asunto` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `rol_problema` varchar(255) DEFAULT NULL,
  `estado` enum('Abierto','En Proceso','Cerrado') NOT NULL DEFAULT 'Abierto',
  `prioridad` enum('Baja','Media','Alta','Urgente') NOT NULL DEFAULT 'Media',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `notificacion_vista` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Indica si el ticket ya fue notificado o visto',
  `notificacion_admin_vista` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 = no visto por admin, 1 = visto'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tickets`
--

INSERT INTO `tickets` (`id`, `usuario_id`, `asignado_a`, `asunto`, `descripcion`, `rol_problema`, `estado`, `prioridad`, `creado_en`, `actualizado_en`, `notificacion_vista`, `notificacion_admin_vista`) VALUES
(1, 9, 1, 'Falla en sistema', 'falla prueba 1', 'recepcionista', 'Abierto', 'Media', '2025-10-11 21:41:27', '2025-10-12 00:45:31', 1, 1),
(2, 9, 1, 'Falla en sistema', 'fall de prueba 2', 'veterinario', 'Abierto', 'Media', '2025-10-11 21:52:37', '2025-10-11 23:21:33', 1, 1),
(3, 9, 1, 'Falla en sistema', 'prueba 3', 'veterinario', 'Abierto', 'Media', '2025-10-11 23:11:25', '2025-10-11 23:21:33', 1, 1),
(4, 9, 1, 'Falla en sistema', 'prueba 4', 'admin', 'Cerrado', 'Media', '2025-10-11 23:22:02', '2025-10-12 00:45:35', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ticket_mensajes`
--

CREATE TABLE `ticket_mensajes` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL COMMENT 'ID del autor del mensaje',
  `mensaje` text NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ticket_mensajes`
--

INSERT INTO `ticket_mensajes` (`id`, `ticket_id`, `usuario_id`, `mensaje`, `creado_en`) VALUES
(0, 4, 1, 'ya se realizo todos los cambios y funcionamiento', '2025-10-11 23:29:43'),
(0, 4, 9, 'Gracias', '2025-10-12 00:39:13'),
(0, 1, 9, 'Hamos una prueba', '2025-10-12 00:39:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turnos_empleado`
--

CREATE TABLE `turnos_empleado` (
  `id` int(11) NOT NULL,
  `empleado_id` int(11) NOT NULL,
  `inicio` datetime NOT NULL,
  `fin` datetime NOT NULL,
  `tipo` varchar(15) DEFAULT 'turno',
  `notas` text DEFAULT NULL,
  `creado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `turnos_empleado`
--

INSERT INTO `turnos_empleado` (`id`, `empleado_id`, `inicio`, `fin`, `tipo`, `notas`, `creado_por`) VALUES
(1, 3, '2025-10-02 18:30:00', '2025-10-03 06:31:00', 'Emergencia', 'Nota prueba 333', 9),
(4, 4, '2025-10-02 06:38:00', '2025-10-03 18:38:00', 'Extras', 'prueba tres\r\n', 9),
(5, 9, '2025-10-02 06:40:00', '2025-10-03 18:40:00', 'Extras', 'preuba', 9),
(7, 9, '2025-10-02 09:52:00', '2025-10-02 06:52:00', 'Nocturno', 'Prueba final?', 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `docusu` varchar(50) NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `apellido` varchar(80) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(64) NOT NULL,
  `reset_expira` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `docusu`, `nombre`, `apellido`, `email`, `telefono`, `password`, `role_id`, `estado`, `creado_en`, `reset_token`, `reset_expira`) VALUES
(1, '0000000001', 'Super', 'Admin', 'andres.rojast98@gmail.com', NULL, '$2y$10$tl9ee/EuUcY6JBbJHjooY.bhoBjsgzAgHPo7liYyF0iI9I7AfdSpG', 1, 1, '2025-09-07 04:07:20', 'bb30e79b94bdad536066225ed38c1b7325a3d79f52bfb68d84aafd0240d9270d', '2025-10-12 02:08:24'),
(3, '0000000003', 'Andres', 'Rojas', 'andres_rojast9@outlook.com', '3105551234', '$2y$10$NRlwUgWy2YLDciO4nard/uA2Lyr38A13B4.MXSoOzyQEK8KiFPvOW', 4, 1, '2025-09-07 04:07:20', '4f26bf98b6bb2b8b77d91d837bf7bba33b66292bc667fea994d98c05c5671eb3', '2025-09-25 04:26:56'),
(4, '0000000004', 'Recepcion', 'Prueba', 'recepcion@vetsmart.test', '3218765432', '$2b$12$SVWcXtiXWXzaQgOaKFTBM.XOpui0aB96ScYWR9TZGfdmYXCsqpU4.', 3, 1, '2025-09-07 04:07:20', '0', '0000-00-00 00:00:00'),
(5, '0000000005', 'Peluquero', 'Prueba', 'peluquero@vetsmart.test', '3009900011', '$2b$12$C6/ANKFcycDsIGX5iM9stu2ZIGSDGxJnjbGUbt.B/gjf4ZiuttpOC', 5, 1, '2025-09-07 04:07:20', '0', '0000-00-00 00:00:00'),
(9, '1073715080', 'Heyder', 'Sterlin', 'andres@prueba.com', '3154448877', '$2y$10$3b1zN67p.pUPWdm4dDbMSuess8EOMuTFqivGRcfRbYo3Sbvm/KkyK', 2, 1, '2025-09-09 02:50:02', '', NULL),
(18, '987654321', 'paula', 'Real', 'paula@prueba.com', '654321596', '$2y$10$6Acda2HFqvmYyKQ.iAW85eU7F7PdoSf25JLcQFcSg6G.ZnechUgUC', 6, 1, '2025-09-12 23:25:35', '', NULL),
(20, '49876321', 'Andres', 'Rojas Sterlin', 'segunda@prueba.com', '3144928505', '$2y$10$6uM4.6Sv82unVdTeb3EphedyHBBcaPxQowVsmIG376blrKkpAvQzS', 6, 1, '2025-10-01 00:19:46', '', NULL),
(21, '98765132', 'Yakeline', 'Sterlin', 'peyahe-77@outlook.com', '3124285749', '$2y$10$Alhov8AGbeVuSTDSn0/yEur9xBoPicIsiJXkFHxtjgb9jIdgCLRs6', 6, 1, '2025-10-01 00:28:22', '', NULL),
(25, '74185291', 'Luisa', 'Arevaloa', 'luisa@example.com', '9518471', '$2y$10$4INL.cpc3ESrncuamkwvZ.Bmq8DNekB2K4vVrJXvLAEq0sdfAa4oC', 3, 1, '2025-10-03 02:02:39', '', NULL),
(28, '6549873281', 'Chayanne', 'Ernesto', 'chayanne@ejemplo.com', '75896485', '$2y$10$LryNrogzbn7H.NS14C023.Jh8CoGXvkLmZVicbirxYQO8WPNt0PMG', 6, 1, '2025-10-04 00:22:57', '', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vacunas`
--

CREATE TABLE `vacunas` (
  `id` int(11) NOT NULL,
  `mascota_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_aplicacion` date NOT NULL,
  `proxima_dosis` date DEFAULT NULL,
  `veterinario_id` int(11) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vacunas`
--

INSERT INTO `vacunas` (`id`, `mascota_id`, `nombre`, `descripcion`, `fecha_aplicacion`, `proxima_dosis`, `veterinario_id`, `creado_en`) VALUES
(4, 1, 'Laura Gómez', 'cxsacasc', '2025-09-02', '2025-09-30', NULL, '2025-09-16 23:37:35'),
(5, 1, 'Baño y Cortes', 'c', '2025-09-03', '2025-09-30', NULL, '2025-09-20 00:30:18'),
(6, 2, 'Parvo', NULL, '2025-10-01', '2025-10-29', NULL, '2025-10-02 00:20:38');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_roles_permisos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_roles_permisos` (
`role_id` int(11)
,`rol` varchar(50)
,`rol_descripcion` varchar(255)
,`modulo` varchar(50)
,`permiso` varchar(100)
,`permiso_descripcion` varchar(255)
,`accion` varchar(50)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_roles_permisos_count`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_roles_permisos_count` (
`role_id` int(11)
,`rol` varchar(50)
,`rol_descripcion` varchar(255)
,`total_permisos` bigint(21)
,`total_modulos` bigint(21)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `rol_permisos`
--
DROP TABLE IF EXISTS `rol_permisos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `rol_permisos`  AS SELECT `role_permissions`.`role_id` AS `role_id`, `role_permissions`.`permission_id` AS `permiso_id` FROM `role_permissions` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_roles_permisos`
--
DROP TABLE IF EXISTS `v_roles_permisos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_roles_permisos`  AS SELECT `r`.`id` AS `role_id`, `r`.`nombre` AS `rol`, `r`.`descripcion` AS `rol_descripcion`, `p`.`modulo` AS `modulo`, `p`.`nombre` AS `permiso`, `p`.`descripcion` AS `permiso_descripcion`, `p`.`accion` AS `accion` FROM ((`roles` `r` join `rol_permisos` `rp` on(`r`.`id` = `rp`.`role_id`)) join `permisos` `p` on(`rp`.`permiso_id` = `p`.`id`)) ORDER BY `r`.`id` ASC, `p`.`modulo` ASC, `p`.`orden` ASC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_roles_permisos_count`
--
DROP TABLE IF EXISTS `v_roles_permisos_count`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_roles_permisos_count`  AS SELECT `r`.`id` AS `role_id`, `r`.`nombre` AS `rol`, `r`.`descripcion` AS `rol_descripcion`, count(`rp`.`permiso_id`) AS `total_permisos`, count(distinct `p`.`modulo`) AS `total_modulos` FROM ((`roles` `r` left join `rol_permisos` `rp` on(`r`.`id` = `rp`.`role_id`)) left join `permisos` `p` on(`rp`.`permiso_id` = `p`.`id`)) GROUP BY `r`.`id`, `r`.`nombre`, `r`.`descripcion` ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `creado_en` (`creado_en`);

--
-- Indices de la tabla `bloqueos`
--
ALTER TABLE `bloqueos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `empleado_id` (`empleado_id`),
  ADD KEY `creado_por` (`creado_por`);

--
-- Indices de la tabla `citas`
--
ALTER TABLE `citas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `mascota_id` (`mascota_id`),
  ADD KEY `empleado_id` (`empleado_id`),
  ADD KEY `servicio_id` (`servicio_id`),
  ADD KEY `creado_por` (`creado_por`),
  ADD KEY `idx_citas_fecha` (`fecha`);

--
-- Indices de la tabla `cliente_detalles`
--
ALTER TABLE `cliente_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idusu` (`idusu`);

--
-- Indices de la tabla `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clave` (`clave`);

--
-- Indices de la tabla `consultas`
--
ALTER TABLE `consultas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mascota_id` (`mascota_id`),
  ADD KEY `empleado_id` (`empleado_id`);

--
-- Indices de la tabla `emp_det`
--
ALTER TABLE `emp_det`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `historial_citas`
--
ALTER TABLE `historial_citas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cita_id` (`cita_id`),
  ADD KEY `cambiado_por` (`cambiado_por`);

--
-- Indices de la tabla `horarios_semana`
--
ALTER TABLE `horarios_semana`
  ADD PRIMARY KEY (`id`),
  ADD KEY `empleado_id` (`empleado_id`);

--
-- Indices de la tabla `login_intentos`
--
ALTER TABLE `login_intentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `logs_actividad`
--
ALTER TABLE `logs_actividad`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `mascotas`
--
ALTER TABLE `mascotas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_mascotas_dueno` (`dueño_id`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `notas_mascotas`
--
ALTER TABLE `notas_mascotas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mascota_id` (`mascota_id`),
  ADD KEY `veterinario_id` (`veterinario_id`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `perfil`
--
ALTER TABLE `perfil`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_nombre_unico` (`nombre`),
  ADD KEY `idx_modulo` (`modulo`),
  ADD KEY `idx_accion` (`accion`),
  ADD KEY `idx_activo` (`activo`),
  ADD KEY `idx_permisos_modulo_orden` (`modulo`,`orden`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `turnos_empleado`
--
ALTER TABLE `turnos_empleado`
  ADD PRIMARY KEY (`id`),
  ADD KEY `empleado_id` (`empleado_id`),
  ADD KEY `creado_por` (`creado_por`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `docusu` (`docusu`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `idx_usuarios_docusu` (`docusu`),
  ADD KEY `idx_usuarios_email` (`email`);

--
-- Indices de la tabla `vacunas`
--
ALTER TABLE `vacunas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `bloqueos`
--
ALTER TABLE `bloqueos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `citas`
--
ALTER TABLE `citas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT de la tabla `cliente_detalles`
--
ALTER TABLE `cliente_detalles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `config`
--
ALTER TABLE `config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `consultas`
--
ALTER TABLE `consultas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `emp_det`
--
ALTER TABLE `emp_det`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `historial_citas`
--
ALTER TABLE `historial_citas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `horarios_semana`
--
ALTER TABLE `horarios_semana`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `login_intentos`
--
ALTER TABLE `login_intentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `logs_actividad`
--
ALTER TABLE `logs_actividad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=166;

--
-- AUTO_INCREMENT de la tabla `mascotas`
--
ALTER TABLE `mascotas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `notas_mascotas`
--
ALTER TABLE `notas_mascotas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `perfil`
--
ALTER TABLE `perfil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `turnos_empleado`
--
ALTER TABLE `turnos_empleado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `vacunas`
--
ALTER TABLE `vacunas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bloqueos`
--
ALTER TABLE `bloqueos`
  ADD CONSTRAINT `bloqueos_ibfk_1` FOREIGN KEY (`empleado_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bloqueos_ibfk_2` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `citas`
--
ALTER TABLE `citas`
  ADD CONSTRAINT `citas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `citas_ibfk_2` FOREIGN KEY (`mascota_id`) REFERENCES `mascotas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `citas_ibfk_3` FOREIGN KEY (`empleado_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `citas_ibfk_4` FOREIGN KEY (`servicio_id`) REFERENCES `servicios` (`id`),
  ADD CONSTRAINT `citas_ibfk_5` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `cliente_detalles`
--
ALTER TABLE `cliente_detalles`
  ADD CONSTRAINT `cliente_detalles_ibfk_1` FOREIGN KEY (`idusu`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `consultas`
--
ALTER TABLE `consultas`
  ADD CONSTRAINT `fk_consultas_empleado` FOREIGN KEY (`empleado_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_consultas_mascota` FOREIGN KEY (`mascota_id`) REFERENCES `mascotas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `emp_det`
--
ALTER TABLE `emp_det`
  ADD CONSTRAINT `emp_det_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `historial_citas`
--
ALTER TABLE `historial_citas`
  ADD CONSTRAINT `historial_citas_ibfk_1` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `historial_citas_ibfk_2` FOREIGN KEY (`cambiado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `horarios_semana`
--
ALTER TABLE `horarios_semana`
  ADD CONSTRAINT `horarios_semana_ibfk_1` FOREIGN KEY (`empleado_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `login_intentos`
--
ALTER TABLE `login_intentos`
  ADD CONSTRAINT `login_intentos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `logs_actividad`
--
ALTER TABLE `logs_actividad`
  ADD CONSTRAINT `logs_actividad_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `mascotas`
--
ALTER TABLE `mascotas`
  ADD CONSTRAINT `mascotas_ibfk_1` FOREIGN KEY (`dueño_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `notas_mascotas`
--
ALTER TABLE `notas_mascotas`
  ADD CONSTRAINT `notas_mascotas_ibfk_1` FOREIGN KEY (`mascota_id`) REFERENCES `mascotas` (`id`),
  ADD CONSTRAINT `notas_mascotas_ibfk_2` FOREIGN KEY (`veterinario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `notificaciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `perfil`
--
ALTER TABLE `perfil`
  ADD CONSTRAINT `perfil_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD CONSTRAINT `solicitudes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `turnos_empleado`
--
ALTER TABLE `turnos_empleado`
  ADD CONSTRAINT `turnos_empleado_ibfk_1` FOREIGN KEY (`empleado_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `turnos_empleado_ibfk_2` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
