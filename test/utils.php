<?php
include '../config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function get_threads_list() {
    global $threads_dir;
    $threads = scandir($threads_dir,1);
    natsort($threads);
    $threads = array_reverse(array_slice($threads, 2), false); // threads list
    return $threads;
}

function get_posts_list($id) {
    global $threads_dir;
    $id = (string)$id;
    $posts = scandir($threads_dir.$id);
    natsort($posts);
    $posts = array_slice($posts, 2); // posts list
    return $posts;
}

function get_post_data($thread_id, $post_id_txt) {
    global $threads_dir;
    $post_path = $threads_dir.$thread_id.'/'.$post_id_txt;
    return json_decode(file_get_contents($post_path));
}

?>