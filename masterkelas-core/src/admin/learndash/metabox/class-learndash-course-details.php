<?php

/**
 * LearnDash Settings Metabox for Course Details
 *
 * @since 3.0.0
 * @package LearnDash\Settings\Metaboxes
 */

if (!defined('ABSPATH')) {
	exit;
}

if ((class_exists('LearnDash_Settings_Metabox')) && (!class_exists('LearnDashCourseDetails'))) {
	/**
	 * Class LearnDash Settings Metabox for Course Details
	 *
	 * @since 3.0.0
	 */
	class LearnDashCourseDetails extends LearnDash_Settings_Metabox {

		/**
		 * Public constructor for class
		 *
		 * @since 3.0.0
		 */
		public function __construct() {
			// What screen ID are we showing on.
			$this->settings_screen_id = 'sfwd-courses';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_metabox_key = 'masterkelas-course-details';

			// Section label/header.
			$this->settings_section_label = "مشخصات کلاس";

			$this->settings_section_description = "اطلاعات مرتبط با استاد، زمان و تنظیمات کلاس";

			// Map internal settings field ID to legacy field ID.
			$this->settings_fields_map = array(
				'mk_short_title' => 'mk_short_title',
				'mk_restricted' => 'mk_restricted',
				'mk_short_desc' => 'mk_short_desc',
				'mk_master_name_fa' => 'mk_master_name_fa',
				'mk_master_name_en' => 'mk_master_name_en',
				'mk_duration' => 'mk_duration',
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

				if (!isset($this->setting_option_values['mk_restricted'])) {
					$this->setting_option_values['mk_restricted'] = '';
				}

				if (!isset($this->setting_option_values['mk_short_desc'])) {
					$this->setting_option_values['mk_short_desc'] = '';
				}

				if (!isset($this->setting_option_values['mk_master_name_fa'])) {
					$this->setting_option_values['mk_master_name_fa'] = '';
				}

				if (!isset($this->setting_option_values['mk_master_name_en'])) {
					$this->setting_option_values['mk_master_name_en'] = '';
				}

				if (!isset($this->setting_option_values['mk_duration'])) {
					$this->setting_option_values['mk_duration'] = '';
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
					'label'   => "عنوان کوتاه کلاس",
					'type'    => 'text',
					'class'   => '-medium',
					'value'   => $this->setting_option_values['mk_short_title'],
					'default' => '',
				),
				'mk_short_desc' => array(
					'name'    => 'mk_short_desc',
					'label'   => "توضیح کوتاه کلاس",
					'type'    => 'textarea',
					'value'   => $this->setting_option_values['mk_short_desc'],
					'default' => '',
				),
				'mk_restricted' => array(
					'name'    => 'mk_restricted',
					'label'   => "محدودیت نمایش براساس IP",
					'type'    => 'checkbox-switch',
					'options'   => array(
						''   => 'غیرفعال',
						'on' => 'فعال',
					),
					'value'   => $this->setting_option_values['mk_restricted'],
					'default' => '',
				),
				'mk_master_name_fa' => array(
					'name'    => 'mk_master_name_fa',
					'label'   => "نام استاد (فارسی)",
					'type'    => 'text',
					'class'   => '-medium',
					'value'   => $this->setting_option_values['mk_master_name_fa'],
					'default' => '',
				),
				'mk_master_name_en' => array(
					'name'    => 'mk_master_name_en',
					'label'   => "نام استاد (انگلیسی)",
					'type'    => 'text',
					'class'   => '-medium',
					'value'   => $this->setting_option_values['mk_master_name_en'],
					'default' => '',
				),
				'mk_duration' => array(
					'name'    => 'mk_duration',
					'label'   => "مدت زمان کلاس",
					'type'    => 'timer-entry',
					'class'   => 'small-text',
					'value'   => $this->setting_option_values['mk_duration'],
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

				if (!isset($settings_values['mk_short_title'])) {
					$settings_values['mk_short_title'] = '';
				}

				if (!isset($settings_values['mk_restricted'])) {
					$settings_values['mk_restricted'] = '';
				}

				if (!isset($settings_values['mk_short_desc'])) {
					$settings_values['mk_short_desc'] = '';
				}

				if (!isset($settings_values['mk_master_name_fa'])) {
					$settings_values['mk_master_name_fa'] = '';
				}

				if (!isset($settings_values['mk_master_name_en'])) {
					$settings_values['mk_master_name_en'] = '';
				}

				if (!isset($settings_values['mk_duration'])) {
					$settings_values['mk_duration'] = '';
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
			if ((!isset($metaboxes['LearnDashCourseDetails'])) && (class_exists('LearnDashCourseDetails'))) {
				$metaboxes['LearnDashCourseDetails'] = LearnDashCourseDetails::add_metabox_instance();
			}

			return $metaboxes;
		},
		50,
		1
	);
}
