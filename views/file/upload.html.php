<?php
	$this->title('Upload file');
?>

<?=$this->html->link('Cancel', $this->_request->env('HTTP_REFERER')); ?>
<?php if ($error): ?>
<div id="error">
	<p>Something got wrong, file not uploaded correctly!</p>
</div>
<?php endif; ?>

<?=$this->form->create(NULL, array('type' => 'file')); ?>
<?=$this->form->field('files[]', array('type' => 'file', 'multiple' => 'true')); ?>
<?=$this->form->submit('Upload'); ?>
<?=$this->form->end(); ?>