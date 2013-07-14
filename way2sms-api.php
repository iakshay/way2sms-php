<?php 

require_once('query_string.php');

function sendWay2SMS($username, $password, $numbers, $message)
{
  $curl = curl_init();
  $timeout = 30;
  $result = array();

  /* Get redirect URL siteX.way2sms.com */
  curl_setopt($curl, CURLOPT_URL, "http://way2sms.com/");
  curl_setopt($curl, CURLOPT_COOKIESESSION, 1);
  curl_setopt($curl, CURLOPT_COOKIEFILE, "cookie_way2sms");
  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($curl, CURLOPT_MAXREDIRS, 20);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.5) Gecko/2008120122 Firefox/3.0.5");
  curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
  $text = curl_exec($curl);
  $way2sms_url = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL); //load balancer url

  /* Login */
  curl_setopt($curl, CURLOPT_REFERER, $refurl);
  curl_setopt($curl, CURLOPT_POST, 1);
  curl_setopt($curl, CURLOPT_POSTFIELDS, "username=".$username."&password=".$password."&button=Login");
  curl_setopt($curl, CURLOPT_URL, $way2sms_url."Login1.action");
  $text = curl_exec($curl);;
  //echo $text;
  /* Get Session ID Ex. 31BC16B2D9036C08F6C0823556B2AA73.w810 */
  preg_match_all('/name="id" id="id" value="([0-9a-zA-Z.]+)"/si', $text, $match);
  $session_id = $match[1][0];

  $numbers = explode(",", $numbers);
  for ($i=0; $i < sizeof($numbers); $i++) { 
    /* Open Quick SMS Page */
    $refurl = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
    curl_setopt($curl, CURLOPT_REFERER, $refurl);
    curl_setopt($curl, CURLOPT_URL, $way2sms_url."singles.action?Token=$session_id");
    $text = curl_exec($curl);

    $params = create_query_string($text);
    $params['textArea'] = $message;
    $query_string = http_build_query($params);
    $query_string = str_replace('Mobile', trim($numbers[$i]), $query_string);

    /* Send request to send message with query_string generated */
    $refurl = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
    curl_setopt($curl, CURLOPT_REFERER, $refurl);
    curl_setopt($curl, CURLOPT_URL, $way2sms_url."stp2p.action");
    curl_setopt($curl, CURLOPT_POSTFIELDS, $query_string);
    $text = curl_exec($curl);

    /* Check if message sent was Successful */
    preg_match_all('/Message has been submitted successfully/si', $text, $match);
    if(sizeof($match[0])){
      $result[$i] = true;
    }else{
      $result[$i] = false;
    }
  }
  /* Logout */
  curl_setopt($curl, CURLOPT_URL, $way2sms_url."entry.jsp");
  curl_setopt($curl, CURLOPT_POSTFIELDS, "id={$session_id}.w811&data=");
  curl_setopt($curl, CURLOPT_REFERER, $refurl);
  $text = curl_exec($curl);

  curl_close($curl);
  return $result;

}
?>
