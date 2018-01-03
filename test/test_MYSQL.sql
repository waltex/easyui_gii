-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Gen 03, 2018 alle 11:21
-- Versione del server: 5.5.36
-- PHP Version: 5.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `test`
--

DELIMITER $$
--
-- Procedure
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `test_multi_sets`()
    DETERMINISTIC
begin
        select user() as first_col;
        select user() as first_col, now() as second_col;
        select user() as first_col, now() as second_col, now() as third_col;
        end$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struttura della tabella `abb_crud`
--

CREATE TABLE IF NOT EXISTS `abb_crud` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CAMPO1` varchar(25) DEFAULT NULL,
  `COMBO` int(11) DEFAULT NULL,
  `DTIN` date DEFAULT NULL,
  `DTFI` date DEFAULT NULL,
  `ATTIVO` int(11) DEFAULT NULL,
  `ID2` int(11) DEFAULT NULL,
  `DT_INS` date DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `FK_ABB_CRUD` (`COMBO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `abb_crud_combo`
--

CREATE TABLE IF NOT EXISTS `abb_crud_combo` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `DESCRI` varchar(25) DEFAULT NULL,
  `DT_INS` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `abb_crud`
--
ALTER TABLE `abb_crud`
  ADD CONSTRAINT `FK_ABB_CRUD` FOREIGN KEY (`COMBO`) REFERENCES `abb_crud_combo` (`ID`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
