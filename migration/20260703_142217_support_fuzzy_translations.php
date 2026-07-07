<?php

declare(strict_types=1);

/**
 * Database migration class
 *
 * @author Lionel Laffineur <lionel@tigron.be>
 */


use \Skeleton\Database\Database;

class Migration_20260703_142217_Support_fuzzy_translations extends \Skeleton\Database\Migration {

	/**
	 * Migrate up
	 *
	 * @access public
	 */
	public function up(): void {
		$db = Database::get();
		$db->query("
			ALTER TABLE `translation_target`
			ADD `fuzzy` tinyint NOT NULL DEFAULT '0' AFTER `translation`;
		");
	}

	/**
	 * Migrate down
	 *
	 * @access public
	 */
	public function down(): void {
	}
}
