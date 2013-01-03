<?php
	$this->title('File manager by Djordje Kovacevic');
	$this->scripts($this->html->script(array(
		'/fm/js/mustache.js',
		'/fm/js/li3_filemanager.min.js'
	)));
	
	$url = array(
		'lib' => $this->url(
			array('library' => 'li3_filemanager', 'controller' => 'Files', 'action' => 'index'),
			array('absolute' => true)
		),
		'current' => $this->url($this->_request->params, array('absolute' => true))
	);
?>

<noscript>
	<div class="well well-small">
		<p><i class="icon-info-sign"></i> <strong>You must enable javascript to use this app!</strong></p>
	</div>
</noscript>

<!-- Security token for protection against CSFR -->
<?=$this->security->requestToken(); ?>

<!-- Alerts placeholder -->
<div id="alerts"></div>

<!-- Buttons placeholder -->
<div class="row">
	<div class="pull-right btn-toolbar">
		<p id="loading" class="btn btn-small btn-primary hide"><i class="icon-download-alt icon-white"></i> Loading...</p>
		
		<button type="button" class="btn btn-small" data-action="refresh"><i class="icon-refresh"></i> Refresh</button>
		
		<div class="btn-group" data-toggle="buttons-checkbox">
			<button type="button" class="btn btn-small btn-info" data-toggle="collapse" data-target="#upload-files-wrapper"><i class="icon-upload icon-white"></i> Upload files</button>
			<button type="button" class="btn btn-small" data-toggle="collapse" data-target="#create-folder-wrapper"> Make new folder</button>
		</div>
		
		<button type="button" id="show-permalink" class="btn btn-small"><i class="icon-magnet"></i> Show permalink</button>
		
		<button type="button" class="btn btn-small" data-toggle="modal" data-target="#clipboard-modal"><i class="icon-list-alt"></i> Show clipboard</button>
		
		<div class="btn-group">
			<button type="button" id="paste" class="btn btn-small disabled">Paste</button>
			<button type="button" id="m-copy" class="btn btn-small disabled">Copy</button>
			<button type="button" id="m-move" class="btn btn-small disabled">Cut</button>
			<button type="button" id="m-remove" class="btn btn-small btn-danger disabled">Delete</button>
		</div>
	</div>
</div>

<!-- Upload form collapse -->
<div id="upload-files-wrapper" class="collapse">
	<div id="upload-proggres" class="progress progress-info hide">
		<div class="bar" style="width: 0%;"></div>
	</div>
	<form id="upload-files" class="form-inline well" enctype="multipart/form-data" method="POST">
		<input type="file" class="span8" name="files[]" id="files-input" multiple />
		<input type="submit" value="Upload" class="btn btn-primary pull-right" data-action="upload" />
	</form>
</div>

<!-- Directory creation form collapse -->
<div id="create-folder-wrapper" class="collapse">
	<form id="create-folder" class="form-inline well">
		<label for="new-dir-name">Folder name:</label>
		<input type="text" class="input-xlarge" name="new_dir_name" id="new-dir-name" />
		<input type="submit" value="Create" class="btn btn-primary pull-right" data-action="mkdir" />
	</form>
</div>

<!-- Breadcrumb placeholder -->
<ul class="breadcrumb"></ul>

<!-- Files/directories list placeholder -->
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th class="span1">Select</th>
			<th class="span3">Name</th>
			<th class="span2">Size</th>
			<th class="span1">Permissions</th>
			<th class="span3">Controls</th>
		</tr>
	</thead>
	<tbody id="files">
	</tbody>
</table>

<!-- Clipboard modal -->
<div class="modal hide" id="clipboard-modal" tabindex="-1" role="dialog" aria-labelledby="Clipboard" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3>Clipboard</h3>
	</div>
	<div class="modal-body">
		<div id="clipboard"></div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-small btn-danger" data-action="clear-clipboard">Clear clipboard</button>
		<button type="button" class="btn btn-small" data-dismiss="modal" aria-hidden="true">Close</button>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		FileManager.init(
			<?php echo json_encode($url); ?>,
			<?php echo json_encode(compact('data', 'breadcrumb')); ?>,
			$('[name="security[token]"]').val()
		);
	});
</script>