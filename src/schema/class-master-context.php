<?php

namespace MasterKelas\Schema;

use MasterKelas\Model\Auth;
use MasterKelas\Region;
use MasterKelas\UserAgent;
use WPGraphQL\AppContext;

/**
 * MasterKelas GraphQL Context
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class MasterContext extends AppContext {

  public UserAgent $ua;
  public Region $region;
  public ?Auth $auth = null;
  public $auth_error = null;

  public function __construct() {
    parent::__construct();

    $ua = new UserAgent();
    $this->ua = $ua;
    $this->region = new Region();

    try {
      $auth = Auth::fromHeader('access', $ua);
      if ($auth && $auth->user) {
        $this->auth = $auth;
        if (!is_user_logged_in()) {
          wp_set_current_user($auth->user->id);
        }
      } else {
        throw new \Exception();
      }
    } catch (\Throwable $th) {
      $this->auth = null;
      $this->auth_error = $th->getMessage();
    }
  }
}
