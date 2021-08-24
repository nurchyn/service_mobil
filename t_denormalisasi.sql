/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50731
 Source Host           : localhost:3306
 Source Schema         : service_mobil

 Target Server Type    : MySQL
 Target Server Version : 50731
 File Encoding         : 65001

 Date: 24/08/2021 11:44:00
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for t_denormalisasi
-- ----------------------------
DROP TABLE IF EXISTS `t_denormalisasi`;
CREATE TABLE `t_denormalisasi`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_perhitungan` int(11) NULL DEFAULT NULL,
  `v11` float(20, 20) NULL DEFAULT NULL,
  `v12` float(20, 20) NULL DEFAULT NULL,
  `v21` float(20, 20) NULL DEFAULT NULL,
  `v22` float(20, 20) NULL DEFAULT NULL,
  `bias_1` float(20, 20) NULL DEFAULT NULL,
  `bias_2` float(20, 20) NULL DEFAULT NULL,
  `aktivasi_z1` float(20, 20) NULL DEFAULT NULL,
  `aktivasi_z2` float(20, 20) NULL DEFAULT NULL,
  `hidden_z1` float(20, 20) NULL DEFAULT NULL,
  `hidden_z2` float(20, 20) NULL DEFAULT NULL,
  `w1` float(20, 20) NULL DEFAULT NULL,
  `w2` float(20, 20) NULL DEFAULT NULL,
  `b` float(20, 20) NULL DEFAULT NULL,
  `y` float(20, 2) NULL DEFAULT NULL,
  `hasil_denom` float(20, 2) NULL DEFAULT NULL,
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  `deleted_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of t_denormalisasi
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
