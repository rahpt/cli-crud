		<div class="form-group">
			<label for="title">{fieldName}</label>
			<input type="text" class="form-control" name="{fieldName}" id="{fieldName}" value='<?= isset(${fieldName}) ? ${fieldName} : set_value('{fieldName}') ?>'>
		</div>
