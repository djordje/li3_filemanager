FileManager.View.Breadcrumb = {
	selector: '.breadcrumb',
	template: '<li><i class="icon-folder-close"></i><span class="divider">:</span></li>{{#data}}<li>{{{element}}}</li>{{/data}}',
	element: function() {
		if (this.url) {
			return '<a href="' + this.url + '" data-action="fetch">' + this[0] + '</a><span class="divider">/</span>';
		} else {
			return this[0];
		}
	},
	data: null,
	render: function() {
		if (typeof this.selector === 'string') {
			this.selector = $(this.selector);
		}
		this.selector.empty().append(Mustache.render(this.template, this));
	}
};