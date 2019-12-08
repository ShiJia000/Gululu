<?php
abstract class api {

	const IP = '127.0.0.1';

	const USERNAME = 'gululu';

	const PWD = '123456';

	const DB = 'nextdoor';

    protected $bolCheckLogin = true;

    public function __construct() {
        if ($this->bolCheckLogin) {
            $this->checkLogin();
        }

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

    /**
     * [check in case of sql injection for string]
     * @param  [type] $param [input parameter]
     * @return [type]        [check result]
     */
    public function check($param) {
        return mysqli_real_escape_string($this->conn, $param);
    }

    protected static function getConn() {
        return mysqli_connect(self::IP, self::USERNAME, self::PWD, self::DB);
    }

    protected function checkLogin() {
        $uid = $_COOKIE['uid'];
        session_start();
        if (isset($_SESSION['uid']) && intval($_SESSION['uid']) === intval($uid)) {

        } else {
            $_SESSION[$uid] = false;
            throw new Exception("You don't have the right to login", -2);
        }
    }

    abstract protected function doExecute();
}