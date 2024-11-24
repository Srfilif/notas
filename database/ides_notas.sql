-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-11-2024 a las 17:11:11
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
-- Base de datos: `ides_notas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_notas`
--

CREATE TABLE `categorias_notas` (
  `id` int(11) NOT NULL,
  `materia_id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias_notas`
--

INSERT INTO `categorias_notas` (`id`, `materia_id`, `nombre`, `descripcion`) VALUES
(20, 8, 'Saber 1', 'Evaluación sobre los ángulos'),
(22, 11, 'Saber 1', 'Evaluacion 2'),
(26, 9, 'Hacer 1', 'Evaluacion Grupal'),
(27, 8, 'Saber 3', 'Evalucion sobre los angulos'),
(29, 13, 'examen 1', '1111111111'),
(31, 8, 'Saber 2', 'Evaluacion x2');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE `cursos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `materia_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`id`, `nombre`, `materia_id`) VALUES
(1, '10-2', 8),
(18, '10-3', 8),
(21, '6-2', 13),
(22, 'Sin Curso', 0),
(28, 'Andres Felipe', 11);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos_actuales`
--

CREATE TABLE `cursos_actuales` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `fecha_inicio` date DEFAULT curdate(),
  `fecha_fin` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cursos_actuales`
--

INSERT INTO `cursos_actuales` (`id`, `usuario_id`, `curso_id`, `fecha_inicio`, `fecha_fin`) VALUES
(48, 1, 22, '2024-11-23', NULL),
(60, 18, 1, '2024-11-23', NULL),
(61, 20, 1, '2024-11-23', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos_dictados`
--

CREATE TABLE `cursos_dictados` (
  `id` int(11) NOT NULL,
  `profesor_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `fecha_inicio` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias`
--

CREATE TABLE `materias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  `fecha_inicio` date NOT NULL DEFAULT current_timestamp(),
  `fecha_fin` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias`
--

INSERT INTO `materias` (`id`, `nombre`, `curso_id`, `descripcion`, `fecha_inicio`, `fecha_fin`) VALUES
(8, 'Estadistica y Geometria', 1, 'Aca aprenderas hacerca de los angulos y las graficas', '2024-11-08', '2024-11-07'),
(9, 'Estadística y Geometría feo', 21, 'Aquí aprenderás acerca de los ángulos y las gráficas', '2024-11-08', '2024-11-07'),
(11, 'Estadistica y Geometria', 22, 'Aca aprenderas hacerca de los angulos y las graficas..', '2024-11-20', '2024-11-20'),
(13, 'Matematicas', 21, 'Aca aprenderas a hacer tus operaciones matematicas', '2000-02-06', '2024-11-23'),
(64, 'Diseño Grafico', 22, 'Aca aprenderas a hacer diseños cheveres', '2024-11-28', '2024-11-08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matriculas`
--

CREATE TABLE `matriculas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `materia_id` int(11) NOT NULL,
  `fecha_inscripcion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notas`
--

CREATE TABLE `notas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `materia_id` int(11) NOT NULL,
  `nota` decimal(4,2) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notas`
--

INSERT INTO `notas` (`id`, `usuario_id`, `materia_id`, `nota`, `categoria_id`) VALUES
(321, 12, 8, 5.00, 19),
(322, 12, 8, 5.00, 20),
(323, 17, 8, 5.00, 19),
(324, 17, 8, 5.00, 20),
(325, 1, 11, 5.00, 22),
(326, 18, 11, 5.00, 22),
(327, 1, 8, 5.00, 19),
(328, 1, 8, 5.00, 20),
(329, 1, 8, 1.00, 27),
(331, 18, 13, 5.00, 29),
(333, 18, 8, 3.00, 19),
(334, 18, 8, 5.00, 20),
(335, 18, 8, 5.00, 27),
(336, 12, 8, 4.00, 27),
(337, 20, 8, 1.00, 20),
(338, 18, 8, 5.00, 31),
(339, 20, 8, 5.00, 27),
(340, 20, 8, 5.00, 31);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesor_curso`
--

CREATE TABLE `profesor_curso` (
  `profesor_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('estudiante','profesor','administrador','desarrollador','moderador') NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `numero_identidad` varchar(20) NOT NULL,
  `sexo` enum('masculino','femenino','otro') NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `fecha_nacimiento` date NOT NULL DEFAULT current_timestamp(),
  `telefono` int(11) NOT NULL,
  `direccion` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`, `fecha_registro`, `numero_identidad`, `sexo`, `avatar`, `fecha_nacimiento`, `telefono`, `direccion`) VALUES
(1, 'Andres Felipe', 'sr.filif@gmail.com', '$2y$10$BTlAMGnFgUDmvV.KVjvhRexN/vVQSMKtR4LknR.dXhCECKCv41xHq', 'profesor', '2024-11-16 21:22:33', '', 'masculino', 'srfilif.png', '2024-11-17', 0, 'Carrera 4 calle 8 norte'),
(12, 'Andres Felipe', 'yt.xfilif@gmail.com', '$2y$10$KoBqRIkM3xTMrA.d2uR9x.KUaiv69UVXGF4A2E2tvKkZS8Kk34Nqe', 'administrador', '2024-11-21 14:49:46', '', 'masculino', NULL, '2024-11-21', 0, ''),
(17, 'Carlos Angel', 'carlos@fl.com', '', 'moderador', '2024-11-21 17:55:31', '', 'masculino', NULL, '2024-11-21', 0, ''),
(18, 'Juan Almeida', 'juanalmeida@gmail.com', '$2y$10$FeHQuNrmWcB8me38zcDCP.fqWwxU27C7YEWeWTy.zRl3qzwWklHly', 'estudiante', '2024-11-22 13:44:44', '', 'masculino', NULL, '2024-11-22', 0, ''),
(20, 'Marlon Tobar', 'marlontobar@gmail.com', '$2y$10$FeHQuNrmWcB8me38zcDCP.fqWwxU27C7YEWeWTy.zRl3qzwWklHly', 'estudiante', '2024-11-23 23:44:09', '', 'masculino', NULL, '2024-11-23', 0, '');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias_notas`
--
ALTER TABLE `categorias_notas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `materia_id` (`materia_id`);

--
-- Indices de la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cursos_actuales`
--
ALTER TABLE `cursos_actuales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `curso_id` (`curso_id`);

--
-- Indices de la tabla `cursos_dictados`
--
ALTER TABLE `cursos_dictados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profesor_id` (`profesor_id`),
  ADD KEY `curso_id` (`curso_id`);

--
-- Indices de la tabla `materias`
--
ALTER TABLE `materias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `curso_id` (`curso_id`);

--
-- Indices de la tabla `matriculas`
--
ALTER TABLE `matriculas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `materia_id` (`materia_id`);

--
-- Indices de la tabla `notas`
--
ALTER TABLE `notas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `materia_id` (`materia_id`),
  ADD KEY `fk_usuarios_id` (`usuario_id`);

--
-- Indices de la tabla `profesor_curso`
--
ALTER TABLE `profesor_curso`
  ADD PRIMARY KEY (`profesor_id`,`curso_id`),
  ADD KEY `curso_id` (`curso_id`);

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
-- AUTO_INCREMENT de la tabla `categorias_notas`
--
ALTER TABLE `categorias_notas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `cursos_actuales`
--
ALTER TABLE `cursos_actuales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT de la tabla `cursos_dictados`
--
ALTER TABLE `cursos_dictados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `materias`
--
ALTER TABLE `materias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT de la tabla `matriculas`
--
ALTER TABLE `matriculas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notas`
--
ALTER TABLE `notas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=341;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `categorias_notas`
--
ALTER TABLE `categorias_notas`
  ADD CONSTRAINT `categorias_notas_ibfk_1` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`);

--
-- Filtros para la tabla `cursos_actuales`
--
ALTER TABLE `cursos_actuales`
  ADD CONSTRAINT `cursos_actuales_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cursos_actuales_ibfk_2` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cursos_dictados`
--
ALTER TABLE `cursos_dictados`
  ADD CONSTRAINT `cursos_dictados_ibfk_1` FOREIGN KEY (`profesor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cursos_dictados_ibfk_2` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `materias`
--
ALTER TABLE `materias`
  ADD CONSTRAINT `materias_ibfk_1` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`);

--
-- Filtros para la tabla `matriculas`
--
ALTER TABLE `matriculas`
  ADD CONSTRAINT `matriculas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `matriculas_ibfk_2` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `notas`
--
ALTER TABLE `notas`
  ADD CONSTRAINT `fk_usuarios_id` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notas_ibfk_2` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`);

--
-- Filtros para la tabla `profesor_curso`
--
ALTER TABLE `profesor_curso`
  ADD CONSTRAINT `profesor_curso_ibfk_1` FOREIGN KEY (`profesor_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `profesor_curso_ibfk_2` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
