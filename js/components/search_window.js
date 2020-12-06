document.addEventListener('DOMContentLoaded', () => {
  const searchWindow = document.querySelector('.search-window');
  if (!searchWindow) return; 
  const btSearch = document.querySelector('button.search');
  const btExit = searchWindow.querySelector('.exit');
  
  function updateWindowPosition() {
    const windowX = window.innerWidth * 0.5 - searchWindow.offsetWidth * 0.5;
    const windowY = window.innerHeight * 0.5 - searchWindow.offsetHeight * 0.5;
    searchWindow.style.left = windowX + 'px';
    searchWindow.style.top = windowY + 'px';
  }

  function showSearchWindow() {
    if (!searchWindow) return;
    searchWindow.style.visibility = 'hidden';
    searchWindow.style.display = 'block';
    updateWindowPosition();
    searchWindow.style.visibility = 'visible';
  }
  
  function closeSearchWindow() {
    if (!searchWindow) return;
    searchWindow.style.display = 'none';
  }

  btSearch.addEventListener('click', () => {
    showSearchWindow();
  });

  btExit.addEventListener('click', () => {
    closeSearchWindow();
  });

  window.addEventListener('resize', event => {
    updateWindowPosition();
  });
});
