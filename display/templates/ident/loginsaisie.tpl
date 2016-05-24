<fieldset class="scheduled-border">
	<legend class="scheduled-border">Saisie/modification d'un compte</legend>
	<form class="form-horizontal" method="post" action="index.php">
	<input type="hidden" name="id" value="{$data.id}">
	<input type="hidden" name="module" value="loginwrite">
	<input type="hidden" name="password" value="{$data.password}">
	

<div class="form-group">
<label for="login" class="col-sm-2 control-label">{$LANG.login.0} :</label>
<div class="col-sm-4">
<input id="login" name="login" value="{$data.login}" autofocus>
</div>
</div>

<div class="form-group">
<label for="nom" class="col-sm-2 control-label">{$LANG.login.9} : </label>
<div class="col-sm-4">
<input id="nom" name="nom" value="{$data.nom}"></div>
</div>
<div class="form-group">
<label for="prenom" class="col-sm-2 control-label">{$LANG.login.10} : </label>
<div class="col-sm-4"><input id="prenom" name="prenom" value="{$data.prenom}"></div>
</div>
<br><label for="mail">{$LANG.login.8} : </label><input type="email" id="mail" class="form-control" name="mail" value="{$data.mail}"> 
<br><label for="datemodif">{$LANG.login.11} : </label><input class="form-control" type="date" id="datemodif" name="datemodif" value="{$data.datemodif}" readonly>
<br><label for="pass1">{$LANG.login.1} : </label> <input type="password" autocomplete="off" id="pass1" name="pass1" onchange="verifieMdp(this.form.pass1, this.form.pass2)">
<label for="pass2">{$LANG.login.12}</label> 
<input type="password" id="pass2" autocomplete="off" name="pass2" onchange="verifieMdp(this.form.pass1, this.form.pass2)">
<br><label for="generate">{$LANG.login.21}</label> 
<input id=generate" type="button" name="generate" value="{$LANG.login.22}" onclick="getPassword('pass1', 'pass2', 'motdepasse')">
	<input name="motdepasse" id="motdepasse" size="20">
<br><label for="actif">{$LANG.login.13}</label>
<span id="actif">
<label class="radio-inline">
<input type="radio" name="actif" value="1" {if $data.actif == 1}checked{/if}>{$LANG.message.yes}
</label>
<label class="radio-inline">
<input type="radio" name="actif" value="0" {if $data.actif == 0}checked{/if}>{$LANG.message.no}
</label>
		
</span>
<button class="btn btn-default" type="submit">{$LANG.message.19}</button>
</form>

{if $data.id>0}
<form action="index.php" method="post" onSubmit='return confirmSuppression()'>
<input type="hidden" name="id" value="{$data.id}">
<input type="hidden" name="module" value="logindelete">
<button type="button" class="btn btn-danger" type="submit">{$LANG.message.20}</button>
</form>
{/if}

</fieldset>


