<?php
	$this->title('Copy');
?>

<?=$this->html->link('Cancel', $this->_request->env('HTTP_REFERER')); ?>
<p><strong>Source:</strong> <?=$path; ?></p>
<?=$this->form->create(); ?>
<?=$this->form->field('dst', array('label' => 'Destination')); ?>
<?=$this->form->submit('Copy'); ?>
<?=$this->form->end(); ?>