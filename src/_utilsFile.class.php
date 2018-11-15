<?php
    class _utilsFile{
        //0000
        var $_sMsg, $_sUploadDir, $_json = [], $_maxSize = 40000;
   
        public function __construct($attr = [])
        {
            $this->_sUploadDir = '/images/';
        }

        public static function reArrayFiles(&$file_post) {
            $file_ary = array();
            $file_count = count($file_post['name']);
            $file_keys = array_keys($file_post);
        
            for ($i=0; $i<$file_count; $i++) {
                foreach ($file_keys as $key) {
                    $file_ary[$i][$key] = $file_post[$key][$i];
                }
            }
        
            return $file_ary;
        }

        public function upload()
        {
            if(!empty($_FILES))
            {
                $this->_sMsg = '';

                foreach($_FILES as $sKey => $aFiles)
                {
                    for($i = 0, $iNumFiles = count($aFiles['tmp_name']); $i < $iNumFiles; $i++)
                    {
                        /**
                         * Assign values ​​of array in simple variables.
                         */
                        $iErrCode = $aFiles['error'][$i];
                        $iSize = $aFiles['size'][$i];
                        $sFileName = preg_replace("/[^A-Z0-9._-]/i", "_", $aFiles['name'][$i]);
                        $parts = pathinfo($sFileName);
                        if($this->_sUploadDir . $sFileName){
                            // don't overwrite an existing file
                            $j = 0;
                            while (file_exists(__DIR__."/../".$this->_sUploadDir . $sFileName)) {
                                $j++;
                                $sFileName = $parts["filename"] . "-" . $j . "." . $parts["extension"];
                            }
                        }
                        
                        $sTmpFile = $aFiles['tmp_name'][$i];
                        $this->_sUploadDir = '/images/';
                        if(is_dir(__DIR__."/..".$this->_sUploadDir.$_POST["clientId"])){
                            $this->_sUploadDir = $this->_sUploadDir.$_POST["clientId"]."/";
                        }elseif(mkdir(__DIR__."/..".$this->_sUploadDir.$_POST["clientId"], 0777)){
                            $this->_sUploadDir = $this->_sUploadDir.$_POST["clientId"]."/";
                        }
                        $sFileDest = $this->_sUploadDir . $sFileName;
                        $sTypeFile = $aFiles['type'][$i];
                        
                        $bIsImgExt = strtolower($parts["extension"]); // Get the file extension
                        
                        if(($bIsImgExt == 'jpeg' || $bIsImgExt == 'bmp' || $bIsImgExt == 'jpg' || $bIsImgExt == 'png' || $bIsImgExt == 'gif') && (strstr($sTypeFile, '/', true) === 'image'))
                        {
                            
                            if($iErrCode == UPLOAD_ERR_OK)
                            {
                                $image = new image([ 'clientId' => $_POST["clientId"], 'originalName' => $aFiles['name'][$i], 'name_saved' => $sFileName, 'url' => $sFileDest ]);
                                $insert = $image->add();
                                if(round($iSize / 1024) > $this->_maxSize){
                                    $this->_json[$aFiles['name'][$i]]['status'] = "Failed";
                                    $this->_json[$aFiles['name'][$i]]['Error_message'] = $sFileName . " size (" . round($iSize / 1024)." KB) can't greater than ".round($this->_maxSize / 1024)." MB";
                                }else if(move_uploaded_file($sTmpFile, __DIR__."/../".$sFileDest)){
                
                                    $base64 = 'data:image/'.$bIsImgExt.';base64,'.base64_encode(file_get_contents(__DIR__."/../".$sFileDest) );
                                    $image = new image([ 'status' => 'success', 'hash' => $insert['hash'], 'clientId' => $_POST["clientId"], 'originalName' => $aFiles['name'][$i],'name_saved' => $sFileName,'url' => $sFileDest, 'base64' => $base64 ]);
                                    
                                    $this->_json[$aFiles['name'][$i]]['status']  = "Success";
                                    $this->_json[$aFiles['name'][$i]]['url']     = "$sFileDest";
                                    $this->_json[$aFiles['name'][$i]]['size']    = round($iSize / 1024) . ' KB';
                                    $this->_json[$aFiles['name'][$i]]['DB']      = $image->change();
                                    
                                    $this->_sMsg .= '<p style="color:green; font-weight:bold; text-align:center">Successful "' . $sFileName . '" file upload!</p>';
                                    $this->_sMsg .= '<p style="text-align:center">Image type: ' . str_replace('image/', '', $sTypeFile) . '<br />';
                                    $this->_sMsg .= 'Size: ' . round($iSize / 1024) . ' KB<br />';
                                    $this->_sMsg .= '<a href="' . $sFileDest . '" title="Click here to see the original file" target="_blank"><img src="' . $sFileDest . '" alt="' . $sFileName  . '" width="300" height="250" style="border:1.5px solid #ccc; border-radius:5px" /></a></p>';
                                }else{
                                    $this->_json[$aFiles['name'][$i]]['status']         = "Failed";
                                }
                            }
                            else
                            {
                                $this->_sMsg .= '<p style="color:red; font-weight:bold; text-align:center">Error while downloading the file "' . $sFileName . '"<br />';
                                $this->_sMsg .= 'Error_code: "' . $iErrCode . '"<br />';
                                $this->_sMsg .= 'Error_message: "' . $this->_aErrFile[$iErrCode] . '"</p>';
                                $this->_json[$aFiles['name'][$i]]['status']         = "Failed";
                                $this->_json[$aFiles['name'][$i]]['Error_code']     = $iErrCode;
                                $this->_json[$aFiles['name'][$i]]['Error_message']  = $sFileDest;
                                
                            }
                        }
                        else
                        {
                            $this->_json[$aFiles['name'][$i]]['status']         = "Failed";
                            $this->_json[$aFiles['name'][$i]]['Error_message']  = "File type incompatible. Please save the image in .jpg, .jpeg, .png or .gif";
                            $this->_sMsg .= '<p style="color:red; font-weight:bold; text-align:center">File type incompatible. Please save the image in .jpg, .jpeg, .png or .gif</p>';
                        }
                    }
                }
            }
            else
            {
                $this->_json[$aFiles['name'][$i]]['status']         = "Failed";
                $this->_json[$aFiles['name'][$i]]['Error_message']  = "You must select at least one file before submitting the form.";
                $this->_sMsg = '<p style="color:red; font-weight:bold; text-align:center">You must select at least one file before submitting the form.</p>';
            }

            return $this;
        }
    }