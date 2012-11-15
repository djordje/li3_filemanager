FileManager.View.Files = {
	selector: '#files',
	template: '{{#data}}{{{element}}}{{/data}}',
	element: function() {
		var path = (this.path === '/')? this.path + this.name : this.path + '/' + this.name;
		var output = '<tr>';
		output += '<td><input type="checkbox" name="selector" value="' + path + '"/></td>';
		output += '<td><strong>';
		if (this.dir) {
			output += '<a href="' + FileManager.Url.params(path) + '" data-action="fetch">' + this.name + '</a>';
		} else {
			output += this.name;
		}
		output += '</strong></td>';
		output += '<td>';
		if (!this.dir) {
			var size = Math.round(this.size / 1024);
			output += size + ' KB';
		}
		output += '</td>';
		output += '<td>' + this.mode + '</td>';
		output += '<td><div class="btn-group">';
		if (this.url) {
			output += '<a href="' + this.url + '" class="btn btn-small';
			if (this.dir) {
				output += ' disabled';
			}
			output += '" target="_blank">Open URL</a>';
		}
		output += '<button type="button" class="btn btn-small" title="Copy" data-action="copy" data-path="' + path + '">Copy</button>';
		output += '<button type="button" class="btn btn-small" title="Cut" data-action="move" data-path="' + path + '">Cut</button>';
		output += '<button type="button" class="btn btn-small btn-danger" title="Delete"  data-action="remove" data-path="' + path + '"><i class="icon-trash icon-white"></i></button>';
		output += '</div></td></tr>';
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