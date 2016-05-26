<h2>Modification du droit d'une application (module de gestion des droits)</h2>
<div class="row">
<div class="col-lg-6">
<a href="index.php?module=appliList">Retour à la liste des applications</a>
&nbsp;<a href="index.php?module=appliDisplay&aclappli_id={$dataAppli.aclappli_id}">
Retour à {$dataAppli.appli} ({$dataAppli.applidetail})
</a>

<form id="acoForm" class="form-horizontal protoform"  method="post" action="index.php">
<input type="hidden" name="aclaco_id" value="{$data.aclaco_id}">
<input type="hidden" name="aclappli_id" value="{$data.aclappli_id}">
<input type="hidden" name="moduleBase" value="aco">
<input type="hidden" name="action" value="Write">
<div class="form-group center">
      <button type="submit" class="btn btn-primary button-valid">{$LANG["message"].19}</button>
      {if $data.aclappli_id > 0 }
      <button class="btn btn-danger button-delete">{$LANG["message"].20}</button>
      {/if}
 </div>
 
<div class="form-group">
<label for="aco" class="control-label col-md-4">Nom du droit utilisé dans l'application <span class="red">*</span> :</label>
<div class="col-md-8"><input type="text" id="aco" name="aco" value="{$data.aco}" autofocus required></div>
</div>
<div class="form-group">
<fieldset class="col-lg-12">
<legend>Groupes disposant du droit :</legend>
{section name=lst loop=$groupes}
<div class="col-md-2 col-sm-offset-2">
      <div class="checkbox">
        <label>
        <input type="checkbox" name="groupes[]" value="{$groupes[lst].aclgroup_id}" {if $groupes[lst].checked == 1}checked{/if}>
        {$groupes[lst].groupe}
        </label>
      </div>
    </div>
{/section}
</fieldset>
</div>

 </form>
</div>
</div>


<span class="red">*</span><span class="messagebas">{$LANG["message"].36}</span>