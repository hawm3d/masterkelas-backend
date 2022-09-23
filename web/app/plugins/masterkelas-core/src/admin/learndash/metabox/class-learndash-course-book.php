<?php

/**
 * LearnDash Settings Metabox for Course Book
 *
 * @since 3.0.0
 * @package LearnDash\Settings\Metaboxes
 */

if (!defined('ABSPATH')) {
	exit;
}

if ((class_exists('LearnDash_Settings_Metabox')) && (!class_exists('LearnDashCourseBook'))) {
	/**
	 * Class LearnDash Settings Metabox for Course Book
	 *
	 * @since 3.0.0
	 */
	class LearnDashCourseBook extends LearnDash_Settings_Metabox {

		/**
		 * Public constructor for class
		 *
		 * @since 3.0.0
		 */
		public function __construct() {
			// What screen ID are we showing on.
			$this->settings_screen_id = 'sfwd-courses';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_metabox_key = 'masterkelas-course-book';

			// Section label/header.
			$this->settings_section_label = "کتاب کار کلاس";

			$this->settings_section_description = "اطلاعات و مشخصات مرتبط با کتاب کار کلاس";

			// Map internal settings field ID to legacy field ID.
			$this->settings_fields_map = array(
				'mk_book_title' => 'mk_book_title',
				'mk_book_desc' => 'mk_book_desc',
				'mk_book_img' => 'mk_book_img',
				'mk_book_pages' => 'mk_book_pages',
				'mk_book_pdf_url' => 'mk_book_pdf_url',
			);
			parent::__construct();
		}

		/**
		 * Initialize the metabox settings values.
		 *
		 * @since 3.0.0
		 */
		public function load_settings_values() {
			parent::load_settings_values();
			if (true === $this->settings_values_loaded) {
				if (!isset($this->setting_option_values['mk_book_title'])) {
					$this->setting_option_values['mk_book_title'] = '';
				}

				if (!isset($this->setting_option_values['mk_book_desc'])) {
					$this->setting_option_values['mk_book_desc'] = '';
				}

				if (!isset($this->setting_option_values['mk_book_img'])) {
					$this->setting_option_values['mk_book_img'] = '';
				}

				if (!isset($this->setting_option_values['mk_book_pages'])) {
					$this->setting_option_values['mk_book_pages'] = '';
				}

				if (!isset($this->setting_option_values['mk_book_pdf_url'])) {
					$this->setting_option_values['mk_book_pdf_url'] = '';
				}
			}

			// Ensure all settings fields are present.
			foreach ($this->settings_fields_map as $_internal => $_external) {
				if (!isset($this->setting_option_values[$_internal])) {
					$this->setting_option_values[$_internal] = '';
				}
			}
		}

		/**
		 * Initialize the metabox settings fields.
		 *
		 * @since 3.0.0
		 */
		public function load_settings_fields() {
			$field_name_wrap             = false;
			$this->setting_option_fields = array(
				'mk_book_title' => array(
					'name'    => 'mk_book_title',
					'label'   => "عنوان کتاب",
					'type'    => 'text',
					'class'   => '-medium',
					'value'   => $this->setting_option_values['mk_book_title'],
					'default' => '',
				),
				'mk_book_desc' => array(
					'name'    => 'mk_book_desc',
					'label'   => "توضیحات کتاب",
					'type'    => 'textarea',
					'value'   => $this->setting_option_values['mk_book_desc'],
					'default' => '',
				),
				'mk_book_img' => array(
					'name'    => 'mk_book_img',
					'label'   => "تصویر جلد کتاب",
					'type'    => 'media-upload',
					'value'   => $this->setting_option_values['mk_book_img'],
					'default' => '',
				),
				'mk_book_pages' => array(
					'name'    => 'mk_book_pages',
					'label'   => "تعداد صفحات کتاب",
					'type'    => 'number',
					'class'   => 'small-text',
					'value'   => $this->setting_option_values['mk_book_pages'],
					'default' => '',
				),
				'mk_book_pdf_url' => array(
					'name'    => 'mk_book_pdf_url',
					'label'   => "آدرس PDF",
					'type'    => 'url',
					'class'   => 'full-text',
					'value'   => $this->setting_option_values['mk_book_pdf_url'],
					'default' => '',
				),
			);

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->setting_option_fields = apply_filters('learndash_settings_fields', $this->setting_option_fields, $this->settings_metabox_key);

			parent::load_settings_fields();
		}

		/**
		 * Filter settings values for metabox before save to database.
		 *
		 * @since 3.0.0
		 *
		 * @param array  $settings_values Array of settings values.
		 * @param string $settings_metabox_key Metabox key.
		 * @param string $settings_screen_id Screen ID.
		 *
		 * @return array $settings_values.
		 */
		public function filter_saved_fields($settings_values = array(), $settings_metabox_key = '', $settings_screen_id = '') {
			if (($settings_screen_id === $this->settings_screen_id) && ($settings_metabox_key === $this->settings_metabox_key)) {

				if (!isset($settings_values['mk_book_title'])) {
					$settings_values['mk_book_title'] = '';
				}

				if (!isset($settings_values['mk_book_desc'])) {
					$settings_values['mk_book_desc'] = '';
				}

				if (!isset($settings_values['mk_book_img'])) {
					$settings_values['mk_book_img'] = '';
				}

				if (!isset($settings_values['mk_book_pages'])) {
					$settings_values['mk_book_pages'] = '';
				}

				if (!isset($settings_values['mk_book_pdf_url'])) {
					$settings_values['mk_book_pdf_url'] = '';
				}

				/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
				$settings_values = apply_filters('learndash_settings_save_values', $settings_values, $this->settings_metabox_key);
			}

			return $settings_values;
		}

		// End of functions.
	}

	add_filter(
		'learndash_post_settings_metaboxes_init_' . learndash_get_post_type_slug('course'),
		function ($metaboxes = array()) {
			if ((!isset($metaboxes['LearnDashCourseBook'])) && (class_exists('LearnDashCourseBook'))) {
				$metaboxes['LearnDashCourseBook'] = LearnDashCourseBook::add_metabox_instance();
			}

			return $metaboxes;
		},
		50,
		1
	);
}
