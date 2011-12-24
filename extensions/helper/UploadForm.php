<?php

namespace li3_filemanager\extensions\helper;

class UploadForm extends \lithium\template\helper\Form {
	
	/**
	 * Generate upload form
	 * @param $submitButtonValue string - value that would be shown on form submit button
	 */
	public function generate($submitButtonValue = 'Upload') {
		return $this->create(NULL, array('type' => 'file'))
			  .$this->field('files[]', array('type' => 'file', 'multiple' => 'true'))
			  .$this->submit($submitButtonValue)
			  .$this->end();
	}
	
}

?>