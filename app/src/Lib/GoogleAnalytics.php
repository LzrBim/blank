<? 
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /lib/GoogleAnalytics.php
 * MODS:
----------------------------------------------------------------------------- */

//returns sessionToken for multiple calls to API
function googleLogin($email,$passwd){
	
	$clientlogin_url = "https://www.google.com/accounts/ClientLogin";
	$clientlogin_post = array(
    "accountType" => "GOOGLE",
    "Email" => $email,
    "Passwd" => $passwd,
    "service" => "analytics",
    "source" => "my-analytics"
	);
 
	$curl = curl_init($clientlogin_url);

	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $clientlogin_post);
	curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

	$response = curl_exec($curl);
 
	preg_match("/Auth=([a-z0-9_\-]+)/i", $response, $matches);
	$sessionToken = $matches[1];

	if (strlen($sessionToken) == 0){
		$sessionToken = "Authentication Failed.";
	}
	 
 	return $sessionToken;
}

//gets the data
function call_api($sessionToken,$url){
	if(empty($sessionToken) || empty($url)){
		echo $url;
		die('error in call_api()');
	}
	
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	//curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	if (isset($_SESSION['authSub'])){
		$curlheader[0] = sprintf("Authorization: AuthSub token=\"%s\"/n", $sessionToken);
	} else {
		$curlheader[0] = "Authorization: GoogleLogin auth=" . $sessionToken;
	}
	curl_setopt($curl, CURLOPT_HTTPHEADER, $curlheader);

	$response = curl_exec($curl);
	curl_close($curl);

	return $response;	    
}

//returns accounts list as array	
function parse_account_list($xml){
	$doc = new DOMDocument();
	if(stripos($xml,"<") !== FALSE)
	{
		$doc->loadXML($xml);
	
		$entries = $doc->getElementsByTagName('entry');
		$i = 0;
		$profiles= array();
		foreach($entries as $entry)
		{
			$profiles[$i] = array();
				
			$title = $entry->getElementsByTagName('title');
			$profiles[$i]["title"] = $title->item(0)->nodeValue;
				
			$entryid = $entry->getElementsByTagName('id');
			$profiles[$i]["entryid"] = $entryid->item(0)->nodeValue;
			
			$tableId = $entry->getElementsByTagName('tableId');
			$profiles[$i]["tableId"] = $tableId->item(0)->nodeValue;
				
			$i++;
		}
		return $profiles;
	} else {
		$sessionToken = "Authentication Failed.";
	}

}
//returns data as array	
function parse_data($xml){
	if(empty($xml)){
		die('no XML provided');
	}
		$doc = new DOMDocument();
		$doc->loadXML($xml);

		$entries = $doc->getElementsByTagName('entry');
		$i = 0;
		$results = array();
		foreach($entries as $entry)
		{
			$countries[$i] = array();
			
			$dimensions = $entry->getElementsByTagName('dimension');
			foreach($dimensions as $dimension)
			{
				$results[$i][ltrim($dimension->getAttribute("name"),"ga:")] =  $dimension->getAttribute('value');
			}
			
			$metrics = $entry->getElementsByTagName('metric');
			foreach($metrics as $metric)
			{
				$results[$i][ltrim($metric->getAttribute('name'),"ga:")] =  $metric->getAttribute('value');
			}
			
			$i++;
		}
		return $results;
}
