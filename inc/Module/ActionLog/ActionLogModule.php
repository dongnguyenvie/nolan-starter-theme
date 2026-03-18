<?php
/**
 * ActionLog module — audit trail with DB table, REST API, and admin viewer
 *
 * @package StarterTheme\Module\ActionLog
 */

namespace StarterTheme\Module\ActionLog;

defined( 'ABSPATH' ) || exit;

class ActionLogModule {

	public static function init(): void {
		new Db\ActionLogTable();
		ActionLogController::register_routes();
		ActionLogAdminPage::init();
	}
}
