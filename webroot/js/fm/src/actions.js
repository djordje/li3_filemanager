FileManager.ls = function(url, save_alerts) {
	$.ajax({
		url: url,
		beforeSend: function() {
			$('body').trigger('loading');
		},
		success: function(data) {
			$('body').trigger('loading');
			if (!save_alerts) {
				save_alerts = false;
			}
			if (!save_alerts) {
				$('.alert').alert('close');
			}
			if (data.data && data.breadcrumb) {
				FileManager.Url.update(url);
				FileManager.View.Files.data = data.data;
				FileManager.View.Breadcrumb.data = data.breadcrumb;
				FileManager.View.Files.render();
				FileManager.View.Breadcrumb.render();
			}
		}
	});
};

FileManager.mkdir = function(name) {
	$.ajax({
		type: 'POST',
		url: FileManager.Url.params('mkdir', FileManager.Url.params()),
		data: {
			new_dir_name: name,
			token: FileManager.token
		}
	}).done(function(response) {
		if (response.regenerate) {
			window.location = FileManager.Url.current;
		}
		if (response.success) {
			FileManager.ls(FileManager.Url.current);
		} else {
			FileManager.View.Alert.data = [response];
			FileManager.View.Alert.render();
		}
	});
};

FileManager.upload = function(files) {
	var xhr = new XMLHttpRequest(),
	fdata = new FormData();
	fdata.append('token', FileManager.token);

	$.each(files, function(k, v) {
		fdata.append('files[]', v);
	});

	xhr.upload.onprogress = function(e) {
		if (e.lengthComputable) {
			var percentComplete = (e.loaded / e.total) * 100;
			$('#upload-proggres').removeClass('hide').find('.bar').css('width', percentComplete);
		}
	};

	xhr.open('POST', FileManager.Url.current, true);
	xhr.onload = function(response) {
		$('#upload-proggres').addClass('hide').find('.bar').css('width', 0);
		
		if (response.regenerate) {
			window.location = FileManager.Url.current;
		}
		if (!response.success) {
			FileManager.View.Alert.data = response.errors;
			FileManager.View.Alert.render();
		}
		FileManager.ls(FileManager.Url.current, !response.success);
	};
	xhr.send(fdata);
};

FileManager.move = function(paths) {
	$.ajax({
		type: 'POST',
		url: FileManager.Url.params('move', FileManager.Url.params()),
		data: {
			from: paths,
			token: FileManager.token
		}
	}).done(function(response) {
		if (response.regenerate) {
			window.location = FileManager.Url.current;
		}
		if (!response.success) {
			FileManager.View.Alert.data = response.errors;
			FileManager.View.Alert.render();
		}
		FileManager.ls(FileManager.Url.current, !response.success);
	});
};

FileManager.copy = function(paths) {
	$.ajax({
		type: 'POST',
		url: FileManager.Url.params('copy', FileManager.Url.params()),
		data: {
			from: paths,
			token: FileManager.token
		}
	}).done(function(response) {
		if (response.regenerate) {
			window.location = FileManager.Url.current;
		}
		if (!response.success) {
			FileManager.View.Alert.data = response.errors;
			FileManager.View.Alert.render();
		}
		FileManager.ls(FileManager.Url.current, !response.success);
	});
};

FileManager.remove = function(paths) {
	$.ajax({
		type: 'POST',
		url: FileManager.Url.params('remove'),
		data: {
			selected: paths,
			token: FileManager.token
		}
	}).done(function(response) {
		if (response.regenerate) {
			window.location = FileManager.Url.current;
		}
		if (!response.success) {
			FileManager.View.Alert.data = response.errors;
			FileManager.View.Alert.render();
		}
		FileManager.ls(FileManager.Url.current, !response.success);
	});
};