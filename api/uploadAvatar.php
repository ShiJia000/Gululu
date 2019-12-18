<?php
/**
 * http://localhost/db_nextdoor/api/uploadAvatar
 * Receive Notification api
 */

require_once 'api.php';
class uploadAvatar extends api{
	
	public function doExecute(){

		$conn = $this->conn;
		$uid = intval($_COOKIE['uid']);
		
		$uploadOk = 1;
		$target_dir = "../uploads/";
		$target_file = $target_dir . basename($_FILES["avatar"]["name"]);


		$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

		// Check if image file is a actual image or fake image
		if(isset($_POST["submit"])) {
		    $check = getimagesize($_FILES["avatar"]["tmp_name"]);
		    if($check !== false) {
		        $uploadOk = 1;
		    } else {
		        throw new Exception("File is not an image.", -1);
		        $uploadOk = 0;
		    }
		}
		// Check if file already exists
		if (file_exists($target_file)) {
			throw new Exception("Sorry, file already exists.", -1);
		    $uploadOk = 0;
		}
		// Check file size
		if ($_FILES["avatar"]["size"] > 500000) {
			throw new Exception("Sorry, your file is too large.", -1);
		    $uploadOk = 0;
		}
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			throw new Exception("Sorry, your file was not uploaded.", -1);

		} else {
		    if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
		    	
		    } else {
		    	throw new Exception("Sorry, there was an error uploading your file.", -1);
		    }
		}

		// file name
		$fileName = basename( $_FILES["avatar"]["name"]);


		// update db
		$sql = 'UPDATE user set photo="' . $fileName . '" WHERE uid=' . $uid . ';';
		$data = $conn->query($sql);

		return $data;
	}
}

try {
	$thisClass = new uploadAvatar;
	$thisClass->res['data'] = $thisClass->doExecute();

} catch (Exception $e) {
	$thisClass->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$thisClass->res['message'] = $e->getMessage();

} finally {
	echo json_encode($thisClass->res);
}

?>