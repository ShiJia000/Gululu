<?php
/**
 * @author     (Jia Shi <js11182@nyu.edu>)
 * sign out api
 */

require_once 'api.php';
class signOut extends api {

	public function doExecute() {
		$lat = $_GET['lat'];
		$lng = $_GET['lng'];
		$googleUrl = "https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyBYKmQLLzSPnRViDDC3iimnrOcQt9kruzs";

		$googleUrl .= "&address=" . urlencode($lat . ',' . $lng);

		$googleApiRes = json_decode($this->curls($googleUrl), true);
		return $googleApiRes['results'][0]['formatted_address'];
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
	$thisClass = new signOut;
	$thisClass->res['data'] = $thisClass->doExecute();

} catch (Exception $e) {
	$thisClass->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$thisClass->res['message'] = $e->getMessage();

} finally {
	echo json_encode($thisClass->res);
}

?>
