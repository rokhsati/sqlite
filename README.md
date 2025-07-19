# SQLite Database Manager

A lightweight, single-page PHP application for managing SQLite databases with a modern, professional interface inspired by Microsoft's design language. This application allows users to browse SQLite database files, view tables and their columns, and execute SQL queries with a user-friendly interface built using Bootstrap 5.3.

## Features
- **Database File Discovery**: Automatically lists SQLite database files (`.sqlite`, `.db`, `.sqlite3`) in the current directory.
- **Table and Column Exploration**: Displays tables in a selected database and their respective columns with data types.
- **SQL Query Execution**: Supports execution of any SQLite query (e.g., SELECT, INSERT, UPDATE, DELETE) with results displayed in a responsive table for SELECT queries or as success/error messages for other queries.
- **Modern UI**: Dark-themed, Microsoft-inspired design with standard font sizes and a responsive layout powered by Bootstrap 5.3.
- **Error Handling**: Robust error handling for database connections and query execution using PDO.

## Requirements
- PHP 7.4 or higher with the PDO SQLite extension enabled.
- A web server (e.g., Apache, Nginx) to serve the PHP application.
- SQLite database files (`.sqlite`, `.db`, or `.sqlite3`) placed in the same directory as the application.

## Installation
1. **Clone the Repository**:
   ```bash
   git clone https://github.com/rokhsati/sqlite-database-manager.git
   ```
2. **Navigate to the Project Directory**:
   ```bash
   cd sqlite-database-manager
   ```
3. **Set Up a Web Server**:
   - Configure a web server (e.g., Apache or Nginx) to serve the project directory.
   - Alternatively, use PHP's built-in server for development:
     ```bash
     php -S localhost:8000
     ```
4. **Place SQLite Files**:
   - Copy your SQLite database files (with `.sqlite`, `.db`, or `.sqlite3` extensions) into the project directory.

## Usage
1. Open the application in a web browser (e.g., `http://localhost:8000` if using PHP's built-in server).
2. Select a database file from the dropdown menu to view its tables.
3. Choose a table to display its columns and their data types.
4. Write and execute SQL queries in the query input area:
   - **SELECT queries**: Results are displayed in a responsive table.
   - **Other queries** (INSERT, UPDATE, DELETE, etc.): Success or error messages are shown, including the number of affected rows.
5. The interface is intuitive, with a dark theme and standard font sizes for a professional look.

## Screenshots
*(Optional: Add screenshots of the application here to showcase the UI.)*

## Contributing
Contributions are welcome! Please follow these steps:
1. Fork the repository.
2. Create a new branch (`git checkout -b feature/your-feature`).
3. Make your changes and commit them (`git commit -m 'Add your feature'`).
4. Push to the branch (`git push origin feature/your-feature`).
5. Open a pull request.

## License
This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Acknowledgments
- Built with [PHP](https://www.php.net/) and [Bootstrap 5.3](https://getbootstrap.com/).
- Inspired by Microsoft's modern application design principles.

## Contact
For questions or feedback, please open an issue on GitHub or contact [sadeghrokhsati@gmail.com](mailto:sadeghrokhsati@gmail.com).