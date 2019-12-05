<?php
/**
 * @author     (Jia Shi <js11182@nyu.edu>)
 * sign in api
 */

require_once 'api.php';
class signIn extends api {

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
		$checkMatch = "SELECT user.* FROM user WHERE email = '" . $this->email . "' AND user_pwd = '" . $this->userPwd . "';";
		$query = $conn->query($checkMatch);
		$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if (count($data) === 0) {
			throw new Exception("This password and the email do not match. Please try again.");
		}

		if (count($data) === 1) {
			return $data;
		} else {
			throw new Exception("An error Occurred!");
		}
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

	/**
	 * [getJson to json format]
	 * @return [type] [description]
	 */
	public function getJson() {
		try {
			$this->res['data'] = $this->doExecute();
		} catch (Exception $e) {
			$this->res['status'] = -1;
			$this->res['message'] = $e->getMessage();
		} finally {
			echo json_encode($this->res);
		}
	}
}
$thisClass = new signIn;
$data = $thisClass->getJson();
?>
