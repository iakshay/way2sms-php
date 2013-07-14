<?php 

/**
 * Returns array with boolean values for success
 *
 * @param  string Way2SMS Username
 * @param  string Way2SMS Password
 * @return string Comma separated Mobile Numbers
 * @param  string Message text
 */

include ('way2sms-api.php');

$result = sendWay2SMS('username','password', 'Mobile', 'Hello');
print_r($result);
?>
