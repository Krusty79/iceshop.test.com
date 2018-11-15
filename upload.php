<?php
header('Content-Type: application/json');
foreach(glob("./src/*.class.php") as $class){
    require_once $class;
}
echo json_encode ((new _utilsFile())->upload()->_json);