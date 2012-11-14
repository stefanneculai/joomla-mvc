<?php

defined('_JEXEC') or die;

class BooksController extends TadaController
{
	public function index()
	{
		$this->theme = 'mt';
		$this->ceva = 'test';
	}

	public function create()
	{
		// $this->theme = 'mt';
	}

	public function update()
	{
		// $this->theme = 'mt';
	}

	public function admin_index()
	{

	}
}