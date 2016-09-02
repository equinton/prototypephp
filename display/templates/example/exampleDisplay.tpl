<h2>Détail EXEMPLE</h2>
<a href="index.php?module=exampleList">Retour à la liste</a> 
{if $droits.gestion == 1}
<a href="index.php?module=exampleModif&idExample={$data.idExample}">
Modifier...
</a>
{/if}


<fieldset class="col-sm-4">
<legend>Informations générales</legend>
<div class="form-display">
<dl class="dl-horizontal">
<dt>Id :</dt>
<dd>{$data.example_id}</dd>
</dl>
<dl class="dl-horizontal">
<dt>Statut :</dt>
<dd>{$data.example_status_name}</dd>
</dl>
<dl class="dl-horizontal">
<dt>Commentaire :</dt>
<dd><div class="">{$data.example_comment}</div></dd>
</dl>
</div>
</fieldset>