<?php
/**
 * @author     (Jia Shi <js11182@nyu.edu>)
 * sign up api
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

		// get lat and long from google api
		$googleUrl = "https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyBYKmQLLzSPnRViDDC3iimnrOcQt9kruzs";

		$googleUrl .= "&address=" . urlencode($this->address);
		
		$googleApiRes = json_decode($this->curls($googleUrl), true);

		$location = $googleApiRes['results'][0]['geometry']['location'];
		$lat = $location['lat'];
		$lng = $location['lng'];
		
		// insert data to user table
		$sql = "INSERT INTO USER (`firstname`, `lastname`, `user_pwd`, `state`, `city`,
				`zipcode`, `address`, `phone_num`, `email`, `latitude`, `longitude`) VALUES ( '" 
	    	. $this->firstname . "', '" 
	    	. $this->lastname . "', '"
	    	. $this->userPwd . "', '"
	    	. $this->state . "', '"
	    	. $this->city . "', "
	    	. $this->zipcode . ", '"
	    	. $this->address . "', "
	    	. $this->phoneNum . ", '"
	    	. $this->email . "', "
	    	. $lat . ", "
	    	. $lng . ");";

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

	private function curls($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}

try {
	$signUp = new signUp;
	$signUp->res['data'] = $signUp->doExecute();

} catch (Exception $e) {
	$signUp->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$signUp->res['message'] = $e->getMessage();

} finally {
	echo json_encode($signUp->res);
}

?>
