<?php

/**
 * LearnDash Settings Metabox for Course Gallery
 *
 * @since 3.0.0
 * @package LearnDash\Settings\Metaboxes
 */

if (!defined('ABSPATH')) {
	exit;
}

if ((class_exists('LearnDash_Settings_Metabox')) && (!class_exists('LearnDashCourseGallery'))) {
	/**
	 * Class LearnDash Settings Metabox for Course Gallery
	 *
	 * @since 3.0.0
	 */
	class LearnDashCourseGallery extends LearnDash_Settings_Metabox {

		/**
		 * Public constructor for class
		 *
		 * @since 3.0.0
		 */
		public function __construct() {
			// What screen ID are we showing on.
			$this->settings_screen_id = 'sfwd-courses';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_metabox_key = 'masterkelas-course-gallery';

			// Section label/header.
			$this->settings_section_label = "گالری کلاس";

			$this->settings_section_description = "عکس ها و مشخصات تیزر کلاس";

			// Map internal settings field ID to legacy field ID.
			$this->settings_fields_map = array(
				'mk_trailer_url' => 'mk_trailer_url',
				'mk_trailer_poster' => 'mk_trailer_poster',
				'mk_cinematic_img' => 'mk_cinematic_img',
				'mk_portrait_img' => 'mk_portrait_img',
				'mk_landscape_img' => 'mk_landscape_img',
				'mk_typography_img' => 'mk_typography_img',
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
				if (!isset($this->setting_option_values['mk_trailer_url'])) {
					$this->setting_option_values['mk_trailer_url'] = '';
				}

				if (!isset($this->setting_option_values['mk_trailer_poster'])) {
					$this->setting_option_values['mk_trailer_poster'] = 0;
				}

				if (!isset($this->setting_option_values['mk_cinematic_img'])) {
					$this->setting_option_values['mk_cinematic_img'] = 0;
				}

				if (!isset($this->setting_option_values['mk_portrait_img'])) {
					$this->setting_option_values['mk_portrait_img'] = 0;
				}

				if (!isset($this->setting_option_values['mk_landscape_img'])) {
					$this->setting_option_values['mk_landscape_img'] = 0;
				}

				if (!isset($this->setting_option_values['mk_typography_img'])) {
					$this->setting_option_values['mk_typography_img'] = 0;
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
				'mk_trailer_url' => array(
					'name'    => 'mk_trailer_url',
					'label'   => "ویدئو تیزر",
					'type'    => 'url',
					'class'   => 'full-text',
					'value'   => $this->setting_option_values['mk_trailer_url'],
				),
				'mk_trailer_poster' => array(
					'name'    => 'mk_trailer_poster',
					'label'   => "پوستر ویدئو تیزر",
					'type'    => 'media-upload',
					'value'   => $this->setting_option_values['mk_trailer_poster'],
					'validate_callback' => array($this, 'validate_section_field_media_upload'),
					'validate_args'     => array(
						'allow_empty' => 1,
					),
				),
				'mk_cinematic_img' => array(
					'name'    => 'mk_cinematic_img',
					'label'   => "عکس سینماتیک (Cinematic)",
					'type'    => 'media-upload',
					'value'   => $this->setting_option_values['mk_cinematic_img'],
					'validate_callback' => array($this, 'validate_section_field_media_upload'),
					'validate_args'     => array(
						'allow_empty' => 1,
					),
				),
				'mk_portrait_img' => array(
					'name'    => 'mk_portrait_img',
					'label'   => "عکس پرتره (Portrait)",
					'type'    => 'media-upload',
					'value'   => $this->setting_option_values['mk_portrait_img'],
					'validate_callback' => array($this, 'validate_section_field_media_upload'),
					'validate_args'     => array(
						'allow_empty' => 1,
					),
				),
				'mk_landscape_img' => array(
					'name'    => 'mk_landscape_img',
					'label'   => "عکس صفحه عریض (Landscape)",
					'type'    => 'media-upload',
					'value'   => $this->setting_option_values['mk_landscape_img'],
					'validate_callback' => array($this, 'validate_section_field_media_upload'),
					'validate_args'     => array(
						'allow_empty' => 1,
					),
				),
				'mk_typography_img' => array(
					'name'    => 'mk_typography_img',
					'label'   => "عکس تایپوگرافی (Typography)",
					'type'    => 'media-upload',
					'value'   => $this->setting_option_values['mk_typography_img'],
					'validate_callback' => array($this, 'validate_section_field_media_upload'),
					'validate_args'     => array(
						'allow_empty' => 1,
					),
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

				if (!isset($settings_values['mk_trailer_url'])) {
					$settings_values['mk_trailer_url'] = '';
				}

				if (!isset($settings_values['mk_trailer_poster'])) {
					$settings_values['mk_trailer_poster'] = 0;
				}

				if (!isset($settings_values['mk_cinematic_img'])) {
					$settings_values['mk_cinematic_img'] = 0;
				}

				if (!isset($settings_values['mk_portrait_img'])) {
					$settings_values['mk_portrait_img'] = 0;
				}

				if (!isset($settings_values['mk_landscape_img'])) {
					$settings_values['mk_landscape_img'] = 0;
				}

				if (!isset($settings_values['mk_typography_img'])) {
					$settings_values['mk_typography_img'] = 0;
				}

				/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
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
		'learndash_post_settings_metaboxes_init_' . learndash_get_post_type_slug('course'),
		function ($metaboxes = array()) {
			if ((!isset($metaboxes['LearnDashCourseGallery'])) && (class_exists('LearnDashCourseGallery'))) {
				$metaboxes['LearnDashCourseGallery'] = LearnDashCourseGallery::add_metabox_instance();
			}

			return $metaboxes;
		},
		50,
		1
	);
}
