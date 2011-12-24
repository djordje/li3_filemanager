<?php
	$this->title('Upload file');
?>

<?=$this->html->link('Cancel', $this->_request->env('HTTP_REFERER')); ?>
<?php if ($error): ?>
<div id="error">
	<p>Something got wrong, file not uploaded correctly!</p>
</div>
<?php endif; ?>

<?=$this->uploadform->generate(); ?>