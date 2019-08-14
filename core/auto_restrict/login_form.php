<style>
	@import url("tpl/style.css");
	.form_content form {
		flex-direction: column;
		margin-top: 1em;
	}
	.form_content input {
		padding: 0.6em 0.6em;
	}
	.form_cookie {
		display: flex;
		align-items: center;
	}
	@viewport{
		width: device-width;
		zoom:1;
	}
</style>

<?php $f=file_exists($auto_restrict['path_to_files'].'/auto_restrict_pass.php'); ?>
<div class="form_content">
	<h1>
			<?php if($f){echo 'Se connecter';}else{echo 'S\'inscrire';} ?>
	</h1>
	<form action='' method='post' name='' >

		<label for='login'>Identifiant </label>
		<input type='text' name='login' id='login' required="required" autofocus/>
			
		<label for='pass'>Mot de passe </label>
		<input type='password' name='pass' id='pass'  required="required"/>	
		
		<?php if($f){echo '<div class="form_cookie"><input id="cookie" type="checkbox" value="cookie" name="cookie"/><label for="cookie">Rester connecté</label></div>';} ?>

		<input type='submit' value='<?php if($f){echo 'Connexion';}else{echo 'Créer un compte';} ?>'/>	
	</form>
</div>