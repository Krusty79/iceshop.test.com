<?php
    header('Content-Type: application/json');
    $search = [];
    if(isset($_GET["clientId"]))
        $search["clientId"] = "%$_GET[clientId]%";
    if(isset($_GET["hash"]))
        $search["hash"] = "%$_GET[hash]%";
    foreach(glob("../src/*.class.php") as $class){
        require_once $class;
    }
    $attr = [];
    if(count($search) > 0)
        $attr["search"] = $search;
    echo json_encode(['data' => (new image())->getAll($attr)]);