<?php
    header('Content-Type: application/json');
    $search = [];
    if(isset($_POST["search"]))
        $search["global"] = "%$_POST[search]%";
    foreach(glob("../src/*.class.php") as $class){
        require_once $class;
    }
    $attr = ['fields' => 'originalName,hash,CONCAT("http://'.$_SERVER['HTTP_HOST'].'","/",clientId,"/",hash) as link'];
    if(count($search) > 0)
        $attr["search"] = $search;
    
    echo json_encode(['data' => (new image())->search($attr)]);