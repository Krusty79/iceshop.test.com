<?php
class image extends DataBase{
    var $dbtab = "images", $status, $clientId, $originalName, $name_saved, $url, $hash, $base64, $uploadedat;
    function __construct($atr = []){
        $this->originalName = isset($atr['originalName']) ? $atr['originalName'] : NULL;
        $this->clientId     = isset($atr['clientId']) ? $atr['clientId'] : NULL;
        $this->status       = isset($atr['status']) ? $atr['status'] : 'processing';
        $this->name_saved   = isset($atr['name_saved']) ? $atr['name_saved'] : NULL;
        $this->url          = isset($atr['url']) ? $atr['url'] : NULL;
        $this->hash         = isset($atr['hash']) ? $atr['hash'] : $this->generateHash();
        $this->base64       = isset($atr['base64']) ? $atr['base64'] : NULL;
        $this->uploadedat   = isset($atr['uploadedat']) ? $atr['uploadedat'] : $this->get_uploadedat();
    }

    function generateHash(){
        if(strlen($this->url) < 5/* || !file_exists(__DIR__."/..".$this->url)*/)
            return NULL;
        $data = implode('', file(__DIR__."/..".$this->url));
        return hash('sha256', $data.$this->url.time());
    }

    function get_uploadedat(){
        if(strlen($this->url) < 5 || !file_exists(__DIR__."/..".$this->url))
            return date('Y-m-d H:i:s');
        return date('Y-m-d H:i:s',filemtime(__DIR__."/..".$this->url)); //date('Y-m-d H:i:s');
    }

    public function getByHash($hash,$attr = []){
        $this->setPDO();
        $fields = isset($attr["fields"])?$attr["fields"]:"*";
        $sql = "SELECT $fields FROM ".$this->dbtab." WHERE hash = ?";
        //print_r($sql);
        $stm = $this->pdo->prepare($sql);
        $stm->execute([$hash]);
        return $stm->fetch(PDO::FETCH_LAZY);
    }
    
    public function getAll($attr=[]){
        $this->setPDO();
        if(isset($attr['search'])){
            $like = " ";
            
            foreach(array_keys($attr['search']) as $i => $field){
                $like .= $i > 0 ? "AND $field LIKE :$field ":"$field LIKE :$field ";
            }
            
            $sql = "SELECT * FROM ".$this->dbtab." WHERE $like";
            $stm = $this->pdo->prepare($sql);
            $stm->execute($attr['search']);
        }else{
            $sql = "SELECT * FROM ".$this->dbtab;
            $stm = $this->pdo->prepare($sql);
            $stm->execute();
        }
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    public function search($attr=[]){
        $fields = isset($attr["fields"])?$attr["fields"]:"*";
        if(isset($attr['search']['global'])){
            $this->setPDO();
            $search = $attr['search']['global'];
            $like = "";
            $values = ["clientId" => $_REQUEST["clientId"]];
            $searchFieds = ['originalName','name_saved','url','hash'];
            
            
            foreach($searchFieds as $i => $field){
                $values[$field] = $search;
                $like .= $i > 0 ? "OR $field LIKE :$field ":"$field LIKE :$field ";
            }
            $sql = "SELECT $fields FROM ".$this->dbtab." WHERE clientId LIKE :clientId AND ( $like )";
            $stm = $this->pdo->prepare($sql);
            $stm->execute($values);
            return $stm->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    function add(){
        $this->insert([
            "clientId"      => $this->clientId,
            //"status"        => $this->status,
            "originalName"  => $this->originalName,
            //"name_saved"    => $this->name_saved,
            "url"           => $this->url,
            "hash"          => $this->hash,
            //"base64"        => $this->base64,
            "uploadedat"    => $this->uploadedat]);
            return $this->getByHash($this->hash,["fields" => "originalName,url,hash,status"]);
    }

    function change(){
        $this->update([
            "status"        => $this->status,
            "name_saved"    => $this->name_saved,
            "url"           => $this->url,
            "hash"          => $this->hash,
            "base64"        => $this->base64,
            "uploadedat"    => $this->uploadedat]);
            return $this->getByHash($this->hash,["fields" => "originalName,url,hash,status"]);
    }
}