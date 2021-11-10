<?php
include 'thread_config.php';
include '../config.php';
include 'utils.php';
?>
<HTML>
    <head>
        <title>Сфедач - <?php echo $thread_name;?></title>
        <link rel="stylesheet" type="text/css" href="../static/krila.css">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    </head>
    <body>
        <div style="max-width:900px;margin:0 auto;">
        <div id="boardsection">
            <a href="/">
                /home/
            </a>
        </div>
        <a href="index.php">
            <img style="width: 100%;" src="logo.png" id="logo">
        </a>
        <br>
        <div id="boardname">
            <?php echo $thread_name;?> - <?php echo $thread_description; ?>
        </div>
        <br>
        <div id="threadcreate">
            <form id="form" enctype="multipart/form-data"  action="post.php" method="post">
                <input placeholder="название темы*" type="text" name="title" id="title" maxlength="<?php echo $title_len;?>" required><br>
                <textarea cols="30" rows="5" placeholder="текст*" name="body" maxlength="<?php echo $text_len;?>" required></textarea><br>
                <input id="file" type="file" name="image[]" multiple required>
                <input type="submit" name="submit" value="Post" style="float: left;">
                <br>
                <br>
            </form>
        </div>
        <hr>
        <?php
        function echo_pages($len_threads) {
            global $threads_on_board;
            echo '<div id="pages">';
            for ($i = 1; $i < (int)ceil($len_threads/$threads_on_board)+1; $i++) {
                echo '<a style="padding: 5px" href="index.php?page='.$i.'">'.$i.'</a>';
            }
            echo '</div><hr>';
        }
        
        function mapf($s) {
            return str_replace(".txt", "", $s);
        }
        
        function delTree($dir) {
            $files = array_diff(scandir($dir), array('.','..'));
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
            }
            return rmdir($dir);
        }
        
        $page = (isset($_GET["page"])) ? ((int)$_GET["page"])-1 : 0;
        
        $path = "threads/";
        if(!is_dir($path)) {
            echo "dir_die";
            die();
        }
        $threads = get_threads_list();
        $threads_len = count($threads);
    
        $tmp = array();
        foreach($threads as $t) {
      	    $tmp[$t] = filemtime($path."/".$t."/");
      	}
      	arsort($tmp);
    
      	$threads = array();
        foreach ($tmp as $key => $val) {
            $threads[] = $key;
        }
    
        if (count($threads) >= $max_threads) {
            $need_id = min(array_map("mapf", $threads));
            delTree("threads/".$need_id);
            header("Location: index.php");
            die();
        }
        
        echo_pages($threads_len);
                
        for($i = $page*$threads_on_board; $i <= $page*$threads_on_board+$threads_on_board; $i++)
        {
            if (isset($threads[$i])) {
                $d_data = get_post_data($threads[$i], "1.txt");
                $name = $d_data->name;
                $date = $d_data->date;
                $title = $d_data->title;
                $body = $d_data->body;
                $include = $d_data->include;
                //$type = $d_data->type;
                echo '<div id="postcontainer">';
                echo '<a id="posttitle" href="thread.php?id='.$threads[$i].'">'.$title.'</a><span id="name"> '.$name.' </span><span id="date"> '.$date.' </span><span id="postno">No. '.$threads[$i].'</span> <span id="op">'.(isset($d_data->op) ? "OP": "").'</span><br>';
                if(!empty($include)) {
                    foreach($include as $img_url) {
                        echo '<img class="a" onclick="this.className = (this.className == `a` ? `b` : `a`  )" src="'.$img_url.'" alt="img" />';
                    }
                }
                echo '<div class="text_hide" onclick="this.className = `text_show`">'.$body.'</div>';
                echo '<span style="font-size: 10pt;color:blue;">'.(count(scandir($path.$threads[$i]))-2).' постов</span>';
                echo '</div><hr>';
            }
        }
        echo_pages($threads_len);
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
