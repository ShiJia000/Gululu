<?php
abstract class api {

	const IP = '127.0.0.1';

	const USERNAME = 'gululu';

	const PWD = '123456';

	const DB = 'nextdoor';

    public function __construct() {

    	$this->res = array(
			'status' => 0,
			'data' => array(),
			'message' => 'Success'
		);
        // connect to db
        $this->conn = self::getConn();

        // is success
        if ($this->conn === false) {
            throw new Exception("No such data.");
        }
    }

    protected static function getConn() {
        return mysqli_connect(self::IP, self::USERNAME, self::PWD, self::DB);
    }

    public function getJsonRes($data) {
    	try {
			$this->res['data'] = $data;
		} catch (Exception $e) {
			$this->res['status'] = -1;
			$this->res['message'] = $e->getMessage();
		} finally {
			echo json_encode($this->res);
		}
    }
}