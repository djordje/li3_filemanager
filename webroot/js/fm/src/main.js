var FileManager = FileManager || {};

FileManager.token = '';

FileManager.init = function(url, data) {
	this.Url.set('lib', url.lib);
	this.Url.set('current', url.current);
	this.View.Files.data = data.data;
	this.View.Breadcrumb.data = data.breadcrumb;
	this.token = $('[name="security[token]"]').val();
	this.attachEvents();
	this.View.Files.render();
	this.View.Breadcrumb.render();
};