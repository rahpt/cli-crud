<?php

namespace {nameSpace}\Controllers;

use App\Controllers\BaseController;
use {nameSpace}\Models\{className}Model;
use Config\Pager;

class {className} extends BaseController
{

	private $model;
	
	public function __construct()
	{
		helper('form');	
		$this->model = new {className}Model();
	}

	private function renderPage($views, $data = [])
	{
		if (!is_array($views)) {
			$views = array($views);
		}

		// echo view('template/header');
		foreach ($views as $view) {
			echo view($view, $data);
		}
		// echo view('template/tail');
	}

	public function index()
	{
		
		$data = [
			'page_title' => 'List',
			'rows' => $this->model->paginate(10),
			'pager' => $this->model->pager,
		];
		
		$this->renderPage('{tableName}/index', $data);
	}

	public function save()
	{
		helper('form');

{fieldRules}
		$data = [
			'page_title' => 'Edit',
		];

		if ($this->validate($rules)) {
			$this->model->save([
{fieldSave}
			]);
			
			$data['rows']  = $this->model->paginate(10);
			$data['pager'] = $this->model->pager;
			echo $this->renderPage('{tableName}/index', $data);
			
		} else {
			echo $this->renderPage('{tableName}/form', $data);
		}
	}

	public function create()
	{
		$data = [
			'page_title' => 'Create'
		];

		echo $this->renderPage('{tableName}/form', $data);
	}

	public function delete($id)
	{
		$this->model->delete($id);

		$data = [
			'page_title' => 'Create',
			'message' => 'Item excluido com sucesso'
		];

		echo $this->renderPage('{tableName}/success', $data);
	}

	public function view($id = null)
	{
		$row	 = $this->model->find($id);

		if (empty($row)) {
			throw new PageNotFoundException('Não encontrado !!!');
		}

		$data = [
			'page_title' => 'View '. $id,
			'row' => $row,
		];

		echo $this->renderPage('{tableName}/view', $data);
	}

	public function edit($id = null)
	{
		$row	 = $this->model->find($id);

		if (empty($row)) {
			throw new PageNotFoundException('Não encontrado !!!');
		}

		$data = [
			'page_title' => 'Edit '. $id,
{fieldEdit}
		];

		echo $this->renderPage('{tableName}/form', $data);
	}
}
