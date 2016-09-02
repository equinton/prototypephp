<div class="row">
<form class="form-horizontal protoform" id="sample_search" action="index.php" method="GET">
<input id="moduleBase" type="hidden" name="moduleBase" value="example">
<input id="action" type="hidden" name="action" value="List">
<input id="isSearch" type="hidden" name="isSearch" value="1">
<div class="form-group">
<label for="example_id" class="col-md-2 control-label">identifiant :</label>
<div class="col-md-4">
<input id="name" type="text" class="form-control" name="example_id" value="{$sampleSearch.example_id}">
</div>
<label for="status_id" class="col-md-2 control-label">Statut :</label>
<div class="col-md-4">
<select id="example_status_id" name="example_status_id" class="form-control">
<option value="" {if $sampleSearch.example_status_id == ""}selected{/if}>Sélectionnez...</option>
{section name=lst loop=$status}
<option value="{$status[lst].example_status_id}" {if $status[lst].example_status_id == $sampleSearch.example_status_id}selected{/if}>
{$status[lst].example_status_name}
</option>
{/section}
</select>
</div>
</div>
<div class="form-group">
<div class="col-md-12">
<input type="submit" class="btn btn-success" value="{$LANG['message'][21]}">
</div>
</div>
</form>
</div>

