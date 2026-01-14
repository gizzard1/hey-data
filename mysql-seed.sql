SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `YOUR_DATA_BASE_NAME` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `YOUR_DATA_BASE_NAME`;

CREATE TABLE `abonos` (
  `id` bigint UNSIGNED NOT NULL,
  `cita_id` bigint UNSIGNED DEFAULT NULL,
  `venta_id` bigint UNSIGNED DEFAULT NULL,
  `payment_method_id` bigint UNSIGNED NOT NULL,
  `payed_qty` decimal(10,2) DEFAULT NULL,
  `total_debt` decimal(10,2) DEFAULT NULL,
  `taxes_payed` decimal(10,2) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `abono_propinas` (
  `id` bigint UNSIGNED NOT NULL,
  `cita_id` bigint UNSIGNED DEFAULT NULL,
  `venta_id` bigint UNSIGNED DEFAULT NULL,
  `payment_method_id` bigint UNSIGNED NOT NULL,
  `payed_qty` decimal(10,2) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `asignacion_servicios` (
  `id` bigint UNSIGNED NOT NULL,
  `selected_service` bigint UNSIGNED DEFAULT NULL,
  `cita_id` bigint UNSIGNED NOT NULL,
  `empleado_id` bigint UNSIGNED NOT NULL,
  `discount_qty` decimal(10,2) DEFAULT NULL,
  `discount_type` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Porcentaje',
  `generated_points` decimal(10,2) DEFAULT NULL,
  `current_price` decimal(10,2) NOT NULL,
  `disccount_price` decimal(10,2) DEFAULT NULL,
  `comission` decimal(10,2) DEFAULT NULL,
  `base_comision` tinyint(1) NOT NULL DEFAULT '0',
  `type_comision_calculated` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percent',
  `iva` enum('0.16','0.08','0') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `start` datetime DEFAULT NULL,
  `selected` tinyint(1) DEFAULT '1',
  `color` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '#E2BBB4',
  `duration` int NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `asignacion_ventas` (
  `id` bigint UNSIGNED NOT NULL,
  `selected_item` bigint UNSIGNED NOT NULL,
  `cita_id` bigint UNSIGNED DEFAULT NULL,
  `venta_id` bigint UNSIGNED DEFAULT NULL,
  `empleado_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `discount_qty` decimal(10,2) DEFAULT NULL,
  `discount_type` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Porcentaje',
  `generated_points` decimal(10,2) DEFAULT NULL,
  `current_price` decimal(10,2) NOT NULL,
  `disccount_price` decimal(10,2) DEFAULT NULL,
  `comission` decimal(10,2) DEFAULT NULL,
  `base_comision` tinyint(1) NOT NULL DEFAULT '0',
  `type_comision_calculated` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percent',
  `iva` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bloqueos` (
  `id` bigint UNSIGNED NOT NULL,
  `empleado_id` bigint UNSIGNED NOT NULL,
  `salon_id` bigint UNSIGNED NOT NULL,
  `description` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `color` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#E2BBB4',
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `caja_aperturas` (
  `id` bigint UNSIGNED NOT NULL,
  `caja_corte_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `caja_chica` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `caja_cortes` (
  `id` bigint UNSIGNED NOT NULL,
  `description` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_bruto` decimal(20,2) NOT NULL,
  `total_neto` decimal(20,2) NOT NULL,
  `ganancia` decimal(20,2) NOT NULL,
  `total_ventas` decimal(20,2) NOT NULL,
  `total_servicios` decimal(20,2) NOT NULL,
  `total_cash` decimal(20,2) NOT NULL,
  `total_NF` decimal(20,2) NOT NULL,
  `total_points` decimal(20,2) NOT NULL,
  `tips` decimal(20,2) NOT NULL,
  `comissions` decimal(20,2) NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `total_cash_real` decimal(20,2) NOT NULL,
  `total_tarjeta` decimal(20,2) NOT NULL,
  `total_tarjeta_real` decimal(20,2) NOT NULL,
  `total_NF_real` decimal(20,2) NOT NULL,
  `propinas_efectivo_real` decimal(20,2) NOT NULL,
  `propinas_banorte_real` decimal(20,2) NOT NULL,
  `propinas_efectivo` decimal(20,2) NOT NULL,
  `propinas_banorte` decimal(20,2) NOT NULL,
  `propinas_tarjeta` decimal(20,2) NOT NULL,
  `propinas_tarjeta_real` decimal(20,2) NOT NULL,
  `caja_chica_real` decimal(20,2) NOT NULL DEFAULT '0.00',
  `caja_chica` decimal(20,2) NOT NULL DEFAULT '0.00',
  `gastos` decimal(20,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `calificacion_cliente_empleados` (
  `id` bigint UNSIGNED NOT NULL,
  `puntaje` int NOT NULL,
  `cliente_id` bigint UNSIGNED DEFAULT NULL,
  `empleado_id` bigint UNSIGNED DEFAULT NULL,
  `comentario` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `calificacion_empleado_clientes` (
  `id` bigint UNSIGNED NOT NULL,
  `puntaje` int DEFAULT NULL,
  `calificado` enum('cliente','empleado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cliente_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categoria_clientes` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `salon_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categoria_clientes_pivs` (
  `id` bigint UNSIGNED NOT NULL,
  `categoria_cliente_id` bigint UNSIGNED NOT NULL,
  `cliente_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categoria_gastos` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `salon_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categoria_productos` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `platform_id` int DEFAULT NULL,
  `salon_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categoria_productos_pivs` (
  `id` bigint UNSIGNED NOT NULL,
  `categoria_producto_id` bigint UNSIGNED NOT NULL,
  `producto_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categoria_servicios` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `salon_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categoria_servicios_pivs` (
  `id` bigint UNSIGNED NOT NULL,
  `categoria_servicio_id` bigint UNSIGNED NOT NULL,
  `servicio_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `citas` (
  `id` bigint UNSIGNED NOT NULL,
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  `generated_points` decimal(10,2) DEFAULT NULL,
  `status` enum('Agendada','Cancelada','Pagada','Confirmada','Pendiente','Bloqueo') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Agendada',
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `salon_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `tax_data_id` bigint UNSIGNED DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `disccount` decimal(10,2) NOT NULL,
  `remember` tinyint(1) NOT NULL,
  `billing` tinyint(1) DEFAULT '0',
  `billing_description` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motivoCancelacion` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `end_real` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `clientes` (
  `id` bigint UNSIGNED NOT NULL,
  `first_name` varchar(35) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(35) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `email` varchar(65) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `want_custom_messages` tinyint(1) DEFAULT '0',
  `want_offers` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `platform_id` int DEFAULT NULL,
  `categoria_cliente_id` bigint UNSIGNED DEFAULT NULL,
  `sexo` enum('masculino','femenino','noBinario') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `procedencia_id` bigint UNSIGNED DEFAULT NULL,
  `postcode` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `salon_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `form_answered` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `comisions` (
  `id` bigint UNSIGNED NOT NULL,
  `empleado_id` bigint UNSIGNED NOT NULL,
  `qty_p` float DEFAULT NULL,
  `qty_s` float DEFAULT NULL,
  `type_comission_p` enum('percent','qty') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percent',
  `type_comission_s` enum('percent','qty') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percent',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `compras` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `description` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `purchase_status` enum('Completada','Pendiente','Incompleta','Devolución') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` bigint UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `coupons` (
  `id` bigint UNSIGNED NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value_amount` decimal(20,2) NOT NULL DEFAULT '0.00',
  `expires_at` date DEFAULT NULL,
  `acumulable` tinyint(1) NOT NULL,
  `asignacion_venta_id` bigint UNSIGNED DEFAULT NULL,
  `redeemed` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `detalles_puntos_ventas` (
  `id` bigint UNSIGNED NOT NULL,
  `cliente_id` bigint UNSIGNED NOT NULL,
  `venta_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `detalle_compras` (
  `id` bigint UNSIGNED NOT NULL,
  `purchase_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `gross_price` decimal(10,2) NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `quantity` int NOT NULL,
  `recived_quantity` int NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `empleados` (
  `id` bigint UNSIGNED NOT NULL,
  `first_name` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color_preset` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#E2BBB4',
  `email` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `visible` tinyint(1) DEFAULT '1',
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `salon_id` bigint UNSIGNED NOT NULL DEFAULT '0',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `encuestas` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `salon_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `entradas` (
  `id` bigint UNSIGNED NOT NULL,
  `qty` int NOT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `iva` enum('0.16','0.08','0') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `producto_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `salon_id` bigint UNSIGNED NOT NULL,
  `marca_id` bigint UNSIGNED DEFAULT NULL,
  `description` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `folio_fiscal` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `folio_interno` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `etiquetas_citas` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '#E2BBB4',
  `salon_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `etiquetas_citas_pivs` (
  `id` bigint UNSIGNED NOT NULL,
  `etiquetas_cita_id` bigint UNSIGNED NOT NULL,
  `cita_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `excepcion_cat_clientes` (
  `id` bigint UNSIGNED NOT NULL,
  `type_comission` enum('percent','qty') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percent',
  `qty` decimal(10,2) NOT NULL DEFAULT '0.00',
  `programa_recompensa_id` bigint UNSIGNED NOT NULL,
  `categoria_cliente_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `excepcion_cat_productos` (
  `id` bigint UNSIGNED NOT NULL,
  `type_comission` enum('percent','qty') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percent',
  `qty` decimal(10,2) NOT NULL,
  `comision_id` bigint UNSIGNED DEFAULT NULL,
  `programa_recompensa_id` bigint UNSIGNED DEFAULT NULL,
  `categoria_producto_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `excepcion_cat_servicios` (
  `id` bigint UNSIGNED NOT NULL,
  `type_comission` enum('percent','qty') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percent',
  `qty` decimal(10,2) NOT NULL,
  `comision_id` bigint UNSIGNED DEFAULT NULL,
  `programa_recompensa_id` bigint UNSIGNED DEFAULT NULL,
  `categoria_servicio_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `excepcion_clientes` (
  `id` bigint UNSIGNED NOT NULL,
  `type_comission` enum('percent','qty') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percent',
  `qty` decimal(10,2) NOT NULL DEFAULT '0.00',
  `programa_recompensa_id` bigint UNSIGNED NOT NULL,
  `cliente_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `excepcion_productos` (
  `id` bigint UNSIGNED NOT NULL,
  `type_comission` enum('percent','qty') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percent',
  `qty` decimal(10,2) NOT NULL,
  `comision_id` bigint UNSIGNED DEFAULT NULL,
  `programa_recompensa_id` bigint UNSIGNED DEFAULT NULL,
  `producto_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `excepcion_producto_pivs` (
  `id` bigint UNSIGNED NOT NULL,
  `comision_id` bigint UNSIGNED NOT NULL,
  `excepcion_producto_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `excepcion_servicios` (
  `id` bigint UNSIGNED NOT NULL,
  `type_comission` enum('percent','qty') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percent',
  `qty` decimal(10,2) NOT NULL,
  `comision_id` bigint UNSIGNED DEFAULT NULL,
  `programa_recompensa_id` bigint UNSIGNED DEFAULT NULL,
  `servicio_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `excepcion_servicio_pivs` (
  `id` bigint UNSIGNED NOT NULL,
  `comision_id` bigint UNSIGNED NOT NULL,
  `excepcion_servicio_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `files` (
  `id` bigint UNSIGNED NOT NULL,
  `model_id` int NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `gastos` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `type` enum('Acreditable','No acreditable') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `folio_fiscal` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `iva` enum('0.16','0.08','0') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('vigente','cancelado','default') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default',
  `salon_id` bigint UNSIGNED NOT NULL,
  `marca_id` bigint UNSIGNED DEFAULT NULL,
  `categoria_id` bigint UNSIGNED DEFAULT NULL,
  `tipo_id` bigint UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `integrations` (
  `id` bigint UNSIGNED NOT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `platform` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `ip` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `out` binary(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `marcas` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rfc` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `salon_id` bigint UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `materials` (
  `id` bigint UNSIGNED NOT NULL,
  `asignacion_id` bigint UNSIGNED DEFAULT NULL,
  `producto_id` bigint UNSIGNED NOT NULL,
  `qty` int DEFAULT '1',
  `sale_price` decimal(10,2) DEFAULT NULL,
  `salon_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `empleado_id` bigint UNSIGNED DEFAULT NULL,
  `cliente_id` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `metodo_pagos` (
  `id` bigint UNSIGNED NOT NULL,
  `Payment_method` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `salon_id` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `metodo_pago_compras` (
  `id` bigint UNSIGNED NOT NULL,
  `purchase_id` bigint UNSIGNED NOT NULL,
  `payment_method_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `metodo_pago_cortes` (
  `id` bigint UNSIGNED NOT NULL,
  `caja_corte_id` bigint UNSIGNED NOT NULL,
  `payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qty` decimal(20,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `is_real` binary(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `metodo_pago_gastos` (
  `id` bigint UNSIGNED NOT NULL,
  `gasto_id` bigint UNSIGNED NOT NULL,
  `Payment_method` enum('Efectivo','Card','Banorte') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(20,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `metodo_pago_servicios` (
  `id` bigint UNSIGNED NOT NULL,
  `cita_id` bigint UNSIGNED NOT NULL,
  `payment_method_id` bigint UNSIGNED DEFAULT '1',
  `tipo` enum('Cantidad','Porcentaje') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(20,2) NOT NULL,
  `change` decimal(20,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `metodo_pago_ventas` (
  `id` bigint UNSIGNED NOT NULL,
  `venta_id` bigint UNSIGNED NOT NULL,
  `payment_method_id` bigint UNSIGNED DEFAULT '1',
  `tipo` enum('Cantidad','Porcentaje') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(20,2) NOT NULL,
  `change` decimal(20,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `metodo_propina_cortes` (
  `id` bigint UNSIGNED NOT NULL,
  `caja_corte_id` bigint UNSIGNED NOT NULL,
  `payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qty` decimal(20,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `is_real` binary(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `preguntas` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `encuesta_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `procedencias` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `salon_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `productos` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `iva` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_product` enum('simple','variable') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gross_price` decimal(10,2) NOT NULL,
  `disccount_price` decimal(10,2) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `unit_type` enum('Unidad','Mililitro','Ampolleta','Artículo','Onza','Gramo','Envase') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Unidad',
  `stock_qty` int DEFAULT NULL,
  `min_stock` int DEFAULT NULL,
  `sku` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `intern_sku` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reward_points` decimal(10,2) DEFAULT NULL,
  `status` enum('publish','pending','draft') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `visibility` enum('visible','hide') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `stock_status` enum('instock','outofstock','onbackorder') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `manage_stock` tinyint NOT NULL DEFAULT '1',
  `platform_id` int DEFAULT NULL,
  `brand_id` bigint UNSIGNED DEFAULT NULL,
  `salon_id` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `producto_asignaciones` (
  `id` bigint UNSIGNED NOT NULL,
  `date_id` bigint UNSIGNED NOT NULL,
  `sales_id` bigint UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `programa_recompensas` (
  `id` bigint UNSIGNED NOT NULL,
  `salon_id` bigint UNSIGNED NOT NULL,
  `qty_p` int DEFAULT NULL,
  `qty_s` int DEFAULT NULL,
  `type_comission_p` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percent',
  `type_comission_s` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percent',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `propinas` (
  `id` bigint UNSIGNED NOT NULL,
  `cita_id` bigint UNSIGNED DEFAULT NULL,
  `empleado_id` bigint UNSIGNED DEFAULT NULL,
  `venta_id` bigint UNSIGNED DEFAULT NULL,
  `payment_method_id` bigint UNSIGNED DEFAULT '1',
  `reference` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(20,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `recompensas_cat_clientes` (
  `id` bigint UNSIGNED NOT NULL,
  `program_id` bigint UNSIGNED DEFAULT NULL,
  `client_category_id` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `recompensas_cat_productos` (
  `id` bigint UNSIGNED NOT NULL,
  `recompensas_producto_id` bigint UNSIGNED DEFAULT NULL,
  `categoria_producto_id` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `recompensas_cat_servicios` (
  `id` bigint UNSIGNED NOT NULL,
  `recompensas_servicio_id` bigint UNSIGNED DEFAULT NULL,
  `categoria_servicio_id` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `recompensas_productos` (
  `id` bigint UNSIGNED NOT NULL,
  `to_all` tinyint(1) DEFAULT '0',
  `percent` decimal(10,2) NOT NULL,
  `salon_id` bigint UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `recompensas_servicios` (
  `id` bigint UNSIGNED NOT NULL,
  `to_all` tinyint(1) DEFAULT '0',
  `percent` decimal(10,2) NOT NULL,
  `salon_id` bigint UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `respuestas` (
  `id` bigint UNSIGNED NOT NULL,
  `eleccion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cliente_id` bigint UNSIGNED DEFAULT NULL,
  `pregunta_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `salons` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(65) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `webPage` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `youtube` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tiktok` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logoFile` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rfc` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start` time DEFAULT NULL,
  `end` time DEFAULT NULL,
  `simulador` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `servicios` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `iva` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gross_price` decimal(10,2) NOT NULL,
  `disccount_price` decimal(10,2) DEFAULT NULL,
  `reward_points` decimal(10,2) DEFAULT NULL,
  `duration` int NOT NULL DEFAULT '15',
  `visibility` enum('hide','visible') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'visible',
  `salon_id` bigint UNSIGNED DEFAULT NULL,
  `brand_id` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tarjetas_puntos` (
  `id` bigint UNSIGNED NOT NULL,
  `intern_barcode` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `balance` decimal(20,2) DEFAULT NULL,
  `cliente_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tax_datas` (
  `id` bigint UNSIGNED NOT NULL,
  `company_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_system` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rfc` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postcode` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cliente_id` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tax_data_clientes` (
  `id` bigint UNSIGNED NOT NULL,
  `cliente_id` bigint UNSIGNED NOT NULL,
  `tax_data_id` bigint UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tipo_gastos` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `salon_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','employee','estilista','recepcionista') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `salon_id` bigint UNSIGNED NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ventas` (
  `id` bigint UNSIGNED NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `items` int NOT NULL,
  `disccount` decimal(10,2) NOT NULL,
  `generated_points` decimal(10,2) DEFAULT NULL,
  `status` enum('Cancelada','Pagada','Devuelto','Pendiente') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pagada',
  `billing` tinyint(1) NOT NULL DEFAULT '0',
  `billing_description` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `tax_data_id` bigint UNSIGNED DEFAULT NULL,
  `salon_id` bigint UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `walogs` (
  `id` bigint UNSIGNED NOT NULL,
  `uid` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sent` tinyint(1) DEFAULT '1',
  `answered` tinyint(1) DEFAULT NULL,
  `cita_id` bigint UNSIGNED NOT NULL,
  `venta_id` bigint UNSIGNED NOT NULL,
  `type` enum('confirmar_cita','enviar_ticket','ofrecer_producto','ofrecer_servicio','enviar_encuesta','error') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE `abonos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `abonos_cita_id_foreign` (`cita_id`),
  ADD KEY `abonos_venta_id_foreign` (`venta_id`),
  ADD KEY `abonos_payment_method_id_foreign` (`payment_method_id`);

ALTER TABLE `abono_propinas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `abono_propinas_cita_id_foreign` (`cita_id`),
  ADD KEY `abono_propinas_venta_id_foreign` (`venta_id`),
  ADD KEY `abono_propinas_payment_method_id_foreign` (`payment_method_id`);

ALTER TABLE `asignacion_servicios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asignacion_servicios_selected_service_foreign` (`selected_service`),
  ADD KEY `asignacion_servicios_cita_id_foreign` (`cita_id`),
  ADD KEY `asignacion_servicios_empleado_id_foreign` (`empleado_id`);

ALTER TABLE `asignacion_ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asignacion_ventas_selected_item_foreign` (`selected_item`),
  ADD KEY `asignacion_ventas_cita_id_foreign` (`cita_id`),
  ADD KEY `asignacion_ventas_venta_id_foreign` (`venta_id`),
  ADD KEY `asignacion_ventas_empleado_id_foreign` (`empleado_id`);

ALTER TABLE `bloqueos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bloqueos_empleado_id_foreign` (`empleado_id`) USING BTREE,
  ADD KEY `bloqueos_salon_id_foreign` (`salon_id`) USING BTREE;

ALTER TABLE `caja_aperturas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `caja_aperturas_caja_corte_id_unique` (`caja_corte_id`),
  ADD KEY `caja_aperturas_user_id_foreign` (`user_id`);

ALTER TABLE `caja_cortes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `caja_cortes_user_id_foreign` (`user_id`);

ALTER TABLE `calificacion_cliente_empleados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `calificacion_cliente_empleados_cliente_id_foreign` (`cliente_id`),
  ADD KEY `calificacion_cliente_empleados_empleado_id_foreign` (`empleado_id`);

ALTER TABLE `calificacion_empleado_clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `calificacion_empleado_clientes_cliente_id_foreign` (`cliente_id`),
  ADD KEY `calificacion_empleado_clientes_user_id_foreign` (`user_id`);

ALTER TABLE `categoria_clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_clientes_salon_id` (`salon_id`) USING BTREE;

ALTER TABLE `categoria_clientes_pivs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_clientes_pivs_categoria_cliente_id_foreign` (`categoria_cliente_id`),
  ADD KEY `categoria_clientes_pivs_cliente_id_foreign` (`cliente_id`);

ALTER TABLE `categoria_gastos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_gastos_salon_id_foreign` (`salon_id`);

ALTER TABLE `categoria_productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_productos_salon_id_foreign` (`salon_id`);

ALTER TABLE `categoria_productos_pivs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_productos_pivs_categoria_producto_id_foreign` (`categoria_producto_id`),
  ADD KEY `categoria_productos_pivs_producto_id_foreign` (`producto_id`);

ALTER TABLE `categoria_servicios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_servicios_salon_id_foreign` (`salon_id`);

ALTER TABLE `categoria_servicios_pivs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_servicios_pivs_categoria_servicio_id_foreign` (`categoria_servicio_id`),
  ADD KEY `categoria_servicios_pivs_servicio_id_foreign` (`servicio_id`);

ALTER TABLE `citas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `citas_customer_id_foreign` (`customer_id`),
  ADD KEY `citas_salon_id_foreign` (`salon_id`),
  ADD KEY `citas_user_id_foreign` (`user_id`),
  ADD KEY `citas_tax_data_id_foreign` (`tax_data_id`) USING BTREE;

ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clientes_categoria_cliente_id_foreign` (`categoria_cliente_id`),
  ADD KEY `clientes_salon_id_foreign` (`salon_id`),
  ADD KEY `clientes_procedencia_id_foreign` (`procedencia_id`) USING BTREE;

ALTER TABLE `comisions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comisions_empleado_id_foreign` (`empleado_id`);

ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `compras_user_id_foreign` (`user_id`),
  ADD KEY `compras_payment_method_foreign` (`payment_method`);

ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `coupons_asignacion_venta_id_foreign` (`asignacion_venta_id`);

ALTER TABLE `detalles_puntos_ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detalles_puntos_ventas_cliente_id_foreign` (`cliente_id`),
  ADD KEY `detalles_puntos_ventas_venta_id_foreign` (`venta_id`);

ALTER TABLE `detalle_compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detalle_compras_purchase_id_foreign` (`purchase_id`),
  ADD KEY `detalle_compras_product_id_foreign` (`product_id`);

ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `empleados_email_unique` (`email`),
  ADD UNIQUE KEY `empleados_phone_number_unique` (`phone_number`),
  ADD UNIQUE KEY `empleados_user_id_unique` (`user_id`),
  ADD KEY `empleados_salon_id_foreign` (`salon_id`);

ALTER TABLE `encuestas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `encuesta_salon_id_foreign` (`salon_id`);

ALTER TABLE `entradas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entradas_producto_id_foreign` (`producto_id`),
  ADD KEY `entradas_user_id_foreign` (`user_id`),
  ADD KEY `entradas_salon_id_foreign` (`salon_id`),
  ADD KEY `entradas_marca_id_foreign` (`marca_id`);

ALTER TABLE `etiquetas_citas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `etiquetas_citas_salon_id_foreign` (`salon_id`);

ALTER TABLE `etiquetas_citas_pivs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `etiquetas_citas_pivs_etiquetas_cita_id_foreign` (`etiquetas_cita_id`),
  ADD KEY `etiquetas_citas_pivs_cita_id_foreign` (`cita_id`);

ALTER TABLE `excepcion_cat_clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `excepcion_cat_clientes_programa_recompensa_id_foreign` (`programa_recompensa_id`) USING BTREE,
  ADD KEY `excepcion_cat_clientes_categoria_cliente_id_foreign` (`categoria_cliente_id`) USING BTREE;

ALTER TABLE `excepcion_cat_productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `excepcion_cat_productos_comision_id_foreign` (`comision_id`),
  ADD KEY `excepcion_cat_productos_categoria_producto_id_foreign` (`categoria_producto_id`),
  ADD KEY `excepcion_cat_productos_programa_recompensa_id_foreign` (`programa_recompensa_id`) USING BTREE;

ALTER TABLE `excepcion_cat_servicios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `excepcion_cat_servicios_categoria_servicio_id_foreign` (`categoria_servicio_id`),
  ADD KEY `excepcion_cat_servicios_comision_id_foreign` (`comision_id`),
  ADD KEY `excepcion_cat_servicios_programa_recompensa_id_foreign` (`programa_recompensa_id`) USING BTREE;

ALTER TABLE `excepcion_clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `excepcion_clientes_programa_recompensa_id_foreign` (`programa_recompensa_id`) USING BTREE,
  ADD KEY `excepcion_clientes_cliente_id_foreign` (`cliente_id`) USING BTREE;

ALTER TABLE `excepcion_productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `excepcion_productos_comision_id_foreign` (`comision_id`),
  ADD KEY `excepcion_productos_producto_id_foreign` (`producto_id`),
  ADD KEY `excepcion_productos_programa_recompensa_id_foreign` (`programa_recompensa_id`) USING BTREE;

ALTER TABLE `excepcion_producto_pivs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `excepcion_producto_pivs_comision_id_foreign` (`comision_id`),
  ADD KEY `excepcion_producto_pivs_excepcion_producto_id_foreign` (`excepcion_producto_id`);

ALTER TABLE `excepcion_servicios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `excepcion_servicios_comision_id_foreign` (`comision_id`),
  ADD KEY `excepcion_servicios_servicio_id_foreign` (`servicio_id`),
  ADD KEY `excepcion_servicios_programa_recompensa_id_foreign` (`programa_recompensa_id`) USING BTREE;

ALTER TABLE `excepcion_servicio_pivs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `excepcion_servicio_pivs_comision_id_foreign` (`comision_id`),
  ADD KEY `excepcion_servicio_pivs_excepcion_servicio_id_foreign` (`excepcion_servicio_id`);

ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `gastos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gastos_user_id_foreign` (`user_id`),
  ADD KEY `gastos_salon_id_foreign` (`salon_id`),
  ADD KEY `Índice 4` (`marca_id`),
  ADD KEY `gastos_categoria_id_foreign` (`categoria_id`) USING BTREE,
  ADD KEY `gastos_tipo_id_foreign` (`tipo_id`) USING BTREE;

ALTER TABLE `integrations`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `logs_user_id_foreign` (`user_id`);

ALTER TABLE `marcas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `marcas_salon_id_foreign` (`salon_id`);

ALTER TABLE `materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `materials_asignacion_id_foreign` (`asignacion_id`),
  ADD KEY `materials_producto_id_foreign` (`producto_id`),
  ADD KEY `materials_salon_id_foreign` (`salon_id`),
  ADD KEY `materials_user_id_foreign` (`user_id`),
  ADD KEY `materials_empleado_id_foreign` (`empleado_id`),
  ADD KEY `materials_cliente_id_foreign` (`cliente_id`);

ALTER TABLE `metodo_pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `metodo_pagos_salons_id` (`salon_id`);

ALTER TABLE `metodo_pago_compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `metodo_pago_compras_purchase_id_foreign` (`purchase_id`),
  ADD KEY `metodo_pago_compras_payment_method_id_foreign` (`payment_method_id`);

ALTER TABLE `metodo_pago_cortes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `metodo_pago_cortes_caja_corte_id_foreign` (`caja_corte_id`);

ALTER TABLE `metodo_pago_gastos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `metodo_pago_gastos_gasto_id_foreign` (`gasto_id`);

ALTER TABLE `metodo_pago_servicios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `metodo_pago_servicios_cita_id_foreign` (`cita_id`),
  ADD KEY `metodo_pago_servicios_payment_method_id_foreign` (`payment_method_id`) USING BTREE;

ALTER TABLE `metodo_pago_ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `metodo_pago_ventas_venta_id_foreign` (`venta_id`),
  ADD KEY `metodo_pago_ventas_payment_method_id_foreign` (`payment_method_id`) USING BTREE;

ALTER TABLE `metodo_propina_cortes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `metodo_propina_cortes_caja_corte_id_foreign` (`caja_corte_id`);

ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

ALTER TABLE `preguntas`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `procedencias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `procedencias_salon_id_foreign` (`salon_id`);

ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `productos_brand_id_foreign` (`brand_id`),
  ADD KEY `productos_salon_id_foreign` (`salon_id`);

ALTER TABLE `producto_asignaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_asignaciones_date_id_foreign` (`date_id`),
  ADD KEY `producto_asignaciones_sales_id_foreign` (`sales_id`);

ALTER TABLE `programa_recompensas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `programa_recompensas_salon_id_foreign` (`salon_id`) USING BTREE;

ALTER TABLE `propinas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `propinas_cita_id_foreign` (`cita_id`),
  ADD KEY `propinas_empleado_id_foreign` (`empleado_id`),
  ADD KEY `propinas_venta_id_foreign` (`venta_id`),
  ADD KEY `propinas_payment_method_id_foreign` (`payment_method_id`) USING BTREE;

ALTER TABLE `recompensas_cat_clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recompensas_cat_clientes_program_id_foreign` (`program_id`),
  ADD KEY `recompensas_cat_clientes_client_category_id_foreign` (`client_category_id`);

ALTER TABLE `recompensas_cat_productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recompensas_cat_productos_recompensas_producto_id_foreign` (`recompensas_producto_id`),
  ADD KEY `recompensas_cat_productos_categoria_producto_id_foreign` (`categoria_producto_id`);

ALTER TABLE `recompensas_cat_servicios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recompensas_cat_servicios_recompensas_servicio_id_foreign` (`recompensas_servicio_id`),
  ADD KEY `recompensas_cat_servicios_categoria_servicio_id_foreign` (`categoria_servicio_id`);

ALTER TABLE `recompensas_productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recompensas_productos_salon_id_foreign` (`salon_id`);

ALTER TABLE `recompensas_servicios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recompensas_servicios_salon_id_foreign` (`salon_id`);

ALTER TABLE `respuestas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `respuestas_cliente_id_foreign` (`cliente_id`),
  ADD KEY `respuestas_pregunta_id_foreign` (`pregunta_id`);

ALTER TABLE `salons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `salons_email_unique` (`email`),
  ADD UNIQUE KEY `salons_phone_unique` (`phone`);

ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `servicios_salon_id_foreign` (`salon_id`),
  ADD KEY `servicios_brand_id_foreign` (`brand_id`) USING BTREE;

ALTER TABLE `tarjetas_puntos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tarjetas_puntos_cliente_id_unique` (`cliente_id`);

ALTER TABLE `tax_datas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tax_datas_cliente_id_foreign` (`cliente_id`) USING BTREE;

ALTER TABLE `tax_data_clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entrega_clientes_delivery_id_foreign` (`tax_data_id`) USING BTREE,
  ADD KEY `entrega_clientes_client_id_foreign` (`cliente_id`) USING BTREE;

ALTER TABLE `tipo_gastos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tipo_gastos_salon_id_foreign` (`salon_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_salon_id_foreign` (`salon_id`);

ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ventas_customer_id_foreign` (`customer_id`),
  ADD KEY `ventas_user_id_foreign` (`user_id`),
  ADD KEY `ventas_salon_id_foreign` (`salon_id`),
  ADD KEY `ventas_tax_data_id_foreign` (`tax_data_id`) USING BTREE;

ALTER TABLE `walogs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `walogs_cita_id_foreign` (`cita_id`),
  ADD KEY `walogs_venta_id_foreign` (`venta_id`);


ALTER TABLE `abonos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `abono_propinas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `asignacion_servicios`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `asignacion_ventas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `bloqueos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `caja_aperturas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `caja_cortes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `calificacion_cliente_empleados`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `calificacion_empleado_clientes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `categoria_clientes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `categoria_clientes_pivs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `categoria_gastos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `categoria_productos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `categoria_productos_pivs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `categoria_servicios`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `categoria_servicios_pivs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `citas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `clientes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `comisions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `compras`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `coupons`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `detalles_puntos_ventas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `detalle_compras`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `empleados`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `encuestas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `entradas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `etiquetas_citas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `etiquetas_citas_pivs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `excepcion_cat_clientes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `excepcion_cat_productos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `excepcion_cat_servicios`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `excepcion_clientes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `excepcion_productos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `excepcion_producto_pivs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `excepcion_servicios`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `excepcion_servicio_pivs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `files`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `gastos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `integrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `marcas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `materials`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `metodo_pagos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `metodo_pago_compras`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `metodo_pago_cortes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `metodo_pago_gastos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `metodo_pago_servicios`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `metodo_pago_ventas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `metodo_propina_cortes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `preguntas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `procedencias`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `productos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `producto_asignaciones`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `programa_recompensas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `propinas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `recompensas_cat_clientes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `recompensas_cat_productos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `recompensas_cat_servicios`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `recompensas_productos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `recompensas_servicios`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `respuestas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `salons`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `servicios`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `tarjetas_puntos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `tax_datas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `tax_data_clientes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `tipo_gastos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `ventas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `walogs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `abonos`
  ADD CONSTRAINT `abonos_cita_id_foreign` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`),
  ADD CONSTRAINT `abonos_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `metodo_pagos` (`id`),
  ADD CONSTRAINT `abonos_venta_id_foreign` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`);

ALTER TABLE `abono_propinas`
  ADD CONSTRAINT `abono_propinas_cita_id_foreign` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`),
  ADD CONSTRAINT `abono_propinas_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `metodo_pagos` (`id`),
  ADD CONSTRAINT `abono_propinas_venta_id_foreign` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`);

ALTER TABLE `asignacion_servicios`
  ADD CONSTRAINT `asignacion_servicios_cita_id_foreign` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `asignacion_servicios_empleado_id_foreign` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `asignacion_servicios_selected_service_foreign` FOREIGN KEY (`selected_service`) REFERENCES `servicios` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `asignacion_ventas`
  ADD CONSTRAINT `asignacion_ventas_cita_id_foreign` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `asignacion_ventas_empleado_id_foreign` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `asignacion_ventas_selected_item_foreign` FOREIGN KEY (`selected_item`) REFERENCES `productos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `asignacion_ventas_venta_id_foreign` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `bloqueos`
  ADD CONSTRAINT `bloqueos_empleado_id_foreign` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `bloqueos_salon_id_foreign` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `caja_aperturas`
  ADD CONSTRAINT `caja_aperturas_caja_corte_id_foreign` FOREIGN KEY (`caja_corte_id`) REFERENCES `caja_cortes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `caja_aperturas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `caja_cortes`
  ADD CONSTRAINT `caja_cortes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `calificacion_cliente_empleados`
  ADD CONSTRAINT `calificacion_cliente_empleados_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `calificacion_cliente_empleados_empleado_id_foreign` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`);

ALTER TABLE `calificacion_empleado_clientes`
  ADD CONSTRAINT `calificacion_empleado_clientes_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `calificacion_empleado_clientes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `categoria_clientes`
  ADD CONSTRAINT `categoria_clientes_salon_id_foreign` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`);

ALTER TABLE `categoria_clientes_pivs`
  ADD CONSTRAINT `categoria_clientes_pivs_categoria_cliente_id_foreign` FOREIGN KEY (`categoria_cliente_id`) REFERENCES `categoria_clientes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `categoria_clientes_pivs_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `categoria_gastos`
  ADD CONSTRAINT `categoria_gastos_salon_id_foreign` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`);

ALTER TABLE `categoria_productos`
  ADD CONSTRAINT `categoria_productos_salon_id_foreign` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `categoria_productos_pivs`
  ADD CONSTRAINT `categoria_productos_pivs_categoria_producto_id_foreign` FOREIGN KEY (`categoria_producto_id`) REFERENCES `categoria_productos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `categoria_productos_pivs_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `categoria_servicios`
  ADD CONSTRAINT `categoria_servicios_salon_id_foreign` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `categoria_servicios_pivs`
  ADD CONSTRAINT `categoria_servicios_pivs_categoria_servicio_id_foreign` FOREIGN KEY (`categoria_servicio_id`) REFERENCES `categoria_servicios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `categoria_servicios_pivs_servicio_id_foreign` FOREIGN KEY (`servicio_id`) REFERENCES `servicios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `citas`
  ADD CONSTRAINT `citas_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `citas_salon_id_foreign` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `citas_tax_data_id_foreign` FOREIGN KEY (`tax_data_id`) REFERENCES `tax_datas` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `citas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_categoria_cliente_id_foreign` FOREIGN KEY (`categoria_cliente_id`) REFERENCES `categoria_clientes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `clientes_procedencia_id_foreign` FOREIGN KEY (`procedencia_id`) REFERENCES `procedencias` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `clientes_salon_id_foreign` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `comisions`
  ADD CONSTRAINT `comisions_empleado_id_foreign` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `compras`
  ADD CONSTRAINT `compras_payment_method_foreign` FOREIGN KEY (`payment_method`) REFERENCES `metodo_pagos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `compras_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `coupons`
  ADD CONSTRAINT `coupons_asignacion_venta_id_foreign` FOREIGN KEY (`asignacion_venta_id`) REFERENCES `asignacion_ventas` (`id`);

ALTER TABLE `detalles_puntos_ventas`
  ADD CONSTRAINT `detalles_puntos_ventas_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `detalles_puntos_ventas_venta_id_foreign` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `detalle_compras`
  ADD CONSTRAINT `detalle_compras_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `productos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_compras_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `compras` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `empleados`
  ADD CONSTRAINT `empleados_salon_id_foreign` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `empleados_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `encuestas`
  ADD CONSTRAINT `encuesta_salon_id_foreign` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`);

ALTER TABLE `entradas`
  ADD CONSTRAINT `entradas_marca_id_foreign` FOREIGN KEY (`marca_id`) REFERENCES `marcas` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `entradas_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `entradas_salon_id_foreign` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `entradas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

ALTER TABLE `etiquetas_citas`
  ADD CONSTRAINT `etiquetas_citas_salon_id_foreign` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`);

ALTER TABLE `etiquetas_citas_pivs`
  ADD CONSTRAINT `etiquetas_citas_pivs_cita_id_foreign` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`),
  ADD CONSTRAINT `etiquetas_citas_pivs_etiquetas_cita_id_foreign` FOREIGN KEY (`etiquetas_cita_id`) REFERENCES `etiquetas_citas` (`id`);

ALTER TABLE `excepcion_cat_clientes`
  ADD CONSTRAINT `excepcion_cat_clientes_categoria_cliente_id` FOREIGN KEY (`categoria_cliente_id`) REFERENCES `categoria_clientes` (`id`),
  ADD CONSTRAINT `excepcion_cat_clientes_programa_recompensa_id` FOREIGN KEY (`programa_recompensa_id`) REFERENCES `programa_recompensas` (`id`);

ALTER TABLE `excepcion_cat_productos`
  ADD CONSTRAINT `excepcion_cat_productos_categoria_producto_id_foreign` FOREIGN KEY (`categoria_producto_id`) REFERENCES `categoria_productos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `excepcion_cat_productos_comision_id_foreign` FOREIGN KEY (`comision_id`) REFERENCES `comisions` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `excepcion_cat_productos_programa_recompensa_id_foreign` FOREIGN KEY (`programa_recompensa_id`) REFERENCES `programa_recompensas` (`id`);

ALTER TABLE `excepcion_cat_servicios`
  ADD CONSTRAINT `excepcion_cat_servicios_categoria_servicio_id_foreign` FOREIGN KEY (`categoria_servicio_id`) REFERENCES `categoria_servicios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `excepcion_cat_servicios_comision_id_foreign` FOREIGN KEY (`comision_id`) REFERENCES `comisions` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `excepcion_cat_servicios_programa_recompensa_id_foreign` FOREIGN KEY (`programa_recompensa_id`) REFERENCES `programa_recompensas` (`id`);

ALTER TABLE `excepcion_clientes`
  ADD CONSTRAINT `excepcion_clientes_cliente_id` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `excepcion_clientes_programa_recompensa_id` FOREIGN KEY (`programa_recompensa_id`) REFERENCES `programa_recompensas` (`id`);

ALTER TABLE `excepcion_productos`
  ADD CONSTRAINT `excepcion_productos_comision_id_foreign` FOREIGN KEY (`comision_id`) REFERENCES `comisions` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `excepcion_productos_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `excepcion_productos_programa_recompensa_id_foreign` FOREIGN KEY (`programa_recompensa_id`) REFERENCES `programa_recompensas` (`id`);

ALTER TABLE `excepcion_producto_pivs`
  ADD CONSTRAINT `excepcion_producto_pivs_comision_id_foreign` FOREIGN KEY (`comision_id`) REFERENCES `comisions` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `excepcion_producto_pivs_excepcion_producto_id_foreign` FOREIGN KEY (`excepcion_producto_id`) REFERENCES `excepcion_productos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `excepcion_servicios`
  ADD CONSTRAINT `excepcion_servicios_comision_id_foreign` FOREIGN KEY (`comision_id`) REFERENCES `comisions` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `excepcion_servicios_programa_recompensa_id_foreign` FOREIGN KEY (`programa_recompensa_id`) REFERENCES `programa_recompensas` (`id`),
  ADD CONSTRAINT `excepcion_servicios_servicio_id_foreign` FOREIGN KEY (`servicio_id`) REFERENCES `servicios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `excepcion_servicio_pivs`
  ADD CONSTRAINT `excepcion_servicio_pivs_comision_id_foreign` FOREIGN KEY (`comision_id`) REFERENCES `comisions` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `excepcion_servicio_pivs_excepcion_servicio_id_foreign` FOREIGN KEY (`excepcion_servicio_id`) REFERENCES `excepcion_servicios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `gastos`
  ADD CONSTRAINT `gastos_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categoria_gastos` (`id`),
  ADD CONSTRAINT `gastos_marca_id_foreign` FOREIGN KEY (`marca_id`) REFERENCES `marcas` (`id`),
  ADD CONSTRAINT `gastos_salon_id_foreign` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `gastos_tipo_id_foreign` FOREIGN KEY (`tipo_id`) REFERENCES `tipo_gastos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `gastos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `logs`
  ADD CONSTRAINT `logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `marcas`
  ADD CONSTRAINT `marcas_salon_id_foreign` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `materials`
  ADD CONSTRAINT `materials_asignacion_id_foreign` FOREIGN KEY (`asignacion_id`) REFERENCES `asignacion_servicios` (`id`),
  ADD CONSTRAINT `materials_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `materials_empleado_id_foreign` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`),
  ADD CONSTRAINT `materials_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `materials_salon_id_foreign` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`),
  ADD CONSTRAINT `materials_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `metodo_pagos`
  ADD CONSTRAINT `metodo_pagos_salons_id` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`);

ALTER TABLE `metodo_pago_compras`
  ADD CONSTRAINT `metodo_pago_compras_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `metodo_pagos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `metodo_pago_compras_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `compras` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `metodo_pago_cortes`
  ADD CONSTRAINT `metodo_pago_cortes_caja_corte_id_foreign` FOREIGN KEY (`caja_corte_id`) REFERENCES `caja_cortes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `metodo_pago_gastos`
  ADD CONSTRAINT `metodo_pago_gastos_gasto_id_foreign` FOREIGN KEY (`gasto_id`) REFERENCES `gastos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `metodo_pago_servicios`
  ADD CONSTRAINT `metodo_pago_servicios_cita_id_foreign` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `metodo_pago_servicios_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `metodo_pagos` (`id`) ON UPDATE CASCADE;

ALTER TABLE `metodo_pago_ventas`
  ADD CONSTRAINT `metodo_pago_ventas_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `metodo_pagos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `metodo_pago_ventas_venta_id_foreign` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `metodo_propina_cortes`
  ADD CONSTRAINT `metodo_propina_cortes_caja_corte_id_foreign` FOREIGN KEY (`caja_corte_id`) REFERENCES `caja_cortes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `procedencias`
  ADD CONSTRAINT `procedencias_salon_id_foreign` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`);

ALTER TABLE `productos`
  ADD CONSTRAINT `productos_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `marcas` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `productos_salon_id_foreign` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `producto_asignaciones`
  ADD CONSTRAINT `producto_asignaciones_date_id_foreign` FOREIGN KEY (`date_id`) REFERENCES `citas` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `producto_asignaciones_sales_id_foreign` FOREIGN KEY (`sales_id`) REFERENCES `asignacion_ventas` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `programa_recompensas`
  ADD CONSTRAINT `programa_recompensas_salon_id_foreign` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `propinas`
  ADD CONSTRAINT `propinas_cita_id_foreign` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `propinas_empleado_id_foreign` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `propinas_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `metodo_pagos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `propinas_venta_id_foreign` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `recompensas_cat_clientes`
  ADD CONSTRAINT `recompensas_cat_clientes_client_category_id_foreign` FOREIGN KEY (`client_category_id`) REFERENCES `categoria_clientes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `recompensas_cat_clientes_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programa_recompensas` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `recompensas_cat_productos`
  ADD CONSTRAINT `recompensas_cat_productos_categoria_producto_id_foreign` FOREIGN KEY (`categoria_producto_id`) REFERENCES `categoria_productos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `recompensas_cat_productos_recompensas_producto_id_foreign` FOREIGN KEY (`recompensas_producto_id`) REFERENCES `recompensas_productos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `recompensas_cat_servicios`
  ADD CONSTRAINT `recompensas_cat_servicios_categoria_servicio_id_foreign` FOREIGN KEY (`categoria_servicio_id`) REFERENCES `categoria_servicios` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `recompensas_cat_servicios_recompensas_servicio_id_foreign` FOREIGN KEY (`recompensas_servicio_id`) REFERENCES `recompensas_servicios` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `recompensas_productos`
  ADD CONSTRAINT `recompensas_productos_salon_id_foreign` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `recompensas_servicios`
  ADD CONSTRAINT `recompensas_servicios_salon_id_foreign` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `respuestas`
  ADD CONSTRAINT `respuestas_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `respuestas_pregunta_id_foreign` FOREIGN KEY (`pregunta_id`) REFERENCES `preguntas` (`id`);

ALTER TABLE `servicios`
  ADD CONSTRAINT `servicios_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `marcas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `servicios_salon_id_foreign` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `tarjetas_puntos`
  ADD CONSTRAINT `tarjetas_puntos_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `tax_datas`
  ADD CONSTRAINT `tax_datas_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `tax_data_clientes`
  ADD CONSTRAINT `entrega_clientes_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `entrega_clientes_tax_data_id_foreign` FOREIGN KEY (`tax_data_id`) REFERENCES `tax_datas` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `tipo_gastos`
  ADD CONSTRAINT `tipo_gastos_salon_id_foreign` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`);

ALTER TABLE `users`
  ADD CONSTRAINT `users_salon_id_foreign` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `ventas_salon_id_foreign` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `ventas_tax_data_id_foreign` FOREIGN KEY (`tax_data_id`) REFERENCES `tax_datas` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `ventas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `walogs`
  ADD CONSTRAINT `walogs_cita_id_foreign` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`),
  ADD CONSTRAINT `walogs_venta_id_foreign` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`);
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;