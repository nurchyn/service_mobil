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

 Date: 24/08/2021 11:44:09
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for t_kendaraan_masuk
-- ----------------------------
DROP TABLE IF EXISTS `t_kendaraan_masuk`;
CREATE TABLE `t_kendaraan_masuk`  (
  `id` int(32) NOT NULL AUTO_INCREMENT,
  `id_customer` int(32) NULL DEFAULT NULL,
  `id_kendaraan` int(32) NULL DEFAULT NULL,
  `invoice` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `id_mekanik` int(34) NULL DEFAULT NULL,
  `keluhan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  `deleted_at` datetime NULL DEFAULT NULL,
  `status` int(34) NULL DEFAULT NULL COMMENT 'null = kendaraan masuk, 1 = dipilih mekanik, 2 = selesai dikerjakan',
  `tgl_selesai` datetime NULL DEFAULT NULL,
  `hitung_pekerjaan` int(11) NULL DEFAULT NULL,
  `hitung_onderdil` int(11) NULL DEFAULT NULL,
  `lama_service` int(11) NULL DEFAULT NULL,
  `hitung_estimasi` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of t_kendaraan_masuk
-- ----------------------------
INSERT INTO `t_kendaraan_masuk` VALUES (4, 1, 1, 'INV-21081100000', NULL, 'coba-coba', '2021-08-01 09:52:50', NULL, NULL, NULL, '2021-08-03 01:15:56', 1, 5, 2, NULL);
INSERT INTO `t_kendaraan_masuk` VALUES (5, 1, 1, 'INV-21081100001', NULL, 'tes coba', '2021-08-11 09:51:26', NULL, NULL, NULL, '2021-08-20 01:15:22', 3, 1, 9, NULL);
INSERT INTO `t_kendaraan_masuk` VALUES (6, 2, 2, 'INV-21081100002', NULL, 'coba tes', '2021-08-20 09:52:50', NULL, NULL, NULL, '2021-08-23 01:15:56', 4, 2, 2, 2);

SET FOREIGN_KEY_CHECKS = 1;
