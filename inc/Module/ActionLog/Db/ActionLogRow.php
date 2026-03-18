<?php
/**
 * BerlinDB Row shape for a single starter_action_logs record
 *
 * @package StarterTheme\Module\ActionLog\Db
 */

namespace StarterTheme\Module\ActionLog\Db;

defined( 'ABSPATH' ) || exit;

class ActionLogRow extends \BerlinDB\Database\Row {

	public int    $id          = 0;
	public int    $user_id     = 0;
	public string $action      = '';
	public string $object_type = '';
	public int    $object_id   = 0;
	public string $message     = '';
	public string $context     = '';
	public string $ip_address  = '';
	public string $created_at  = '0000-00-00 00:00:00';

	public function __construct( $item = null ) {
		parent::__construct( $item );
		$this->id        = (int) $this->id;
		$this->user_id   = (int) $this->user_id;
		$this->object_id = (int) $this->object_id;
	}
}
