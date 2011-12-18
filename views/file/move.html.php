<?php
	$this->title('Move file or directory');
?>

<?=$this->html->link('Cancel', $this->_request->env('HTTP_REFERER')); ?>
<p><strong>Move file from:</strong> <?=$path; ?></p>
<?=$this->form->create(); ?>
<?=$this->form->field('to'); ?>
<?=$this->form->submit('Move'); ?>
<?=$this->form->end(); ?>