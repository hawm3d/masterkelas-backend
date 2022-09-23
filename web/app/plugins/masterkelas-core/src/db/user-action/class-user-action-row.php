<?php

namespace MasterKelas\Database;

use BerlinDB\Database\Row;
use Carbon\Carbon;
use MasterKelas\MasterException;

/**
 * User Action row shape
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class User_Action_Row extends Row {
	public function __construct($item) {
		parent::__construct($item);

		$this->id = (int) $this->id;
		$this->uid = (string) $this->uid;
		$this->user_id = (int) $this->user_id;
		$this->session_id = (int) ($this->session_id ?? 0);
		$this->creator_id = (int) ($this->creator_id ?? 0);
		$this->action = (string) $this->action;
		$this->priority = (int) $this->priority;
		$this->data = $this->data && !empty($this->data) ? json_decode((string) $this->data, true) : null;
		$this->config = $this->config && !empty($this->config) ? json_decode((string) $this->config, true) : null;
		$this->status = (int) $this->status;
		$this->created_at = $this->created_at ? Carbon::parse($this->created_at) : null;
		$this->updated_at = $this->updated_at ? Carbon::parse($this->updated_at) : null;
	}

	public function data_prop($prop) {
		return is_array($this->data) && isset($this->data[$prop]) ? $this->data[$prop] : null;
	}

	public function get_fields() {
		return $this->data_prop('fields');
	}
}
