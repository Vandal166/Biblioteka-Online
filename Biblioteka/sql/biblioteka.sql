-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sty 03, 2025 at 06:33 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

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
-- Struktura tabeli dla tabeli `autor`
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
(64, 'Test', 'Test'),
(65, 'Testt', 'Testst'),
(68, 'Rthr', 'Er'),
(69, 'erger', 'erg'),
(70, 'erg', 'erg'),
(71, 'wef', 'wef'),
(72, 'test', 'owy'),
(73, 'Test', 'Owy');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `autor_ksiazki`
--

CREATE TABLE `autor_ksiazki` (
  `ID` int(11) NOT NULL,
  `ID_ksiazki` int(11) NOT NULL,
  `ID_autora` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `czytelnik`
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
(9, 'Katarzyna', 'Kaczmarek', '1655832354', '574869291', 'kat.kaczme@example.com', 'katczm12453', '$2y$10$eoifk2QlyMeF6VIdZA232uDzjN1.rtDDmLzHW9OskwZfjN75stJGK'),
(10, 'Adam', 'Wiśniewski', '4581483908', '123585933', 'adam.wisn@onet.pl', 'admwis', '$2y$10$iC1V0IMwaAEXoGiLmvwT/.exG33hx96lXBu3Bfd7sNYqngMM9SL4i'),
(14, 'Hertgerht', 'Wefwf', '8428017327', '231423421', 'rtgrhft@onet.pl', 'erkghek32', '$2y$10$W68Gm1N27AkPNlE/F0Pop.A3hRP8Q2iS0DHToN7/qVYwfaKUhCfcy'),
(25, 'Greeg', 'Herthrt', '7015471562', '234534634', 'ehrjrtj@onet.pl', 'ethjrtrtjwe12', '$2y$10$gwv6aX1oM4K/Wm2BnLZR/u5XlDqg/lWnAJl/4pjji.ZiI5U3NUt3O'),
(30, 'Erg', 'Gere', '3483212614', '345345353', 'egrerg@onet.pl', 'gerger', '$2y$10$SEGSREtuv76h3Hg7SctEguBoXwNHf85SFkRb2X66dXbDp08PJv7oS'),
(32, 'Gerger', 'Egrerg', '4117686075', '345345352', 'egrerg2@onet.pl', 'gerger2', '$2y$10$.5q44BGQHgC0qamITZ6EP.tW5FTidLbnSUc0X2wle00/bn.HQf3gW'),
(33, 'Test', 'Test', '7446174120', '345232422', 'kamilroma8@gmail.com', 'kamil', '$2y$10$TKopYiujKZmdiMyIdZa9BeIZYypw6TnfiQ.100ORBLsCKvYGQkQPe'),
(34, 'Rthrth', 'Erg', '3695423881', '453343634', 'rthrtyjrtjy2o@onet.pl', 'rtjrty234', '$2y$10$8j0m.YhjVYSy/GJWHBjWSe.XmOeveB5ynrvpYqqeRKIxle1nGl69e');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `egzemplarz`
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
(4, 1, 0, 'gr'),
(5, 1, 0, 'gr');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `gatunek`
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
-- Struktura tabeli dla tabeli `gatunek_ksiazki`
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
(1, 24, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `ksiazka`
--

CREATE TABLE `ksiazka` (
  `ID` int(11) NOT NULL,
  `tytul` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ksiazka`
--

INSERT INTO `ksiazka` (`ID`, `tytul`) VALUES
(24, 'Oczami kota– tajemnice nocy'),
(26, 'Dzień, który zmienił wszystko'),
(27, 'Wszechświat: Początek i koniec..');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pracownik`
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
(5, 'Rgeger', 'Erge', 'uzytkownik', 'greghr@onert.pl', 'ergeherth', '$2y$10$rawQeHuh4YD0IW31T0WAPe3Kpyl2JT9/WpcDPhggqJfQHgmTOc6kS');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `reset_hasla`
--

CREATE TABLE `reset_hasla` (
  `ID` int(11) NOT NULL,
  `ID_uzytkownika` int(11) NOT NULL,
  `poziom_uprawnien` enum('administrator','bibliotekarz','uzytkownik') NOT NULL,
  `token` varchar(255) NOT NULL,
  `data_wygenerowania` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reset_hasla`
--

INSERT INTO `reset_hasla` (`ID`, `ID_uzytkownika`, `poziom_uprawnien`, `token`, `data_wygenerowania`) VALUES
(1, 30, '', '95923d180e3ddae4192cd77d2aae454693b3dedd448d9beaa58576120c04778c', '2025-01-03 15:36:14'),
(2, 30, '', '60d925628fe4ee1846ecab4163a55f775614df87400c61a7a6954b9c4ed1be9e', '2025-01-03 15:40:40'),
(3, 30, '', 'd94d9de73155561ab5ad9bdebcbde9eca34c5976a408e06f28c653d5708703d7', '2025-01-03 15:44:04'),
(4, 30, '', 'c8fe0b75ce597cb213e391cb2d81bb6da46e065a2b96d6dcd052371cbc7f1f54', '2025-01-03 15:44:28'),
(5, 30, '', 'afe9c942e5ff6d2fd6565fe6e4e6152f858b460961413db0d596656928968ff0', '2025-01-03 15:46:00'),
(6, 30, '', '1d9f392c834179662b53e5e88191036848cb7651b3eb690f45eaeacf05b13f2b', '2025-01-03 15:46:29'),
(7, 30, '', '2e167b3ed23870d964f84dcdc4b6b8642656b88aea352275de7739f57ed51f3d', '2025-01-03 15:48:12'),
(8, 30, '', '3dc3a4de593db86a82ff6e3193d7b0e19df1a29cc7104d6682f1ce69a891d65f', '2025-01-03 15:50:51'),
(9, 30, '', 'bc891beef0390110e944b23924f7ed7b6f498a91fd2b6ce632591cc46a59e116', '2025-01-03 15:51:55'),
(10, 30, '', 'c176b7c7ab45854f4a55772d4b362f4534620eccb0447f9fb17ad27c9d70009b', '2025-01-03 15:53:53'),
(11, 30, '', 'be01d4273899ae62dd6715290c2e76c72f48fcd645e5bbf4ddcaa258cca2395f', '2025-01-03 15:54:06'),
(12, 30, '', 'd9805688c34ec44b304c3a4a1cd456619684ee5b691ed71c57563591d3889ca3', '2025-01-03 15:54:17'),
(14, 30, '', '18e99cbc2e717c8208a414b8b42aa6024e20abc6a848bca621980ae1ecfff9aa', '2025-01-03 15:56:04'),
(15, 30, '', 'b1eb05e7fe8d982faa7328065d9e44fac18e80998857efc850b7bee42e16a5e1', '2025-01-03 15:58:55'),
(16, 30, '', 'b5b03b22c35531b671330bc041197384bfea92f9836095981a06e78e193acb73', '2025-01-03 15:58:57'),
(17, 30, '', '7fcf24f25325b9b28a944cbd4f63e7978cb2abb0c7212c6ac692215fb27e62d1', '2025-01-03 15:59:31'),
(18, 32, '', 'd5e263ef8622a558ab1e21d8f17a505cf4ad00f916702b36d0a7777b7317f0cd', '2025-01-03 16:21:07'),
(19, 33, '', 'd5fd93d116a410bbd6551b40b07714f081f2069ed55f382086f03663af71bd0e', '2025-01-03 16:21:48'),
(20, 33, '', '4fe1e41015a94767ed676e3d56f665b78c7d7aaca1e2bce71302bd785eea5b3a', '2025-01-03 16:24:56'),
(21, 33, '', '1cee4b8bbfe15acef0e098c6bd22febd9196d6875d2add6e3e0b21cd9e3ff335', '2025-01-03 16:26:28'),
(22, 33, '', 'd476bd470572509efdb9230a0f55981c77580adc90c59da4921d9ade3007be23', '2025-01-03 16:26:49'),
(23, 33, '', '9de463f15347354f8a20f43ea51d9f39f198976b5136ea6a8c103ac883cf019c', '2025-01-03 16:27:16'),
(24, 33, '', '58cdc9151e341e7702550f9063342aa6cce14db293aa6a8bf5d5e0c559aa2171', '2025-01-03 16:27:27'),
(25, 33, '', '779f151fd2312f895620259746afda6e81f555d06d868cf0710ed459f18da954', '2025-01-03 16:42:25'),
(27, 9, '', '0ceb994e6e607990f2e14c99469e02dc5fe287e7387d42d4532f45d5b7a488ac', '2025-01-03 16:49:01'),
(28, 32, '', '9729e90f4593f9274a5d41d2391a602dd625988cda2c54a3de1826828a446573', '2025-01-03 16:49:09');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `rezerwacja`
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
(2, 3, 10, '2024-12-08', 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `wydanie`
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
  `czy_elektronicznie` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wydanie`
--

INSERT INTO `wydanie` (`ID`, `ID_ksiazki`, `ID_wydawnictwa`, `ISBN`, `data_wydania`, `numer_wydania`, `jezyk`, `ilosc_stron`, `czy_elektronicznie`) VALUES
(1, 24, 1, '3245353453453', '2024-12-12', '23423452452523534531', 'polski', '255', 1),
(3, 24, 1, '1231234234522', '2024-12-06', '32453453535345332111', 'ertgh', '4353', 0),
(4, 24, 1, '3333333333333', '2024-12-20', '43444444444444444444', 'regerg', '15', 1),
(5, 24, 1, '2342423423422', '2025-01-06', '34444443242342342341', 'erghe', '643', 1),
(8, 24, 1, '3245253452342', '2025-01-12', '43234645234242423343', 'rthrht', '352', 1),
(26, 24, 1, '3453634623436', '2025-01-01', '43645457454745745222', 'rthrth', '234', 1),
(28, 24, 1, '2342453453343', '2025-01-05', '23423453453535345345', 'rgeegr', '3434', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `wydawnictwo`
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
(1, 'test', 'test');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `wypozyczenie`
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
(2, 9, 3, 4, '2024-12-06', '2024-12-03', '2024-12-04'),
(8, 9, 3, 4, '2025-01-02', '2025-02-06', '2025-01-27'),
(9, 9, 3, 4, '2025-01-02', '2025-02-06', '2025-01-28');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `autor`
--
ALTER TABLE `autor`
  ADD PRIMARY KEY (`ID`);

--
-- Indeksy dla tabeli `autor_ksiazki`
--
ALTER TABLE `autor_ksiazki`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_idksiazki` (`ID_ksiazki`),
  ADD KEY `fk_autor` (`ID_autora`);

--
-- Indeksy dla tabeli `czytelnik`
--
ALTER TABLE `czytelnik`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `nr_karty` (`nr_karty`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `login` (`login`);

--
-- Indeksy dla tabeli `egzemplarz`
--
ALTER TABLE `egzemplarz`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_idwydania` (`ID_wydania`);

--
-- Indeksy dla tabeli `gatunek`
--
ALTER TABLE `gatunek`
  ADD PRIMARY KEY (`ID`);

--
-- Indeksy dla tabeli `gatunek_ksiazki`
--
ALTER TABLE `gatunek_ksiazki`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_idksiazk` (`ID_ksiazki`),
  ADD KEY `fk_idgat` (`ID_gatunku`);

--
-- Indeksy dla tabeli `ksiazka`
--
ALTER TABLE `ksiazka`
  ADD PRIMARY KEY (`ID`);

--
-- Indeksy dla tabeli `pracownik`
--
ALTER TABLE `pracownik`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeksy dla tabeli `reset_hasla`
--
ALTER TABLE `reset_hasla`
  ADD PRIMARY KEY (`ID`);

--
-- Indeksy dla tabeli `rezerwacja`
--
ALTER TABLE `rezerwacja`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_wydanie` (`ID_wydania`),
  ADD KEY `fk_czyt` (`ID_czytelnika`);

--
-- Indeksy dla tabeli `wydanie`
--
ALTER TABLE `wydanie`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ISBN` (`ISBN`),
  ADD UNIQUE KEY `numer_wydania` (`numer_wydania`),
  ADD KEY `fk_wydawnictwo` (`ID_wydawnictwa`),
  ADD KEY `fk_ksiazka` (`ID_ksiazki`);

--
-- Indeksy dla tabeli `wydawnictwo`
--
ALTER TABLE `wydawnictwo`
  ADD PRIMARY KEY (`ID`);

--
-- Indeksy dla tabeli `wypozyczenie`
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
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `autor_ksiazki`
--
ALTER TABLE `autor_ksiazki`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `czytelnik`
--
ALTER TABLE `czytelnik`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `egzemplarz`
--
ALTER TABLE `egzemplarz`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `gatunek`
--
ALTER TABLE `gatunek`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `gatunek_ksiazki`
--
ALTER TABLE `gatunek_ksiazki`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `ksiazka`
--
ALTER TABLE `ksiazka`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `pracownik`
--
ALTER TABLE `pracownik`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `reset_hasla`
--
ALTER TABLE `reset_hasla`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `rezerwacja`
--
ALTER TABLE `rezerwacja`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `wydanie`
--
ALTER TABLE `wydanie`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `wydawnictwo`
--
ALTER TABLE `wydawnictwo`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `wypozyczenie`
--
ALTER TABLE `wypozyczenie`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
