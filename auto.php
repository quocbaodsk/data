<?php
error_reporting(0);
set_time_limit(0);
function cURL($url, $data = '', $referer = 'https://vnlike.net/'){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT,true);
    curl_setopt($ch, CURLOPT_TCP_NODELAY,true);      
    curl_setopt($ch, CURLOPT_REFERER, $referer);                  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //curl_setopt($ch, CURLOPT_PROXY, $poxySocks4);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/9.0 (Windows NT 20.0; WOW46; rv:59.6) Gecko/20100101 Firefox/99.0");
    curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ .'/'.$_GET['token'].'.txt');
  curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ .'/'.$_GET['token'].'.txt');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    if($data){
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  }
    return curl_exec($ch);
    curl_close($ch);
}
//
$tB = microtime(true);
// site vnlike.net

# config
if(!isset($_GET['token']))
    die('Chưa nhập token?');
$token = $_GET['token'];
# login to website
$url_login = "https://vnlike.net/login.php?token=".$token;
$login = cURL($url_login);
# get id post in feed token
$get_me = json_decode(cURL("https://graph.facebook.com/v6.0/me?access_token=".$token), 1)['id'];
if(!isset($get_me))
    die('token die');
$get_feed = json_decode(cURL("https://graph.facebook.com/v6.0/".$get_me."/feed?access_token=".$token),1)['data'][0]['id'];
if(!isset($get_feed))
    die('Không tìm thấy bài viết nào');
$current_like = json_decode(cURL("https://graph.facebook.com/".$get_feed."?fields=likes.summary(true)&access_token=".$token), 1)['likes']['summary']['total_count'];
# send like to id post
$send = cURL("https://vnlike.net/autolike/send-likes.php", 'id_post_feed='.$get_feed);
# conv html to text
//preg_match('#Hi (.+?) - (.+?) #', $send, $return);
//echo 'User: '.$return[1];
//
$tA = microtime(true); 
$load = number_format((($tA - $tB) * 1000), 2)." seconds"; 
//
if(preg_match('#Đợi thêm (.+?) giây#', $send, $return))
{
  echo $return[0].' - Load in '.$load;
} else {
  $new_like = json_decode(cURL("https://graph.facebook.com/".$get_feed."?fields=likes.summary(true)&access_token=".$token), 1)['likes']['summary']['total_count'];
  $up = $new_like-$current_like;
  echo 'tăng '.$up.' (like củ: '.$current_like.') like thành công cho bài viết id: '.$get_feed.' - Load in '.$load;
}

// site update

#


// clear cache / cookie
unlink(''.$_GET['token'].'.txt');
