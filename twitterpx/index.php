<?
require_once '../twitterpool/TwitterTokens.php';
require './config.php';

$twitterreq=$_GET["req"];
unset($_GET["req"]);

$twitterTokens=new TwitterTokens;
$auth=$twitterTokens->getRandomToken();
$tmhOAuth->config['user_token']=$auth['token'];
$tmhOAuth->config['user_secret']=$auth['secret'];

if (isset($_POST)&&(count($_POST)>0)) {
	//POST
	$code = $tmhOAuth->request('POST', $tmhOAuth->url('1.1/'.$twitterreq), $_POST);
} else {
	//GET
	$code = $tmhOAuth->request('GET', $tmhOAuth->url('1.1/'.$twitterreq), $_GET);
}
echo $tmhOAuth->response['response'];
?>