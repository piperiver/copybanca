/*Tabla de log de aprobaciòn de estudios */
CREATE TABLE log_aprobacion (
  id INT(11) NOT NULL,
  estudio_id INT(11),
  user_id INT(11),
  aprobacion INT(11),
  created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FOREIGN KEY (estudio_id) REFERENCES estudios(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
)



/*Tablas para la gestion de procesos juridicos*/

-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 26-07-2018 a las 19:36:24
-- Versión del servidor: 5.6.38
-- Versión de PHP: 7.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de datos: `bancariz_produccion`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `juicios`
--

CREATE TABLE `juicios` (
  `id` int(10) UNSIGNED NOT NULL,
  `idProcesoJuridico` int(10) NOT NULL,
  `ciudad` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `departamento` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `estadoProceso` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `expediente` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fechaInicioProceso` timestamp NULL DEFAULT NULL,
  `fechaUltimoMovimiento` timestamp NULL DEFAULT NULL,
  `IdJuicio` int(20) NOT NULL,
  `instanciaProceso` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nitsActor` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nombresActor` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nitsDemandado` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nombresDemandado` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `numeroJuzgado` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `rangoPretenciones` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tieneGarantias` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tipoDeCausa` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tipoJuzgado` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `procesosjuridicos`
--

CREATE TABLE `procesosjuridicos` (
  `id` int(11) NOT NULL,
  `idValoracion` int(10) NOT NULL,
  `fechaConsulta` timestamp NULL DEFAULT NULL,
  `respuestaWs` text NOT NULL,
  `usuario` int(10) NOT NULL,
  `status` varchar(2) DEFAULT NULL,
  `mensajeError` varchar(1000) DEFAULT NULL,
  `descripcionMensajeError` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `juicios`
--
ALTER TABLE `juicios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `procesosjuridicos`
--
ALTER TABLE `procesosjuridicos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `juicios`
--
ALTER TABLE `juicios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `procesosjuridicos`
--
ALTER TABLE `procesosjuridicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
