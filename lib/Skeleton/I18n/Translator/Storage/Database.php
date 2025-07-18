<?php
/**
 * Translator\Database class
 *
 */

namespace Skeleton\I18n\Translator\Storage;

class Database extends \Skeleton\I18n\Translator\Storage {

	/**
	 * Add a translation
	 *
	 * @access public
	 * @param string $string
	 * @param string $translated_string
	 */
	public function add_translation($string, $translated, bool $fuzzy = false): void {
		parent::add_translation($string, $translated);

		try {
			$translation_source = \Skeleton\I18n\Translator\Storage\Database\Translation\Source::get_by_name_string($this->name, $string);
		} catch (\Exception $e) {
			$translation_source = new \Skeleton\I18n\Translator\Storage\Database\Translation\Source();
			$translation_source->name = $this->name;
			$translation_source->string = $string;
			$translation_source->save();
		}

		try {
			$translation_target = \Skeleton\I18n\Translator\Storage\Database\Translation\Target::get_by_source_language($translation_source, $this->language);
			$translation_target->translation = $translated;
			$translation_target->save();
		} catch (\Exception $e) {
			$translation_target = new \Skeleton\I18n\Translator\Storage\Database\Translation\Target();
			$translation_target->translation_source_id = $translation_source->id;
			$translation_target->language_id = $this->language->id;
			$translation_target->translation = $translated;
			$translation_target->save();
		}
	}

	/**
	 * Delete a translation
	 *
	 * @access public
	 * @param string $string
	 */
	public function delete_translation($string) {
		parent::delete_translation($string);

		try {
			$translation_source = \Skeleton\I18n\Translator\Storage\Database\Translation\Source::get_by_name_string($this->name, $string);
		} catch (\Exception $e) {
			return;
		}

		$translation_targets = \Skeleton\I18n\Translator\Storage\Database\Translation\Target::get_by_source($translation_source);
		foreach ($translation_targets as $key => $translation_target) {
			if ($translation_target->language->name_short == $this->language->name_short) {
				unset($translation_targets[$key]);
				$translation_target->delete();
			}
		}

		if (count($translation_targets) == 0) {
			$translation_source->delete();
		}
	}

	/**
	 * Load translations from storage
	 *
	 * @access public
	 * @return array $translation
	 */
	public function load_translations(): ?array {
		$sources = \Skeleton\I18n\Translator\Storage\Database\Translation\Source::get_by_name($this->name);
		$translations= [];
		foreach ($sources as $source) {
			try {
				$translation_target = \Skeleton\I18n\Translator\Storage\Database\Translation\Target::get_by_source_language($source, $this->language);
				$translations[$source->string] = $translation_target->translation;
			} catch (\Exception $e) {
				continue;
			}
		}
		return $translations;
	}

	/**
	 * Empty the storage for the given language
	 *
	 * @access public
	 */
	public function empty(): void {
		$sources = \Skeleton\I18n\Translator\Storage\Database\Translation\Source::get_by_name($this->name);
		foreach ($sources as $source) {
			try {
				$translation_target = \Skeleton\I18n\Translator\Storage\Database\Translation\Target::get_by_source_language($source, $this->language);
			} catch (\Exception $e) {
				continue;
			}
			$translation_target->delete();
		}
	}

	/**
	 * Save the translation to storage
	 *
	 * @access public
	 */
	public function save_translations(): void {
		// Nothing to do. Every action is live
	}

	/**
	 * Get last modified
	 *
	 * @access public
	 * @return \Datetime $last_modified
	 */
	public function get_last_modified(): ?\Datetime {
		return \Skeleton\I18n\Translator\Storage\Database\Translation\Target::get_last_modified($this->name);
	}
}
