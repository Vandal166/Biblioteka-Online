-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 16, 2025 at 10:12 AM
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

--
-- Dumping data for table `autor`
--

INSERT INTO `autor` (`ID`, `imie`, `nazwisko`) VALUES
(59, 'Maria', 'Nowak'),
(60, 'Jan', 'Kowalski'),
(61, 'Aleksandra', 'Wiśniewska'),
(62, 'Piotr', 'Zieliński'),
(63, 'Anna', 'Kamińska'),
(64, 'Piotr', 'Zie'),
(155, 'Opiopoipiopiop', 'Opiopoipiopiop');

-- --------------------------------------------------------

--
-- Table structure for table `autor_ksiazki`
--

CREATE TABLE `autor_ksiazki` (
  `ID` int(11) NOT NULL,
  `ID_ksiazki` int(11) NOT NULL,
  `ID_autora` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `autor_ksiazki`
--

INSERT INTO `autor_ksiazki` (`ID`, `ID_ksiazki`, `ID_autora`) VALUES
(10, 24, 59),
(11, 26, 62),
(12, 26, 64),
(15, 34, 59),
(45, 70, 59),
(46, 69, 59),
(47, 73, 59),
(48, 73, 60),
(49, 72, 155),
(50, 78, 59);

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

--
-- Dumping data for table `czytelnik`
--

INSERT INTO `czytelnik` (`ID`, `imie`, `nazwisko`, `nr_karty`, `telefon`, `email`, `login`, `haslo`) VALUES
(9, 'Katarzyna', 'Kaczmarek', '3459226504', '574869291', 'kat.kaczme@example.com', 'katczm12453', '$2y$10$eoifk2QlyMeF6VIdZA232uDzjN1.rtDDmLzHW9OskwZfjN75stJGK'),
(10, 'Adam', 'Wiśniewski', '4581483908', '123585933', 'adam.wisn@onet.pl', 'admwis', '$2y$10$iC1V0IMwaAEXoGiLmvwT/.exG33hx96lXBu3Bfd7sNYqngMM9SL4i'),
(14, 'Hertgerht', 'Wefwf', '8428017327', '231423421', 'rtgrhft@onet.pl', 'erkghek32', '$2y$10$W68Gm1N27AkPNlE/F0Pop.A3hRP8Q2iS0DHToN7/qVYwfaKUhCfcy'),
(25, 'Greeg', 'Herthrt', '7015471562', '234534634', 'ehrjrtj@onet.pl', 'ethjrtrtjwe12', '$2y$10$gwv6aX1oM4K/Wm2BnLZR/u5XlDqg/lWnAJl/4pjji.ZiI5U3NUt3O'),
(32, 'Gerger', 'Egrerg', '4117686075', '345345352', 'egrerg2@onet.pl', 'gerger2', '$2y$10$.5q44BGQHgC0qamITZ6EP.tW5FTidLbnSUc0X2wle00/bn.HQf3gW'),
(33, 'Test', 'Test', '7446174120', '345232422', 'kamilroma8@gmail.com', 'kamil', '$2y$10$0T/NGoMEcs9IRAeqw6B7DeOvJjTvOrdJMHy28t2cUW2P/hRMnvT8C'),
(34, 'Rthrth', 'Erg', '3695423881', '453343634', 'rthrtyjrtjy2o@onet.pl', 'rtjrty234', '$2y$10$8j0m.YhjVYSy/GJWHBjWSe.XmOeveB5ynrvpYqqeRKIxle1nGl69e'),
(35, 'Użytkownik', 'Biblioteki', '4084259264', '921456712', 'user@lib.com', 'User', '$2y$10$b9A7XftkWiQlSkxPK176Oe7TQIa1E4anM/5NUbh.zKYegEx7yG7mO');

-- --------------------------------------------------------

--
-- Table structure for table `egzemplarz`
--

CREATE TABLE `egzemplarz` (
  `ID` int(11) NOT NULL,
  `ID_wydania` int(11) NOT NULL,
  `czy_dostepny` tinyint(1) NOT NULL DEFAULT 0,
  `stan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `egzemplarz`
--

INSERT INTO `egzemplarz` (`ID`, `ID_wydania`, `czy_dostepny`, `stan`) VALUES
(3, 1, 1, 'dobry'),
(18, 3, 1, 'gr'),
(19, 4, 1, 'gr'),
(20, 8, 0, 'hrt'),
(21, 26, 1, 'hrt'),
(23, 5, 0, 'test'),
(24, 1, 0, 'rtgrt'),
(27, 46, 0, 'blbylbylf'),
(28, 44, 1, 'lol'),
(29, 45, 0, 'yhyh'),
(34, 46, 0, 'yh');

-- --------------------------------------------------------

--
-- Table structure for table `gatunek`
--

CREATE TABLE `gatunek` (
  `ID` int(11) NOT NULL,
  `nazwa` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gatunek`
--

INSERT INTO `gatunek` (`ID`, `nazwa`) VALUES
(1, 'dramat'),
(2, 'fantasy'),
(3, 'horror'),
(4, 'komedia');

-- --------------------------------------------------------

--
-- Table structure for table `gatunek_ksiazki`
--

CREATE TABLE `gatunek_ksiazki` (
  `ID` int(11) NOT NULL,
  `ID_ksiazki` int(11) NOT NULL,
  `ID_gatunku` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gatunek_ksiazki`
--

INSERT INTO `gatunek_ksiazki` (`ID`, `ID_ksiazki`, `ID_gatunku`) VALUES
(1, 24, 1),
(10, 27, 4),
(11, 26, 3),
(12, 34, 1),
(13, 27, 3),
(25, 69, 2),
(26, 70, 1),
(27, 72, 3),
(28, 78, 3);

-- --------------------------------------------------------

--
-- Table structure for table `ksiazka`
--

CREATE TABLE `ksiazka` (
  `ID` int(11) NOT NULL,
  `tytul` varchar(50) NOT NULL,
  `zdjecie` varchar(255) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ksiazka`
--

INSERT INTO `ksiazka` (`ID`, `tytul`, `zdjecie`) VALUES
(24, 'Oczami kota– tajemnice nocy', 'Biblioteka/images/677d9adb74d10-Chad_Soldier_TF2.jpg'),
(25, 'Matematyka w praktyce', 'Biblioteka/images/677d904301b57-Kjermejt.jpg'),
(26, 'Dzień, który zmienił wszystko', 'Biblioteka/images/677d925558f3d-Sipper.png'),
(27, 'Wszechświat: Początek i koniec..', 'Biblioteka/images/677d977214c90-Afrotitan.png'),
(34, 'Dziady :C', 'Biblioteka/images/677d904301b57-Kjermejt.jpg'),
(69, 'gererh', 'Biblioteka/images/677d965748586-Shock.jpg'),
(70, 'ghtrr', 'Biblioteka/images/677eca5baa173-2.png'),
(71, 'opiopoipiopiop', 'Biblioteka/images/677d965748586-Shock.jpg'),
(72, 'Nowa książka', ''),
(73, 'La testo de fiesta', ''),
(74, 'Nowa strona test test', ''),
(75, 'A', ''),
(76, 'B', ''),
(77, 'C', 'Biblioteka/images/677d965748586-Shock.jpg'),
(78, 'Dziady', 'Biblioteka/images/error.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `pracownik`
--

CREATE TABLE `pracownik` (
  `ID` int(11) NOT NULL,
  `imie` varchar(50) NOT NULL,
  `nazwisko` varchar(50) NOT NULL,
  `poziom_uprawnien` enum('administrator','bibliotekarz','uzytkownik') NOT NULL DEFAULT 'bibliotekarz',
  `email` varchar(100) NOT NULL,
  `login` varchar(50) NOT NULL,
  `haslo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pracownik`
--

INSERT INTO `pracownik` (`ID`, `imie`, `nazwisko`, `poziom_uprawnien`, `email`, `login`, `haslo`) VALUES
(1, 'Kamil', 'Wojtas', 'administrator', 'fajnyMail@mail.com', 'AdminW', '$2y$10$iQr7B5doUiH5ev7EbcNzR.tCQ9A.q1EAbi776IqltG6/xAybtSh2i'),
(3, 'Jan', 'Kowalski', 'bibliotekarz', 'fajnyMail2@mail.com', 'BibliW', '$2y$10$iQr7B5doUiH5ev7EbcNzR.tCQ9A.q1EAbi776IqltG6/xAybtSh2i'),
(4, 'Kamil', 'Rom', 'administrator', 'admin01@gmail.com', 'admin01', '$2y$10$f7KF.OrH80iYWr9IHNapbuBO95MovN1gqNJNIj9fEjkJfyIIiM.lC'),
(5, 'Rgeger', 'Erge', 'uzytkownik', 'greghr@onert.pl', 'ergeherth', '$2y$10$rawQeHuh4YD0IW31T0WAPe3Kpyl2JT9/WpcDPhggqJfQHgmTOc6kS'),
(30, 'Biblio', 'Tekarz', 'bibliotekarz', 'bib@onet.pl', 'biblio', '$2y$10$yM4L.se53IPQuI6KpGL8kOFVXkULr6VkwMp6eOo3n69mBy28kPk1G');

-- --------------------------------------------------------

--
-- Table structure for table `reset_hasla`
--

CREATE TABLE `reset_hasla` (
  `ID` int(11) NOT NULL,
  `ID_czytelnik` int(11) DEFAULT NULL,
  `ID_pracownik` int(11) DEFAULT NULL,
  `poziom_uprawnien` enum('administrator','bibliotekarz','uzytkownik') NOT NULL,
  `token` varchar(64) NOT NULL,
  `data_wygenerowania` timestamp NULL DEFAULT current_timestamp()
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

--
-- Dumping data for table `rezerwacja`
--

INSERT INTO `rezerwacja` (`ID`, `ID_wydania`, `ID_czytelnika`, `data_rezerwacji`, `czy_wydana`) VALUES
(1, 1, 9, '2024-11-27', 1),
(2, 3, 10, '2024-12-08', 0),
(15, 1, 9, '2025-01-08', 1),
(26, 3, 35, '2025-01-14', 0),
(30, 46, 35, '2025-01-14', 0),
(33, 1, 35, '2025-01-15', 0),
(34, 45, 9, '2025-01-03', 0),
(38, 1, 10, '2025-01-31', 0);

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
  `ilosc_stron` varchar(4) NOT NULL,
  `pdf` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wydanie`
--

INSERT INTO `wydanie` (`ID`, `ID_ksiazki`, `ID_wydawnictwa`, `ISBN`, `data_wydania`, `numer_wydania`, `jezyk`, `ilosc_stron`, `pdf`) VALUES
(1, 24, 1, '3245353453453', '2024-12-12', '23423452452523534531', 'polski', '255', 'Biblioteka/books/asdninoidasinodsadnas.pdf'),
(3, 24, 1, '1231234234522', '2024-12-06', '32453453535345332111', 'ertgh', '4353', 'Biblioteka/books/6782e0508f187-python_lista_6.pdf'),
(4, 24, 1, '3333333333333', '2024-12-20', '43444444444444444444', 'regerg', '15', '0'),
(5, 24, 1, '2342423423422', '2025-01-06', '34444443242342342341', 'erghe', '643', '1'),
(8, 26, 1, '3245253452342', '2025-01-12', '43234645234242423343', 'rthrht', '352', '1'),
(26, 24, 1, '3453634623436', '2025-01-01', '43645457454745745222', 'rthrth', '234', '1'),
(31, 24, 1, '3453453632342', '3454-03-31', '45635234241234143333', 'rtegrhrt', '3453', '1'),
(44, 69, 3, '3456457456353', '2025-01-24', '78678678456345345234', 'jtyktyuj', '456', '0'),
(45, 70, 1, '4536457568568', '2025-01-10', '85678566546346345345', 'trhrt', '56', '0'),
(46, 34, 3, '5475686767542', '2025-01-03', '54756856834535363463', 'guwno', '3', '0'),
(47, 24, 1, '1111111111111', '2025-01-21', '11111111111111111111', 'Mlloojinini', '1234', '0'),
(48, 24, 1, '9876543212345', '2025-02-07', '22222222222222222222', 'Makao', '12', '0'),
(49, 24, 2, '1234321234543', '2025-01-06', '12212334356667889000', 'NNNNN', '32', NULL),
(50, 24, 1, '7527852372357', '2024-12-16', '23569256796923475625', 'JKJJGYH', '14', NULL),
(53, 69, 2, '5757575757575', '2025-01-17', '57757575757577557575', 'vbvb', '2', 'Biblioteka/books/67865663c9779-w1_full.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `wydawnictwo`
--

CREATE TABLE `wydawnictwo` (
  `ID` int(11) NOT NULL,
  `nazwa` varchar(50) NOT NULL,
  `kraj` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wydawnictwo`
--

INSERT INTO `wydawnictwo` (`ID`, `nazwa`, `kraj`) VALUES
(1, 'test', 'test'),
(2, 'WSiP', 'Polska'),
(3, 'PolSmr', 'Francja');

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
  `data_oddania` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wypozyczenie`
--

INSERT INTO `wypozyczenie` (`ID`, `ID_czytelnika`, `ID_egzemplarza`, `ID_pracownika`, `data_wypozyczenia`, `termin_oddania`, `data_oddania`) VALUES
(2, 9, 3, 4, '2024-12-06', '2024-12-03', '2025-01-16'),
(8, 9, 3, 4, '2025-01-02', '2025-02-06', NULL),
(9, 9, 3, 4, '2025-01-02', '2025-02-06', '2222-02-22'),
(14, 35, 3, 30, '3223-02-12', '2222-04-04', NULL),
(15, 33, 27, 30, '1212-12-22', '2233-03-23', NULL),
(16, 25, 3, 30, '2025-01-25', '2025-01-08', NULL);

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
-- Indexes for table `reset_hasla`
--
ALTER TABLE `reset_hasla`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID_czytelnik` (`ID_czytelnik`),
  ADD KEY `ID_pracownik` (`ID_pracownik`);

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
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156;

--
-- AUTO_INCREMENT for table `autor_ksiazki`
--
ALTER TABLE `autor_ksiazki`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `czytelnik`
--
ALTER TABLE `czytelnik`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `egzemplarz`
--
ALTER TABLE `egzemplarz`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `gatunek`
--
ALTER TABLE `gatunek`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `gatunek_ksiazki`
--
ALTER TABLE `gatunek_ksiazki`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `ksiazka`
--
ALTER TABLE `ksiazka`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `pracownik`
--
ALTER TABLE `pracownik`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `reset_hasla`
--
ALTER TABLE `reset_hasla`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `rezerwacja`
--
ALTER TABLE `rezerwacja`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `wydanie`
--
ALTER TABLE `wydanie`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `wydawnictwo`
--
ALTER TABLE `wydawnictwo`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `wypozyczenie`
--
ALTER TABLE `wypozyczenie`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `autor_ksiazki`
--
ALTER TABLE `autor_ksiazki`
  ADD CONSTRAINT `fk_autor` FOREIGN KEY (`ID_autora`) REFERENCES `autor` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_idksiazki` FOREIGN KEY (`ID_ksiazki`) REFERENCES `ksiazka` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `egzemplarz`
--
ALTER TABLE `egzemplarz`
  ADD CONSTRAINT `fk_idwydania` FOREIGN KEY (`ID_wydania`) REFERENCES `wydanie` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `gatunek_ksiazki`
--
ALTER TABLE `gatunek_ksiazki`
  ADD CONSTRAINT `fk_idgat` FOREIGN KEY (`ID_gatunku`) REFERENCES `gatunek` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_idksiazk` FOREIGN KEY (`ID_ksiazki`) REFERENCES `ksiazka` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `reset_hasla`
--
ALTER TABLE `reset_hasla`
  ADD CONSTRAINT `reset_hasla_ibfk_1` FOREIGN KEY (`ID_czytelnik`) REFERENCES `czytelnik` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `reset_hasla_ibfk_2` FOREIGN KEY (`ID_pracownik`) REFERENCES `pracownik` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `rezerwacja`
--
ALTER TABLE `rezerwacja`
  ADD CONSTRAINT `fk_czyt` FOREIGN KEY (`ID_czytelnika`) REFERENCES `czytelnik` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_wydanie` FOREIGN KEY (`ID_wydania`) REFERENCES `wydanie` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `wydanie`
--
ALTER TABLE `wydanie`
  ADD CONSTRAINT `fk_ksiazka` FOREIGN KEY (`ID_ksiazki`) REFERENCES `ksiazka` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_wydawnictwo` FOREIGN KEY (`ID_wydawnictwa`) REFERENCES `wydawnictwo` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `wypozyczenie`
--
ALTER TABLE `wypozyczenie`
  ADD CONSTRAINT `fk_czytelnik` FOREIGN KEY (`ID_czytelnika`) REFERENCES `czytelnik` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_egzemplarze` FOREIGN KEY (`ID_egzemplarza`) REFERENCES `egzemplarz` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pracownik` FOREIGN KEY (`ID_pracownika`) REFERENCES `pracownik` (`ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
