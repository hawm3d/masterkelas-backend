<?php

namespace MasterKelas;

/**
 * WP CLI Commands.
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class Commands {
	public static function init() {
		if (defined('WP_CLI') && \WP_CLI) {
			include_once __DIR__ . '/commands/master-factory.php';
		}
	}
}
