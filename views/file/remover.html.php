<?php
	$this->title('Remove recursive');
?>

<?=$this->html->link('Cancel', $this->_request->env('HTTP_REFERER')); ?>
<p>Something got wrong...</p>
<?=$this->html->link('Get back', array('File::browse', 'args' => $parrent)); ?>