# Fully Responsive PHP & MySQL Portfolio

This project is a complete, database-driven portfolio website built with PHP, MySQL, CSS, and JavaScript. It features a fully responsive design, a secure admin panel for content management, and is optimized for deployment on services like InfinityFree.

## Features

- **Fully Responsive:** Desktop-first design with a fixed sidebar that transforms into a slide-in drawer on mobile devices.
- **Dynamic Content:** All portfolio sections (About, Education, Experience, etc.) are managed dynamically from a MySQL database.
- **Admin Panel:** A secure, password-protected admin area (`/admin`) allows you to perform full CRUD (Create, Read, Update, Delete) operations on your portfolio content without touching any code.
- **Light/Dark Mode:** A theme toggle allows users to switch between light and dark modes, with their preference saved locally.
- **Secure Downloads:** A "Download Center" provides password-protected access to files (e.g., your resume), and tracks download counts.
- **Contact Form:** A functional contact form that sends submissions directly to your email.

---

## 🚀 Deployment Instructions for InfinityFree

Follow these steps to get your portfolio live on an InfinityFree account.

### Step 1: Upload Files

1.  Log in to your InfinityFree account.
2.  Navigate to the **File Manager**.
3.  Open the `htdocs` directory. **Do not delete this directory.**
4.  Upload all the files and folders from the `htdocs` folder in this project into the `htdocs` directory on the server. The `htdocs` directory on the server should now contain `index.php`, the `admin` folder, `css` folder, etc.

### Step 2: Create the Database

1.  From your InfinityFree dashboard, go to the **Control Panel**.
2.  Find and open the **MySQL Databases** tool.
3.  Create a new database by entering a name (e.g., `portfolio`). Click **Create Database**.
4.  InfinityFree will create the database and provide you with the following critical details. **Copy these down - you will need them in the next step.**
    -   **MySQL Hostname** (e.g., `sql201.epizy.com`)
    -   **MySQL Database Name** (e.g., `epiz_12345678_portfolio`)
    -   **MySQL Username** (e.g., `epiz_12345678`)
    -   **MySQL Password** (This is your InfinityFree account password)

### Step 3: Import the Database Tables

1.  From the Control Panel, open **phpMyAdmin**.
2.  Select your newly created database from the list on the left.
3.  Click the **Import** tab at the top.
4.  Under "File to import", click **Choose File** and select the `database.sql` file from this project.
5.  Scroll down and click **Go**. This will create all the necessary tables and populate them with some default content.

### Step 4: Configure the Database Connection

1.  Go back to the **File Manager**.
2.  Navigate to `htdocs/php/`.
3.  Right-click on the `db_config.php` file and select **Edit**.
4.  Replace the placeholder values with the database credentials you copied in Step 2.

    ```php
    // Example configuration:
    define('DB_SERVER', 'sql201.epizy.com');      // Your MySQL Hostname
    define('DB_USERNAME', 'epiz_12345678');       // Your MySQL Username
    define('DB_PASSWORD', 'YourInfinityFreePassword'); // Your account password
    define('DB_NAME', 'epiz_12345678_portfolio'); // Your MySQL Database Name
    ```
5.  Save the file.

### Step 5: Update Contact Form Email

1.  In the File Manager, navigate to `htdocs/php/`.
2.  Edit the `contact_handler.php` file.
3.  On line 8, change the fallback email from `'your-email@example.com'` to your actual email address. This is a fallback in case the database connection fails. The script will automatically use the email you set in the admin panel otherwise.
4.  Save the file.

### Step 6: Log In to the Admin Panel

1.  Go to your website's URL and add `/admin` to the end (e.g., `http://yourdomain.epizy.com/admin`).
2.  Log in with the default credentials:
    -   **Username:** `admin`
    -   **Password:** `password`
3.  **IMPORTANT:** It is highly recommended that you change the default password immediately. You can do this by going into **phpMyAdmin**, finding the `users` table, editing the `admin` user, and replacing the password hash with a new one.

---

## Managing Your Content

-   **Images:** To add a profile picture, upload an image (e.g., `profile.jpg`) to the `htdocs/images/` directory. Then, in the Admin Panel -> Site Settings, update the "Hero Photo URL" to `images/profile.jpg`. The same process applies to project images.
-   **Downloads:** To add a downloadable file, first upload it to a new directory you create called `htdocs/downloads/`. Then, in the Admin Panel -> Downloads, add the new entry, providing the correct file path and a password.
-   **All other content** (About Me text, Education, Experience, etc.) can be fully managed through the admin panel.