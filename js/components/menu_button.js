document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.menu-button').forEach(button => button.addEventListener('click', event => {
    const menuId = button.getAttribute('menu');
    const menu = document.getElementById(menuId);

    if (menu != undefined) {
      const menuActive = button.classList.contains('active');
      if (!menuActive) {
        button.classList.add('active');
        menu.classList.add('active');
      }
      else {
        button.classList.remove('active');
        menu.classList.remove('active');
      }
      localStorage.setItem('menuActive', !menuActive);
    }
	}));
});