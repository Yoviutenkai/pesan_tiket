document.addEventListener('DOMContentLoaded', function () {
  var toggles = document.querySelectorAll('[data-sidebar-toggle]');
  var sidebar = document.querySelector('.sidebar');
  if (!sidebar || !toggles.length) {
    return;
  }

  var overlay = document.querySelector('.sidebar-overlay');
  if (!overlay) {
    overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);
  }

  function setOpen(isOpen) {
    sidebar.classList.toggle('show', isOpen);
    overlay.classList.toggle('show', isOpen);
    toggles.forEach(function (btn) {
      btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
  }

  toggles.forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      setOpen(!sidebar.classList.contains('show'));
    });
  });

  overlay.addEventListener('click', function () {
    setOpen(false);
  });

  window.addEventListener('resize', function () {
    if (window.innerWidth > 991) {
      setOpen(false);
    }
  });
});
