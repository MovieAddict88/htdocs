# Modern PHP & MySQL Portfolio Website

This repository contains the source code for a modern, responsive portfolio website built with PHP and MySQL. It features a complete admin panel for content management and a public-facing site to display the portfolio.

## Features

*   **Admin Panel:** Secure login, and full CRUD (Create, Read, Update, Delete) functionality for all portfolio sections.
*   **Responsive Design:** Desktop-first design with a fixed sidebar, which collapses to a mobile-friendly drawer navigation.
*   **Dynamic Content:** All content is fetched from a MySQL database, making the site fully manageable from the admin panel.
*   **Light/Dark Mode:** A theme switcher allows users to toggle between light and dark modes, with their preference saved locally.
*   **PWA Ready:** The site is a Progressive Web App, complete with a manifest file and a service worker for offline capabilities and home screen installation.
*   **Built-in Analytics:** A lightweight, self-hosted analytics tracker logs page views without relying on external services.

## Getting Started

To get the project up and running on your local server, follow these steps:

### 1. Database Configuration

The project requires a `config.php` file to connect to your database. This file is not included in the repository for security reasons.

1.  Navigate to the `includes/` directory.
2.  Make a copy of `config.sample.php` and rename it to `config.php`.
3.  Open `config.php` and enter your MySQL database credentials (host, database name, username, and password).

### 2. Database Setup

Once your configuration is in place, you need to create the database and all the necessary tables.

1.  Open the `setup.php` file in the root directory in your web browser (e.g., `http://localhost/setup.php`).
2.  The script will connect to your database and create all the required tables.
3.  **IMPORTANT:** For security, you must delete the `setup.php` file from your server after the setup is complete.

### 3. Required Assets

This project requires a few image assets that are not included in the repository. You will need to create and place them in the correct locations:

*   **Profile Photo:** `assets/images/profile.jpg` (A square profile picture for the hero section and sidebar).
*   **PWA Icons:**
    *   `assets/images/icons/icon-192x192.png`
    *   `assets/images/icons/icon-512x512.png`

### 4. Admin Login

After setting up the database, you'll need an admin account to log in.

1.  A default admin user is not created automatically for security. You can create one by temporarily modifying the `admin/login.php` or creating a one-time script to insert a user with a hashed password into the `users` table. (Note: A future version will include a more user-friendly admin creation process).
2.  The default login page is at `admin/login.php`.

## Project Structure

*   `/admin`: Contains all backend files for the admin panel.
*   `/assets`: Holds all static assets like CSS, JavaScript, and images.
*   `/includes`: Contains core files like the database connection and configuration samples.
*   `/sections`: Modular PHP files for each section of the public-facing portfolio.
*   `index.php`: The main entry point for the public website.
*   `sw.js` & `manifest.json`: Files for the Progressive Web App functionality.