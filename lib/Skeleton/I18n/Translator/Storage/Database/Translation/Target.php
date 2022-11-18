<?php
/**
 * Translator Storage Database Translation class
 *
 * @author Lionel Laffineur <lionel@tigron.be>
 */

namespace Skeleton\I18n\Translator\Storage\Database\Translation;

use \Skeleton\Database\Database;

class Target {
	use \Skeleton\Object\Model;
	use \Skeleton\Object\Get;
	use \Skeleton\Object\Save;
	use \Skeleton\Object\Delete;

	/**
	 * Class configuration
	 *
	 * @access private
	 * @var array $class_configuration
	 */
	private static $class_configuration = [
		'database_table' => 'translation_target'
	];

	/**
	 * get by source
	 *
	 * @access public
	 * @param Source $source
	 * @return Translation
	 */
	public static function get_by_source(Source $source) {
		$db = Database::get();
		$results = [];
		$ids = $db->get_column("SELECT id FROM translation_target WHERE translation_source_id = ?", [ $source->id ]);
		foreach ($ids as $id) {
			$results[] = self::get_by_id($id);
		}
		return $results;
	}

	/**
	 * get by source language
	 *
	 * @access public
	 * @param Source $source
	 * @param Language $language
	 * @return Translation
	 */
	public static function get_by_source_language(Source $source, \Skeleton\I18n\Language $language) {
		$db = Database::get();
		$row = $db->get_row("SELECT * FROM translation_target WHERE translation_source_id = ? AND language_id = ?", [ $source->id, $language->id ]);
		if ($row == null) {
			throw new \Exception("No translation found");
		}
		$target = new self();
		$target->id= $row['id'];
		$target->details = $row;
		return $target;
	}

	public static function get_last_modified($translator_name) {
		$db = Database::get();
		$updated = $db->get_one('
			SELECT ifnull(translation_target.updated, translation_target.created) as last_modified
			FROM translation_source, translation_target
			WHERE translation_target.translation_source_id=translation_source.id and name= ?
			ORDER BY last_modified DESC LIMIT 1', [ $translator_name ]);
		if ($updated === null) {
			return null;
		}
		return new \DateTime($updated);
	}
}
