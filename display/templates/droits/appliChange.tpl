<h2>Modification d'une application (module de gestion des droits)</h2>

<a href="index.php?module=appliList">Retour à la liste des applications</a>
<div class="row">
<div class="col-md-6">
<form class="form-horizontal protoform" id="appliForm" method="post" action="index.php">
<input type="hidden" name="aclappli_id" value="{$data.aclappli_id}">
<input type="hidden" name="moduleBase" value="appli">
<input type="hidden" name="action" value="Write">
<div class="form-group">

<label for="appli" class="control-label col-md-4">Nom de l'application <span class="red">*</span> :</label>
<div class="col-md-8">
<input id="appli" type="text" name="appli" class="form-control" value="{$data.appli}" autofocus required>
</div>
</div>

<div class="form-group">
<label for="applidetail" class="control-label col-md-4">Description : </label>
<div class="col-md-8">
<input id="applidetail" type="text" class="form-control" name="applidetail" value="{$data.applidetail}">
</div>
</div>
<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-primary button-valid">{$LANG["message"].19}</button>
      {if $data.aclappli_id > 0 }
      <button class="btn btn-danger button-delete">{$LANG["message"].20}</button>
      {/if}
    </div>
  </div>

</form>
</div>
</div>

<span class="red">*</span><span class="green">Champ obligatoire</span>