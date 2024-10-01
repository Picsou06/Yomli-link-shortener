<?php

function addSlash($string){
    if (substr($string,strlen($string)-1,1)!='/'&&!empty($string)) {
    	return $string.'/';
    } else {
    	return $string;
    }
}

function save($file, $data) {
    file_put_contents($file, json_encode($data));
}

function load($file) {
    if (file_exists($file)) {
        return json_decode(file_get_contents($file), true);
    }
    return [];
}

function getURL(){
    $url = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] : 'https://'.$_SERVER["SERVER_NAME"];
    $url .= $_SERVER["SCRIPT_NAME"];
    return $url;
}

function newid($nb=3){
	$id='';
	$cons='qzwxrsdcftgvbhnjklpm';
	$voy='aeiouy';
	for($i=1;$i<=$nb;$i++) {
		$id.=$cons[rand(0,strlen($cons))];
		$id.=$voy[rand(0,strlen($voy))];
	}
	return $id;
}


// Yomli
function readJSON($file) {
    $json = file_get_contents($file);
    $decoded = json_decode($json,true);
    if (is_array($decoded)) {
        unlink($file); // Delete after parsing
        return $decoded;
    } else {
        return false;
    }  
}

function writeJSON($data, $file) {
    $fp = fopen($file, 'w');
    $result = fwrite($fp, json_encode($data));
    fclose($fp);
    if($result===false) {
        return $result;
    }
    return true;    
}

function downloadFile($file) {
    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($file).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        $result = readfile($file);
        unlink($file); // Delete after download
        exit;
        if($result===false) {
            return $result;
        }
    } else {
        return false;
    }
}

function uploadFile($file, $path) {
    $errors = [];
    $extensions = ['json'];
    $fileExtension = strtolower(end(explode('.',$file['name'])));

    // $uploadPath = $path . DIRECTORY_SEPARATOR . basename($file['name']);

    if (isset($_POST['submit'])) {
        if (!in_array($fileExtension, $extensions)) {
            $errors[] = "Ce type de fichier n'est pas accepté. Veuillez uploader un fichier JSON.";
        }
        if ($file['size'] > 2000000) {
            $errors[] = "La taille de ce fichier est supérieure à 2 Mo. Veuillez uploader un fichier plus léger.";
        }
        if (empty($errors)) {
            $success = move_uploaded_file($file['tmp_name'], $path);
            if ($success) {
                return true;
            } else {
                $errors[] = "Une erreur est survenue. Veuillez recommencer.";
            }
        }
        return $errors;
    }
}