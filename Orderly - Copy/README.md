# ORDERLY - Minimal PHP Todo Application

ORDERLY is a minimal PHP-based todo application designed to help you manage your tasks efficiently. This project features a clean, formal user interface with rectangular UI elements and supports both light and dark modes for comfortable usage in different lighting conditions.

## Features

- User authentication (login, signup, logout)
- Task management with create, update, delete functionality
- Responsive and formal UI design with rectangular elements
- Light mode and dark mode toggle with persistent user preference
- Minimal dependencies and easy setup

## Folder Structure

- `index.php` - Landing page
- `dashboard.php` - User dashboard to manage todos
- `login.php`, `signup.php`, `logout.php` - Authentication pages
- `todos_api.php` - API endpoint for todo CRUD operations
- `inc/` - Includes folder for shared components like header, footer, auth, and database connection
- `assets/` - Static assets including CSS, JS, and images

## Setup and Launch

1. Ensure you have PHP installed on your system.

2. Clone or download the project files to your local machine.

3. Start a local PHP development server in the project root directory:

   ```bash
   php -S localhost:8000
   ```

4. Open your browser and navigate to [http://localhost:8000](http://localhost:8000).

5. You can now use the ORDERLY todo application. Use the theme toggle button in the navigation bar to switch between light and dark modes.

## Notes

- The application uses a SQLite database (`data.sqlite`) for storing user and todo data.
- The theme preference is saved in the browser's localStorage and applied on page load.

## License

This project is open source and available under the MIT License.
