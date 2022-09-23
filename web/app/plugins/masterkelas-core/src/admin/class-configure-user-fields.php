<?php
namespace MasterKelas\Admin;

use MasterKelas\Validator;

/**
 * Configure User Fields
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class ConfigureUserFields
{ 
	public static function hooks() {
		add_action('user_new_form', [__CLASS__, 'user_new_form']);
		add_action('register_form', [__CLASS__, 'show_user_profile']);
		add_action('show_user_profile', [__CLASS__, 'show_user_profile']);
		add_action('edit_user_profile', [__CLASS__, 'show_user_profile']);
		add_action('user_profile_update_errors', [__CLASS__, 'user_profile_update_errors'], 10, 3);
		add_filter('registration_errors', [__CLASS__, 'registration_errors'], 10, 3);
		add_action('personal_options_update', [__CLASS__, 'personal_options_update']);
		add_action('edit_user_profile_update', [__CLASS__, 'personal_options_update']);
		add_action('user_register', [__CLASS__, 'personal_options_update']);
		add_action('edit_user_created_user', [__CLASS__, 'personal_options_update']);
	}

  /**
   * Add Mobile field to User
   */
  public static function user_new_form( $operation ) {
    ?>
    <script type="text/javascript">
        jQuery('#email').closest('tr').removeClass('form-required').find('.description').remove();
        // Uncheck send new user email option by default
        <?php if (isset($operation) && $operation === 'add-new-user') : ?>
            jQuery('#send_user_notification').removeAttr('checked');
        <?php endif; ?>
    </script>
    <?php

    if ( 'add-new-user' !== $operation ) {
      return;
    }

    $mobile = !empty( $_POST['mobile'] ) ? sanitize_text_field( $_POST['mobile'] ) : '';
    return self::user_mobile_field($mobile);
  }

  public static function show_user_profile( $user ) {
    $mobile = get_the_author_meta( 'mobile', $user->ID );
    return self::user_mobile_field($mobile);
  }

  public static function user_mobile_field( $mobile ) {
    ?>
    <h3>اطلاعات شخصی</h3>
    <table class="form-table">
      <tr>
        <th><label for="mobile">شماره موبایل</label></th>
        <td>
          <input type="text" id="mobile" name="mobile" class="regular-text" value="<?php echo esc_attr( $mobile ); ?>">
        </td>
      </tr>
    </table>
    <?php
  }

  public static function user_profile_update_errors( $errors, $update ) {
    $errors->remove('empty_email');
    
    if ( !empty( $_POST['mobile'] ) && !Validator::mobile($_POST['mobile'], true) )
      $errors->add( 'mobile', "لطفا یک شماره موبایل معتبر وارد نمائید." );
  }

  public static function registration_errors( $errors ) {
    if ( !empty( $_POST['mobile'] ) && !Validator::mobile($_POST['mobile'], true) )
      $errors->add( 'mobile', "لطفا یک شماره موبایل معتبر وارد نمائید." );

    return $errors;
  }

  public static function personal_options_update( $user_id ) {
    if ( !current_user_can( 'edit_user', $user_id ) )
      return false;

    $mobile = sanitize_text_field($_POST['mobile']);
    if ( !empty( $mobile ) && Validator::mobile($mobile, true) ) {
      update_user_meta( $user_id, 'mobile', $mobile );
    }
  }
}
