-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: %%DATABASE%%:3306
-- Generation Time: Jan 07, 2025 at 03:39 PM
-- Server version: 10.5.27-MariaDB
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `%%DATABASE%%` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `%%DATABASE%%`;

CREATE TABLE IF NOT EXISTS `%%PREFIX%%Form` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` tinytext NOT NULL,
  `Title` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `Description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `Content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `Image` varchar(1024) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `Method` tinytext DEFAULT NULL,
  `Action` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `Target` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `Email` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `Template` tinytext NOT NULL,
  `Access` int(11) NOT NULL DEFAULT 0,
  `Status` tinytext DEFAULT NULL,
  `Attributes` longtext DEFAULT NULL,
  `Style` longtext DEFAULT NULL,
  `Script` longtext DEFAULT NULL,
  `CreateTime` datetime NOT NULL DEFAULT current_timestamp(),
  `UpdateTime` datetime NOT NULL DEFAULT current_timestamp(),
  `MetaData` longtext DEFAULT NULL,
  PRIMARY KEY (`Id` )
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS `%%PREFIX%%Form_Field` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `FormId` int(11) DEFAULT NULL,
  `Type` tinytext NOT NULL,
  `Name` tinytext NOT NULL,
  `Title` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `Description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `Value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `Options` longtext DEFAULT NULL,
  `Required` tinyint DEFAULT NULL,
  `Attributes` longtext DEFAULT NULL,
  `Priority` int(11) NOT NULL DEFAULT 0,
  `Access` int(11) NOT NULL DEFAULT 0,
  `Status` tinytext DEFAULT NULL,
  `CreateTime` datetime NOT NULL DEFAULT current_timestamp(),
  `UpdateTime` datetime NOT NULL DEFAULT current_timestamp(),
  `MetaData` longtext DEFAULT NULL,
  PRIMARY KEY (`Id` )
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS `%%PREFIX%%Form_Inbox` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `FormId` int(11) DEFAULT NULL,
  `UserId` int(11) DEFAULT NULL,
  `Data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `CreateTime` datetime NOT NULL DEFAULT current_timestamp(),
  `UpdateTime` datetime NOT NULL DEFAULT current_timestamp(),
  `MetaData` longtext DEFAULT NULL,
  PRIMARY KEY (`Id` )
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

COMMIT;