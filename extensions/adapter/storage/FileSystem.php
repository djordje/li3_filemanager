<?php

/**
 * @copyright Copyright 2012, Djordje Kovacevic (http://djordjekovacevic.com)
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_filemanager\extensions\adapter\storage;

use DirectoryIterator;

class FileSystem extends \lithium\core\Object {
	
	/**
	 * List directory conntent
	 * @param string $location
	 * @return boolean
	 */
	public function ls($location = null) {
		$location = "{$this->_config['location']}/{$location}";
		
		if (is_dir($location)) {
			$directory = new DirectoryIterator($location);
		} else {
			return false;
		}
		
		$scan = array();
		
		foreach ($directory as $d) {
			if ($d->isDot()) {
				continue;
			}
			
			$url = rtrim($this->_config['url'], '/');
			$path = substr($d->getPath(), strlen($this->_config['location']));
			$path = ($path) ?: '/';
			
			if ($url) {
				$prefix = ($path === '/') ? null : $path;
				$url .= "{$prefix}/{$d->getFilename()}";
			}
			
			$scan[] = array(
				'name' => $d->getFilename(),
				'dir'  => $d->isDir(),
				'url'  => $url,
				'path' => $path,
				'size' => ($d->isDir())? null : $d->getSize(),
				'mode' => substr(sprintf('%o', $d->getPerms()), -4)
			);
		}
		
		return $scan;
	}
	
	/**
	 * Create directory
	 * @param string $name
	 * @param array $options
	 * @return boolean
	 */
	public function mkdir($name, array $options = array()) {
		$defauts = array('mode' => 0777, 'recursive' => true);
		$options += $defauts;
		
		$name = "{$this->_config['location']}/{$name}";
		
		if (!is_dir($name)) {
			return mkdir($name, $options['mode'], $options['recursive']);
		}
		return false;
	}
	
	/**
	 * Upload file to destination
	 * @param array $file
	 * @param string $dest
	 * @param array $options
	 * @return boolean
	 */
	public function upload(array $file, $dest, array $options = array()) {
		$defaults = array('overwrite' => false);
		$options += $defaults;
		
		$dest = "{$this->_config['location']}/{$dest}";
		
		if ($file['error'] === UPLOAD_ERR_OK && is_dir($dest)) {
			$name = "{$dest}/{$file['name']}";
			if (!$options['overwrite'] && file_exists($name)) {
				return false;
			}
			move_uploaded_file($file['tmp_name'], $name);
			return true;
		}
		return false;
	}
	
	/**
	 * Copy file or directory
	 * @param string $source
	 * @param string $dest
	 * @param array $options
	 * @return boolean
	 */
	public function copy($source, $dest, array $options = array()) {
		$defaults = array('prepandLocation' => true);
		$options += $defaults;
		
		if ($options['prepandLocation']) {
			$source = "{$this->_config['location']}/{$source}";
			$dest = "{$this->_config['location']}/{$dest}";
		}
		
		if (file_exists($source) &&!file_exists($dest)) {
			if (is_dir($source)) {
				if (mkdir($dest, 0777)) {
					foreach (new DirectoryIterator($source) as $s) {
						$this->copy(
							"{$source}/{$s->getFilename()}",
							"{$dest}/{$s->getFilename()}",
							array('prepandLocation' => false)
						);
					}
					return true;
				}
				return false;
			}
			return copy($source, $dest);
		}
		return false;
	}
	
	/**
	 * Move file or directory to another place
	 * @param string $oldname
	 * @param string $newname
	 * @return boolean
	 */
	public function move($oldname, $newname) {
		$oldname = "{$this->_config['location']}/{$oldname}";
		$newname = "{$this->_config['location']}/{$newname}";
		
		if (file_exists($oldname) && !file_exists($newname)) {
			return rename($oldname, $newname);
		}
		return false;
	}
	
	/**
	 * Remove file or directory
	 * @param string $name
	 * @param array $options
	 * @return boolean
	 */
	public function remove($name, array $options = array()) {
		$defaults = array('recursive' => true, 'prepandLocation' => true);
		$options += $defaults;
		
		if ($options['prepandLocation']) {
			$name = "{$this->_config['location']}/{$name}";
		}
		
		if (!file_exists($name)) {
			return false;
		}
		
		if (is_dir($name)) {
			if ($options['recursive']) {
				foreach (new DirectoryIterator($name) as $d) {
					if ($d->isDot()) {
						continue;
					}
					$this->remove("{$name}/{$d->getFilename()}", array(
						'prepandLocation' => false
					));
				}
			}
			return rmdir($name);
		}
		return unlink($name);
	}
	
}

?>