# natel Enterprise Website

A modern, professional enterprise website with a comprehensive admin panel for content management.

## Features

### Public Website
- Responsive design with mobile-first approach
- Home page with hero section and services overview
- About page with company information
- Services page showcasing offerings
- Projects portfolio with filtering
- Contact page with form and map integration
- SEO-friendly structure

### Admin Panel
- User authentication and authorization
- Site settings management
- Services management (CRUD operations)
- Projects management with image uploads
- Contact form message management
- User management system
- About page content management
- Dashboard with analytics

## Technology Stack

### Frontend
- React 18 with Vite
- Tailwind CSS for styling
- React Router for navigation
- Axios for API communication
- Framer Motion for animations
- React Hook Form for form handling
- React Hot Toast for notifications

### Backend
- PHP 8+ with MVC architecture
- MySQL database
- PDO for database operations
- JWT for authentication
- RESTful API design
- File upload handling

## Project Structure

```
natel/
├── frontend/                 # React frontend application
│   ├── src/
│   │   ├── components/       # Reusable UI components
│   │   ├── pages/           # Page components
│   │   ├── services/        # API service functions
│   │   ├── assets/          # Images, icons, styles
│   │   └── layouts/         # Layout wrappers
│   ├── public/
│   └── package.json
├── backend/                 # PHP backend API
│   ├── api/
│   │   ├── controllers/     # API controllers
│   │   ├── models/          # Data models
│   │   ├── routes/          # API route definitions
│   │   ├── middlewares/     # Authentication, validation
│   │   ├── config/          # Configuration files
│   │   └── uploads/         # File uploads directory
│   ├── index.php
│   └── .htaccess
├── docs/                    # Documentation
│   └── deployment.md
└── database.sql             # Database schema
```

## Installation

### Prerequisites
- PHP 8.0+
- MySQL 5.7+
- Node.js 16+
- npm

### Backend Setup
1. Navigate to the backend directory
2. Install PHP dependencies: `composer install`
3. Update database configuration in `api/config/core.php`
4. Import the database schema: `mysql -u [username] -p infinity_enterprise < database.sql`

### Frontend Setup
1. Navigate to the frontend directory
2. Install dependencies: `npm install`
3. Build the project: `npm run build`

## API Endpoints

### Authentication
- `POST /api/login` - User login

### Public Endpoints
- `GET /api/settings` - Get site settings
- `GET /api/services` - Get active services
- `GET /api/projects` - Get active projects
- `GET /api/about` - Get about page content
- `POST /api/messages` - Submit contact form

### Admin Endpoints (require JWT token)
- `GET /api/services` - Get all services
- `POST /api/services` - Create service
- `PUT /api/services/{id}` - Update service
- `DELETE /api/services/{id}` - Delete service
- `GET /api/projects` - Get all projects
- `POST /api/projects` - Create project
- `PUT /api/projects/{id}` - Update project
- `DELETE /api/projects/{id}` - Delete project
- And more for messages, users, and settings

## Admin Credentials

Default admin user is created during database setup:
- Email: `admin@infinity-enterprise.com`
- Password: `password123` (hashed in database)

## Development

### Frontend Development
1. Navigate to the frontend directory
2. Run `npm run dev` to start the development server

### Backend Development
1. Ensure your web server can serve PHP files
2. Access the API endpoints via your configured domain

## Deployment

See `docs/deployment.md` for detailed deployment instructions.

## Security

- Passwords are hashed using PHP's password_hash function
- JWT tokens with expiration for authentication
- Input validation and sanitization
- Prepared statements to prevent SQL injection
- File upload validation and secure storage

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## License

This project is licensed under the MIT License.

## Support

For support, please contact the development team or open an issue in the repository.