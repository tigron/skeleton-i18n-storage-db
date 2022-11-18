<?php
/**
 * Database migration class
 *
 * @author Lionel Laffineur <lionel@tigron.be>
 */
namespace Skeleton\I18n;

use \Skeleton\Database\Database;

class Migration_20221109_150638_init_database extends \Skeleton\Database\Migration {

	/**
	 * Migrate up
	 *
	 * @access public
	 */
	public function up() {
		$db = Database::get();

		$db->query("
			CREATE TABLE `translation_source` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
			  `string` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
			  `created` datetime NOT NULL,
			  `updated` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `name_string` (`name`,`string`(512))
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		", []);

		$db->query("
			CREATE TABLE `translation_target` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `translation_source_id` int(11) NOT NULL,
			  `language_id` int(11) unsigned NOT NULL,
			  `translation` mediumtext COLLATE utf8_unicode_ci NOT NULL,
			  `created` datetime NOT NULL,
			  `updated` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `translation_source_id_language_id` (`translation_source_id`,`language_id`),
			  KEY `language_id` (`language_id`),
			  CONSTRAINT `translation_target_ibfk_1` FOREIGN KEY (`translation_source_id`) REFERENCES `translation_source` (`id`),
			  CONSTRAINT `translation_target_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `language` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");

	}

	/**
	 * Migrate down
	 *
	 * @access public
	 */
	public function down() {

	}
}
