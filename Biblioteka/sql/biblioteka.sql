-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 16, 2024 at 05:54 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `biblioteka`
--

-- --------------------------------------------------------

--
-- Table structure for table `autor`
--

CREATE TABLE `autor` (
  `ID` int(11) NOT NULL,
  `imie` varchar(50) NOT NULL,
  `nazwisko` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `autor_ksiazki`
--

CREATE TABLE `autor_ksiazki` (
  `ID` int(11) NOT NULL,
  `ID_ksiazki` int(11) NOT NULL,
  `ID_autora` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `czytelnik`
--

CREATE TABLE `czytelnik` (
  `ID` int(11) NOT NULL,
  `imie` varchar(50) NOT NULL,
  `nazwisko` varchar(50) NOT NULL,
  `nr_karty` varchar(20) NOT NULL,
  `telefon` varchar(9) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `login` varchar(50) NOT NULL,
  `haslo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `egzemplarz`
--

CREATE TABLE `egzemplarz` (
  `ID` int(11) NOT NULL,
  `ID_wydania` int(11) NOT NULL,
  `dostepny` tinyint(1) NOT NULL DEFAULT 0,
  `stan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gatunek`
--

CREATE TABLE `gatunek` (
  `ID` int(11) NOT NULL,
  `nazwa` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gatunek_ksiazki`
--

CREATE TABLE `gatunek_ksiazki` (
  `ID` int(11) NOT NULL,
  `ID_ksiazki` int(11) NOT NULL,
  `ID_gatunku` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ksiazka`
--

CREATE TABLE `ksiazka` (
  `ID` int(11) NOT NULL,
  `tytul` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pracownik`
--

CREATE TABLE `pracownik` (
  `ID` int(11) NOT NULL,
  `imie` varchar(50) NOT NULL,
  `nazwisko` varchar(50) NOT NULL,
  `poziom_uprawnien` enum('administrator','bibliotekarz','uzytkownik') NOT NULL DEFAULT 'uzytkownik',
  `login` varchar(50) NOT NULL,
  `haslo` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rezerwacja`
--

CREATE TABLE `rezerwacja` (
  `ID` int(11) NOT NULL,
  `ID_wydania` int(11) NOT NULL,
  `ID_czytelnika` int(11) NOT NULL,
  `data_rezerwacji` date NOT NULL,
  `czy_wydana` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wydanie`
--

CREATE TABLE `wydanie` (
  `ID` int(11) NOT NULL,
  `ID_ksiazki` int(11) NOT NULL,
  `ID_wydawnictwa` int(11) NOT NULL,
  `ISBN` varchar(13) NOT NULL,
  `data_wydania` date NOT NULL,
  `numer_wydania` varchar(20) NOT NULL,
  `jezyk` varchar(50) NOT NULL,
  `czy_elektronicznie` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wydawnictwo`
--

CREATE TABLE `wydawnictwo` (
  `ID` int(11) NOT NULL,
  `nazwa` varchar(50) NOT NULL,
  `kraj` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wypozyczenie`
--

CREATE TABLE `wypozyczenie` (
  `ID` int(11) NOT NULL,
  `ID_czytelnika` int(11) NOT NULL,
  `ID_egzemplarza` int(11) NOT NULL,
  `ID_pracownika` int(11) NOT NULL,
  `data_wypozyczenia` date NOT NULL,
  `termin_oddania` date NOT NULL,
  `dzien_oddania` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `autor`
--
ALTER TABLE `autor`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `autor_ksiazki`
--
ALTER TABLE `autor_ksiazki`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_idksiazki` (`ID_ksiazki`),
  ADD KEY `fk_autor` (`ID_autora`);

--
-- Indexes for table `czytelnik`
--
ALTER TABLE `czytelnik`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `nr_karty` (`nr_karty`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `login` (`login`);

--
-- Indexes for table `egzemplarz`
--
ALTER TABLE `egzemplarz`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_idwydania` (`ID_wydania`);

--
-- Indexes for table `gatunek`
--
ALTER TABLE `gatunek`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `gatunek_ksiazki`
--
ALTER TABLE `gatunek_ksiazki`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_idksiazk` (`ID_ksiazki`),
  ADD KEY `fk_idgat` (`ID_gatunku`);

--
-- Indexes for table `ksiazka`
--
ALTER TABLE `ksiazka`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `pracownik`
--
ALTER TABLE `pracownik`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `rezerwacja`
--
ALTER TABLE `rezerwacja`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_wydanie` (`ID_wydania`),
  ADD KEY `fk_czyt` (`ID_czytelnika`);

--
-- Indexes for table `wydanie`
--
ALTER TABLE `wydanie`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ISBN` (`ISBN`),
  ADD UNIQUE KEY `numer_wydania` (`numer_wydania`),
  ADD KEY `fk_wydawnictwo` (`ID_wydawnictwa`),
  ADD KEY `fk_ksiazka` (`ID_ksiazki`);

--
-- Indexes for table `wydawnictwo`
--
ALTER TABLE `wydawnictwo`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `wypozyczenie`
--
ALTER TABLE `wypozyczenie`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_egzemplarze` (`ID_egzemplarza`),
  ADD KEY `fk_czytelnik` (`ID_czytelnika`),
  ADD KEY `fk_pracownik` (`ID_pracownika`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `autor`
--
ALTER TABLE `autor`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `autor_ksiazki`
--
ALTER TABLE `autor_ksiazki`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `czytelnik`
--
ALTER TABLE `czytelnik`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `egzemplarz`
--
ALTER TABLE `egzemplarz`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `gatunek`
--
ALTER TABLE `gatunek`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gatunek_ksiazki`
--
ALTER TABLE `gatunek_ksiazki`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ksiazka`
--
ALTER TABLE `ksiazka`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pracownik`
--
ALTER TABLE `pracownik`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rezerwacja`
--
ALTER TABLE `rezerwacja`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wydanie`
--
ALTER TABLE `wydanie`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wydawnictwo`
--
ALTER TABLE `wydawnictwo`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wypozyczenie`
--
ALTER TABLE `wypozyczenie`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `autor_ksiazki`
--
ALTER TABLE `autor_ksiazki`
  ADD CONSTRAINT `fk_autor` FOREIGN KEY (`ID_autora`) REFERENCES `autor` (`ID`),
  ADD CONSTRAINT `fk_idksiazki` FOREIGN KEY (`ID_ksiazki`) REFERENCES `ksiazka` (`ID`);

--
-- Constraints for table `egzemplarz`
--
ALTER TABLE `egzemplarz`
  ADD CONSTRAINT `fk_idwydania` FOREIGN KEY (`ID_wydania`) REFERENCES `wydanie` (`ID`);

--
-- Constraints for table `gatunek_ksiazki`
--
ALTER TABLE `gatunek_ksiazki`
  ADD CONSTRAINT `fk_idgat` FOREIGN KEY (`ID_gatunku`) REFERENCES `gatunek` (`ID`),
  ADD CONSTRAINT `fk_idksiazk` FOREIGN KEY (`ID_ksiazki`) REFERENCES `ksiazka` (`ID`);

--
-- Constraints for table `rezerwacja`
--
ALTER TABLE `rezerwacja`
  ADD CONSTRAINT `fk_czyt` FOREIGN KEY (`ID_czytelnika`) REFERENCES `czytelnik` (`ID`),
  ADD CONSTRAINT `fk_wydanie` FOREIGN KEY (`ID_wydania`) REFERENCES `wydanie` (`ID`);

--
-- Constraints for table `wydanie`
--
ALTER TABLE `wydanie`
  ADD CONSTRAINT `fk_ksiazka` FOREIGN KEY (`ID_ksiazki`) REFERENCES `ksiazka` (`ID`),
  ADD CONSTRAINT `fk_wydawnictwo` FOREIGN KEY (`ID_wydawnictwa`) REFERENCES `wydawnictwo` (`ID`);

--
-- Constraints for table `wypozyczenie`
--
ALTER TABLE `wypozyczenie`
  ADD CONSTRAINT `fk_czytelnik` FOREIGN KEY (`ID_czytelnika`) REFERENCES `czytelnik` (`ID`),
  ADD CONSTRAINT `fk_egzemplarze` FOREIGN KEY (`ID_egzemplarza`) REFERENCES `egzemplarz` (`ID`),
  ADD CONSTRAINT `fk_pracownik` FOREIGN KEY (`ID_pracownika`) REFERENCES `pracownik` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
