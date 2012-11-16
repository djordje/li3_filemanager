FileManager.Queue = {
	lastAction: '',
	allowedActions: ['copy', 'move', 'remove'],
	queued: [],

	add: function(action, value, reset) {
		var exists = false;
		if (!action || !value) {
			return false;
		}
		if (!!reset) {
			this.reset();
		}
		for (var i = this.allowedActions.length - 1; i >= 0; i--) {
			if (this.allowedActions[i] === action) {
				this.lastAction = action;
				for (var n = this.queued.length - 1; n >= 0; n--) {
					if (this.queued[n] === value) {
						exists = true;
					}
				}
			}
		}
		if (!exists) {
			return this.queued.push(value);
		}
		return false;
	},

	remove: function(value) {
		if (this.last_action === '') {
			return false;
		}
		for (var i = this.queued.length - 1; i >= 0; i--) {
			if (this.queued[i] === value) {
				return !this.queued.splice(i, 1);
			}
		}
	},

	reset: function() {
		this.lastAction = '';
		this.actionExists = false;
		this.queued = [];
	}
};