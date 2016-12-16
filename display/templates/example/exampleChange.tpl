<h2>Modification EXEMPLE</h2>
<div class="row">
<div class="col-md-12">
<a href="index.php?module=exampleList">
<img src="display/images/list.png" height="25">
Retour à la liste
</a>
{if $data.example_id > 0}
<a href="index.php?module=exampleDisplay&example_id={$data.example_id}">
<img src="display/images/edit.gif" height="25">
Retour au détail </a>
{/if}
</div>
<fieldset class="col-sm-6">
<legend>Texte du fieldset</legend>

<form class="form-horizontal protoform" id="exampleForm" method="post" action="index.php">
<input type="hidden" name="example_id" value="{$data.example_id}">
<input type="hidden" name="moduleBase" value="example">
<input type="hidden" name="action" value="Write">

<div class="form-group">
<label for="event_date" class="control-label col-md-4">Date<span class="red">*</span> :</label>
<div class="col-md-8">
<input id="example_date" name="example_date" required value="{$data.example_date}" class="form-control datepicker" >
</div>
</div>

<!--  exemple de lecture d'une liste associee (boite deroulante) -->
<div class="form-group">
<label for="example_status_id" class="control-label col-md-4">Type d'évenement<span class="red">*</span> :</label>
<div class="col-md-8">
<select id="example_status_id" name="example_status_id" class="form-control">
{section name=lst loop=$exampleStatus}
<option value="{$exampleStatus[lst].example_status_id}" {if $exampleStatus[lst].example_status_id == $data.example_status_id}selected{/if}>
{$exampleStatus[lst].example_status_name}
</option>
{/section}
</select>
</div>
</div>

<div class="form-group">
<label for="example_comment" class="control-label col-md-4">Commentaire :</label>
<div class="col-md-8">
<textarea id="example_comment" name="example_comment"  class="form-control" rows="3">{$data.event_comment}</textarea>
</div>
</div>

<div class="form-group center">
      <button type="submit" class="btn btn-primary button-valid">{$LANG["message"].19}</button>
      {if $data.example_id > 0 }
      <button class="btn btn-danger button-delete">{$LANG["message"].20}</button>
      {/if}
 </div>
</form>
</fieldset>
</div>

<span class="red">*</span><span class="messagebas">{$LANG["message"].36}</span>