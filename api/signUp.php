<?php
/**
 * @author     (Jia Shi <js11182@nyu.edu>)
 * http://localhost/db_nextdoor/api/signUp
 * 
 */
require_once 'api.php';
class signUp extends api {

	public function doExecute() {
		$conn = $this->conn;

		// in case of sql injection
		$this->firstname = $this->check($_POST['firstname']);
		$this->lastname = $this->check($_POST['lastname']);
		$this->userPwd = md5($this->check($_POST['userPwd']));
		$this->state = $this->check($_POST['state']);
		$this->city = $this->check($_POST['city']);
		$this->zipcode = intval($_POST['zipcode']);
		$this->address = $this->check($_POST['address']);
		$this->phoneNum = intval($_POST['phoneNum']);
		$this->email = $this->check($_POST['email']);

		// check not null
		$this->checkNotNull();

		// check if the email is unique;
		$checkEmail = "SELECT email FROM user WHERE email = '" . $this->email . "';";
		$query = $conn->query($checkEmail);
		$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if (count($data) !== 0) {
			throw new Exception("Email exists! Please sign in or sign up with another email.");
		}
		
		// insert data to user table
		$sql = "INSERT INTO USER (`firstname`, `lastname`, `user_pwd`, `state`, `city`,
				`zipcode`, `address`, `phone_num`, `email`) VALUES ( '" 
	    	. $this->firstname . "', '" 
	    	. $this->lastname . "', '"
	    	. $this->userPwd . "', '"
	    	. $this->state . "', '"
	    	. $this->city . "', "
	    	. $this->zipcode . ", '"
	    	. $this->address . "', "
	    	. $this->phoneNum . ", '"
	    	. $this->email . "');";

	    $data = $conn->query($sql);

		if ($data == 1) {
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
		if (!$this->firstname) {
			throw new Exception("Firstname cannot be null!");
		}
		if (!$this->lastname) {
			throw new Exception("Firstname cannot be null!");
		}
		if (!$this->userPwd) {
			throw new Exception("Password cannot be null!");
		}
		if (!$this->state) {
			throw new Exception("State cannot be null!");
		}
		if (!$this->city) {
			throw new Exception("City cannot be null!");
		}
		if (!$this->zipcode) {
			throw new Exception("Zipcode cannot be null!");
		}
		if (!$this->address) {
			throw new Exception("Address cannot be null!");
		}
		if (!$this->phoneNum) {
			throw new Exception("Phone num cannot be null!");
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
$neighborFeed = new signUp;
$data = $neighborFeed->getJson();
?>
