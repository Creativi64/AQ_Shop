-- `##_plg_products_attributes` create size:XS,S,M,L  colour:red,black,green,blue  material:viscose,silk  weight:100gr,200gr,500gr

INSERT INTO `##_plg_products_attributes` (`attributes_id`,`attributes_parent`,`attributes_model`,`attributes_image`,`attributes_color`,`sort_order`,`status`,`attributes_templates_id`) VALUES (1,0,'size',NULL,'',0,1,0);
INSERT INTO `##_plg_products_attributes` (`attributes_id`,`attributes_parent`,`attributes_model`,`attributes_image`,`attributes_color`,`sort_order`,`status`,`attributes_templates_id`) VALUES (2,0,'material',NULL,'',1,1,0);
INSERT INTO `##_plg_products_attributes` (`attributes_id`,`attributes_parent`,`attributes_model`,`attributes_image`,`attributes_color`,`sort_order`,`status`,`attributes_templates_id`) VALUES (3,0,'colour',NULL,'',-5,1,2);
INSERT INTO `##_plg_products_attributes` (`attributes_id`,`attributes_parent`,`attributes_model`,`attributes_image`,`attributes_color`,`sort_order`,`status`,`attributes_templates_id`) VALUES (4,1,'XS',NULL,'',0,1,0);
INSERT INTO `##_plg_products_attributes` (`attributes_id`,`attributes_parent`,`attributes_model`,`attributes_image`,`attributes_color`,`sort_order`,`status`,`attributes_templates_id`) VALUES (5,1,'S',NULL,'',1,1,0);
INSERT INTO `##_plg_products_attributes` (`attributes_id`,`attributes_parent`,`attributes_model`,`attributes_image`,`attributes_color`,`sort_order`,`status`,`attributes_templates_id`) VALUES (6,1,'M',NULL,'',2,1,0);
INSERT INTO `##_plg_products_attributes` (`attributes_id`,`attributes_parent`,`attributes_model`,`attributes_image`,`attributes_color`,`sort_order`,`status`,`attributes_templates_id`) VALUES (7,1,'L',NULL,'',3,1,0);
INSERT INTO `##_plg_products_attributes` (`attributes_id`,`attributes_parent`,`attributes_model`,`attributes_image`,`attributes_color`,`sort_order`,`status`,`attributes_templates_id`) VALUES (8,2,'viscose',NULL,'',0,1,0);
INSERT INTO `##_plg_products_attributes` (`attributes_id`,`attributes_parent`,`attributes_model`,`attributes_image`,`attributes_color`,`sort_order`,`status`,`attributes_templates_id`) VALUES (9,2,'silk',NULL,'',0,1,0);
INSERT INTO `##_plg_products_attributes` (`attributes_id`,`attributes_parent`,`attributes_model`,`attributes_image`,`attributes_color`,`sort_order`,`status`,`attributes_templates_id`) VALUES (10,3,'black',NULL,'#3e363a',0,1,0);
INSERT INTO `##_plg_products_attributes` (`attributes_id`,`attributes_parent`,`attributes_model`,`attributes_image`,`attributes_color`,`sort_order`,`status`,`attributes_templates_id`) VALUES (11,3,'red',NULL,'#d43d2c',0,1,0);
INSERT INTO `##_plg_products_attributes` (`attributes_id`,`attributes_parent`,`attributes_model`,`attributes_image`,`attributes_color`,`sort_order`,`status`,`attributes_templates_id`) VALUES (12,3,'green',NULL,'#79ce3b',0,1,0);
INSERT INTO `##_plg_products_attributes` (`attributes_id`,`attributes_parent`,`attributes_model`,`attributes_image`,`attributes_color`,`sort_order`,`status`,`attributes_templates_id`) VALUES (13,3,'blue',NULL,'#349edb',0,1,0);
INSERT INTO `##_plg_products_attributes` (`attributes_id`,`attributes_parent`,`attributes_model`,`attributes_image`,`attributes_color`,`sort_order`,`status`,`attributes_templates_id`) VALUES (14,0,'weight',NULL,'',0,1,0);
INSERT INTO `##_plg_products_attributes` (`attributes_id`,`attributes_parent`,`attributes_model`,`attributes_image`,`attributes_color`,`sort_order`,`status`,`attributes_templates_id`) VALUES (15,14,'100gr',NULL,'',0,1,0);
INSERT INTO `##_plg_products_attributes` (`attributes_id`,`attributes_parent`,`attributes_model`,`attributes_image`,`attributes_color`,`sort_order`,`status`,`attributes_templates_id`) VALUES (16,14,'200gr',NULL,'',1,1,0);
INSERT INTO `##_plg_products_attributes` (`attributes_id`,`attributes_parent`,`attributes_model`,`attributes_image`,`attributes_color`,`sort_order`,`status`,`attributes_templates_id`) VALUES (17,14,'500gr',NULL,'',2,1,0);


