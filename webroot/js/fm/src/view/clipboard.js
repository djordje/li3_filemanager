FileManager.View.Clipboard = {
	selector: '#clipboard',
	template: '{{#data}}{{{element}}}{{/data}}',
	element: function() {
		var tmpl = '<div class="clearfix">';
			tmpl += '<p class="pull-left">' + this + '</p>';
			tmpl += '<button type="button" class="btn btn-mini btn-warning pull-right" title="Remove from clipboard" data-action="clipboard-remove" data-path="' + this + '"><i class="icon-trash icon-white"></i></button>';
		tmpl += '</div>';
		return tmpl;
	},
	data: function() {
		return FileManager.Queue.queued;
	},
	render: function() {
		if (typeof this.selector === 'string') {
			this.selector = $(this.selector);
		}
		this.selector.empty().append(Mustache.render(this.template, this));
	}
};