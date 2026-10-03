# Simple File Sharing Web App

A PHP-based file sharing website that allows users to securely upload, view, and delete files associated with their accounts. The application uses Apache and Linux filesystem permissions to store user files outside of the public web directory while still allowing the web application to manage them.

## Features

* User login and account-based file management
* Upload files associated with a specific user
* View and download uploaded files
* Delete personal files
* Secure storage outside `public_html`
* Server-side file and username validation
* Protected filesystem access and permissions
* URLs that do not expose internal server paths
* Custom creative features
* W3C-compliant HTML/CSS

## Technologies

* **PHP**
* **HTML/CSS**
* **Apache**
* **Linux**

## Security

Security was a major focus of the project. User-uploaded files are stored outside the public web directory to prevent direct filesystem access. The application validates user input and file names, escapes output, and follows **Filter Input, Escape Output (FIEO)** practices.

Filesystem permissions were also configured to allow the Apache/PHP processes to access the necessary user files without exposing the underlying server directory structure through URLs.

## Project

This project was developed as part of a web development course to practice building file-management functionality, handling Linux filesystem permissions, and applying secure web development principles.
