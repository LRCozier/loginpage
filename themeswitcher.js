document.addEventListener('DOMContentLoaded', () => {
  const themeToggleButton = document.getElementById('theme-toggle-btn');
  const sunIcon = document.getElementById('sun-icon');
  const moonIcon = document.getElementById('moon-icon');

  const applyTheme = (theme) => {
      if (theme === 'dark') {
          document.documentElement.setAttribute('data-theme', 'dark');
          sunIcon.style.display = 'block';
          moonIcon.style.display = 'none';
      } else {
          document.documentElement.removeAttribute('data-theme');
          sunIcon.style.display = 'none';
          moonIcon.style.display = 'block';
      }
  };

  // Check the saved theme in localStorage on page load
  const savedTheme = localStorage.getItem('theme') || 'light';
  applyTheme(savedTheme);

  // Event listener for the toggle button
  themeToggleButton.addEventListener('click', () => {
      // Determine the new theme
      const currentTheme = document.documentElement.getAttribute('data-theme');
      const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

      // Apply the new theme
      applyTheme(newTheme);

      // Save the new theme to localStorage
      localStorage.setItem('theme', newTheme);
  });
});