(() => {
  const button = document.querySelector('.admin-menu-button');
  const backdrop = document.querySelector('.admin-sidebar-backdrop');
  const sidebar = document.querySelector('.admin-sidebar');
  if (!button || !backdrop || !sidebar) return;

  const close = () => {
    document.body.classList.remove('admin-nav-open');
    button.setAttribute('aria-expanded', 'false');
    button.setAttribute('aria-label', 'Open navigation');
  };
  button.addEventListener('click', () => {
    const open = document.body.classList.toggle('admin-nav-open');
    button.setAttribute('aria-expanded', String(open));
    button.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation');
    if (open) sidebar.querySelector('a, summary')?.focus();
  });
  backdrop.addEventListener('click', () => { close(); button.focus(); });
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && document.body.classList.contains('admin-nav-open')) {
      close(); button.focus();
    }
  });
  window.matchMedia('(min-width: 992px)').addEventListener('change', event => {
    if (event.matches) close();
  });
})();
