<?php
class DataBase{
    private  $host = "localhost", $dbname = "iceshop", $dbuser = "root", $dbpass = "";
    var $pdo,$dbtab;
    public function __construct($atr = []){
        $this->setPDO();
    }
    public function setPDO($atr = []){
        $this->pdo = new PDO(
            'mysql:host='.$this->host.';dbname='.$this->dbname.'',
            $this->dbuser,
            $this->dbpass
        );
    }
    public function pdoSet($values) {
        $set = '';
        foreach ($values as $field => $value) {
            $set.="`".str_replace("`","``",$field)."`". "=:$field, ";
        }
        return substr($set, 0, -2); 
    }
    public function insert($values = []){
        $this->setPDO();
        $sql = "INSERT INTO ".$this->dbtab." SET ".$this->pdoSet($values);
        $stm = $this->pdo->prepare($sql);
        $stm->execute($values);
    }

    public function update($values = []){
        $this->setPDO();
        $sql = "UPDATE ".$this->dbtab." SET ".$this->pdoSet($values)." WHERE hash=:hash";
        $stm = $this->pdo->prepare($sql);
        $stm->execute($values);
    }
}