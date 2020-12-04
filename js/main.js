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
    xhttp.addEventListener('error', function() {
      reject(this.response);
    });
    let url = './interface.php?request=' + request;
    if (parameters) {
      const parameterString = Object.keys(parameters).map(key => key + '=' + parameters[key]).join('&');
      url += '&' + parameterString;
    }
    xhttp.open('GET', url, true);
    xhttp.send();
  });
}
