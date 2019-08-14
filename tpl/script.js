// Code from http://stackoverflow.com/questions/400212/how-do-i-copy-to-the-clipboard-in-javascript
function copyTextToClipboard(text) {
	var textArea = document.createElement("textarea");
	textArea.style.position = 'fixed';
	textArea.style.top = 0;
	textArea.style.left = 0;
	textArea.style.width = '2em';
	textArea.style.height = '2em';
	textArea.style.padding = 0;
	textArea.style.border = 'none';
	textArea.style.outline = 'none';
	textArea.style.boxShadow = 'none';
	textArea.style.background = 'transparent';
	textArea.value = text;

	document.body.appendChild(textArea);
	textArea.select();

	try {
		var successful = document.execCommand('copy');
		var msg = successful ? 'successful' : 'unsuccessful';
		console.log('Copying text command was ' + msg);
	} catch (err) {
		alert('La copie dans le presse-papier ne fonctionne pas sur ce navigateur.');
	}

	document.body.removeChild(textArea);
	return successful;
}

function copyThis(text, item) {
	var success = copyTextToClipboard(text);
	if(success) {
		item.classList.add('copied');
		window.setTimeout(function(){
			item.classList.remove('copied');
		}, 750);
	}
}