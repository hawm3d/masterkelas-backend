<?php

namespace MasterKelas\Database;

use BerlinDB\Database\Row;
use Carbon\Carbon;
use MasterKelas\MasterException;

/**
 * Notification row shape
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class Notification_Row extends Row {
	public function __construct($item) {
		parent::__construct($item);

		$this->id = (int) $this->id;
		$this->uuid = (string) $this->uid;
		$this->user_id = (int) $this->user_id;
		$this->action_id = (int) ($this->action_id ?? 0);
		$this->type = (string) $this->type;
		$this->template = (string) $this->template;
		$this->data = $this->data && !empty($this->data) ? json_decode((string) $this->data) : null;
		$this->status = (int) $this->status;
		$this->created_at = $this->created_at ? Carbon::parse($this->created_at) : null;
		$this->read_at = $this->read_at ? Carbon::parse($this->read_at) : null;
	}
}
