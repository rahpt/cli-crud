<?php

namespace rahpt\Commands\Controllers;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use rahpt\Commands\CliCrud;

/**
 * Description of CrudController
 *
 * @author jose.proenca
 */
class CrudController extends BaseCommand
{

	protected $group		 = 'Crud';
	protected $name			 = 'crud:controller';
	protected $description	 = 'Create a new template controller.';
	protected $usage		 = 'crud:controller <controller_name> [Options]';
	protected $arguments	 = [
		'controller_name' => 'The controller name.',
	];
	protected $options		 = [
		// '-nobase' => 'Do not extends BaseControllers Class.',
		// '-usemodel' => 'Choose models.',
		// '-model' => 'Create a new model for use with the  new controller.',
		// '-space' => 'Create folders and files according to the path you typed.',
		// '-rest' => 'Generate files related to Resource Routes',
		// '-rest -p' => 'Generate files related to Presenter Routes, then use the "-rest -p" options.',
		// '-rest -d' => 'The names of controller and router are different.',
		// '-rest -o' => 'Select options to create the function what you want. ',
		// '-rest -w' => "Generate update and delete methods that work with HTML forms"
	];
	private $controllerName;
	private $routerName;
	private $appPath;
	private $templatePath;
	private $defNames		 = false;

	public function run(array $params = [])
	{
		$userNameInput			 = CliCreate::getName($params, "controller");
		$this->controllerName	 = ucfirst($userNameInput);
		$this->appPath			 = APPPATH;
		$this->templatePath		 = CliCrud::getPath(dirname(__FILE__), "template");
		$option					 = $this->getOption();
		if ($this->defNames) {
			$this->routerName = Cru::getName($params, "router");
		} else {
			$this->routerName = $userNameInput;
		}
		$this->extraOption(CliCrud::getOptions());
		if ($option == "rest") {
			$this->writeRest();
		} else if ($option == "restP") {
			//$this->writeRestP();
		} else {
			$this->writeBasic();
		}

		return;
	}

	private function writeRest()
	{
		return;
	}

	private function writeBasic()
	{
		return;
	}

}
