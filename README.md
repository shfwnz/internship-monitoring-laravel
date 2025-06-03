# SimPKL - Internship Monitoring System

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="200" alt="Laravel Logo">
</p>

SimPKL is a comprehensive internship monitoring system built with Laravel and Filament for the backend/admin panel, and Vue.js for the frontend interface. This system helps educational institutions manage and monitor student internships with role-based permissions and real-time tracking capabilities.

## Features

- **Role-Based Access Control**: Multi-level user management with permissions
- **Student Management**: Track student internship status and details
- **Industry Partnership**: Manage industry partners and their business fields
- **Teacher Supervision**: Assign teachers to supervise student internships
- **Real-time Monitoring**: Track internship progress and documentation
- **API Integration**: RESTful API for frontend integration
- **File Management**: Handle internship documentation and reports
- **Database Triggers**: Automatic student status updates

## Tech Stack

### Backend
- **Laravel** - PHP web application framework
- **Filament** - Admin panel and dashboard
- **Spatie Laravel Permission** - Role and permission management
- **Laravel Sanctum** - API authentication
- **MySQL** - Database management

### Frontend
- **Vue.js** - Progressive JavaScript framework
- **Repository**: [Internship Monitoring Vue](https://github.com/shfwnz/internship-monitoring-vue.git)

## Database Schema

The system includes the following main entities:
- **Users**: System users with polymorphic role relationships
- **Students**: Student information with internship status
- **Teachers**: Teacher information and supervision assignments
- **Industries**: Industry partners with business field categorization
- **Internships**: Core internship records with relationships
- **Business Fields**: Industry categorization

## Prerequisites

- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL/MariaDB
- Git

## Installation

### Backend Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/shfwnz/internship-monitoring-laravel.git backend
   cd backend
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Environment configuration**
   ```bash
   cp .env.example .env
   ```
   
   Update your `.env` file with database credentials and other configuration:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=simpkl
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

4. **Generate application key**
   ```bash
   php artisan key:generate
   ```

5. **Generate JWT secret** (if using JWT authentication)
   ```bash
   php artisan jwt:secret
   ```

6. **Run database migrations**
   ```bash
   php artisan migrate --seed
   ```

7. **Install Filament Shield for permissions**
   ```bash
   php artisan shield:install admin
   php artisan shield:generate --all
   ```

8. **Start the development server**
    ```bash
    php artisan serve --host=0.0.0.0 --port=8000
    ```

### Frontend Setup

1. **Clone the Vue.js frontend repository**
   ```bash
   git clone https://github.com/shfwnz/internship-monitoring-vue.git frontend
   cd frontend
   ```

2. **Install Node.js dependencies**
   ```bash
   npm install
   ```

3. **Configure API endpoint**
   Update the API base URL in your Vue.js configuration to point to your Laravel backend.

4. **Start the development server**
   ```bash
   npm run dev
   ```

## Usage

### Admin Panel Access
- Navigate to `ip_addr:8000/admin` to access the Filament admin panel
- Login with the `superadmin@exmaple.com` and password `12345678`
- Manage users, students, teachers, industries, and internships

### API Endpoints
The system provides RESTful API endpoints for:
- User authentication and authorization
- Student management
- Internship tracking
- Industry and teacher management
- File uploads and document management

### Frontend Interface
- Access the Vue.js frontend for student and teacher interfaces
- Real-time internship monitoring and status updates
- Document submission and progress tracking

## Database Triggers

The system includes automatic triggers that:
- Set student status to `true` when an internship is created
- Set student status to `false` when an internship is deleted

## Role-Based Permissions

The system supports multiple user roles:
- **Super Admin**: Full system access
- **Teacher**: Student supervision and monitoring
- **Student**: Personal internship management
- **Custom**: Custom role name and permission

## File Structure

```
├── app/
│   ├── Http/Controllers/     # API Controllers
│   ├── Models/              # Eloquent Models
│   └── Filament/           # Filament Resources
├── database/
│   ├── migrations/         # Database Migrations
│   └── seeders/           # Database Seeders
├── routes/
│   ├── api.php            # API Routes
│   └── web.php            # Web Routes
└── README.md
```

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## Related Repositories

- **Frontend (Vue.js)**: [Internship Monitoring Vue](https://github.com/shfwnz/internship-monitoring-vue.git)

## Support

For support and questions, please open an issue in the repository or contact the development team.

---

**SimPKL** - Simplifying internship monitoring for educational institutions.