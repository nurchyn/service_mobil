/*
 Navicat Premium Data Transfer

 Source Server         : local-mysql
 Source Server Type    : MySQL
 Source Server Version : 100413
 Source Host           : localhost:3306
 Source Schema         : service_mobil

 Target Server Type    : MySQL
 Target Server Version : 100413
 File Encoding         : 65001

 Date: 22/08/2021 16:18:36
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for t_perhitungan
-- ----------------------------
DROP TABLE IF EXISTS `t_perhitungan`;
CREATE TABLE `t_perhitungan`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_kendaraan_1` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'x1',
  `id_kendaraan_2` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'x2',
  `arr_input_x1` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `arr_input_x2` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `arr_input_t` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `alpha` float(20, 2) NULL DEFAULT NULL,
  `arr_bobot_v11_awal` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `arr_bobot_v12_awal` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `arr_bobot_v21_awal` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `arr_bobot_v22_awal` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `arr_bias_1_awal` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `arr_bias_2_awal` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `arr_bobot_w1_awal` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `arr_bobot_w2_awal` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `arr_bobot_b_awal` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `epoch` int(11) NULL DEFAULT 0,
  `jml_baris_input` int(11) NULL DEFAULT NULL,
  `mse_terkecil` float(20, 20) NULL DEFAULT NULL,
  `created_at` datetime(0) NULL DEFAULT NULL,
  `updated_at` datetime(0) NULL DEFAULT NULL,
  `deleted_at` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of t_perhitungan
-- ----------------------------
INSERT INTO `t_perhitungan` VALUES (1, '1', '2', '[0.5,0,0.75,0.25,1]', '[0.25,0,1,0.5,1]', '[0.0555555555555556,0,0.444444444444444,0.666666666666667,1]', 0.10, '[0.03,0.03,0.03,0.03,0.03]', '[0.02,0.02,0.02,0.02,0.02]', '[0.2,0.2,0.2,0.2,0.2,0.2]', '[0.3,0.3,0.3,0.3,0.3,0.3]', '[0.7,0.7,0.7,0.7,0.7]', '[0.3,0.3,0.3,0.3,0.3]', '[0.5,0.5,0.5,0.5,0.5]', '[0.09,0.09,0.09,0.09,0.09]', '[0.31,0.31,0.31,0.31,0.31]', 10, 5, 0.09701459854841232000, '2021-08-22 16:16:53', NULL, NULL);

SET FOREIGN_KEY_CHECKS = 1;
