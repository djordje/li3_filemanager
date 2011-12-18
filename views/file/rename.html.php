<?php
	$this->title('Rename file or directory');
?>

<?=$this->html->link('Cancel', $this->_request->env('HTTP_REFERER')); ?>
<p><strong>Old name: </strong><?=$name; ?></p>
<?=$this->form->create(); ?>
<?=$this->form->field('to', array('label' => 'New name', 'value' => $name)); ?>
<?=$this->form->submit('Rename'); ?>
<?=$this->form->end(); ?>