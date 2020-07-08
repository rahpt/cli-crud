<script>
	function confirmDelete() {
		if (confirm("Deseja excluir?")) {
			return true;
		}
		return false;
	}
</script>
<h4>{className}</h4>
<div class="row my-3">
	<a href="/{tableName}/create" class="btn btn-primary">Novo</a>
</div>
<table class="table">
	<thead>
		<tr>
{tableHeadFields}
			<th>Ações</th>
		</tr>
	</thead>
	<?php if (!empty($rows) && is_array($rows)) : ?>
		<tbody>
			<?php foreach ($rows as $row): ?>
				<tr>
{tableRowFields}
					<td>
						<a href="/{tableName}/view/<?= $row['id'] ?>">view</a> -
						<a href="/{tableName}/edit/<?= $row['id'] ?>">edit</a> -
						<a href="/{tableName}/delete/<?= $row['id'] ?>" onclick="return confirmDelete()">apagar</a>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	<?php else : ?>
		<tr>
			<td>Sem registros a exibir</td>
		</tr>
	<?php endif; ?>
</table>
<div class="row">
	<?= $pager->links() ?>
</div>

