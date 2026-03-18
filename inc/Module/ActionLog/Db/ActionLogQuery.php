<?php
/**
 * BerlinDB Query class for starter_action_logs
 *
 * @package StarterTheme\Module\ActionLog\Db
 */

namespace StarterTheme\Module\ActionLog\Db;

defined( 'ABSPATH' ) || exit;

class ActionLogQuery extends \BerlinDB\Database\Query {

	protected $table_name       = 'starter_action_logs';
	protected $table_schema     = ActionLogSchema::class;
	protected $item_name        = 'action_log';
	protected $item_name_plural = 'action_logs';
	protected $item_shape       = ActionLogRow::class;
}
