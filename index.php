<?php 
	/**
	 * Go index page.
	 *
	 * If you're wondering what this is all about, 
	 * check out the README.md file.
	 *
	 * Project repo:        https://github.com/yomli/yomli-go
	 *
	 */

include 'data/config.php';
include 'core/lib.php';
$databaseFile = 'data/base.php';

// Password protection
if ($privateGo) {
	if (isset($_GET['url'])||empty($_GET)) {
		$auto_restrict['root']='./';
		$auto_restrict['path_from_root']='core/auto_restrict';
		$auto_restrict['redirect_success']='index.php';
		include $auto_restrict['root'].$auto_restrict['path_from_root'].'/auto_restrict.php';
	}	
}

// Load base
if (is_file($databaseFile)) {
	$base = load($databaseFile);
} else { 
	$base = [];
}

// Add new URL
if (isset($_GET['url'])) {
	if (empty($_GET['code'])) {
		$id = newid();
	} else {
		$id = preg_replace('#[^a-zA-Z0-9]#','',$_GET['code']);
	}
	while (!empty($base[$id])) {
		$id = newid();
	}

	$base[$id] = strip_tags($_GET['url']);
	save($databaseFile,$base);

	$question = ($stripQuestion) ? '' : '?';
	$str = addSlash(str_replace('index.php','',getURL())).$question.$id;
	$msg = 'Votre URL raccourcie&nbsp;: ';
	$msg .= '<a href="'.$str.'" data-code="'.$id.'" id="short-link">'.$str.'</a></strong>';
	$msg .= '<a href="#" title="Copier dans le presse-papier" id="clipboard" class="hidden button" onclick="copyThis(\''.$str.'\', this)">📋</a>';
}

// Any other parameter
if (!empty($_GET)&&count($_GET)==1){
	$get = array_keys($_GET);
	$get = $get[0];
	if (!empty($base[$get])){
		header('location: '.$base[$get]);
		exit;
	} else {
		$msg = 'URL inconnue.';
	}
}

?>

	<?php include 'tpl/header.html'; ?>

	<a href="admin.php" class="button" id="button-admin" title="Administration">⚙️</a>
	<a href="user.php" class="button hidden" id="button-user">Mes liens</a>

	<?php if (empty($msg)) { ?>

		<form action="index.php" method="get">
			<input type="url" name="url" placeholder="URL à raccourcir" required />
			<input type="text" name="code" placeholder="Raccourci personnalisé (optionnel)"/>
			<input type="submit" value="Go&nbsp;!"/>
			<?php if ($privateGo) { newToken(); } ?>
		</form>	

	<?php } else { 
			echo '<h3>'.$msg.'</h3><a href="'.$_SERVER['HTTP_REFERER'].'" class="button" id="button-back" title="Retour">⬅️</a>'; 
			if (!empty($qrcodeAPI)&&isset($_GET['url'])) {
				echo '<img src="'.$qrcodeAPI.urlencode($_GET["url"]).'" class="qrcode" />'; 
			} ?>
			<script type="text/javascript" src="tpl/script.js"></script>
			<script type="text/javascript">//<![CDATA[
				var copyButton = document.getElementById('clipboard');
				copyButton.classList.remove('hidden');
			//]]></script> 
	<?php } ?>


	<?php if (isset($_GET['url'])) { ?>
		<script type="text/javascript">//<![CDATA[
			;(function (window, document, undefined) {
				// Feature test for localStorage
				if(!('localStorage' in window)) return;
				var shortLink = document.getElementById('short-link').getAttribute('data-code');
				var data = [];
				// Get links
				var localLinks = localStorage.getItem('go--links');
				if (!localLinks) {
					data[0] = shortLink;
				} else {
					data = JSON.parse(localLinks);
					data.push(shortLink);
				}
				localStorage.setItem('go--links', JSON.stringify(data));
			})(window, document);	
		//]]></script>
	<?php } ?>

	<script type="text/javascript">//<![CDATA[
		;(function (window, document, undefined) {
			// Feature test for localStorage
			if(!('localStorage' in window)) return;
			
			// Get links
			var localLinks = localStorage.getItem('go--links');
			if (!localLinks) return;
			
			// Create a form to POST
			var form = document.createElement('form');
			form.style.visibility = 'hidden';
			form.method = 'POST';
			form.action = 'user.php';
			var input = document.createElement('input');
			input.name = 'links';
			input.value = localLinks;
			form.appendChild(input);
			document.body.appendChild(form);
			
			// Unhide 'My links'
			var userButton = document.getElementById('button-user');
			userButton.classList.remove('hidden');

			// On click, post to user
			userButton.addEventListener('click', function(e){
				e.preventDefault();
				e.stopPropagation();
    			form.submit();
 			}, false);
		})(window, document);	
	//]]></script>

	<?php include 'tpl/footer.html'; ?>