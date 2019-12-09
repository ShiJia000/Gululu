<?php
/**
 * @author     (Jia Shi <js11182@nyu.edu>)
 * sign out api
 */

require_once 'api.php';
class signOut extends api {

	protected $bolCheckLogin = false;

	public function doExecute() {
		$conn = $this->conn;

		// in case of sql injection
		$this->email = intval($_COOKIE['uid']);
		
		session_start();
		unset($_SESSION['uid']);
		session_destroy();

		return array();
	}
}

try {
	$thisClass = new signOut;
	$thisClass->res['data'] = $thisClass->doExecute();

} catch (Exception $e) {
	$thisClass->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$thisClass->res['message'] = $e->getMessage();

} finally {
	echo json_encode($thisClass->res);
}

?>
