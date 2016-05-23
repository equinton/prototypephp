<div class="navbar-header">
<div class="bandeau">
<img src="display/images/logo.png" width="40">
<p class="navbar-text">{$LANG.message.1}</p>
{$menu}
</div>
</div>

<div class="row">
<div class="col-md-12">
<h1>
<img src="display/images/logo.png" width="40">{$LANG.message.1}
<div class="header_right">
<a href='index.php?module=setlanguage&langue=fr'>
<img src='display/images/drapeau_francais.png' height='20' border='0'>
</a>
<a href='index.php?module=setlanguage&langue=en'>
<img src='display/images/drapeau_anglais.png' height='20' border='0'>
</a>
&nbsp;
{if $ident_type == "BDD" || $ident_type == "LDAP-BDD"}
<a href='index.php?module=loginChangePassword'>
<img src='display/images/key.png' height='20' border='0'>
</a>
&nbsp;
{/if}
{if $isConnected == 1}
<img src='display/images/key_green.png' height='20' border='0' title="{$LANG['message'].33}">
{else}
<img src='display/images/key_red.png' height='20' border='0' title="{$LANG['message'].8}">
{/if}
</div>
</h1>
</div>
</div>



<div class="row">
<div class="col-md-12"><div class="menu">{$menu}</div></div>
</div>
<div class="row">
<div class="col-md-12">
<div class="titre2">{$message}</div>
</div>
</div>