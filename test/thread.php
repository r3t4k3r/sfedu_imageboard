<?php
include '../config.php';
include 'thread_config.php';
include 'utils.php'
?>
<HTML>
    <head>
        <link rel="stylesheet" type="text/css" href="../static/krila.css">
        <title>
            <?php echo $thread_name;?> - тред
        </title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    </head>
    <body>
        <div style="max-width:900px;margin:0 auto;">
            <div id="boardsection"><div id="boardsection">
                <a href="/">/home/</a>
            </div>
        </div>
        <a href="index.php">
            <img style="width: 100%;" src="logo.png" id="logo">
        </a>
        <br>
        <div id="threadcreate">
            <form id="form" enctype="multipart/form-data"  action="postcomment.php" method="post">
                <input placeholder="тема сообщения" type="text" name="title" id="title" maxlength="<?php echo $title_len; ?>" ><br>
                <textarea id="textarea" cols="30" rows="5" placeholder="*текст" name="body" maxlength="<?php echo $text_len; ?>" required></textarea><br>
                OP: 
                <input type="checkbox" name="op" value="1">
                <input id="file" type="file" name="image[]" multiple><br>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($_GET["id"]); ?>">
                <input type="submit" name="submit" value="Post" style="float: left;"><br><br>
            </form>
        </div>
        <hr>
        <?php
            
            function echo_pages($len_posts, $thread_id) {
                global $posts_on_thread;
                echo '<div id="pages">';
                for ($i = 1; $i < (int)ceil($len_posts/$posts_on_thread)+1; $i++) {
                    echo '<a style="padding: 5px;" href="thread.php?id='.$thread_id.'&page='.$i.'">'.$i.'</a>';
                }
                echo '</div><hr>';
            }
        
            $thread_id = (int)$_GET["id"];
            $page = (isset($_GET["page"]) && (int)$_GET["page"] > 0) ? ((int)$_GET["page"])-1 : 0;
            
            $threads = get_threads_list();
            
            if (!in_array($thread_id, $threads)) {
                echo "Нет такого треда";
                die();
            }
            
            $posts = get_posts_list($thread_id); # get post by thread id
            $len_posts = count($posts);
            
            echo_pages($len_posts, $thread_id);
            
            $posts = array_slice($posts, $posts_on_thread*$page, $posts_on_thread);
            
            foreach($posts as $post_name) {
                $post_data = get_post_data($thread_id, $post_name);
                if (!isset($post_data)){
                    continue;
                }
                $name = $post_data->name;
                $date = $post_data->date;
                $title = $post_data->title;
                $body = $post_data->body;
                $include = $post_data->include;
                $post_id = substr($post_name, 0, -4);
    			echo '<div id="postcontainer"><a name="'.$post_id.'"></a><span id="posttitle">'.$title.' </span><span id="name">'.$name.' </span><span id="date">'.$date.'</span> <span style="color:blue;" onclick="var v=document.getElementById(`textarea`);v.value += `>>'.$post_id.'\n`"> >>'.$post_id.'</span> <span id="op">'.(isset($post_data->op) ? "OP": "").'</span> <br>';
    			if(!empty($include)) {
                    foreach($include as $img_url) {
                        echo '<img class="a" onclick="this.className = (this.className == `a` ? `b` : `a`  )" src="'.$img_url.'" alt="img" />';
                    }
                }
                echo '<div class="text_hide" onclick="this.className = `text_show`">'.$body.'</div>';
                echo '</div><hr>';
            }
            echo_pages($len_posts, $thread_id);
        ?>
        <script type="text/javascript">
            const form = document.getElementById("form");
            const fileInput = document.getElementById("file");
    
            window.addEventListener('paste', e => {
                if(e.clipboardData.files.length != 0){
                    fileInput.files = e.clipboardData.files;
                }
            });
            form.addEventListener('submit', function(){
            	var btn = this.querySelector("input[type=submit], button[type=submit]");
            	btn.disabled = true;
            });
        </script>
     </body>
</HTML>
