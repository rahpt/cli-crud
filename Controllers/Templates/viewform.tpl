<h2><?= isset($id) ? "Edit" : "Create" ?></h2>
<?= \Config\Services::validation()->listErrors(); ?>
<form action="/{tableName}/save" method="post">
	<input type="hidden" name="id" value="<?= isset($id) ? $id : set_value('id') ?>">
{viewInputs}
	<input type="submit">
</form>
