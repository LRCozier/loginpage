document.addEventListener('DOMContentLoaded', () => {
  const themeToggleButton = document.getElementById('theme-toggle-btn');

  const applyTheme = (theme) => {
      if (theme === 'dark') {
          document.documentElement.setAttribute('data-theme', 'dark');
          themeToggleButton.setAttribute('aria-checked', 'true');
      } else {
          document.documentElement.removeAttribute('data-theme');
          themeToggleButton.setAttribute('aria-checked', 'false');
      }
  };

  const savedTheme = localStorage.getItem('theme') || 'light';
  applyTheme(savedTheme);

  themeToggleButton.addEventListener('click', () => {
      const currentTheme = document.documentElement.getAttribute('data-theme');
      const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
      applyTheme(newTheme);
      localStorage.setItem('theme', newTheme);
  });
});