<?php

namespace li3_filemanager\controllers;

use li3_filemanager\extensions\data\Filesystem;

class FileController extends \lithium\action\Controller {
	
	/**
	 * Define application view paths
	 */
	protected function _init() {
		$this->_render['paths']['template'] = '{:library}/views/{:controller}/{:template}.{:type}.php';
		$this->_render['paths']['layout'] = LITHIUM_APP_PATH . '/views/layouts/default.html.php';
		$this->_render['paths']['element'] = '{:library}/views/elements/{:template}.html.php';
		parent::_init();
	}

	/**
	 * ! Currently redirect to browse
	 */
	public function index() {
		return $this->redirect('File::browse');
	}
	
	/**
	 * Take args from url and pass it to Filesystem::ls()
	 */
	public function browse() {
		$path = func_get_args();
		if (empty ($path)) {
			$parrent = array();
			$path = '*';
		} else {
			$parrent = $path;
			$path = join('/', $path).'/*';
			$createParrent = function($path) {
				$end = count($path) - 1;
				unset ($path[$end]);
				return $path;
			};
			$parrent = $createParrent($parrent);
		}
		$ls = Filesystem::ls($path);
		$empty = FALSE;
		if ($ls === FALSE) {
			$empty = TRUE;
		}
		return compact('ls', 'empty', 'parrent');
		
	}
	
	/**
	 * Create dir in location that match current url args
	 */
	public function mkdir() {
		if ($this->request->data) {
			$path = NULL;
			if (func_get_args()) {
				$path .= join('/', func_get_args());
				$path .= '/';
			}
			$path .= $this->request->data['name'];
			if (Filesystem::mkdir($path)) {
				return $this->redirect(array('File::browse', 'args' => $this->request->params['args']));
			}
		}
	}
	
	/**
	 * Copy file or dir that match current url args to posted location
	 */
	public function copy() {
		if (func_get_args()) {
			$path = join('/', func_get_args());
			if ($this->request->data) {
				if (Filesystem::cp($path, $this->request->data['dst'])) {
					return $this->redirect('File::browse');
				}
			}
			return compact('path');
		} else {
			return $this->redirect('File::browse');
		}
	}
	
	/**
	 * Rename file or dir that match current url args to posted name
	 */
	public function rename() {
		if (func_get_args()) {
			$argf = func_get_args();
			$path = join('/', $argf);
			$name = end($argf);
			$createTo = function($to, $arg) {
				$end = count($arg) - 1;
				unset ($arg[$end]);
				if (empty ($arg)) {
					$new = $to;
				} else {
					$new = '/'.$to;
				}
				return join('/', $arg).$new;
			};
			if ($this->request->data) {
				if (Filesystem::mv($path, $createTo($this->request->data['to'], $argf))) {
					return $this->redirect('File::browse');
				}
			}
			return compact('name');
		} else {
			return $this->redirect('File::browse');
		}
	}
	
	/**
	 * Move file or dir from location that match url args to posted location
	 */
	public function move() {
		if (func_get_args()) {
			$path = join('/', func_get_args());
			if ($this->request->data) {
				if (Filesystem::mv($path, $this->request->data['to'])) {
					return $this->redirect('File::browse');
				}
			}
			return compact('path');
		} else {
			return $this->redirect('File::browse');
		}
	}

	/**
	 * Delete file or dir (if empty) that match current url args
	 */
	public function remove() {
		if (func_get_args()) {
			$path = join('/', func_get_args());
			$createParrent = function($path) {
				$end = count($path) - 1;
				unset ($path[$end]);
				return $path;
			};
			$parrent = $createParrent($this->request->params['args']);
			if (Filesystem::rm($path)) {
				return $this->redirect(array('File::browse', 'args' => $parrent));
			} else {
				return compact('parrent');
			}
		} else {
			return $this->redirect('File::browse');
		}
	}
	
	/**
	 * Delete dir (if not empty) that match current url args
	 * Use carefully because this will delte everything inside that dir
	 */
	public function remover() {
		if (func_get_args()) {
			$path = join('/', func_get_args());
			$createParrent = function($path) {
				$end = count($path) - 1;
				unset ($path[$end]);
				return $path;
			};
			$parrent = $createParrent($this->request->params['args']);
			if (Filesystem::rm($path, TRUE)) {
				return $this->redirect(array('File::browse', 'args' => $parrent));
			} else {
				return compact('parrent');
			}
		} else {
			return $this->redirect('File::browse');
		}
	}

	/**
	 * Upload posted file or files to location that match current url args
	 */
	public function upload() {
		$error = FALSE;
		if ($this->request->data) {
			$path = func_get_args();
			if (empty ($path)) {
				$path = '.';
			} else {
				$path = join('/', $path);
			}
			if (Filesystem::upload($this->request->data['files'], $path)) {
				return $this->redirect(array('File::browse', 'args' => $this->request->params['args']));
			} else {
				$error = TRUE;
			}
		}
		return compact('error');
	}
	
}

?>