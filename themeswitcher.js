document.addEventListener('DOMContentLoaded', () => {
  const themeToggleButton = document.getElementById('theme-toggle-btn');
  const themeLabel = document.getElementById('theme-toggle-label');

  const applyTheme = (theme) => {
      if (theme === 'dark') {
          document.documentElement.setAttribute('data-theme', 'dark');
          themeLabel.textContent = 'Switch to Light Mode';
      } else {
          document.documentElement.removeAttribute('data-theme');
          themeLabel.textContent = 'Switch to Dark Mode';
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