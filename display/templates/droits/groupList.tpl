<h2>Liste des groupes de logins</h2>
	<div class="row">
	<div class="col-md-6">
<a href="index.php?module=groupChange&aclgroup_id=0">
Nouveau groupe racine...
</a>

<table id="groupListe" class="table table-bordered table-hover datatable" 
		data-order='[[ 1, "asc" ]]' data-page-length='25'>
<thead>
<tr>
<th>Nom du groupe</th>
<th>Nombre de<br>logins déclarés</th>
<th>Rajouter un<br>groupe fils</th>
</tr>
</thead>
<tbody>
{section name=lst loop=$data}
<tr>
<td>
<a href="index.php?module=groupChange&aclgroup_id={$data[lst].aclgroup_id}&aclgroup_id_parent={$data[lst].aclgroup_id_parent}">
{for $boucle = 1 to $data[lst].level}
&nbsp;&nbsp;&nbsp;
{/for}
{$data[lst].groupe}
</a>
</td>
<td class="center">{$data[lst].nblogin}</td>
<td class="center">
<a href="index.php?module=groupChange&aclgroup_id=0&aclgroup_id_parent={$data[lst].aclgroup_id}">
<img src="display/images/droits/list-add.png" height="20">
</a>
</tr>
{/section}
</tbody>
</table>
</div>
</div>