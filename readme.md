# Responsive Login & Registration System

A secure, modern, and fully responsive user authentication system built with vanilla PHP, JavaScript, CSS, and HTML. This project serves as a portfolio piece to demonstrate fundamental full-stack web development skills, focusing on security, user experience, and maintainable code.

**[► Live Demo ◄]()**

---

### Key Features

* **Secure User Authentication:** Full registration, login, and logout functionality with robust server-side validation.
* **Sleek Dark/Light Mode Toggle:** A modern, animated UI toggle that respects user preference across sessions using `localStorage`.
* **Polished User Experience (UX):** Includes features like a password visibility toggle and clear, dynamic feedback messages for a smooth user journey.
* **Fully Responsive Design:** A mobile-first approach ensures a seamless and accessible interface on all devices, from phones to desktops.
* **Secure Session Management:** Protects user sessions, restricts access to protected pages, and ensures secure session destruction upon logout.
* **Modern Social Login UI:** Includes front-end components ready for future integration with social authentication providers.
* **Demo-Ready Data Store:** Uses a simple JSON file to simulate a database for immediate, hassle-free demonstration.

### Technology Stack

* **Backend:** PHP 8.x
* **Frontend:**
    * HTML5
    * CSS3 (Flexbox, CSS Custom Properties/Variables, Transitions)
    * Vanilla JavaScript (ES6)
* **Development Environment:** Works with any standard PHP server (e.g., PHP's built-in server, XAMPP, MAMP).

---

### Project Structure

The project is organized logically to separate concerns between front-end display, back-end processing, and styling.
---

### Project Structure

The project is organized logically to separate concerns between front-end display, back-end processing, and styling.

/
├── login.php             # Main login page
├── register.php          # User registration page
├── success.php           # Protected page for logged-in users
├── logout.php            # Securely handles user logout
|
├── login_handle.php     # Processes login credentials
├── register_handle.php  # Validates and saves new user data
|
├── users.json            # File-based user data store (simulated DB)
|
├── styles.css             # All styles, including dark/light themes
└── themeswitcher.js     # Logic for the theme toggle

---

### How It Works: Core Concepts & Implementation

This project was built with a focus on security, modern development practices, and ease of demonstration.

#### Architectural Choice: Simulating a Database with a JSON File

For this portfolio project, a deliberate choice was made to use a `users.json` file to simulate a database. This decision has several key advantages for a demonstration environment:

1.  **Portability & Ease of Use:** Anyone can clone the repository and run the project instantly with a local PHP server, without the need to set up a MySQL database, create tables, or manage credentials.
2.  **Focus on Business Logic:** This approach allows the core application logic—**validation, security, and session handling**—to be the star of the show. The handlers (`login_handler.php`, `register_handler.php`) are architected in a way that is "data-source agnostic."
3.  **Production-Ready Design:** The backend logic is completely decoupled from the data storage mechanism. Migrating this project to a production environment with a MySQL database would only require swapping the file read/write functions with PDO prepared statements. The critical validation and security logic would remain unchanged.

#### Security Implementation

Security was a top priority throughout the development of this system.

* **Password Hashing:** User passwords are **never** stored in plain text. The `password_hash()` and `password_verify()` functions are used, implementing PHP's recommended standard for secure password storage.
* **Server-Side Validation:** All user input is rigorously validated on the server to prevent invalid data and common vulnerabilities.
* **Session Security:** Upon a successful login, `session_regenerate_id(true)` is called to prevent session fixation attacks. The dedicated `logout.php` script ensures that sessions are properly unset and destroyed.

#### Front-End & User Experience

The front-end is built to be modern, intuitive, and responsive.

* **Theme Switcher:** The UI features a polished, animated theme-switcher built with clean HTML, CSS transitions, and modern JavaScript. The theme preference is persisted in `localStorage` to prevent a "flash of unstyled content" (FOUC) and provide a consistent user experience.
* **Dynamic Feedback:** The forms provide clear, specific feedback for both errors (e.g., "Passwords do not match") and successes ("Registration successful!"), guiding the user through the authentication process.

---

### Local Setup & Installation

To run this project on your local machine, you will need PHP installed.

1.  **Clone the repository:**
    ```bash
    git clone [https://github.com/your-username/your-repo-name.git](https://github.com/your-username/your-repo-name.git)
    ```

2.  **Navigate to the project directory:**
    ```bash
    cd your-repo-name
    ```

3.  **Start the built-in PHP development server:**
    ```bash
    php -S localhost:8000
    ```

4.  **Open your web browser** and go to the following address:
    [http://localhost:8000/login.php](http://localhost:8000/login.php)

The application is now running locally. You can test the registration flow by creating a new user or log in with the default user:
* **Username:** `admin`
* **Password:** `password123`