<?php
set_time_limit(0);
define ('_PROXY', serialize(array('proxyIP' => '127.0.0.1','proxyType' => 'CURLPROXY_HTTP','proxyPort' => '6600','proxyUser' => '','proxyPWD' => '')));

function rand_uga()
{
		$uga_arr = array("Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_1 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/10.0 Mobile/14E304 Safari/602.1",
							"Mozilla/5.0 (Linux; U; Android 4.4.2; en-us; SCH-I535 Build/KOT49H) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30",
							"Mozilla/5.0 (Linux; Android 7.0; SM-A310F Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.91 Mobile Safari/537.36 OPR/42.7.2246.114996",
							"Mozilla/5.0 (Android 7.0; Mobile; rv:54.0) Gecko/54.0 Firefox/54.0",
							"Mozilla/5.0 (Linux; Android 7.0; SAMSUNG SM-G955U Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/5.4 Chrome/51.0.2704.106 Mobile Safari/537.36"
							);
		$rand_keys = array_rand($uga_arr,2);
		return $uga_arr[$rand_keys[0]];
}

function make_referer($url)
{
		$uarr = parse_url($url);
		return $uarr['scheme']."://".$uarr['host']."/";
}

function curl_get($url,$referer,$use_proxy = 0,$ugas = '')
{
        //$ip=getIP();
        $UGA = isset($ugas) ? $ugas : 'Mozilla/5.0 (Linux; Android 7.0; SM-G930V Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.125 Mobile Safari/537.36';
        $ch = curl_init();
        $headers = array();

        //$headers[] = "X-FORWARDED-FOR: ".$ip;
        //$headers[] = "CLIENT-IP: ".$ip;

        $headers[] = "Accept: */*";
        $headers[] = "Cache-Control: max-age=0";
        $headers[] = "Connection: close";
        //$headers[] = "Keep-Alive: 300";
        //$headers[] = "Cookie: caches=xxxxx";
        $headers[] = "Accept-Charset: utf-8;ISO-8859-1;iso-8859-2;q=0.7,*;q=0.7";
        $headers[] = "Accept-Language: en-us,en;q=0.5";
        $headers[] = "Pragma: "; // browsers keep this blank.
        //var_dump($headers);
        curl_setopt($ch, CURLOPT_USERAGENT, $UGA);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if(!empty($referer))
        {
                curl_setopt($ch, CURLOPT_REFERER, $referer);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT,50);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        if($use_proxy == 1)
        {
                $proxy = unserialize(_PROXY);
                //var_dump($proxy);
                curl_setopt($ch, CURLOPT_PROXY, $proxy['proxyIP']);
                curl_setopt($ch, CURLOPT_PROXYTYPE, $proxy['proxyType']);
                curl_setopt($ch, CURLOPT_PROXYPORT, $proxy['proxyPort']);
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy['proxyUser'].':'.$proxy['proxyPWD']);
        }
        $datas = curl_exec($ch);
        if(curl_errno($ch))
        {
                echo "error:".curl_error($ch)."----".date("Y-m-d H:i:s")."\n";
                curl_close($ch);
                return false;
        }
        $info = curl_getinfo($ch);
        $info['datas'] = $datas;
        //var_dump($info);
        curl_close($ch);
        return $info;
}

if(!function_exists('curl_init'))
{
		echo "Need enable curl ext...\n";
		exit;
}

//if(isset($_REQUEST['go']))
//$url = urldecode(http_build_query($_GET));
$url = str_replace($_SERVER['PHP_SELF']."?","",$_SERVER['REQUEST_URI']);
if(empty($url))
{
	echo ":( url is empty?</br>";
	exit;
}
if(substr($url,0,7) !== "http://" && substr($url,0,8) !== "https://")
{
	echo ":( url is not normal.</br>";
	exit;
}

$referer = make_referer($url);
$uga = rand_uga();
/*
echo "SERVER[\"REQUEST_URI\"]:".$_SERVER["REQUEST_URI"]."\n";
echo "SERVER[\"URL\"]:".$_SERVER["URL"]."\n";
echo $url."\n";
echo $referer."\n";
echo $uga."\n";
*/
//echo make_referer("https://www.1004cd.com/m/view.php?bo_table=vod1&wr_id=16890&page=1");
$cts = curl_get($url,$referer,0,$uga);
echo $cts['datas'];
?>