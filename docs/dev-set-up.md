# Developer Environment Setup Guide

This project uses **Docker**, **PHP**, **Nginx**, and **MySQL** to set up a complete development environment in seconds.

---

## 🚀 Getting Started

### 1. Clone the Repository

```bash
git clone https://github.com/dhannajaya17686/reidhub.git
cd reidhub
```

### 2. Run the Containers

```bash
docker-compose up --build
```

This command does the following:
- 🐘 Starts a MySQL database server
- 🐳 Builds and runs your PHP application
- 🌐 Sets up Nginx as a web server (exposed on http://localhost:8080)
- 📊 Provides phpMyAdmin for database management (exposed on http://localhost:8081)
- 🗃️ Persists your MySQL data in a named Docker volume (dbdata)

### 3. Main Considerations

Please do not change the docker code. Please change only if it is necessary.

---

## ⚙️ Configuration (Docker & Database)

### 🧾 Environment Variable Summary

| Variable              | Value          | Description                                                    |
| --------------------- | -------------- | -------------------------------------------------------------- |
| `MYSQL_ROOT_PASSWORD` | `root`         | Root password for MySQL                                        |
| `MYSQL_DATABASE`      | `reidhub`      | The default database name created on container startup         |
| `MYSQL_USER`          | `reidhubuser`  | Username for application database access                       |
| `MYSQL_PASSWORD`      | `reidhubpass`  | Password for the application database user                     |
| `MySQL Port`          | `3307`         | MySQL runs on this port (mapped to localhost:3307)            |
| `Web Port`            | `8080`         | Nginx web server port                                          |
| `phpMyAdmin Port`     | `8081`         | phpMyAdmin interface port                                      |

Your PHP application can connect to MySQL using:

```php
$host = 'db';
$dbname = 'reidhub';
$username = 'reidhubuser';
$password = 'reidhubpass';
$port = 3306;
```

### 🌐 Accessing the Services

- **Web Application** → http://localhost:8080
- **phpMyAdmin** → http://localhost:8081
  - Username: `root`
  - Password: `root`
- **MySQL Database** → Connect to localhost:3307 using:
  - Root Username: `root`
  - Root Password: `root`
  - App Username: `reidhubuser`
  - App Password: `reidhubpass`
  - Database: `reidhub`

---

## 🧹 Maintenance Commands

### 🐳 Docker Commands

| Action                         | Command                                               |
| ------------------------------ | ----------------------------------------------------- |
| Start containers               | `docker-compose up --build`                          |
| Start containers (after build) | `docker-compose up`                                   |
| Stop containers                | `docker-compose down`                                 |
| Stop & remove volumes          | `docker-compose down -v`                             |
| Force remove a container       | `sudo docker rm -f <container-id>`                   |
| View running containers        | `docker ps`                                           |
| View logs for PHP app          | `docker-compose logs app`                            |
| View logs for Nginx            | `docker-compose logs nginx`                          |
| View logs for MySQL            | `docker-compose logs db`                             |
| Execute command in PHP app     | `docker-compose exec app <your-command>`             |
| Open a shell in the PHP app    | `docker-compose exec app sh`                         |
| Login to MySQL CLI             | `mysql -h localhost -P 3307 -u root -p`              |

For MySQL CLI access, it will prompt you for the password (use `root` for the root user).

#### 🧠 Quick Recap of `mysql` CLI Flags

| Flag | Description                                |
| ---- | ------------------------------------------ |
| `-h` | Host (e.g., `localhost` or container name) |
| `-P` | Port (`3307` for external access)          |
| `-u` | Username (e.g., `root` or `reidhubuser`)   |
| `-p` | Prompt for password                        |

### 🐘 Useful MySQL CLI Commands

Once you're logged in to MySQL using:

```bash
mysql -h localhost -P 3307 -u root -p
```

Once inside the MySQL CLI, you can use the following commands:

| Action                                  | Command                                                  |
| --------------------------------------- | -------------------------------------------------------- |
| List all databases                      | `SHOW DATABASES;`                                        |
| Use a specific database                 | `USE reidhub;`                                           |
| List all tables in the current database | `SHOW TABLES;`                                           |
| Show table structure/schema             | `DESCRIBE table_name;`                                   |
| List all users                          | `SELECT User, Host FROM mysql.user;`                    |
| Create a new table                      | `CREATE TABLE users (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255));` |
| View records from a table               | `SELECT * FROM users;`                                   |
| Insert data into a table                | `INSERT INTO users (name) VALUES ('Alice');`            |
| Delete a record                         | `DELETE FROM users WHERE id = 1;`                       |
| Exit the MySQL CLI                      | `exit;`                                                  |

> 💡 Tip: All SQL statements must end with a semicolon (`;`).

### 📊 phpMyAdmin Access

For a more user-friendly database management experience, access phpMyAdmin at http://localhost:8081:

- **Server:** `db` (automatically configured)
- **Username:** `root`
- **Password:** `root`

This provides a web-based interface for managing your MySQL database, creating tables, running queries, and more.

---

## 🔧 Development Tips

- Your PHP files are mounted in the `/var/www/html` directory inside the container
- Changes to your PHP code are reflected immediately (no need to rebuild)
- Nginx configuration can be found in `./nginx/default.conf`
- Database data persists between container restarts thanks to the `dbdata` volume

---

## 👥 Test User Accounts

### Admin Account (Auto-created)

| Field | Value |
|-------|-------|
| Email | `admin@reidhub.com` |
| Password | `admin@reid123` |
| Created | Automatically when database initializes |
| Encrypted Password | `$2y$10$xCO6LXIp9wlsb07zLP9pUeB/6Xg7.etvN0k/fWY9HvftBKgf8L6CS` |

---

### Test User Accounts (Manual Creation)

For testing and development, create these three user accounts **manually** in the application:

#### User 1

| Field | Value |
|-------|-------|
| Email | `user1@reidhub.com` |
| Password | `user1@reid123` |
| Registration No | `2024IS001` |
| First Name | `Test` |
| Last Name | `User One` |

#### User 2

| Field | Value |
|-------|-------|
| Email | `user2@reidhub.com` |
| Password | `user2@reid123` |
| Registration No | `2024IS002` |
| First Name | `Test` |
| Last Name | `User Two` |

#### User 3

| Field | Value |
|-------|-------|
| Email | `user3@reidhub.com` |
| Password | `user3@reid123` |
| Registration No | `2024IS003` |
| First Name | `Test` |
| Last Name | `User Three` |

