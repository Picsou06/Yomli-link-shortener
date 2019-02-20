<?php 
# raccourcisseur d'url par Bronco: http://warriordudimanche.net
# modifié par yomli : https://code.yom.li/

include('data/config.php');
include('core/lib.php');

// auto_restrict by Bronco
// https://github.com/broncowdd/auto_restrict
if($privateGo) {
	if (isset($_GET['url'])||empty($_GET)) {
		$auto_restrict['root']='./';
		$auto_restrict['path_from_root']='core/auto_restrict';
		$auto_restrict['redirect_success']='index.php';
		include($auto_restrict['root'].$auto_restrict['path_from_root'].'/auto_restrict.php');
	}	
}

// Load base
if (is_file('data/base.php')) {
	$base=load('data/base.php');
} else { 
	$base=[];
}

// Add new URL
if (isset($_GET['url'])) {
	if (empty($_GET['code'])) {
		$id=newid();
		while (!empty($base[$id])) {
			$id=newid();
		}
	} else {
		$id=preg_replace('#[^a-zA-Z0-9]#','',$_GET['code']);
	}
	$base[$id]=strip_tags($_GET['url']);
	save('data/base.php',$base);
	// $str=addSlash(str_replace('index.php','',getURL())).'?'.$id;
	// If .htaccess, use that instead :
	$str=addSlash(str_replace('index.php','',getURL())).$id;
	$msg='Votre URL raccourcie&nbsp;: <a href="'.$str.'">'.$str.'</a></strong><a href="#" title="Copier dans le presse-papier" id="clipboard" class="hidden button" onclick="copyThis(\''.$str.'\', this)">📋</a>';
}

// Any other parameter
if (!empty($_GET)&&count($_GET)==1){
	$get=array_keys($_GET);
	$get=$get[0];
	if (!empty($base[$get])){
		header('location: '.$base[$get]);
		exit;
	} else {
		$msg='URL inconnue.';
	}
}

?>

	<?php include('tpl/tpl.header.php'); ?>

	<a href="admin.php" class="button admin" title="Supprimer une URL">⚙️</a>

	<?php if (empty($msg)) { ?>

	<form action="index.php" method="get">
		<h2>Raccourcir un lien</h2>
		<input required="true" type="url" name="url" placeholder="URL à raccourcir"/>
		<input type="text" name="code" placeholder="Texte du raccourci personnalisé (facultatif)"/>
		<input type="submit" value="Go&nbsp;!"/>
		<?php if($privateGo) { newToken(); } ?>
	</form>	

	<?php } else { echo '<h3>'.$msg.'</h3><a href="'.$_SERVER['HTTP_REFERER'].'" class="button back" title="Retour">⬅️</a>'; 
				if (!empty($qrcodeAPI)&&isset($_GET['url'])) {
					echo '<img src="'.$qrcodeAPI.urlencode($_GET["url"]).'" class="qrcode" />'; 
				}

			include('tpl/tpl.script.php'); ?>
			<script>//<![CDATA[
    			var copyBtn = document.getElementById('clipboard');
    			copyBtn.classList.remove('hidden');
			//]]></script> 

		<?php } ?>

	<?php include('tpl/tpl.footer.php'); ?>