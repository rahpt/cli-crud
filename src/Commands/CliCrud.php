<?php

namespace rahpt\Commands;

use CodeIgniter\CLI\CLI;
use CodeIgniter\CLI\BaseCommand;

/**
 * CliCrud
 *
 * Cli-Crud is an extension of CodeIgniter4 spark CLI. It will help you generate
 * template files more quickly when developing projects with CodeIgniter4.
 *
 * @author jose.proenca
 */
class CliCrud extends BaseCommand
{

	protected $group		 = 'crud';
	protected $name			 = 'crud:info';
	protected $description	 = 'Displays basic application information.';

	public function run(array $params)
	{
		
	}

}
