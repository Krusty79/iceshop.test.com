<?php
    if(isset($_GET['hash'])){
        foreach(glob("./src/*.class.php") as $class){
            require_once $class;
        }
        $image = (new image())->getByHash($_GET['hash']);
        if(file_exists(__DIR__.$image['url'])){
            $content = file_get_contents(__DIR__.$image['url']);
            header('Content-Type: image');
            header('Content-Length: '.strlen( $content ));
            header('Content-disposition: inline; filename="' . $image['name'] . '"');
            header('Cache-Control: public, must-revalidate, max-age=0');
            header('Pragma: public');
            header('Last-Modified: '.$image['uploadedat']);
            echo $content;
          }else{
            header("HTTP/1.1 404 Moved Permanently");
            exit();
          }
    }else{
        header("HTTP/1.1 404 Moved Permanently");
        exit();
    }