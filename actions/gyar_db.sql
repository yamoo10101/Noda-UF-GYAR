-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Värd: localhost:3306
-- Tid vid skapande: 17 dec 2025 kl 10:09
-- Serverversion: 5.7.24
-- PHP-version: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databas: `gyar_db2`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `annons`
--

CREATE TABLE `annons` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `titel` varchar(255) NOT NULL,
  `beskrivning` text NOT NULL,
  `foretagsnamn` varchar(255) DEFAULT NULL,
  `adress` varchar(255) DEFAULT NULL,
  `sokt_tjanst` varchar(255) DEFAULT NULL,
  `anstallningsform` varchar(100) DEFAULT NULL,
  `skapad_datum` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellstruktur `annonsbilder`
--

CREATE TABLE `annonsbilder` (
  `id` int(11) NOT NULL,
  `annons_id` int(11) NOT NULL,
  `bild_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellstruktur `annons_tags`
--

CREATE TABLE `annons_tags` (
  `id` int(11) NOT NULL,
  `annons_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellstruktur `matches`
--

CREATE TABLE `matches` (
  `id` int(11) NOT NULL,
  `user1_id` int(11) NOT NULL,
  `user2_id` int(11) NOT NULL,
  `datum` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellstruktur `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `text` text NOT NULL,
  `skickat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellstruktur `swipes`
--

CREATE TABLE `swipes` (
  `id` int(11) NOT NULL,
  `from_user` int(11) NOT NULL,
  `to_user` int(11) NOT NULL,
  `typ` enum('upp','ner') NOT NULL DEFAULT 'upp',
  `datum` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellstruktur `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `namn` varchar(100) NOT NULL,
  `kategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellstruktur `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `namn` varchar(100) NOT NULL,
  `användarnamn` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `losenord` varchar(255) NOT NULL,
  `stad` varchar(100) DEFAULT NULL,
  `profilbild` varchar(255) DEFAULT NULL,
  `biografi` text,
  `kontotyp` enum('arbetstagare','arbetsgivare') NOT NULL,
  `skapad_datum` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumpning av Data i tabell `users`
--

INSERT INTO `users` (`id`, `namn`, `användarnamn`, `email`, `losenord`, `stad`, `profilbild`, `biografi`, `kontotyp`, `skapad_datum`) VALUES
(6, 'Alva Lindgren', 'alva', 'alva.lindgren10@gmail.com', '$2y$10$vn3Lq/v/wOyyoXDqc/wMqeYWAKu/SzR/LG6HVS3MUAYWiHJa4d2oS', NULL, NULL, NULL, 'arbetstagare', '2025-12-04 09:47:14'),
(7, 'Ella Lindgren', 'ella', 'ella@gmail', '$2y$10$86N5oUaoo/mjyw4gvKNoMuTYT9LbuN1gzl0MJ9r4JBp8GNwaA4FMG', NULL, NULL, NULL, 'arbetstagare', '2025-12-04 10:03:27'),
(8, 'hej', 'hej', 'hej@gmail.com', '$2y$10$3FmgZjqpsMQWdDsHXstk8ei3Qh9BH0vPLi4aaM2Cu4zTHrrdc9xZy', NULL, NULL, NULL, 'arbetstagare', '2025-12-04 10:04:10'),
(9, 'test1', 'test1', 'test1@gmail.com', '$2y$10$IToGoPE/L5.Y5h8rY95aeOYLSts26bq0Uff.PVIq63yFPPlY8lK5W', NULL, NULL, NULL, 'arbetstagare', '2025-12-04 10:05:18');

-- --------------------------------------------------------

--
-- Tabellstruktur `user_tags`
--

CREATE TABLE `user_tags` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Index för dumpade tabeller
--

--
-- Index för tabell `annons`
--
ALTER TABLE `annons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Index för tabell `annonsbilder`
--
ALTER TABLE `annonsbilder`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_annons_id` (`annons_id`);

--
-- Index för tabell `annons_tags`
--
ALTER TABLE `annons_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_annons_id` (`annons_id`),
  ADD KEY `idx_tag_id` (`tag_id`);

--
-- Index för tabell `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user1` (`user1_id`),
  ADD KEY `idx_user2` (`user2_id`);

--
-- Index för tabell `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_match_id` (`match_id`),
  ADD KEY `idx_sender_id` (`sender_id`);

--
-- Index för tabell `swipes`
--
ALTER TABLE `swipes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_from_user` (`from_user`),
  ADD KEY `idx_to_user` (`to_user`);

--
-- Index för tabell `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `användarnamn` (`användarnamn`);

--
-- Index för tabell `user_tags`
--
ALTER TABLE `user_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_tag_id` (`tag_id`);

--
-- AUTO_INCREMENT för dumpade tabeller
--

--
-- AUTO_INCREMENT för tabell `annons`
--
ALTER TABLE `annons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `annonsbilder`
--
ALTER TABLE `annonsbilder`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `annons_tags`
--
ALTER TABLE `annons_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `matches`
--
ALTER TABLE `matches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `swipes`
--
ALTER TABLE `swipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT för tabell `user_tags`
--
ALTER TABLE `user_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restriktioner för dumpade tabeller
--

--
-- Restriktioner för tabell `annons`
--
ALTER TABLE `annons`
  ADD CONSTRAINT `fk_annons_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restriktioner för tabell `annonsbilder`
--
ALTER TABLE `annonsbilder`
  ADD CONSTRAINT `fk_annonsbilder_annons` FOREIGN KEY (`annons_id`) REFERENCES `annons` (`id`) ON DELETE CASCADE;

--
-- Restriktioner för tabell `annons_tags`
--
ALTER TABLE `annons_tags`
  ADD CONSTRAINT `fk_annons_tags_annons` FOREIGN KEY (`annons_id`) REFERENCES `annons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_annons_tags_tag` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

--
-- Restriktioner för tabell `matches`
--
ALTER TABLE `matches`
  ADD CONSTRAINT `fk_match_user1` FOREIGN KEY (`user1_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_match_user2` FOREIGN KEY (`user2_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restriktioner för tabell `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_messages_match` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_messages_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restriktioner för tabell `swipes`
--
ALTER TABLE `swipes`
  ADD CONSTRAINT `fk_swipe_from_user` FOREIGN KEY (`from_user`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_swipe_to_user` FOREIGN KEY (`to_user`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restriktioner för tabell `user_tags`
--
ALTER TABLE `user_tags`
  ADD CONSTRAINT `fk_user_tags_tag` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_tags_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
