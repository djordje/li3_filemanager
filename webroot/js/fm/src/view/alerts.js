FileManager.View.Alert = {
	selector: '#alerts',
	template: '{{#data}}{{{element}}}{{/data}}',
	element: function() {
		var output = '<div class="alert">';
			output += '<button type="button" class="close" data-dismiss="alert">×</button>';
			output += this.error;
		output += '</div>';
		return output; 
	},
	data: null,
	render: function() {
		if (typeof this.selector === 'string') {
			this.selector = $(this.selector);
		}
		this.selector.empty().append(Mustache.render(this.template, this));
	}
};