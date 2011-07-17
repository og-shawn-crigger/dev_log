SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `dev_log_test_table`
-- ----------------------------
DROP TABLE IF EXISTS `dev_log_test_table`;
CREATE TABLE `dev_log_test_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `primary_hobby` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dev_log_test_table
-- ----------------------------
INSERT INTO `dev_log_test_table` VALUES ('1', 'shawn', 'street fighter');
INSERT INTO `dev_log_test_table` VALUES ('2', 'danielle', 'reading');
INSERT INTO `dev_log_test_table` VALUES ('3', 'elly', 'eating');
INSERT INTO `dev_log_test_table` VALUES ('4', 'serena', 'reading');
