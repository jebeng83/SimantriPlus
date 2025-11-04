/*
 Navicat Premium Data Transfer

 Source Server         : LOCALHOST
 Source Server Type    : MySQL
 Source Server Version : 100420 (10.4.20-MariaDB)
 Source Host           : 127.0.0.1:3306
 Source Schema         : simrs_bunda_dev

 Target Server Type    : MySQL
 Target Server Version : 100420 (10.4.20-MariaDB)
 File Encoding         : 65001

 Date: 23/06/2024 07:32:21
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for antripendaftaran
-- ----------------------------
DROP TABLE IF EXISTS `antripendaftaran`;
CREATE TABLE `antripendaftaran` (
  `nomor` char(5) NOT NULL,
  `no_rawat` varchar(17) DEFAULT NULL,
  `taskid_1` datetime DEFAULT NULL,
  `taskid_2` datetime DEFAULT NULL,
  PRIMARY KEY (`nomor`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for antripendaftaran_nomor
-- ----------------------------
DROP TABLE IF EXISTS `antripendaftaran_nomor`;
CREATE TABLE `antripendaftaran_nomor` (
  `nomor` char(5) NOT NULL,
  `status` enum('0','1','2','3') DEFAULT NULL,
  `jam` datetime DEFAULT NULL,
  `loket` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`nomor`),
  KEY `nomor` (`nomor`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

SET FOREIGN_KEY_CHECKS = 1;
