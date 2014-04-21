<?php
print "Content-type: text/html\n\n";

# copyright 2013, WWU
# license: GNU AFFERO GENERAL PUBLIC LICENSE - https://www.gnu.org/licenses/agpl.html
# description: this script is called by EZproxy via the III PATRONAPI method, and returns a user's details (name, email, etc...).  The format of a III Patron API request is http://www.example.com/PATRONAPI/username/dump
# warning: make sure that your Apache config only allows access to this script from your web server (you would not want the public to have access to this file).
# setup: enter your AlmaSDK username, institutionCode and password below, in the $options section

$response = "";
$userName = $_SERVER['QUERY_STRING'];
$current_filename = basename($_SERVER['PHP_SELF']);

if ($userName == "") {
    echo "<h1>Missing username.</h1>";
    exit();
} else {

    /* make sure the user:
     1) is a Summit Visiting Patron (user type)
     2) is an active user
     3) account has not expired
    */

    $options = array(
        'exceptions'=>true,
        'trace'=>1,
        'cache_wsdl'=>WSDL_CACHE_NONE,
        'login' => 'AlmaSDK-yourAlmaWebServicesUserName-institutionCode-01ALLIANCE_yourInstitutionCode',
        'password' => 'yourAlmaSDKpassword'
    );

    $client = new SoapClient('https://na01.alma.exlibrisgroup.com/almaws/repository/UserWebServices?wsdl', $options);
		# TODO: save this wsdl file locally, and reference it locally

    try {
        $params = array(
            'arg0' => $userName
        );

        $response = $client->getUserDetails($params);
        if ((string) $response->SearchResults->errorsExist === 'true') {
            echo "Error " . $current_filename . ":" . __LINE__;
            exit();
        }

        $base = new \SimpleXMLElement($response->SearchResults);
        $children = $base->result->children('http://com/exlibris/urm/user_record/xmlbeans');
        $thisChild = $children[0];

        $expirationDate = $thisChild->userDetails->expiryDate;
        $firstName = $thisChild->userDetails->firstName;
        $lastName = $thisChild->userDetails->lastName;
        $userGroup = $thisChild->userDetails->userGroup;
        $status = $thisChild->userDetails->status;
        $blocks_xml = $thisChild->userBlockList->userBlock;

        if ($expirationDate) {
            $expiryDate = strtotime($expirationDate);
            $now = time();

            if ($expiryDate < $now) {
                echo "<h2>Error - account has expired.</h2>";
                exit();
            }
        }

        if ($status != "Active") {
            echo "<h2>Error - user is not active.</h2>";
            exit();
        }

        if ($userGroup != "SummitVP") {
            echo "<h2>Error - not a Summit visiting patron.</h2>";
            exit();
        }

        // does this user have any blocks on his/her account?
        /* skip this for now */
        /*
        foreach ($blocks_xml as $block_xml) {
            $block_status = (string) $block_xml->status;
            if ($block_status == "Active") {
                echo "<h2>Error - this account has a block preventing access to library resources.  Please contact the library for assistance.</h2>";
                exit();

                # TODO: email the library to give them a heads-up about this user?
                # $block_note = (string) $block_xml->note;
            }
        }
		*/

        $emails_xml = $thisChild->userAddressList->userEmail;
        foreach ($emails_xml as $email_xml) {
            #if ($email_xml->attributes()['preferred'] == 'true') {
                $email = (string) $email_xml->email[0];
            #}
        }

        /* this user is valid; send this info back to EZproxy */
        echo "<HTML><BODY>";
        echo "USER NAME[pu]=" . $userName . "<BR>\n";
        echo "PATRN NAME[givenname]=" . $firstName . "<BR>\n";
        echo "PATRN NAME[sn]=" . $lastName . "<BR>\n";
        echo "EMAIL ADDR[pz]=" . $email . "<BR>\n";
        echo "CATEGORY[p47]=SummitVP<BR>\n";
        echo "EXP DATE[p43]=" . date("m-d-Y", $expiryDate) . "<BR>\n";
        echo "</BODY></HTML>";

    } catch (Exception $e) {
        echo "<pre>" . var_dump($e) . "</pre>";
        echo '<p>Caught exception: ',  $e->getMessage(), "\n";
    }

}
?>