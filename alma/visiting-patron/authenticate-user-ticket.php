<?php

# copyright 2013, WWU
# license: GNU AFFERO GENERAL PUBLIC LICENSE - https://www.gnu.org/licenses/agpl.html
# description: this script is called by EZproxy via the III PATRONAPI method, and returns a user's details (name, email, etc...).
# setup: change the $ezproxy variable to point to your server, and enter your AlmaSDK username, institutionCode and password below, in the $options section

$ezproxy = "https://ezproxynre.yourUniversity.edu";

$response = "";
$username = isset($_REQUEST['user']) ? $_REQUEST['user'] : "";
$password = isset($_REQUEST['pass']) ? $_REQUEST['pass'] : "";
$url = $_REQUEST['url'];

if ($username == "" || $password == "") {
	echo "Missing username and/or password.  Please go back and try again.";
   	exit();
} else {

	# try to authenticate;
    try {
        $options = array(
            'exceptions'=>true,
            'trace'=>1,
            'cache_wsdl'=>WSDL_CACHE_NONE,
	        'login' => 'AlmaSDK-yourAlmaWebServicesUserName-institutionCode-01ALLIANCE_yourInstitutionCode',
	        'password' => 'yourAlmaSDKpassword'
        );
        $client = new SoapClient('https://na01.alma.exlibrisgroup.com/almaws/repository/UserAuthenticationWebServices?wsdl', $options);
		# TODO: save this wsdl file locally, and reference it locally

    } catch (Exception $e) {
        echo "<h2>Exception Error!</h2>";
        echo $e->getMessage();
    }

    try {
		$params = array(
			'arg0' => $username
			,'arg1' => $password
		);

        $response = $client->authenticateUser($params);

        $base = new \SimpleXMLElement($response->SearchResults);
        if ((string) $base->result === 'true') {

			$secret = "yourEzproxyUserTxtFileMD5secret";		// this must match what is in the ticket section of user.txt
			$packet = '$u' . time() . '$e';
			$ticket = urlencode(md5($secret . $username . $packet) . $packet);

			$username_urlencoded = urlencode($username);
			$ezproxy_url = $ezproxy . "/login?user=" . $username_urlencoded . "&ticket=" . $ticket . "&auth=d2&url=" . $url;

			header("Location: " . $ezproxy_url);
			exit();

		} else {
			echo "Login failed.  Please go back and try again.";
			# TODO: make this pretty
			exit();
		}

    } catch (Exception $e) {
        echo "<pre>" . var_dump($e) . "</pre>";
        echo '<p>Caught exception: ',  $e->getMessage(), "\n";
    }
}

?>