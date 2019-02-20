<?php
# raccourcisseur d'url par Bronco: http://warriordudimanche.net
# modifié par Yomli

include('data/config.php');
include('core/lib.php');

// Note: vu que PHP considère que false est empty,
// il suffit de vérifier que $privateGo n'est pas commenté
if(isset($privateGo)) {
	$auto_restrict['root']='./';
	$auto_restrict['path_from_root']='core/auto_restrict';
	$auto_restrict['redirect_success']='admin.php';
	include($auto_restrict['root'].$auto_restrict['path_from_root'].'/auto_restrict.php');	
}

// Load base
if (is_file('data/base.php')) {
	$base=load('data/base.php');
} else { 
	$base=[];
}

// Delete URL
if (isset($_GET['delete'])) {
	if (!empty($base[$_GET['delete']])) {
		unset($base[$_GET['delete']]);
	    save('data/base.php',$base);	
	    $msg='Redirection supprimée.';
	}
}

// Purge base
if (isset($_GET['purge'])) {
	if (is_file('data/base.php')) {
		$success=unlink('data/base.php');
		if ($success) {
			$msg='Base purgée.';
		} else {
			$msg='La base n\'a pas pu être purgée. Supprimez le fichier data/base.php';
		}
	}
}

// Export base
if (isset($_GET['export'])) {
	if (is_file('data/base.php')) {
		$export = 'data/go-export_'.date('Y-m-d_His').'.json';
		$success = writeJSON($base, $export);
		if ($success) {
			$success = downloadFile($export);
			if (!$success) {
				$msg = "Problème de téléchargement. Votre export est disponible à l'emplacement <a href='".$export."'>".$export."</a>";
			}
		} else {
			$msg = "Erreur d'écriture.";
		}
	} else {
		$msg = "Il n'y a rien à exporter.";
	}
}

// Import base
if (isset($_FILES['import'])) {
	$file = 'data/go-import.json';
	$success = uploadFile($_FILES['import'], $file);
	if (!is_array($success)) {
		$json = readJSON($file);
		if(is_array($json)) {
			foreach ($json as $code => $url) {
				$base[$code]=strip_tags($url);
			}
			save('data/base.php',$base);
			$msg = "Les liens ont bien été importés.";
		} else {
			$msg = "Erreur de lecture du fichier.";
		}
	} else {
		$msg = "Il y a eu un problème avec l'upload du fichier&nbsp;:";
		foreach ($success as $key => $value) {
			$msg .= "<br />" . $value;
		}
	}
}

?>

<?php include('tpl/tpl.header.php'); ?>

<a href="<?php echo (isset($privateGo)) ? '?logout=ok' : 'index.php'; ?>" class="button admin" title="Déconnexion">⚙️</a>

<?php if (empty($msg)) { ?>
	<h2>Importer des liens</h2>
	<form action="admin.php" method="post" enctype="multipart/form-data">
        <input type="file" name="import" required="true"/>
        <input type="submit" name="submit" value="Importer des URL"/>
        <?php if(isset($privateGo)) { newToken(); } ?>
        <a href="admin.php?export&token=<?php if(isset($privateGo)) { newToken(true); } ?>" class="button text">📤&nbsp;Exporter vos URL</a>
    </form>
	
	<h2>Supprimer un lien</h2>

	<?php if (!empty($base)) { ?>

	<form action="admin.php" method="get">
		<input required="true" type="text" name="delete" placeholder="Tapez le texte du raccourci"/>
		<input type="submit" value="Supprimer"/>
		<?php if(isset($privateGo)) { newToken(); } ?>
	</form>
	
	
		<table>
			<tr>
				<th>URL</th>
				<th>Code</th>
				<?php if(!empty($qrcodeAPI)){echo '<th>&nbsp;</th>';} ?>
				<th>&nbsp;</th>
			</tr>
			<?php foreach ($base as $code => $url) { 
					$str=addSlash(str_replace('admin.php','',getURL())).$code;
				?>
					<tr>
						<td><a href="<?php echo $url; ?>"><?php echo $url; ?></a></td>
						<td><input type="text" value="<?php echo $code; ?>" onclick="copyThis('<?php echo $str; ?>', this); select()" /><a></a></td>
						<?php if(!empty($qrcodeAPI)){echo '<td><a href="'.$qrcodeAPI.urlencode($url).'" target="_blank" title="QRCode">📱</a></td>';} ?>
						<td><a href="admin.php?delete=<?php echo $code; ?>&token=<?php if(isset($privateGo)) { newToken(true); } ?>" class="button" title="Supprimer">❌</a></td>
					</tr>
				<?php } ?>
		</table>

		<h2>Purger les liens</h2>
		<p>Attention, cela va supprimer tous les liens !
			<a href="admin.php?purge&token=<?php if(isset($privateGo)) { newToken(true); } ?>" class="button red text">☠️&nbsp;Purger</a>
		</p>

	<?php include('tpl/tpl.script.php'); 
		} else { 
			echo '<h3>Aucun lien à supprimer.</h3>'; 
		}
	 } else { 
	 	echo '<h3>'.$msg.'</h3><a href="admin.php" class="button back" title="Retour">⬅️</a>'; } 
	 ?>

<?php include('tpl/tpl.footer.php'); ?>