<?php

namespace MasterKelas\Queue;

/**
 * Queue clients and workers
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class Queue {

  /**
   * @var Queue Single instance of the plugin
   */
  static public $instance;

  /**
   * Returns the singleton instance of the Queue. Creates the
   * instance if it is absent.
   *
   * @return Queue instance of Queue
   */
  static public function get_instance() {
    if (is_null(self::$instance)) {
      self::$instance = new Queue();
    }

    return self::$instance;
  }

  /**
   * @var \MasterKelas\Queue\Client The Client object used to enqueue jobs
   */
  public $client;

  /**
   * @var \MasterKelas\Queue\Worker The Worker object used to execute jobs
   */
  public $worker;

  /**
   * @var string Configuration constants are prefixed by MASTER_QUEUE by default.
   * ;w
   * Eg:- MASTER_QUEUE_JOBS_PER_WORKER
   */
  public $config_prefix = 'MASTER_QUEUE';

  /**
   * @var int Number of jobs to execute per worker, Default 1
   */
  public $jobs_per_worker;

  /** @var string Custom worker class */
  public $worker_class;

  /** @var Custom client class */
  public $client_class;

  /** @var string Job queue backend */
  public $backend;

  /*
	 * @var bool Indicates if the plugin executed a job.
	 *
	 * Only one run is allowed.
	 */
  public $did_run    = false;

  /*
	 * @var bool Indicates if the plugin was enabled. The Queue can only be
	 * enabled once
	 */
  public $did_enable = false;

  /**
   * Enables the plugin by registering it's clients and workers.
   * Ignored if already enabled
   */
  public function enable() {
    if (!$this->did_enable) {
      $this->get_client()->register();
      $this->get_worker()->register();

      $this->did_enable = true;
    }
  }

  /**
   * Starts processing jobs in the Worker.
   *
   * Only one run is permitted.
   *
   * @return int The exit status code (only for PHPUnit)
   */
  public function run() {
    if ($this->did_run) {
      return false;
    }

    $this->did_run = true;

    return $this->work();
  }

  /**
   * Executes jobs on the current Worker. A Worker will taken up
   * only one job by default. If MASTER_QUEUE_JOBS_PER_WORKER is defined
   * that many jobs will be executed before it exits.
   *
   * This method will exit with the result code based on
   * success/failure of executing the job.
   *
   * @return int 0 for success and 1 for failure
   */
  public function work() {
    for ($i = 0; $i < $this->get_jobs_per_worker(); $i++) {
      $result      = $this->get_worker()->work();
      $result_code = $result ? 0 : 1;
    }

    return $this->quit($result_code);
  }

  /**
   * Adds a new job to the Client with the specified arguments and
   * priority.
   *
   * @param string $hook The action hook name for the job
   * @param array $args Optional arguments for the job
   * @param string $priority Optional priority of the job
   * @return bool true or false depending on the Client
   */
  public function add($hook, $args = array(), $priority = 'normal') {
    return $this->get_client()->add(
      $hook,
      $args,
      $priority
    );
  }

  /* Helpers */
  /**
   * Returns the Client object used to add jobs. Creates the instance
   * of the client lazily.
   *
   * @return \MasterKelas\Queue\Client The client instance
   */
  function get_client() {
    if (is_null($this->client)) {
      $this->client = $this->build_client();
    }

    return $this->client;
  }

  /**
   * Returns the Worker object used to execute jobs. Creates the instance
   * of the worker lazily.
   *
   * @param \MasterKelas\Queue\Worker The worker instance
   */
  function get_worker() {
    if (is_null($this->worker)) {
      $this->worker = $this->build_worker();
    }

    return $this->worker;
  }

  /**
   * Conditionally builds a new Client object.
   *
   * If the constant MASTER_QUEUE_CLIENT_CLASS is defined, it will return an instance of
   * that class. If not, MASTER_QUEUE_BACKEND is checked to chose the client class. If not,
   * default to cron client.
   *
   * @return \MasterKelas\Queue\Client New instance of the Client
   */
  function build_client() {
    if (!$this->has_config('CLIENT_CLASS')) {
      if ($this->has_config('BACKEND')) {
        $backend = $this->get_config('BACKEND');

        if ('gearman' === strtolower($backend)) {
          return new \MasterKelas\Queue\Gearman\Client();
        } elseif ('rabbitmq' === strtolower($backend)) {
          return new \MasterKelas\Queue\RabbitMQ\Client();
        } else {
          return new \MasterKelas\Queue\Gearman\Client();
          // return new \MasterKelas\Queue\Cron\Client();
        }
      } else {
        return new \MasterKelas\Queue\Gearman\Client();
        // return new \MasterKelas\Queue\Cron\Client();
      }
    } else {
      $klass = $this->get_config('CLIENT_CLASS');
      return new $klass();
    }
  }

  /**
   * Conditionally builds a new Worker object.
   *
   * If the constant MASTER_QUEUE_WORKER_CLASS is defined it will return an instance of
   * that class. If not, MASTER_QUEUE_BACKEND is checked to chose the worker class. If not,
   * default to cron.
   *
   * @return \MasterKelas\Queue\Worker New instance of the Worker
   */
  function build_worker() {
    if (!$this->has_config('WORKER_CLASS')) {
      if ($this->has_config('BACKEND')) {
        $backend = $this->get_config('BACKEND');

        if ('gearman' === strtolower($backend)) {
          return new \MasterKelas\Queue\Gearman\Worker();
        } elseif ('rabbitmq' === strtolower($backend)) {
          return new \MasterKelas\Queue\RabbitMQ\Worker();
        } else {
          return new \MasterKelas\Queue\Gearman\Worker();
          // return new \MasterKelas\Queue\Cron\Worker();
        }
      } else {
        return new \MasterKelas\Queue\Gearman\Worker();
        // return new \MasterKelas\Queue\Cron\Worker();
      }
    } else {
      $klass = $this->get_config('WORKER_CLASS');
      return new $klass();
    }
  }

  /**
   * Returns the jobs to execute per worker instance
   *
   * @return int Defaults to 1
   */
  function get_jobs_per_worker() {
    return $this->get_config(
      'JOBS_PER_WORKER',
      1
    );
  }

  /**
   * Helper to pickup config options from Constants with fallbacks.
   *
   * Order of lookup is,
   *
   * 1. Local Property
   * 2. Constant of that name
   * 3. Default specified
   *
   * Eg:- get_config( 'FOO', 'abc', 'MY' )
   *
   * will look for,
   *
   * 1. Local Property $this->my_foo
   * 2. Constant MY_FOO
   * 3. Defaualt ie:- abc
   *
   * @param string $constant Name of constant to lookup
   * @param string $default Optional default
   * @param string $config_prefix Optional config prefix, Default is MASTER_QUEUE
   * @return mixed The value of the config
   */
  function get_config($constant, $default = '', $config_prefix = '') {
    $variable      = strtolower($constant);
    $config_prefix = empty($config_prefix) ? $this->config_prefix : $config_prefix;
    $constant      = $config_prefix . '_' . $constant;

    if (property_exists($this, $variable)) {
      if (is_null($this->$variable)) {
        if (defined($constant)) {
          $this->$variable = constant($constant);
        } else {
          $this->$variable = $default;
        }
      }

      return $this->$variable;
    } else {
      throw new \Exception(
        "Fatal Error - Public Var($variable) not declared"
      );
    }
  }

  /**
   * Checks if a config option is defined. Empty strings are treated
   * as an undefined config option.
   *
   * @param string $constant The name of the constant
   * @return bool True or false depending on whether the config is present
   */
  function has_config($constant) {
    $value = $this->get_config($constant);
    return !empty($value);
  }

  /**
   * Helper to quit with a status code. When running under PHPUnit it
   * returns the status_code instead of exiting immediately.
   *
   * @param int $status_code The status between 0-255 to quit with.
   */
  function quit($status_code) {
    if (!defined('PHPUNIT_RUNNER')) {
      exit($status_code);
    } else {
      return $status_code;
    }
  }
}
