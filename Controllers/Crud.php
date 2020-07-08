<?php

namespace App\Commands\Crud;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Services;

class Crud extends BaseCommand
{

	/** BaseCommand ovewriten properties */
	protected $group		 = 'crud';
	protected $name			 = 'crud';
	protected $description	 = 'Creates all crud files from table.';
	protected $usage		 = 'crud [table_name] [Options]';
	protected $arguments	 = [
		'table_name' => 'The table name',
	];
	protected $options		 = [
		'-m' => 'Set module name',
		'-n' => 'Set namespace',
		'-all' => 'Create All Tables',
	];

	/** private properties */
	private $db;
	private $tables;
	private $nameSpace;
	private $homePath;
	private $fields;
	private $properties;
	private $excludeFields = ['id', 'deleted_at', 'created_at', 'updated_at'];

	public function __construct()
	{
		helper('inflector');
		helper('filesystem');
	}

	private function loadProperties($table)
	{
		$this->fields		 = $this->db->getFieldData($table);
		$this->properties	 = [
			'className' => pascalize($table),
			'tableName' => Strtolower($table),
			'nameSpace' => $this->nameSpace,
		];
		$this->loadFieldNames();
		$this->loadFieldSaves();
		$this->loadFieldRules();
		$this->loadViewInputs();
		$this->loadFieldView();
		$this->loadFieldEdit();
		$this->loadTableHeadFields();
		$this->loadTableRowFields();
	}

	private function loadFieldNames()
	{
		foreach ($this->fields as $field) {
			if (in_array($field->name, $this->excludeFields)) {
				continue;
			}
			$fields[] = $field->name;
		}
		$this->properties['fieldNames'] = sprintf("'%s'", implode("', '", $fields));
	}

	private function loadTableHeadFields()
	{
		$return = '';
		foreach ($this->fields as $field) {
			if (in_array($field->name, $this->excludeFields)) {
				continue;
			}
			$nl		 = ($return != '') ? PHP_EOL : '';
			$return	 .= $nl . sprintf("			<th>%s</th>", ucfirst($field->name));
		}
		$this->properties['tableHeadFields'] = $return;
	}

	private function loadFieldView()
	{
		$return = '			<h2>VIEW <?= $row["id"]?></h2>';
		$return .= '			<table>';
		foreach ($this->fields as $field) {
			if (in_array($field->name, $this->excludeFields)) {
				continue;
			}
			$nl		 = ($return != '') ? PHP_EOL : '';
			$return	 .= $nl . sprintf('			<tr><td><b>%s</b></td><td><?= $row["%s"]?></td></tr>', ucfirst($field->name), $field->name);
		}
		$return .= '			</table>';
		$this->properties['tableViewFields'] = $return;
	}

	private function loadTableRowFields()
	{
		$return = '';
		foreach ($this->fields as $field) {
			if (in_array($field->name, $this->excludeFields)) {
				continue;
			}
			$nl		 = ($return != '') ? PHP_EOL : '';
			$text	 = sprintf('<td><a href="/{tableName}/view/<?= $row[\'id\'] ?>"><?= $row[\'%s\'] ?></a></td>', $field->name);
			$return	 .= $nl . $this->translateProperties($text, false);
		}
		$this->properties['tableRowFields'] = $return;
	}

	function translateFields($template, $field)
	{
		$properties = [
			'fieldName' => strtolower($field->name),
		];

		$content = file_get_contents(__DIR__ . "/Templates/$template.tpl");
		foreach ($properties as $key => $value) {
			$content = str_replace('{' . $key . '}', $value, $content);
		}
		return $content;
	}

	private function loadViewInputs()
	{
		$return = '';
		foreach ($this->fields as $field) {
			if (in_array($field->name, $this->excludeFields)) {
				continue;
			}
			$return .= $this->translateFields("viewformvarchar", $field);
		}
		$this->properties['viewInputs'] = $return;
	}

	private function loadFieldSaves()
	{
		$return = '';
		foreach ($this->fields as $field) {
			if (in_array($field->name, $this->excludeFields)) {
				continue;
			}
			$return .= sprintf('				\'%s\' => $this->request->getVar(\'%s\'),', $field->name, $field->name) . PHP_EOL;
		}
		$this->properties['fieldSave'] = $return;
	}

	private function loadFieldEdit()
	{
		$return = '';
		foreach ($this->fields as $field) {
			$nl		 = ($return != '') ? PHP_EOL : '';
			$return	 .= $nl . sprintf('		"%s" => $row["%s"],', $field->name,$field->name);
		}
		$this->properties['fieldEdit'] = $return;
	}

	private function loadFieldRules()
	{
		$return = '';
		foreach ($this->fields as $field) {
			if (in_array($field->name, $this->excludeFields)) {
				continue;
			}
			$nl		 = ($return != '') ? PHP_EOL : '';
			$return	 .= $nl . sprintf('		$rules[\'%s\'] = \'required|min_length[0]|max_length[%s]\';', $field->name, $field->max_length);
		}
		$this->properties['fieldRules'] = $return;
	}

	private function mkdir($dirName, $mode = 0777)
	{
		if (!file_exists($dirName)) {
			mkdir($dirName, $mode, true);
		}
	}

	function translateProperties($template, $loadFromTemplate = true)
	{
		if ($loadFromTemplate) {
			$content = file_get_contents(__DIR__ . "/Templates/$template.tpl");
		} else {
			$content = $template;
		}

		foreach ($this->properties as $key => $value) {
			$content = str_replace('{' . $key . '}', $value, $content);
		}
		return $content;
	}

	private function createModel($table)
	{
		$mvcPath	 = '/Models/';
		$fileName	 = pascalize($table) . 'Model.php';
		$path		 = $this->homePath . $mvcPath;
		$pathFile	 = $path . $fileName;
		$content	 = $this->translateProperties('model', $table);

		$this->mkdir($path);
		if (!write_file($pathFile, $content)) {
			CLI::error(lang('Erro ao criar ' . $pathFile));
			return;
		}

		CLI::write('Created Model: ' . CLI::color($mvcPath . $fileName, 'green'));
	}

	private function createController($table)
	{
		$mvcPath	 = '/Controllers/';
		$fileName	 = pascalize($table) . '.php';
		$path		 = $this->homePath . $mvcPath;
		$pathFile	 = $path . $fileName;

		$this->mkdir($path);

		$content = $this->translateProperties('controller', $table);
		if (!write_file($pathFile, $content)) {
			CLI::error(lang('Erro ao criar ' . $pathFile));
			return;
		}
		CLI::write('Created Controller: ' . CLI::color($mvcPath . $fileName, 'green'));
	}

	private function createViewIndex($table)
	{
		$mvcPath	 = '/Views/' . strtolower($table) . '/';
		$fileName	 = 'index.php';
		$path		 = $this->homePath . $mvcPath;
		$pathFile	 = $path . $fileName;

		$this->mkdir($path);

		$contentIndex = $this->translateProperties('viewindex', $table);

		if (!write_file($pathFile, $contentIndex)) {
			CLI::error(lang('Erro ao criar ' . $pathFile));
			return;
		}
		CLI::write('Created View: ' . CLI::color($mvcPath . $fileName, 'green'));
	}

	private function createViewForm($table)
	{
		$mvcPath	 = '/Views/' . strtolower($table) . '/';
		$fileName	 = 'form.php';
		$path		 = $this->homePath . $mvcPath;
		$pathFile	 = $path . $fileName;

		$this->mkdir($path);

		$content = $this->translateProperties('viewform', $table);

		if (!write_file($pathFile, $content)) {
			CLI::error(lang('Erro ao criar ' . $pathFile));
			return;
		}
		CLI::write('Created View: ' . CLI::color($mvcPath . $fileName, 'green'));
	}

	private function createViewSuccess($table)
	{
		$mvcPath	 = '/Views/' . strtolower($table) . '/';
		$fileName	 = 'success.php';
		$path		 = $this->homePath . $mvcPath;
		$pathFile	 = $path . $fileName;

		$this->mkdir($path);

		$content = $this->translateProperties('viewsuccess', $table);

		if (!write_file($pathFile, $content)) {
			CLI::error(lang('Erro ao criar ' . $pathFile));
			return;
		}
		CLI::write('Created View: ' . CLI::color($mvcPath . $fileName, 'green'));
	}

	private function createView($table)
	{
		$mvcPath	 = '/Views/' . strtolower($table) . '/';
		$fileName	 = 'view.php';
		$path		 = $this->homePath . $mvcPath;
		$pathFile	 = $path . $fileName;

		$this->mkdir($path);

		$content = $this->translateProperties('view', $table);

		if (!write_file($pathFile, $content)) {
			CLI::error(lang('Erro ao criar ' . $pathFile));
			return;
		}
		CLI::write('Created View: ' . CLI::color($mvcPath . $fileName, 'green'));
	}

	private function create($tables)
	{
		if (!is_array($tables)) {
			$tables = [$tables];
		}

		foreach ($tables as $table) {
			$this->loadProperties($table);
			$this->createController($table);
			$this->createModel($table);
			$this->createView($table);
			$this->createViewIndex($table);
			$this->createViewForm($table);
			$this->createViewSuccess($table);
		}
	}

	public function run(array $params = [])
	{
		$tableName = array_shift($params);

		$this->db		 = db_connect();
		$this->tables	 = $this->db->listTables();

		if (empty($tableName)) {
			$i = 1;
			CLI::write(sprintf('[A]-%s', 'ALL') . PHP_EOL);
			foreach ($this->tables as $table) {
				CLI::write(sprintf('[%s]-%s', $i++, $table));
			}

			$tableIndex = CLI::prompt(lang('Nome da tabela'));
			if (Strtolower($tableIndex) == 'a') {
				$tableName = $this->tables;
			} else {
				$tableName = $this->tables[$tableIndex - 1];
			}
		}

		/* NameSpace */
		$this->module	 = $params['-m'] ?? CLI::getOption('m');
		$this->nameSpace = $params['-n'] ?? CLI::getOption('n');
		$this->homePath	 = APPPATH;

		if (!empty($this->module)) {
			$this->homePath	 = ROOTPATH . $this->module;
			$this->nameSpace = empty(!$this->nameSpace) ? $this->nameSpace : $this->module;
		} else {
			$this->nameSpace = 'App';
		}

		$this->create($tableName);
	}

}
