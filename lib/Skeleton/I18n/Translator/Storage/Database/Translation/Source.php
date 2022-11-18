<?php
/**
 * Translator Storage Database Translation Source class
 *
 * @author Lionel Laffineur <lionel@tigron.be>
 */

namespace Skeleton\I18n\Translator\Storage\Database\Translation;

use \Skeleton\Database\Database;

class Source {
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
		'database_table' => 'translation_source'
	];

	/**
	 * get translation targets
	 *
	 * @access public
	 * @return Target[]
	 */
	public function get_translation_targets() {
		return Target::get_by_source($this);
	}

	/**
	 * get by name string
	 *
	 * @access public
	 * @param $name
	 * @param $string
	 * @return Source[] $sources
	 */
	public static function get_by_name_string($name, $string) {
		$db = Database::get();
		$row = $db->get_row("SELECT * FROM translation_source WHERE name = ? AND string = ?", [ $name, $string ]);
		if ($row == null) {
			throw new \Exception("Source not found");
		}
		$source = new self();
		$source->id = $row['id'];
		$source->details = $row;
		return $source;
	}

	/**
	 * get by name
	 *
	 * @access public
	 * @param $name
	 * @return Source[] $sources
	 */
	public static function get_by_name($name) {
		$db = Database::get();
		$data = $db->get_all("SELECT * FROM translation_source WHERE name = ?", [ $name ]);
		$result = [];
		foreach ($data as $row) {
			$source = new self();
			$source->id = $row['id'];
			$source->details = $row;
			$result[] = $source;
		}
		return $result;
	}
}
