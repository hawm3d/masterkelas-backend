<?php

namespace MasterKelas;

use GraphQL\Server\ServerConfig;
use MasterKelas\Schema\MasterContext;

/**
 * The core graphql class.
 *
 * Graphql schema, types and context
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class GraphQL {
  public static function hooks() {
    add_action('graphql_server_config', [__CLASS__, 'server']);
    add_action('graphql_register_types', [__CLASS__, 'register'], 10, 1);
    add_filter('graphql_access_control_allow_headers', [__CLASS__, 'set_aca_header']);
    self::set_ace_header();
  }

  public static function server(ServerConfig $config) {
    $master_context = new MasterContext();
    $master_context->viewer = wp_get_current_user();
    $master_context->root_url = get_bloginfo('url');
    $master_context->request = !empty($_REQUEST) ? $_REQUEST : null;
    $master_context->type_registry = \WPGraphQL::get_type_registry();
    $config->setContext($master_context);
  }

  public static function register() {
    // Main types
    \MasterKelas\Schema\Object\SiteInformation::register();
    \MasterKelas\Schema\Object\MetaTags::register();
    \MasterKelas\Schema\Object\Pagination::register();

    // Auth types
    \MasterKelas\Schema\Object\Auth::register();
    \MasterKelas\Schema\Object\AuthMethod::register();
    \MasterKelas\Schema\Object\AuthenticatedUser::register();
    \MasterKelas\Schema\Object\UserSession::register();
    \MasterKelas\Schema\Object\RememberedUser::register();
    \MasterKelas\Schema\Object\LoginInit::register();
    \MasterKelas\Schema\Object\RegisterInit::register();
    \MasterKelas\Schema\Object\RegisterCompleteInit::register();

    // User types
    \MasterKelas\Schema\Object\UserSuspension::register();
    \MasterKelas\Schema\Object\UserAccount::register();
    \MasterKelas\Schema\Object\UserAction::register();
    \MasterKelas\Schema\Object\UserProfile::register();

    // Other types
    \MasterKelas\Schema\Object\DidYouKnow::register();
    \MasterKelas\Schema\Object\VisitorInformation::register();
    \MasterKelas\Schema\Object\Notification::register();
    \MasterKelas\Schema\Object\Image::register();
    \MasterKelas\Schema\Object\Video::register();
    \MasterKelas\Schema\Object\Price::register();
    \MasterKelas\Schema\Object\CourseDuration::register();
    \MasterKelas\Schema\Object\CourseCategory::register();
    \MasterKelas\Schema\Object\Course::register();
    \MasterKelas\Schema\Object\Courses::register();
    \MasterKelas\Schema\Object\Lesson::register();
    \MasterKelas\Schema\Object\Subscription::register();

    // Main queries
    \MasterKelas\Schema\Query\SiteInfo::register();
    \MasterKelas\Schema\Query\PageHead::register();

    // Auth queries
    \MasterKelas\Schema\Query\Register::register();
    \MasterKelas\Schema\Query\RegisterComplete::register();
    \MasterKelas\Schema\Query\Login::register();
    \MasterKelas\Schema\Query\LoginActiveMethods::register();
    \MasterKelas\Schema\Query\Logout::register();
    \MasterKelas\Schema\Query\RefreshSession::register();
    \MasterKelas\Schema\Query\Whoami::register();

    // Other queries
    \MasterKelas\Schema\Query\Boot::register();
    \MasterKelas\Schema\Query\Action\ReadAction::register();
    \MasterKelas\Schema\Query\Categories::register();
    \MasterKelas\Schema\Query\Course::register();
    \MasterKelas\Schema\Query\Courses::register();
    \MasterKelas\Schema\Query\CourseSlugs::register();
    \MasterKelas\Schema\Query\Lesson::register();
    \MasterKelas\Schema\Query\LessonPaths::register();
    \MasterKelas\Schema\Query\Subs::register();
    \MasterKelas\Schema\Query\CreateOrder::register();
    \MasterKelas\Schema\Query\MySub::register();

    // Auth mutations
    \MasterKelas\Schema\Mutation\OTP::register();
    \MasterKelas\Schema\Mutation\VerifyOTP::register();
    \MasterKelas\Schema\Mutation\Google::register();
    \MasterKelas\Schema\Mutation\NationalityValidation::register();
  }

  public static function set_ace_header() {
    Schema::append_headers(["Access-Control-Expose-Headers" => "*"]);
  }

  public static function set_aca_header(array $headers) {
    $headers[] = 'X-Refresh-Token';

    return (array) $headers;
  }
}
