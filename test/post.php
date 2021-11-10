<?php
include '../config.php';
include 'thread_config.php';
include '../parse.php';

$link = array();

//chage it
if($_FILES['image']['size'][0] != 0){
    $errors= array();
    $names = array_slice($_FILES['image']['tmp_name'], 0, 4);
    foreach($names as $tmp_name) {
        $img_data = file_get_contents($tmp_name);
        
        $client_id = "4409588f10776f7";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.imgur.com/3/image.json');
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Client-ID ' . $client_id));
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('image' => base64_encode($img_data)));
        
        $reply = curl_exec($ch);
        curl_close($ch);
        
        $reply = json_decode($reply);
        //echo $reply;
        if (isset($reply->data->link)) {
            $link[] = $reply->data->link;
        }
    }
}
$parsedown = new Parsedown();

$title = (isset($_POST['title']) && $_POST['title'] < $title_len) ? htmlspecialchars(strip_tags(preg_replace("~[\p{M}]~uis","", $_POST['title']))) : "";
$body = (isset($_POST['body']) && $_POST['body'] < $text_len) ? $parsedown->parse(strip_tags(str_replace("\n", "\n\n", preg_replace("~[\p{M}]~uis","", $_POST['body'])))) : "";
$date = date('m/d/y H:i:s');

//var_dump($_FILES);

if($title == '' || $body == '' || count($link) == 0) {
    echo "Вы не можете создать тред без текста поста, темы и картинки";
    die();
}

$info = array("name" => $name,
  "date" => $date,
  "title" => $title,
  "body" => $body,
  "include" => $link,
  "op" => 1);
  
$metainfo = json_encode($info);

$threads = scandir($thread_prefix."threads",1);
natsort($threads);
$threads = array_reverse($threads, false);
$latestthread = (int)$threads[0];
$latestthread += 1;
mkdir($thread_prefix."threads/".(string)$latestthread);
$filepath = $thread_prefix."threads/".(string)$latestthread."/1.txt";
file_put_contents($filepath, $metainfo);
header("Location: ".$thread_prefix."thread.php?id=".$latestthread);
die();
?>
