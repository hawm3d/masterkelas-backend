<?php
namespace MasterKelas\Database;

use BerlinDB\Database\Row;
use Carbon\Carbon;
use MasterKelas\Model\OTP;

/**
 * OTP row shape
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class OTP_Row extends Row
{
  public function __construct( $item ) {
		parent::__construct( $item );

		$this->id = (int) $this->id;
		$this->type = (string) $this->type;
		$this->recipient = (string) $this->recipient;
		$this->token = (string) $this->token;
		$this->created_at = $this->created_at ? Carbon::parse($this->created_at) : null;
		$this->expire_at = $this->expire_at ? Carbon::parse($this->expire_at) : null;
		$this->status = (int) $this->status;
		$this->data = $this->data && !empty($this->data) ? json_decode((string) $this->data, true): [];
	}

	public function model() {
		return new OTP([
			"id" => $this->id,
			"type" => $this->type,
			"recipient" => $this->recipient,
		]);
	}

	public function data_prop($prop) {
		return is_array($this->data) && isset($this->data[$prop]) ? $this->data[$prop] : null;
	}

	public function get_is_waiting() {
		return $this->status === 0;
	}

	public function get_is_success() {
		return $this->status === 1 && $this->data_prop("provider-status") === 1;
	}

	public function get_is_expired() {
		return $this->expire_at ? $this->expire_at->addSeconds(10)->isPast() : null;
	}

	public function get_error() {
		if ($this->is_waiting || $this->is_success) return false;

		$error = [];

		if ( $this->data_prop("provider-status") === 2 ) {
			$error = [
				'type' => "provider",
				'response' => $this->data_prop("provider-response")
			];
		}

		return $error;
	}
}
