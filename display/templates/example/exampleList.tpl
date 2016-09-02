<h2>Liste EXEMPLE</h2>
{include file="example/exampleSearch.tpl"}
{if $isSearch == 1}
{if $droits["gestion"] == 1}
<a href="index.php?module=exampleChange&example_id=0">
New...
</a>
{/if}

<table id="exampleList" class="table table-bordered table-hover datatable " >
<thead>
<tr>
<th>Date</th>
<th>Comments</th>
<th>status</th>
</tr>
</thead><tbody>
{section name=lst loop=$data}
<tr>
<td>
{if $droits["gestion"] == 1}
<a href="index.php?module=exampleChange&example_id={$data[lst].example_id}">
{$data[lst].example_date}
</a>
{else}
{$data[lst].example_date}
{/if}
</td>
<td>{$data[lst].comment}</td>
<td><span class="textareaDisplay">{$data[lst].example_comment}</span></td>
</tr>
{/section}
</tbody>
</table>
{/if}
