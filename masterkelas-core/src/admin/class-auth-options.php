<?php
namespace MasterKelas\Admin;

/**
 * Login and Register sections and fields
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class AuthOptions
{ 
	public static function set($opt_name) {
		\Redux::set_section($opt_name, [
      'title'  => "تنظیمات احراز هویت",
      'id'     => 'auth-section',
      'desc'   => "تنظیمات مرتبط با نحوه ورود و ثبت نام کاربران",
      'icon'   => 'fa-regular fa-user-lock',
      'fields' => self::auth_fields()
    ]);

		\Redux::set_section($opt_name, [
      'title'  => "موبایل",
      'heading'  => "تنظیمات موبایل",
      'id'     => 'auth-mobile-section',
      'desc'   => "تنظیمات احراز هویت کاربران با موبایل",
      'icon'   => 'fa-regular fa-mobile',
      'fields' => self::auth_mobile_fields(),
      "subsection" => true
    ]);

		\Redux::set_section($opt_name, [
      'title'  => "ایمیل",
      'heading'  => "تنظیمات ایمیل",
      'id'     => 'auth-email-section',
      'desc'   => "تنظیمات احراز هویت کاربران با ایمیل",
      'icon'   => 'fa-regular fa-envelope',
      'fields' => self::auth_email_fields(),
      "subsection" => true
    ]);

		\Redux::set_section($opt_name, [
      'title'  => "گوگل",
      'heading'  => "تنظیمات گوگل",
      'id'     => 'auth-google-section',
      'desc'   => "تنظیمات احراز هویت کاربران با گوگل",
      'icon'   => 'fa-brands fa-google',
      'fields' => self::auth_google_fields(),
      "subsection" => true
    ]);
	}

  public static function auth_fields() {
    return [
      [
        "id" => "auth-mobile",
        "type" => "switch",
        "title" => "ورود و ثبت نام با موبایل",
        "subtitle" => "ورود و ثبت نام کاربران با ارسال کد تائید به شماره موبایل",
        "on" => "فعال",
        "off" => "غیرفعال",
        'default' => true
      ],
      [
        "id" => "auth-email",
        "type" => "switch",
        "title" => "ورود و ثبت نام با ایمیل",
        "subtitle" => "ورود و ثبت نام کاربران با ارسال کد تائید به آدرس ایمیل",
        "on" => "فعال",
        "off" => "غیرفعال",
        'default' => true
      ],
      [
        "id" => "auth-google",
        "type" => "switch",
        "title" => "ورود و ثبت نام با گوگل",
        "subtitle" => "ورود و ثبت نام کاربران با گوگل",
        "on" => "فعال",
        "off" => "غیرفعال",
        'default' => true
      ],
      
    ];
  }

  public static function auth_mobile_fields() {
    return [
      [
        "id" => "auth-mobile-code-length",
        "type" => "text",
        "title" => "تعداد ارقام کد تائید",
        "desc" => "از بین 4 تا 8 انتخاب کنید",
        "default" => 5,
        'validate_callback' => '\MasterKelas\Admin\validate_code_length'
      ],
      [
        "id" => "auth-mobile-code-expire",
        "type" => "text",
        "title" => "زمان اعتبار کد تائید",
        "subtitle" => "بعد از گذشت چند ثانیه کد تائید باطل شود؟",
        "desc" => "بر حسب ثانیه (هر 3600 ثانیه = 1 ساعت)",
        "default" => MINUTE_IN_SECONDS * 20,
        'validate' => ['numeric', 'not_empty']
      ],
      [
        "id" => "auth-mobile-code-retry",
        "type" => "text",
        "title" => "زمان درخواست ارسال مجدد کد تائید",
        "subtitle" => "بعد از گذشت چند ثانیه کاربر می تواند مجدد کد تائید درخواست کند؟",
        "desc" => "بر حسب ثانیه (هر 3600 ثانیه = 1 ساعت)",
        "default" => 45,
        'validate' => ['numeric', 'not_empty']
      ],
      [
        "id" => "auth-mobile-restrict",
        "type" => "switch",
        "title" => "محدودیت کاربران غیرایرانی",
        "subtitle" => "ورود و ثبت نام با موبایل تنها برای افراد داخل ایران فعال باشد؟",
        "on" => "بله",
        "off" => "خیر",
        'default' => true,
      ],
    ];
  }

  public static function auth_email_fields() {
    return [
      [
        "id" => "auth-email-code-length",
        "type" => "text",
        "title" => "تعداد ارقام کد تائید ایمیل",
        "desc" => "از بین 4 تا 8 انتخاب کنید",
        "default" => 6,
        'validate_callback' => '\MasterKelas\Admin\validate_code_length'
      ],
      [
        "id" => "auth-email-code-expire",
        "type" => "text",
        "title" => "زمان اعتبار کد تائید",
        "subtitle" => "بعد از گذشت چند ثانیه کد تائید باطل شود؟",
        "desc" => "بر حسب ثانیه (هر 3600 ثانیه = 1 ساعت)",
        "default" => HOUR_IN_SECONDS,
        'validate' => ['numeric', 'not_empty']
      ],
      [
        "id" => "auth-email-code-retry",
        "type" => "text",
        "title" => "زمان درخواست ارسال مجدد کد تائید",
        "subtitle" => "بعد از گذشت چند ثانیه کاربر می تواند مجدد کد تائید درخواست کند؟",
        "desc" => "بر حسب ثانیه (هر 3600 ثانیه = 1 ساعت)",
        "default" => 45,
        'validate' => ['numeric', 'not_empty']
      ],
      [
        "id" => "auth-email-allowed-providers",
        "type" => "multi_text",
        "title" => "سرویس دهنده های مجاز",
        "subtitle" => "آدرس سرویس دهنده های مجاز ایمیل را وارد کنید.",
        "desc" => "مانند: gmail.com, yahoo.com",
        'validate_callback' => '\MasterKelas\Admin\validate_email_provider',
        'default' => [
          "gmail.com",
          "yahoo.com",
          "outlook.com"
        ],
      ],
      [
        "id" => "auth-email-restrict",
        "type" => "switch",
        "title" => "محدودیت کاربران غیرایرانی",
        "subtitle" => "ورود و ثبت نام با ایمیل تنها برای افراد داخل ایران فعال باشد؟",
        "on" => "بله",
        "off" => "خیر",
        'default' => false,
      ],
    ];
  }

  public static function auth_google_fields() {
    return [
      [
        "id" => "auth-google-client-id",
        "type" => "text",
        "title" => "کلاینت آیدی",
        "desc" => "Client ID را وارد نمائید",
        "default" => "XXXXXXXXXXXXXXXXXXXXXXXXXXXXX"
      ],
      [
        "id" => "auth-google-client-secret",
        "type" => "text",
        "title" => "کلید خصوصی",
        "desc" => "Client Secret را وارد نمائید",
        "default" => "XXXXXXXXXXXXXXXXXXXXXXXXXXXXX"
      ],
      [
        "id" => "auth-google-restrict",
        "type" => "switch",
        "title" => "محدودیت کاربران غیرایرانی",
        "subtitle" => "ورود و ثبت نام با گوگل تنها برای افراد داخل ایران فعال باشد؟",
        "on" => "بله",
        "off" => "خیر",
        'default' => false,
      ],
    ];
  }
}

if ( ! function_exists( 'validate_code_length' ) ) {
  function validate_code_length($field, $value, $existing_value) {
    $error   = false;

    if (!is_numeric($value) || (int) $value > 8 || (int) $value < 4) {
      $error = true;
      $value = $existing_value;
    }

    $return['value'] = (int) $value;

    if ( true === $error ) {
      $field['msg']    = 'یک عدد بین 4 تا 8 انتخاب کنید.';
      $return['error'] = $field;
    }

    return $return;
  }
}

if ( ! function_exists( 'validate_email_provider' ) ) {
  function validate_email_provider($field, $value, $existing_value) {
    $error = false;

    if (is_array($value) && !empty($value)) {
      foreach ($value as $provider) {
        $url = "http://{$provider}";

        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
          $error = true;
          $value = $existing_value;
        }
      }
    }

    $return['value'] = $value;

    if ( true === $error ) {
      $field['msg']    = 'لطفا دامنه سرویس دهنده را وارد نمائید. (مانند: gmail.com)';
      $return['error'] = $field;
    }

    return $return;
  }
}