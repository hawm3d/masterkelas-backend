<?php

/**
 * LearnDash Settings Metabox for Lesson Details
 *
 * @since 3.0.0
 * @package LearnDash\Settings\Metaboxes
 */

if (!defined('ABSPATH')) {
	exit;
}

if ((class_exists('LearnDash_Settings_Metabox')) && (!class_exists('LearnDashLessonDetails'))) {
	/**
	 * Class LearnDash Settings Metabox for Lesson Details
	 *
	 * @since 3.0.0
	 */
	class LearnDashLessonDetails extends LearnDash_Settings_Metabox {

		/**
		 * Public constructor for class
		 *
		 * @since 3.0.0
		 */
		public function __construct() {
			// What screen ID are we showing on.
			$this->settings_screen_id = 'sfwd-lessons';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_metabox_key = 'masterkelas-lesson-details';

			// Section label/header.
			$this->settings_section_label = "مشخصات درس";

			$this->settings_section_description = "اطلاعات و تنظیمات عمومی درس";

			// Map internal settings field ID to legacy field ID.
			$this->settings_fields_map = array(
				'mk_short_title' => 'mk_short_title',
				'mk_short_desc' => 'mk_short_desc',
				'mk_duration' => 'mk_duration',
				'mk_lesson_poster' => 'mk_lesson_poster',
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
				if (!isset($this->setting_option_values['mk_short_title'])) {
					$this->setting_option_values['mk_short_title'] = '';
				}

				if (!isset($this->setting_option_values['mk_short_desc'])) {
					$this->setting_option_values['mk_short_desc'] = '';
				}

				if (!isset($this->setting_option_values['mk_duration'])) {
					$this->setting_option_values['mk_duration'] = '';
				}

				if (!isset($this->setting_option_values['mk_lesson_poster'])) {
					$this->setting_option_values['mk_lesson_poster'] = 0;
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
				'mk_short_title' => array(
					'name'    => 'mk_short_title',
					'label'   => "عنوان کوتاه درس",
					'type'    => 'text',
					'class'   => '-medium',
					'value'   => $this->setting_option_values['mk_short_title'],
					'default' => '',
				),
				'mk_short_desc' => array(
					'name'    => 'mk_short_desc',
					'label'   => "توضیح کوتاه درس",
					'type'    => 'textarea',
					'value'   => $this->setting_option_values['mk_short_desc'],
					'default' => '',
				),
				'mk_duration' => array(
					'name'    => 'mk_duration',
					'label'   => "مدت زمان درس",
					'type'    => 'timer-entry',
					'class'   => 'small-text',
					'value'   => $this->setting_option_values['mk_duration'],
					'default' => '',
				),
				'mk_lesson_poster' => array(
					'name'    => 'mk_lesson_poster',
					'label'   => "پوستر درس",
					'type'    => 'media-upload',
					'value'   => $this->setting_option_values['mk_lesson_poster'],
					'validate_callback' => array($this, 'validate_section_field_media_upload'),
					'validate_args'     => array(
						'allow_empty' => 1,
					),
				),
			);

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-lesson-access-settings.php */
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

				if (!isset($settings_values['mk_short_title'])) {
					$settings_values['mk_short_title'] = '';
				}

				if (!isset($settings_values['mk_short_desc'])) {
					$settings_values['mk_short_desc'] = '';
				}

				if (!isset($settings_values['mk_duration'])) {
					$settings_values['mk_duration'] = '';
				}

				if (!isset($settings_values['mk_lesson_poster'])) {
					$settings_values['mk_lesson_poster'] = 0;
				}

				/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-lesson-access-settings.php */
				$settings_values = apply_filters('learndash_settings_save_values', $settings_values, $this->settings_metabox_key);
			}

			return $settings_values;
		}

		/**
		 * Validate settings field.
		 *
		 * @since 3.0.0
		 *
		 * @param string $val Value to be validated.
		 * @param string $key settings fields key.
		 * @param array  $args Settings field args array.
		 *
		 * @return integer $val.
		 */
		public function validate_section_field_media_upload($val, $key, $args = array()) {
			// Get the digits only.
			$val = absint($val);
			if ((isset($args['field']['validate_args']['allow_empty'])) && (true === $args['field']['validate_args']['allow_empty']) && (empty($val))) {
				$val = '';
			}
			return $val;
		}

		// End of functions.
	}

	add_filter(
		'learndash_post_settings_metaboxes_init_' . learndash_get_post_type_slug('lesson'),
		function ($metaboxes = array()) {
			if ((!isset($metaboxes['LearnDashLessonDetails'])) && (class_exists('LearnDashLessonDetails'))) {
				$metaboxes['LearnDashLessonDetails'] = LearnDashLessonDetails::add_metabox_instance();
			}

			return $metaboxes;
		},
		50,
		1
	);
}
