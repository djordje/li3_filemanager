FileManager.attachEvents = function() {
	
	//!
	// jQuery selectors
	// --------------------------------------------------------------------------------------------
	var $body = $('body'),
		$dirName = $('#new-dir-name'),
		$files = $('#files-input'),
		$clipboard = $('#clipboard'),
		$loading = $('#loading'),
		
		$paste = $('#paste'),
		$mcopy = $('#m-copy'),
		$mmove = $('#m-move'),
		$mremove = $('#m-remove');
	
	
	//!
	// Button toolbar events
	// --------------------------------------------------------------------------------------------
	
	// Refresh
	$body.on('click.filemanager', '[data-action="refresh"]', function(e) {
		e.stopPropagation();
		e.preventDefault();
		FileManager.ls(FileManager.Url.current);
		$body.trigger('disableButtons.filemanager');
	});
	
	// Paste
	$body.on('click.filemanager', '#paste', function(e) {
		e.stopPropagation();
		e.preventDefault();
		FileManager[FileManager.Queue.lastAction](FileManager.Queue.queued);
		FileManager.Queue.reset();
		$paste.addClass('disabled');
		$clipboard.empty();
	});
	
	// Multiple Copy
	$body.on('click.filemanager', '#m-copy', function(e) {
		e.stopPropagation();
		e.preventDefault();
		$('[name=selector]:checked').each(function() {
			var $el = $(this);
			FileManager.Queue.add('copy', $el.val(), false);
			$el.attr('checked', false);
		});
		$body.trigger('queued.filemanager');
	});
	
	// Multiple Move
	$body.on('click.filemanager', '#m-move', function(e) {
		e.stopPropagation();
		e.preventDefault();
		$('[name=selector]:checked').each(function() {
			var $el = $(this);
			FileManager.Queue.add('move', $el.val(), false);
			$el.attr('checked', false);
		});
		$body.trigger('queued.filemanager');
	});
	
	// Multiple Remove
	$body.on('click.filemanager', '#m-remove', function(e) {
		e.stopPropagation();
		e.preventDefault();
		var paths = [];
		$('[name="selector"]:checked').each(function() {
			paths.push($(this).val());
		});		
		FileManager.remove(paths);
		$body.trigger('queued.filemanager');
	});
	
	// Show permalink in promt window
	$body.on('click.filemanager', '#show-permalink', function() {
		window.prompt('"Ctrl + C" to copy permalink to this location:', FileManager.Url.current);
	});
	
	
	//!
	// Table events
	// --------------------------------------------------------------------------------------------
	
	// Fetch
	$body.on('click.filemanager', '[data-action="fetch"]', function(e) {
		e.stopPropagation();
		e.preventDefault();
		FileManager.ls($(this).attr('href'));
		$body.trigger('disableButtons.filemanager');
	});
	
	// Remove
	$body.on('click.filemanager', '[data-action="remove"]', function(e) {
		e.stopPropagation();
		e.preventDefault();
		FileManager.remove([$(this).attr('data-path')]);
	});
	
	// Copy
	$body.on('click.filemanager', '[data-action="copy"]', function(e) {
		e.stopPropagation();
		e.preventDefault();
		FileManager.Queue.add('copy', $(this).attr('data-path'), true);
		$body.trigger('queued.filemanager');
	});
	
	// Move
	$body.on('click.filemanager', '[data-action="move"]', function(e) {
		e.stopPropagation();
		e.preventDefault();
		FileManager.Queue.add('move', $(this).attr('data-path'), true);
		$body.trigger('queued.filemanager');
	});
	
	// If selected files enable buttons
	$body.on('change.filemanager', '[name=selector]', function() {
		var $cel = $('[name="selector"]:checked');
		if ($cel.length > 0) {
			$body.trigger('enableButtons');
		} else {
			$body.trigger('disableButtons');
		}
	});
	
	
	
	//!
	// Form events
	// --------------------------------------------------------------------------------------------
	
	// Mkdir
	$body.on('submit.filemanager', '#create-folder', function(e) {
		e.stopPropagation();
		e.preventDefault();
		var $el = $(this);
		FileManager.mkdir($el.find('[name="new_dir_name"]').val());
		$el[0].reset();
	});
	
	// File upload XHR2 for IE10+ and other browsers
	$body.on('submit.filemanager', '#upload-files', function(e) {
		e.preventDefault();
		if (!$.browser.msie || $.browser.msie && parseInt($.browser.version, 10) > 9) {
			FileManager.upload($files[0].files);
			$(this)[0].reset();
		} else {
			var $f = $(this);
			$f.attr('action', FileManager.Url.current);
			$f.append('<input type="hidden" name="token" value="' + FileManager.token + '" />');
			$f[0].submit();
		}
	});
	
	// Focus to input on show (create folder form)
	$body.on('show', '#create-folder-wrapper', function() {
		$dirName.focus();
	});
	
	
	// !
	// Modal events
	// --------------------------------------------------------------------------------------------
	
	// Remove from clipboard
	$body.on('click.filemanager', '[data-action="clipboard-remove"]', function() {
		FileManager.Queue.remove($(this).attr('data-path'));
		$body.trigger('queued.filemanager');
	});
	
	// Clear clipboard
	$body.on('click.filemanager', '[data-action="clear-clipboard"]', function() {
		FileManager.Queue.reset();
		$body.trigger('queued.filemanager');
	});
	
	
	// !
	// Custom events
	// --------------------------------------------------------------------------------------------
	
	// Content added to queue
	$body.on('queued.filemanager', function() {
		FileManager.View.Clipboard.render();
		if (!!FileManager.Queue.queued.length) {
			$paste.removeClass('disabled');
		} else {
			$paste.addClass('disabled');
		}
		$body.trigger('disableButtons.filemanager');
	});
	
	// Enable copy/move/remove buttons
	$body.on('enableButtons.filemanager', function() {
		$mcopy.removeClass('disabled');
		$mmove.removeClass('disabled');
		$mremove.removeClass('disabled');
	});
	
	// Disable copy/move/remove buttons
	$body.on('disableButtons.filemanager', function() {
		$mcopy.addClass('disabled');
		$mmove.addClass('disabled');
		$mremove.addClass('disabled');
	});
	
	// Toggle loading icon
	$body.on('loading.filemanager', function() {
		$loading.toggleClass('hide');
	});
	
};