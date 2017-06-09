function O(i) {
	return typeof i == 'object' ? i : document.getElementById(i);
}
function S(i) {
	return O(i).style;
}
function C(name, tag) {
	var elements	= document.getElementsByTagName(tag);
	var objects		= [];

	for (var i = 0 ; i < elements.length ; i++) {
	if (elements[i].className == name) {
			objects.push(elements[i]);
		}
	}
	return objects
}
// final function to edit profile items
function EditProfileItem (item) {
	var show = item + 'show' + 'frm';
	O(show).parentNode.removeChild(O(show));	// remove showcase (pun)
	var edit = item + 'edit' + 'frm';
	O(edit).removeAttribute("hidden");				// make edit form visible
}
