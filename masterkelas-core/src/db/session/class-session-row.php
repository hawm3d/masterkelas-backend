<?php

namespace MasterKelas\Database;

use BerlinDB\Database\Row;
use Carbon\Carbon;
use MasterKelas\MasterException;
use MasterKelas\Model\Session;
use MasterKelas\Model\User;
use MasterKelas\UserAgent;

/**
 * Session row shape
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class Session_Row extends Row {
	public $user = null;

	public function __construct($item) {
		parent::__construct($item);

		$this->id = (int) $this->id;
		$this->user_id = (int) $this->user_id;
		$this->app_id = (int) $this->app_id ?? 0;
		$this->terminator_id = (int) $this->terminator_id ?? null;
		$this->fingerprint = strtoupper((string) $this->fingerprint);
		$this->ua = $this->ua && !empty($this->ua) ? json_decode((string) $this->ua, true) : [];
		$this->data = $this->data && !empty($this->data) ? json_decode((string) $this->data, true) : [];
		$this->status = (int) $this->status;
		$this->created_at = $this->created_at ? Carbon::parse($this->created_at) : null;
		$this->terminated_at = $this->terminated_at ? Carbon::parse($this->terminated_at) : null;
		$this->last_activity = $this->last_activity ? Carbon::parse($this->last_activity) : null;
	}

	public function user() {
		if (!$this->user)
			$this->user = new User($this->user_id);

		return $this->user;
	}

	public function app() {
		// return app
	}

	public function terminator() {
		Session::find([
			"id" => $this->terminator_id
		]);
	}

	public function get_is_active() {
		return $this->status === 1 && !$this->terminated_at;
	}

	public function get_is_deactive() {
		return $this->status === 0;
	}

	public function get_is_locked() {
		return $this->status === 2;
	}

	public function get_is_terminated() {
		return $this->status === 3 || $this->terminated_at;
	}

	public function get_is_junk() {
		return $this->is_deactive || $this->terminated;
	}

	public function get_is_online() {
		return $this->is_active
			&& $this->last_activity
			&& $this->last_activity->greaterThanOrEqualTo(
				Carbon::now()->subSeconds(
					Session::get_max_inactivity_time()
				)
			);
	}

	public function get_creation_method() {
		return $this->data_prop('creation_method');
	}

	public function get_creation_ip() {
		return $this->data_prop('creation_ip');
	}

	public function data_prop($prop) {
		return is_array($this->data) && isset($this->data[$prop]) ? $this->data[$prop] : null;
	}

	public function ua_prop($prop) {
		return is_array($this->ua) && isset($this->ua[$prop]) ? $this->ua[$prop] : null;
	}

	public function validate_ua(UserAgent $ua) {
		$client = $ua->detector->getClient();
		$session_client = $this->get_ua_client();
		if (
			($session_client['type'] ?? "") !== ($client['type'] ?? "")
			|| ($session_client['family'] ?? "") !== ($client['family'] ?? "")
			|| (isset($session_client['version']) && (float) $session_client['version'] > (float) $client['version'])
		)
			throw new MasterException("invalid.client");

		$device_name = $ua->detector->getDeviceName() ?? "";
		$device_brand = $ua->detector->getBrandName() ?? "";
		$device_model = $ua->detector->getModel() ?? "";
		$session_device = $this->get_ua_device();
		if (
			($session_device['type'] ?? "") !== $device_name
			|| ($session_device['brand'] ?? "") !== $device_brand
			|| ($session_device['model'] ?? "") !== $device_model
		)
			throw new MasterException("invalid.device");

		$os = $ua->detector->getOs();
		$session_os = $this->get_ua_os();
		if (
			($session_os['name'] ?? "") !== ($os['name'] ?? "")
			|| ($session_os['version'] ?? "") !== ($os['version'] ?? "")
			|| ($session_os['platform'] ?? "") !== ($os['platform'] ?? "")
		)
			throw new MasterException("invalid.os");

		return true;
	}

	public function get_ua_client() {
		$client = $this->ua_prop("client") ?? [];

		return [
			"type" => $client['type'] ?? null,
			"name" => $client['name'] ?? null,
			"version" => $client['version'] ?? null,
			"family" => $client['family'] ?? null,
		];
	}

	public function get_ua_device() {
		$device = $this->ua_prop("device") ?? [];

		return [
			"type" => $device['type'] ?? null,
			"brand" => $device['brand'] ?? null,
			"model" => $device['model'] ?? null,
		];
	}

	public function get_ua_os() {
		$os = $this->ua_prop("os") ?? [];

		return [
			"name" => $os['name'] ?? null,
			"version" => $os['version'] ?? null,
			"platform" => $os['platform'] ?? null,
		];
	}
}
