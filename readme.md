# Responsive Login & Registration System

A secure, modern, and fully responsive user authentication system built with vanilla PHP, JavaScript, CSS, and HTML. This project serves as a portfolio piece to demonstrate fundamental full-stack web development skills, focusing on security, user experience, and maintainable code.

**[► Live Demo (Link to your deployed project) ◄]()**

---

### Key Features

* **Secure User Registration & Login:** Server-side validation and password hashing.
* **Responsive Design:** A seamless, mobile-first experience on any device.
* **Dark/Light Mode Theme:** A persistent theme toggle that respects user preference via `localStorage`.
* **Secure Session Management:** Protects user sessions and restricted content.
* **Password Visibility Toggle:** Enhances user experience when typing complex passwords.
* **Clear User Feedback:** Dynamic and specific error and success messages.
* **Modern Social Login UI:** Includes front-end elements for social authentication providers.
* **Demo-Ready Data Store:** Uses a simple JSON file to simulate a database for immediate, hassle-free demonstration.

### Technology Stack

* **Backend:** PHP 8.4.11
* **Frontend:**
    * HTML5
    * CSS3
    * Vanilla JavaScript
* **Development Environment:** Works with any standard PHP server (e.g., PHP's built-in server, XAMPP, MAMP).

---

### Project Structure

The project is organized logically to separate concerns between front-end display, back-end processing, and styling.

Certainly. Here is the complete Markdown for your README.md file, ready to be copied and pasted directly into your file.

Markdown

# Responsive Login & Registration System

A secure, modern, and fully responsive user authentication system built with vanilla PHP, JavaScript, CSS, and HTML. This project serves as a portfolio piece to demonstrate fundamental full-stack web development skills, focusing on security, user experience, and maintainable code.

**[► Live Demo (Link to your deployed project) ◄](https://your-deployment-url.com)**

---

### Key Features

* **Secure User Registration & Login:** Server-side validation and password hashing.
* **Responsive Design:** A seamless, mobile-first experience on any device.
* **Dark/Light Mode Theme:** A persistent theme toggle that respects user preference via `localStorage`.
* **Secure Session Management:** Protects user sessions and restricted content.
* **Password Visibility Toggle:** Enhances user experience when typing complex passwords.
* **Clear User Feedback:** Dynamic and specific error and success messages.
* **Modern Social Login UI:** Includes front-end elements for social authentication providers.
* **Demo-Ready Data Store:** Uses a simple JSON file to simulate a database for immediate, hassle-free demonstration.

### Technology Stack

* **Backend:** PHP 8.x
* **Frontend:**
    * HTML5
    * CSS3 (Flexbox, CSS Custom Properties/Variables)
    * Vanilla JavaScript (ES6)
* **Development Environment:** Works with any standard PHP server (e.g., PHP's built-in server, XAMPP, MAMP).

---

### Project Structure

The project is organized logically to separate concerns between front-end display, back-end processing, and styling.

/
├── login.php             # Main login page
├── register.php          # User registration page
├── success.php           # Protected page for logged-in users
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
* **Server-Side Validation:** All user input is rigorously validated on the server to prevent invalid data and common vulnerabilities. This includes checks for empty fields, email format, username constraints, and password strength.
* **Session Security:** Upon a successful login, `session_regenerate_id(true)` is called to prevent session fixation attacks. Protected pages like `success.php` verify the existence of a valid session, redirecting unauthorized users.

#### Front-End & User Experience

The front-end is built to be modern, intuitive, and responsive.

* **Dark Mode:** The theme-switching functionality is implemented using CSS Custom Properties (Variables), which is the modern standard. This allows for an instant theme change without reloading the page or causing a "flash of unstyled content" (FOUC). User preference is saved in `localStorage` for a persistent experience across sessions.
* **Dynamic Feedback:** The forms provide clear, specific feedback for both errors (e.g., "Passwords do not match") and successes ("Registration successful!"), guiding the user through the authentication process.

---

### Local Setup & Installation

To run this project on your local machine, you will need PHP installed.

1.  **Clone the repository:**
    ```bash
    git clone [https://github.com/LRCozier/loginpage.git](https://github.com/LRCozier/loginpage.git)
    ```

2.  **Navigate to the project directory:**
    ```bash
    cd loginpage
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