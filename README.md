![Reid Hub Banner](./public/assets/images/logo.png)

# ReidHub


## About ReidHub

ReidHub is an all-in-one platform built for campus communities, featuring marketplace functionality, academic resource sharing, community engagement tools, and a lost & found system. The platform is designed to enhance campus life by providing essential digital services in one centralized location.

> ⚠️ This project is under active development and not ready for public use.

---

## 📖 Docs

Here is a comprehensive documentation for the codebase and guides on how to set up dev environment 

- [Setting Up Development Environment](./docs/dev-set-up.md)
- [Contribution Guide](./docs/contribution.md)
- [Development Issues and Troubleshooting Guide](./docs/dev-troubleshoot.md)
- [Architechure](./docs/architecture.md)
- [Frontend Guide](./docs/frontend-guide.md)
- [Database Schema](./docs/database-schema.md)

---

## 🚀 Features

### 🛒 Market Place Module

- **Merch Store**: Post, review, and purchase merchandise from campus sellers
- **Secondhand Marketplace**: Buy and sell pre-owned items within the community
- **Pro Account Share**: Connect users to share subscription costs for premium services

### 📚 Academic Resources Module

- **Edu Video Archive**: A library of educational videos and resources
- **Edu Tracker**: Subscribe to modules and receive notifications for new content
- **Study Forum**: Ask questions, provide answers, and engage in academic discussions

### 🤝 Community and Social Module

- **Blog Posts**: Create and share blog content with the community
- **Shared Event Calendar**: View and submit campus events
- **Clubs and Societies Pages**: Dedicated spaces for campus organizations

### 🔍 Lost and Found System

- Submit lost or found item reports
- Browse listings with filtering options
- Automated notifications for critical items

## 💻 Technologies

- **Hack** (63.3%)
- **PHP** (23.9%)
- **CSS** (10.3%)
- **JavaScript** (2.2%)
- **Docker** (0.3%)

## 🔧 Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/dhannajaya17686/reidhub.git
   cd reidhub
   ```

2. Set up using Docker:
   ```bash
   docker-compose up -d
   ```

3. Configure environment settings:
   ```bash
   cp .env.example .env
   # Edit .env with your configuration
   ```

4. Install dependencies:
   ```bash
   composer install
   npm install
   ```

5. Run database migrations:
   ```bash
   php artisan migrate
   ```

## 📘 Usage

After installation, access the platform at `http://localhost:8000` (or your configured domain).

### Admin Access
- Default admin credentials: 
  - Email: `admin@reidhub.local`
  - Password: `password`
  - **Important**: Change these credentials immediately after first login

### User Roles
- **Regular User**: Can access marketplace, academic resources, and community features
- **Club Admin**: Can manage specific club/society pages
- **Admin**: Has full access to moderation and platform management

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/amazing-feature`
3. Commit your changes: `git commit -m 'Add some amazing feature'`
4. Push to the branch: `git push origin feature/amazing-feature`
5. Open a pull request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 📞 Contact

For questions or support, please reach out to the project maintainers.