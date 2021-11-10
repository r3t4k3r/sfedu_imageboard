<?php
include '../config.php';
include 'thread_config.php';
include '../parse.php';

function delTree($dir) {
   $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
      (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
}

$id = $_POST["id"];

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
        if (isset($reply->data->link)) {
            $link[] = $reply->data->link;
        }
    }
}
$parsedown = new Parsedown();

$post_text = strip_tags(str_replace("\n", "\n\n", preg_replace("~[\p{M}]~uis","", $_POST['body'])));
preg_match_all('/>>\d+/', $_POST['body'], $matches);
foreach($matches[0] as $match) {
    $post_text = str_replace($match, '<a id="reply" href="#'.substr($match, 2).'">&gt;&gt;'.substr($match, 2).'</a>', $post_text);
}

$title = (isset($_POST['title']) && $_POST['title'] < $title_len) ? htmlspecialchars(strip_tags(preg_replace("~[\p{M}]~uis","", $_POST['title']))) : "";
$body = (isset($_POST['body']) && $_POST['body'] < $text_len) ? $parsedown->parse($post_text) : "";
$date = date('m/d/y H:i:s');

if($body == '') {
    echo "Вы не можете создать ответ без текста поста";
    die();
}

$info = array("name" => $name,
  "date" => $date,
  "title" => $title,
  "body" => $body,
  "include" => $link);

if (isset($_POST['op'])) {
    $info["op"] = 1;
}  

$metainfo = json_encode($info);

$threads = scandir($thread_prefix."threads",1);
natsort($threads);
$threads = array_reverse($threads, false);

if(!in_array($id, $threads)) {
    echo "Тред не существует";
    die();
}
$posts = scandir('threads/'.$id);
natsort($posts);
$posts = array_reverse(array_slice($posts, 2), false); // posts list
if (count($posts) >= $max_posts) {
    delTree("threads/".$id);
    header("Location: index.php");
    die();
}
$post_id = substr($posts[0], 0, -4) + 1; // need post id
file_put_contents($thread_prefix."threads/".$id.'/'.$post_id.'.txt', $metainfo);

header("Location: ".$thread_prefix."thread.php?id=".$id."#".$post_id);
die();
?>
