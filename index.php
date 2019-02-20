<?php 
# raccourcisseur d'url par Bronco: http://warriordudimanche.net
function addSlash($string){
    if (substr($string,strlen($string)-1,1)!='/'&&!empty($string)){return $string.'/';}else{return $string;}
}
function load($file=null){
    if (empty($file)){return false;}
    return (file_exists($file) ? unserialize(gzinflate(base64_decode(substr(file_get_contents($file),9,-strlen(6))))) : array() );
}
function save($file=null,$data=null){
    if (empty($file)){return false;}
    if (empty($data)){$data=array();}
    return file_put_contents($file, '<?php /* '.base64_encode(gzdeflate(serialize($data))).' */ ?>');
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
	for($i=1;$i<=$nb;$i++){
		$id.=$cons[rand(0,count($cons))];
		$id.=$voy[rand(0,count($voy))];
	}
	return $id;
}

if (is_file('base.php')){
	$base=load('base.php');
}else{$base=[];}
if (isset($_POST['url'])){
	if (empty($_POST['code'])){
		$id=newid();
		while (!empty($base[$id])){
			$id=newid();
		}
	}else{
		$id=preg_replace('#[^a-zA-Z0-9]#','',$_POST['code']);
	}
	$base[$id]=strip_tags($_POST['url']);
	save('base.php',$base);
	$str=addSlash(str_replace('index.php','',getURL())).'?'.$id;
	$msg='Votre url raccourcie: <strong><a href="'.$str.'">'.$str.'</a></strong>';
}
if (isset($_GET['del'])){
	if (!empty($base[$_GET['del']])){
		unset($base[$_GET['del']]);
	    save('base.php',$base);	
	    $msg='Redirection supprimée <a href="index.php">Retour</a>';
	}
}
if (!isset($_GET['del'])&&!empty($_GET)&&count($_GET)==1){
	$get=array_keys($_GET);
	$get=$get[0];
	if (!empty($base[$get])){
		header('location: '.$base[$get]);
		exit;
	}else{$msg='Url inconnue';}
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Go!</title>
	<link rel="shortcut icon" href="go.png"/>
	<style type="text/css">
		body{background:url(go.png) no-repeat top center;padding-top:70px;}
		section{max-width:800px;margin:0 auto;}
		input{margin:2px;width:300px;padding: 5px;border-radius:3px;background:rgba(0,0,0,0.1);border:1px solid rgba(0,0,0,0.2);color:rgba(0,0,0,0.8);}
		input[type=submit]{width:100px;background:rgba(0,0,0,0.7);border:1px solid rgba(0,0,0,0.8);color:white;}
		input[name=del]{width:620px;}
		h4{text-align: center;padding-bottom: 25px;border-bottom:solid 1px rgba(0,0,0,0.2);}
		footer{max-width:800px;margin:0 auto;opacity:0.2;margin-top:50px;padding-top:25px;border-top:1px solid black;text-align: center;}
		a{text-decoration: none;color:black;font-weight: bold}
		@media (max-width:800px){
			input{display:block;width:80%!important;margin:10px auto;padding:15px;font-size:1.2em;}
			section{text-align: center;}
		}
	</style>
</head>
<body><section>
	<h4>Raccourcisseur d'url minimaliste</h4>
	<?php if (empty($msg)){ ?>
	<form action="index.php" method="post">
		<h1>Ajouter une url</h1>
		<input required="true" type="url" name="url" placeholder="*coller une url ici"/>
		<input type="text" name="code" placeholder="nom de raccourci (optionnel)"/>
		<input type="submit" value="Go!"/>
	</form>
	<form action="index.php" method="get">
		<h1>Supprimer une url</h1>
		<input required="true" type="text" name="del" placeholder="coller le code ici"/>
		<input type="submit" value="Supprimer"/>
	</form>
	<?php }else{ echo '<h1>'.$msg.'</h1>';} ?>
</section>
<footer>Codé à l'arrache par <a href="http://warriordudimanche.net">Bronco</a></footer>
</body>
</html>
