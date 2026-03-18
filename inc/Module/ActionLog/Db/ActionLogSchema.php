<?php
/**
 * BerlinDB Schema for wp_starter_action_logs
 *
 * @package StarterTheme\Module\ActionLog\Db
 */

namespace StarterTheme\Module\ActionLog\Db;

defined( 'ABSPATH' ) || exit;

class ActionLogSchema extends \BerlinDB\Database\Schema {

	public $columns = [
		[
			'name'     => 'id',
			'type'     => 'bigint',
			'length'   => '20',
			'unsigned' => true,
			'extra'    => 'auto_increment',
			'primary'  => true,
			'sortable' => true,
		],
		[
			'name'     => 'user_id',
			'type'     => 'bigint',
			'length'   => '20',
			'unsigned' => true,
			'default'  => '0',
			'sortable' => true,
		],
		[
			'name'       => 'action',
			'type'       => 'varchar',
			'length'     => '100',
			'default'    => '',
			'sortable'   => true,
			'searchable' => true,
		],
		[
			'name'    => 'object_type',
			'type'    => 'varchar',
			'length'  => '50',
			'default' => '',
		],
		[
			'name'     => 'object_id',
			'type'     => 'bigint',
			'length'   => '20',
			'unsigned' => true,
			'default'  => '0',
		],
		[
			'name'    => 'message',
			'type'    => 'text',
			'default' => '',
		],
		[
			'name'    => 'context',
			'type'    => 'longtext',
			'default' => '',
		],
		[
			'name'       => 'ip_address',
			'type'       => 'varchar',
			'length'     => '45',
			'default'    => '',
			'searchable' => true,
		],
		[
			'name'       => 'created_at',
			'type'       => 'datetime',
			'default'    => '0000-00-00 00:00:00',
			'created'    => true,
			'date_query' => true,
			'sortable'   => true,
		],
	];
}
