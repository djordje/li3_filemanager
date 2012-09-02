<?php

/**
 * @copyright Copyright 2012, Djordje Kovacevic (http://djordjekovacevic.com)
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_filemanager\controllers;

use lithium\net\http\Router;
use lithium\security\validation\RequestToken;
use li3_filemanager\extensions\storage\Location;

/**
 * This is example controller, you can use it for your file manager, or change it in your app.
 * This controller handle everything via AJAX expext initial render.
 */
class FilesController extends \lithium\action\Controller {
	
	private $_link = array(
		'library' => 'li3_filemanager', 'controller' => 'Files', 'action' => 'index'
	);
	
	/**
	 * Setup controller to use application layout, and plugin view templates
	 * Setup response headers for all actions
	 */
	protected function _init() {
		$this->_render['paths'] = array(
			'template' => '{:library}/views/{:controller}/{:template}.{:type}.php',
			'layout'   => LITHIUM_APP_PATH . '/views/layouts/default.html.php',
			'element'  => '{:library}/views/elements/{:template}.html.php'
		);
		parent::_init();
		$this->response->cache(false);
	}

	/**
	 * Fetch data for current path
	 * On first access return HTML
	 * Next time you fetch via AJAX return just JSON that we render client side
	 * 
	 * html:method GET
	 */
	public function index() {
		$path = $this->request->args? join('/', $this->request->args) : null;
		$data = Location::ls($path);
		
		if ($data === false) {
			return $this->redirect($this->_link);
		}
		
		$breadcrumb = array(
			array('Index', 'url' => Router::match(
				$this->_link, $this->request, array('absolute' => true)
			))
		);
		$args = array();

		foreach ($this->request->args as $arg) {
			$args[] = $arg;
			$this->_link += array('args' => $args);
			$breadcrumb[] = array($arg, 'url' => Router::match(
				$this->_link, $this->request, array('absolute' => true)
			));
		}
		$breadcrumb[count($breadcrumb) - 1]['url'] = null;
		
		if ($this->request->is('ajax')) {
			return $this->render(array('json' => compact('data', 'breadcrumb')));
		}
		
		return compact('data', 'breadcrumb');
	}

	/**
	 * Make new directory at current location
	 * 
	 * html:method POST
	 */
	public function mkdir() {
		if (!RequestToken::check($this->request->data['token'])) {
			return $this->render(array('json' => array('regenerate' => true)));
		}
		
		if ($this->request->args) {
			$name = join('/', $this->request->args) . "/{$this->request->data['new_dir_name']}";
		} else {
			$name = $this->request->data['new_dir_name'];
		}
		
		$mkdir = Location::mkdir($name);
		$error = null;
		if (!$mkdir) {
			$error = "Directory <strong>{$name}</strong> not created!";
		}
		return $this->render(array('json' => array('success' => $mkdir, 'error' => $error)));
	}
	
	/**
	 * Copy files/directories from one to another location
	 * 
	 * html:method POST
	 */
	public function copy() {
		if ($this->request->is('ajax') && $this->request->data) {
			if (!RequestToken::check($this->request->data['token'])) {
				return $this->render(array('json' => array('regenerate' => true)));
			}
			$success = true;
			$errors = array();
			$args = $this->request->args;
			
			foreach ($this->request->data['from'] as $from) {
				$to = $args;
				$to[] = end(explode('/', $from));
				$to = join('/', $to);
				$copy = Location::copy($from, $to);
				if (!$copy) {
					$errors[] = array('error' => "File/dir not copied from: <strong>{$from}</strong> to: <strong>{$to}</strong>");
				}
				if (!$copy && $success) {
					$success = false;
				}
			}
			
			return $this->render(array('json' => compact('success', 'errors')));
		}
		return $this->redirect($this->_link);
	}
	
	/**
	 * Move files/directories from one to another location
	 * 
	 * html:method POST
	 */
	public function move() {
		if ($this->request->is('ajax') && $this->request->data) {
			if (!RequestToken::check($this->request->data['token'])) {
				return $this->render(array('json' => array('regenerate' => true)));
			}
			$success = true;
			$errors = array();
			$args = $this->request->args;

			foreach ($this->request->data['from'] as $from) {
				$to = $args;
				$to[] = end(explode('/', $from));
				$to = join('/', $to);
				$copy = Location::move($from, $to);
				if (!$copy) {
					$errors[] = array('error' => "File/dir not moved from: <strong>{$from}</strong> to: <strong>{$to}</strong>");
				}
				if (!$copy && $success) {
					$success = false;
				}
			}

			return $this->render(array('json' => compact('success', 'errors')));
		}
		return $this->redirect($this->_link);
	}
	
	/**
	 * Upload files to current location
	 * 
	 * html:method POST
	 */
	public function upload() {
		if ($this->request->data) {
			if (!RequestToken::check($this->request->data['token'])) {
				return $this->render(array('json' => array('regenerate' => true)));
			}
			$success = true;
			$errors = array();
			$dest = join('/', $this->request->args);
			
			foreach ($this->request->data['files'] as $file) {
				$upload = Location::upload($file, $dest);
				if (!$upload) {
					$errors[] = array('error' => "{$file['name']} not upload");
				}
				if (!$upload && $success) {
					$success = false;
				}
			}
			
			if ($this->request->is('ajax')) {
				return $this->render(array('json' => compact('success', 'errors')));
			}
		}
		
		return $this->redirect($this->_link + array('args' => $this->request->args));
	}

	/**
	 * Remove directory or file
	 * 
	 * html:method POST
	 */
	public function remove() {
		if ($this->request->is('ajax') && $this->request->data) {
			if (!RequestToken::check($this->request->data['token'])) {
				return $this->render(array('json' => array('regenerate' => true)));
			}
			$success = true;
			$errors = array();

			foreach ($this->request->data['selected'] as $path) {
				$remove = Location::remove($path);
				if (!$remove) {
					$errors[] = array('error' => "<strong>{$path}</strong> not removed");
				}
				if (!$copy && $success) {
					$success = false;
				}
			}

			return $this->render(array('json' => compact('success', 'errors')));
		}
		return $this->redirect($this->_link);
	}
	
}

?>