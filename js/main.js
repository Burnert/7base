// Open the page with main menu already open if the flag is set
const mainMenuActive = localStorage.getItem('menuActive') == 'true';

function sendInterfaceRequest(request, parameters) {
	return new Promise((resolve, reject) => {
		const xhttp = new XMLHttpRequest();
		xhttp.addEventListener('readystatechange', function()	{
			if (this.readyState == 4 && this.status == 200) {
				resolve(this.response);
			}
		});
		let url = './php/interface.php?request=' + request;
		if (parameters) {
			const parameterString = Object.keys(parameters).map(key => key + '=' + parameters[key]).join('&');
			url += '&' + parameterString;
		}
		xhttp.open('GET', url, true);
		xhttp.send();
	});
}

document.addEventListener('DOMContentLoaded', () => {
	document.querySelectorAll('.b-refresh').forEach(button => button.addEventListener('click', async () => {
			const lang = document.getElementById('language').value;
			const result = await sendInterfaceRequest('apply_settings', { lang });
			location.reload();
		}));
});
