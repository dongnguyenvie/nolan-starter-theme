<?php
/**
 * BerlinDB Table registration for wp_starter_action_logs
 *
 * @package StarterTheme\Module\ActionLog\Db
 */

namespace StarterTheme\Module\ActionLog\Db;

defined( 'ABSPATH' ) || exit;

use BerlinDB\Database\Table;

class ActionLogTable extends Table {

	protected $name    = 'starter_action_logs';
	protected $version = 20260319;

	protected function set_schema(): void {
		$this->schema = "
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id bigint(20) UNSIGNED NOT NULL DEFAULT 0,
			action varchar(100) NOT NULL DEFAULT '',
			object_type varchar(50) NOT NULL DEFAULT '',
			object_id bigint(20) UNSIGNED NOT NULL DEFAULT 0,
			message text NOT NULL,
			context longtext NOT NULL,
			ip_address varchar(45) NOT NULL DEFAULT '',
			created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY (id),
			KEY user_id (user_id),
			KEY action (action),
			KEY object_type_id (object_type, object_id)
		";
	}

	public function upgrade(): void {}
}
