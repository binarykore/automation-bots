<?php
$_miner = [];
$_cookies = [];
libxml_use_internal_errors(true);
function toFixed($_integer,$_decimals) {
  return(number_format($_integer,$_decimals,'.',""));
}
function parseFloat($floatString){
    $LocaleInfo = localeconv();
    $floatString = str_replace($LocaleInfo["mon_thousands_sep"],"",$floatString);
    $floatString = str_replace($LocaleInfo["mon_decimal_point"],".",$floatString);
    return(floatval($floatString));
}
function updatedBalanceBCH($_data){
	return(("[ Miner Balance ]: ").toFixed(parseFloat($_data["walletBalance"] + (((microtime(true) - $_data["lastWithdraw"]) / 86400) * (2.65/100)) * $_data["deposit"]),10)." [ BCH ]");
}
//Please Insert BCH Address
$_miner["a"] = readline("Please Enter your BCHAddress:\n");
$_miner["url"] = ("https://bchjolly.com/index.php?action=login&email=".($_miner["a"]));
$_miner["req"] = ("https://bchjolly.com/");
$_miner["data"] = ("https://bchjolly.com/?action=getdata");
$_miner["daemon"] = curl_init();
curl_setopt($_miner["daemon"],CURLOPT_URL,$_miner["url"]);
curl_setopt($_miner["daemon"],CURLOPT_RETURNTRANSFER,true);
curl_setopt($_miner["daemon"],CURLOPT_HEADER,true);
$_output = curl_exec($_miner["daemon"]);
$_hash = json_decode($_output,true);
curl_close($_miner["daemon"]);
preg_match_all('/^Set-Cookie:\s*([^;]*)/mi',$_output,$_matchsticks);
foreach($_matchsticks[1] as $_items) {
	parse_str($_items,$_coke);
	$_cookies = array_merge($_cookies,$_coke);
}
$_miner["c"] = ("cookie: __cfduid=".$_cookies["__cfduid"]."; PHPSESSID=".$_cookies["PHPSESSID"]);
print_r($_hash);
if(empty($_hash["error"])){
	$_miner["h"] = [
		"authority: bchjolly.com",
		"method: GET",
		"path: /",
		"scheme: https",
		"accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
		"accept-encoding: gzip, deflate, br",
		"accept-language: en-PH,en-US;q=0.9,en;q=0.8",
		"cache-control: max-age=0",
		($_miner["c"]),
		"sec-fetch-dest: document",
		"sec-fetch-mode: navigate",
		"sec-fetch-site: none",
		"sec-fetch-user: ?1",
		"user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.141 Safari/537.36"
	];
	$_minerd = curl_init();
	curl_setopt($_minerd,CURLOPT_URL,$_miner["req"]);
	curl_setopt($_minerd,CURLOPT_HTTPHEADER,$_miner["h"]);
	curl_setopt($_minerd,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($_minerd,CURLOPT_ENCODING,"gzip");
	$_bot = curl_exec($_minerd);
	curl_close($_minerd);
	$_body = new DOMDocument();
	$_body -> validateOnParse = true;
	$_body -> loadHTML($_bot);
	$_body -> preserveWhiteSpace = false;
	$_bchaddress = $_body -> getElementById("email");
	echo("Logged In: ( ".$_bchaddress -> getAttribute("value")." )\n");
	$_body -> saveHTML();
	do{
		$_minergd = curl_init();
		curl_setopt($_minergd,CURLOPT_URL,$_miner["data"]);
		curl_setopt($_minergd,CURLOPT_HTTPHEADER,$_miner["h"]);
		curl_setopt($_minergd,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($_minergd,CURLOPT_ENCODING,"gzip");
		$_data = json_decode(curl_exec($_minergd),true);
		curl_close($_minergd);
		echo(updatedBalanceBCH($_data)."\n");
		sleep(2);
	}while(!empty($_bchaddress -> getAttribute("value")));
}//
?>
