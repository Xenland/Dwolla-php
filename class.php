<?php

class dwolla{

	function __construct(){
 
		$this->client_id = "";
		$this->client_secret = "";

		$this->client_id = urlencode($this->client_id);
		$this->client_secret = urlencode($this->client_secret);

		$this->my_dwolla_account = "000-000-0000";
          $this->destination_account = "0000=0000=000";
		$this->destination_comment = "ForDanielHerda";
		$this->pin = "1313";


                           $this->code = "";

                           $this->send_code = "";

		if (!$this->code){
			$url = "https://www.dwolla.com/oauth/v2/authenticate?client_id={$this->client_id}&response_type=code&redirect_uri=http://localhost/proposed_solution2.php&scope=send";
			echo $url;
			exit;
			$result = file_get_contents($url);
			print_r($result);
			exit;

		}

		$url = "https://www.dwolla.com/oauth/v2/token?client_id={$this->client_id}&client_secret={$this->client_secret}&grant_type=authorization_code&redirect_uri=http://localhost/proposed_solution2.php&code={$this->code}";

		$result = file_get_contents($url);

		$result = json_decode($result);

		if(isset($result->error)){
			print_r($result);
		}


		$this->raw_token = $result->access_token;
		$this->token = urlencode($result->access_token);


		$url = "https://www.dwolla.com/oauth/v2/token?client_id={$this->client_id}&client_secret={$this->client_secret}&grant_type=authorization_code&redirect_uri=http://localhost/proposed_solution2.php&code={$this->send_code}";

		$result = file_get_contents($url);

		$result = json_decode($result);

		if(isset($result->error)){
			print_r($result);
		}

		$this->send_token = $result->access_token;
	}



public function getBalance()
{
 $url = "https://www.dwolla.com/oauth/rest/balance/?oauth_token=" . $this->token;

$result = $this->getUrl($url);
$result = json_decode($result);
return $result->Response;

}

public function getTransactions()
{
 $url = "https://www.dwolla.com/oauth/rest/transactions/?oauth_token=" . $this->token;

$result = $this->getUrl($url);
$result = json_decode($result);
if ($result-Success == 1)
return $result->Response;

}

public function send($amount)
{
 $url = "https://www.dwolla.com/oauth/rest/accountapi/send";

$array = array(
               "oauth_token" => $this->send_token,
               "pin" => urlencode($this->pin),
               "destinationType" => "Dwolla",
               "facilitatorAmount" => 0,
               "assumeCosts" => 'false',
               "notes" => urlencode($this->destination_comment),
               "destinationId" => urlencode($this->destination_account),
               "amount" => urlencode($amount),
               
               
               
               
               
               
               
               );

$result = $this->getUrl($url, $array);

print_r($result);


$result = json_decode($result);
if ($result->SendResult)
{
 return $result->SendResult;
}
else

return -1;


}

function getURL($url, $req=false)
{
 
 
 
 $ch = curl_init();
curl_setopt_array($ch, array(
                             CURLOPT_URL => $url,
                             CURLOPT_HEADER => false,
                             CURLOPT_TIMEOUT => 15,
                             CURLOPT_RETURNTRANSFER => true,
                             
                             ));
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");


if ($req)
{
 // generate the POST data string
$post_data = http_build_query($req, '', '&');

curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($req));

$headers = array(
                 'Accept: application/json',
                 'Content-type: application/json',
                 'Connection: keep-alive',
                 
                 
                 
                 );
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

}

curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);

curl_setopt ($ch, CURLOPT_CAINFO, "/var/www/arb/cacert.pem");


$response = @curl_exec($ch);

if (!$response)
{
 echo "Error: ";
echo curl_error($ch);
}

return $response;
}







}

	$dwolla_connection = new dwolla();
	echo $dwolla_connection->send("1.00");

	?>