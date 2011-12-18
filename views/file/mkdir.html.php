<?php
	$this->title('Make directory');
?>

<?=$this->html->link('Cancel', $this->_request->env('HTTP_REFERER')); ?>
<?=$this->form->create(); ?>
<?=$this->form->field('name'); ?>
<?=$this->form->submit('Create'); ?>
<?=$this->form->end(); ?>