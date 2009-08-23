-- phpMyAdmin SQL Dump
-- version 3.2.0-beta1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Czas wygenerowania: 23 Sie 2009, 13:40
-- Wersja serwera: 5.0.75
-- Wersja PHP: 5.2.6-3ubuntu4.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Baza danych: `test`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `table`
--

CREATE TABLE IF NOT EXISTS `table` (
  `id` int(11) NOT NULL,
  `'table'` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `table`
--


-- --------------------------------------------------------

--
-- Struktura tabeli dla  `test`
--

CREATE TABLE IF NOT EXISTS `test` (
  `id` int(11) NOT NULL auto_increment COMMENT 'Id',
  `Nazwa` text NOT NULL COMMENT 'Nazwa opis',
  `publicated` tinyint(1) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `publicated` (`publicated`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Zrzut danych tabeli `test`
--


