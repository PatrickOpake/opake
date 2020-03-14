function updateParam(name, value, loc) {
	var loc = new String(loc ? loc : location);

	if (loc.indexOf('?') != -1) {
		var re = new RegExp('\&?' + name + '\=[^\&]*', 'g');
		loc = loc.replace(re, '');
		if (value || value === 0) {
			loc += '&' + name + '=' + encodeURIComponent(value);
		}
		return loc;
	}
	else {
		return loc + '?' + name + '=' + encodeURIComponent(value);
	}
}