<?php
	$this->title('Index');
?>

<div id="breadcrumbs" style="font-weight: bold;">
	<?=$this->html->link('Index', 'File::browse'); ?>
	<?php
		$args = $this->_request->params['args'];
		foreach ($args as $k => $v) {
			$path = array();
			for ($i = 0; $i <= $k; $i++) {
				$path[] = $args[$i];
			}
			?>
				 / <?=$this->html->link($v, array('File::browse', 'args' => $path)); ?>
			<?php
		}
	?>
</div>

<?=$this->html->link('Make new directory here', array('File::mkdir', 'args' => ($this->_request->params['args']))); ?> | 
<?=$this->html->link('Upload file here', array('File::upload', 'args' => ($this->_request->params['args']))); ?>

<?php if(!empty ($ls)): ?>
<table>
	<tr>
		<th>Name</th>
		<th>Path</th>
		<th>Mode</th>
		<th>Size</th>
		<th>Controlls</th>
	</tr>
	<?php	foreach ($ls['dirs'] as $dir): ?>
	<tr>
		<td><strong><?=$this->html->link('[ '.$dir['name'].' ]', array('File::browse', 'args' => ($dir['path']))); ?></strong></td>
		<td><?=$dir['path']; ?></td>
		<td><?=$dir['mode']; ?></td>
		<td>[ dir ]</td>
		<td>
			<?=$this->html->link('Delete if empty', array('File::remove', 'args' => ($dir['path']))); ?> | 
			<?=$this->html->link('Delete recursive', array('File::remover', 'args' => ($dir['path']))); ?> |
			<?=$this->html->link('Copy', array('File::copy', 'args' => ($dir['path']))); ?> | 
			<?=$this->html->link('Rename', array('File::rename', 'args' => ($dir['path']))); ?> | 
			<?=$this->html->link('Move', array('File::move', 'args' => ($dir['path']))); ?>
		</td>
	</tr>
	<?php	endforeach; ?>
	<?php	foreach ($ls['files'] as $file): ?>
	<tr>
		<td><strong><?=$file['name']; ?></strong></td>
		<td><?=$file['path']; ?></td>
		<td><?=$file['mode']; ?></td>
		<td><?=$file['size']; ?> bytes</td>
		<td>
			<?=$this->html->link('Delete', array('File::remove', 'args' => ($file['path']))); ?> | 
			<?=$this->html->link('Copy', array('File::copy', 'args' => ($file['path']))); ?> | 
			<?=$this->html->link('Rename', array('File::rename', 'args' => ($file['path']))); ?> | 
			<?=$this->html->link('Move', array('File::move', 'args' => ($file['path']))); ?>
		</td>
	</tr>
	<?php	endforeach; ?>
	
</table>
<?php endif; ?>
<?php if ($empty): ?>
<h4>Directory does not exists <?=$this->html->link('go back to browser index', 'File::browse'); ?></h4>
<?php endif; ?>
