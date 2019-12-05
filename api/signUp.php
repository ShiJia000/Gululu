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
		$firstname = $this->check($_POST['firstname']);
		$lastname = $this->check($_POST['lastname']);
		$userPwd = md5($this->check($_POST['userPwd']));
		$state = $this->check($_POST['state']);
		$city = $this->check($_POST['city']);
		$zipcode = intval($_POST['zipcode']);
		$address = $this->check($_POST['address']);
		$phoneNum = intval($_POST['phoneNum']);
		$email = $this->check($_POST['email']);

		// check if the email is unique;
		$checkEmail = "SELECT email FROM user WHERE email = '" . $email . "';";
		$query = mysqli_query($this->conn, $checkEmail);
		$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if (count($data) !== 0) {
			throw new Exception("Email exists! Please sign in or sign up with another email.");
		}
		
		// insert data to user table
		$sql = "INSERT INTO USER (`firstname`, `lastname`, `user_pwd`, `state`, `city`,
				`zipcode`, `address`, `phone_num`, `email`) VALUES ( '" 
	    	. $firstname . "', '" 
	    	. $lastname . "', '"
	    	. $userPwd . "', '"
	    	. $state . "', '"
	    	. $city . "', "
	    	. $zipcode . ", '"
	    	. $address . "', "
	    	. $phoneNum . ", '"
	    	. $email . "');";

	    $data = $conn->query($sql);

		if ($data == 1) {
			return $data;
		} else {
			throw new Exception("An error Occurred!");
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
