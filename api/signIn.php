<?php
/**
 * @author     (Jia Shi <js11182@nyu.edu>)
 * sign in api
 */

require_once 'api.php';
class signIn extends api {

	protected $bolCheckLogin = false;

	public function doExecute() {
		$conn = $this->conn;

		// in case of sql injection
		$this->email = $this->check($_POST['email']);
		$this->userPwd = md5($this->check($_POST['userPwd']));

		// check not null
		$this->checkNotNull();

		// check if the email exits;
		$checkEmail = "SELECT email FROM user WHERE email = '" . $this->email . "';";
		$query = $conn->query($checkEmail);
		$data = mysqli_fetch_all($query, MYSQLI_ASSOC);
		if (count($data) === 0) {
			throw new Exception("This email does not exit. Please try another one.");
		}

		// check if the email & pwd match
		$checkMatch = "SELECT uid, firstname, lastname, state, city, zipcode, address, phone_num, photo, self_intro, family_intro, profile_timestamp, email FROM user WHERE email = '" . $this->email . "' AND user_pwd = '" . $this->userPwd . "';";
		$query = $conn->query($checkMatch);
		$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if (count($data) === 0) {
			throw new Exception("This password and the email do not match. Please try again.");
		}

		if (count($data) !== 1) {
			throw new Exception("An error Occurred!");
		}

		session_start();
		$_SESSION['uid'] = $data[0]['uid'];

		return $data[0];
	}

	/**
	 * [checkNotNull description]
	 * @return [type] [void]
	 */
	public function checkNotNull() {
		if (!$this->userPwd) {
			throw new Exception("Password cannot be null!");
		}
		if (!$this->email) {
			throw new Exception("Email cannot be null!");
		}
	}
}

try {
	$thisClass = new signIn;
	$thisClass->res['data'] = $thisClass->doExecute();

} catch (Exception $e) {
	$thisClass->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$thisClass->res['message'] = $e->getMessage();

} finally {
	echo json_encode($thisClass->res);
}

?>
