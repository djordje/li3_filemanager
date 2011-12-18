<?php
	$this->title('Remove');
?>

<?=$this->html->link('Cancel', $this->_request->env('HTTP_REFERER')); ?>
<p>Something got wrong...</p>
<p>If you trying to remove directory that is not empty use "recursve delete"</p>
<?=$this->html->link('Get back', array('File::browse', 'args' => $parrent)); ?>