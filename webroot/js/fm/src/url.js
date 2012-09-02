FileManager.Url = {
	lib: '',
	current: '',
	
	trim: function(string) {
		while (string.charAt(0) === '/') {
			string = string.substr(1);
		}
		while (string.charAt(string.length - 1) === '/') {
			string = string.substr(0, string.length - 1);
		}
		return string;
	},
	
	filter: function(input) {
		var filtered;
		switch (typeof input) {
			case 'string':
				filtered = this.trim(input);
				break;
			case 'object':
				$.each(input, function(k, v) {
					filtered[k] = this.filter(v);
				});
				filtered = filtered.join('/');
				break;
		}
		return filtered;
	},
	
	set: function(k, v) {
		if (typeof this[k] === 'string' && this[k] === '' && v) {
			return this[k] = this.trim(v);
		}
		return false;
	},
	
	update: function(url) {
		return this.current = this.trim(url);
	},
	
	params: function() {
		var argsLength = arguments.length,
			args = [];
		if (argsLength < 1) {
			return this.trim(this.current.substring(this.current.length, this.lib.length));
		}
		for (var i = argsLength - 1; i>= 0; i--) {
			args[i] = this.filter(arguments[i]);
		}
		return this.lib + '/' + escape(args.join('/'));
	}
};