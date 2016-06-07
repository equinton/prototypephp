	<div class="navbar navbar-default" role="navigation">
	<div class="container-fluid">
	
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse"
				data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span> <span
					class="icon-bar"></span> <span class="icon-bar"></span> <span
					class="icon-bar"></span>
			</button>
			<div class="navbar-brand"><img src="display/images/logo.png" height="25"></div>
			<span class="navbar-text"><b>{$LANG.message.1}</b></span>
		</div>
		<!-- Affichage du menu -->
		<div class="collapse navbar-collapse" id="navbar_collapse">
		<ul class="nav navbar-nav sm">{$menu}
		</ul>

		<!-- Boutons a droite du menu -->
		<ul class="nav navbar navbar-right">
			<a href='index.php?module=setlanguage&langue=fr'> <img
				src='display/images/drapeau_francais.png' height='20' border='0'>
			</a>
			<a href='index.php?module=setlanguage&langue=en'> <img
				src='display/images/drapeau_anglais.png' height='20' border='0'>
			</a> &nbsp;
			<a href='index.php?module=loginChangePassword'> <img
				src='display/images/key.png' height='20' border='0' title="{$LANG["login"][31]}">
			</a> &nbsp;
{if $isConnected == 1}
<a href="index.php?module=disconnect">
<img src='display/images/key_green.png' height='20' border='0' title="{$LANG['message'].33}"></a>
{else}
<a href="index.php?module=connexion">
<img src='display/images/key_red.png' height='20' border='0' title="{$LANG['message'].8}">
</a>
{/if}			
		</ul>
		</div>
	</div>
</div>
<div class="container-fluid">
<div class="row">
	<div class="col-md-12">
		<p class="message">{$message}</p>
	</div>
</div>
</div>