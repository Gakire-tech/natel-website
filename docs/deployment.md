# Infinity Enterprise - Deployment Documentation

## Project Overview
Infinity Enterprise is a modern, professional enterprise website built with React frontend and PHP backend. The application features a public-facing website and a comprehensive admin panel for content management.

## Technology Stack
- **Frontend**: React (Vite), Tailwind CSS, React Router, Axios
- **Backend**: PHP 8+, MySQL, PDO, JWT Authentication
- **Architecture**: Separate frontend/backend with REST API

## Prerequisites
- Web server with PHP 8+ support (Apache/Nginx)
- MySQL 5.7+
- Node.js 16+ and npm
- Composer (for PHP dependencies)

## Installation Steps

### 1. Backend Setup

#### Database Configuration
1. Create a new MySQL database:
   ```sql
   CREATE DATABASE infinity_enterprise;
   ```

2. Import the database schema:
   ```bash
   mysql -u [username] -p infinity_enterprise < database.sql
   ```

#### PHP Backend Configuration
1. Navigate to the backend directory:
   ```bash
   cd /path/to/infinity/backend
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Update database configuration in `api/config/core.php`:
   ```php
   define('DB_HOST', 'localhost');        // Your database host
   define('DB_NAME', 'infinity_enterprise'); // Your database name
   define('DB_USER', 'your_username');    // Your database username
   define('DB_PASS', 'your_password');    // Your database password
   ```

4. Update JWT secret in `api/config/core.php`:
   ```php
   define('JWT_SECRET_KEY', 'your_strong_secret_key_here');
   ```

5. Ensure the uploads directory has write permissions:
   ```bash
   chmod 755 uploads/
   ```

### 2. Frontend Setup

#### React Frontend Configuration
1. Navigate to the frontend directory:
   ```bash
   cd /path/to/infinity/frontend
   ```

2. Install dependencies:
   ```bash
   npm install
   ```

3. Update API base URL in `src/services/api.js`:
   ```javascript
   const API_BASE_URL = 'https://yourdomain.com/backend';
   ```

4. Build the frontend:
   ```bash
   npm run build
   ```

## Server Configuration

### Apache Configuration
Create a `.htaccess` file in the backend root directory:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

### Nginx Configuration
Add the following to your Nginx server block:

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/infinity/frontend/dist;
    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }

    location /backend {
        alias /path/to/infinity/backend;
        try_files $uri $uri/ /backend/index.php$is_args$args;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $request_filename;
    }
}
```

## Environment Configuration

### Backend Environment Variables
Create a `.env` file in the backend directory (requires vlucas/phpdotenv):
```
DB_HOST=localhost
DB_NAME=infinity_enterprise
DB_USER=your_username
DB_PASS=your_password
JWT_SECRET_KEY=your_strong_secret_key_here
```

### Frontend Environment Variables
Create a `.env` file in the frontend directory:
```
VITE_API_BASE_URL=https://yourdomain.com/backend
```

## Security Considerations

1. **API Security**:
   - Use HTTPS in production
   - Implement rate limiting
   - Validate and sanitize all inputs
   - Use prepared statements to prevent SQL injection

2. **File Upload Security**:
   - Validate file types and sizes
   - Store uploads outside the web root
   - Rename files to prevent execution

3. **Authentication**:
   - Use strong JWT secrets
   - Implement token expiration
   - Secure sensitive routes

## Deployment Checklist

### Before Going Live
- [ ] Update database credentials
- [ ] Set strong JWT secret
- [ ] Configure proper domain URLs
- [ ] Set file permissions
- [ ] Enable HTTPS
- [ ] Test all functionality
- [ ] Optimize images and assets
- [ ] Set up error logging

### Post-Deployment
- [ ] Verify admin login works
- [ ] Test contact form functionality
- [ ] Confirm all pages load correctly
- [ ] Check API endpoints are working
- [ ] Verify file uploads work
- [ ] Test responsive design on mobile

## Troubleshooting

### Common Issues

1. **API calls failing**:
   - Check CORS settings
   - Verify API URL in frontend
   - Ensure PHP error reporting is enabled during debugging

2. **File uploads not working**:
   - Check directory permissions
   - Verify upload size limits in PHP config
   - Ensure upload directory exists

3. **Database connection errors**:
   - Verify database credentials
   - Check database server status
   - Confirm database exists and is accessible

### Error Logs
- Frontend: Browser console and build logs
- Backend: Server error logs and PHP error logs
- Database: MySQL error logs

## Maintenance

### Regular Tasks
- Backup database regularly
- Update dependencies periodically
- Monitor site performance
- Review security logs
- Update content as needed

### Updating the Application
1. Backup current version
2. Deploy new frontend build
3. Update backend files
4. Run database migrations if needed
5. Test all functionality

## Support and Contact

For technical support or questions about deployment:
- Email: support@infinity-enterprise.com
- Documentation: https://docs.infinity-enterprise.com

---

**Note**: Always test changes in a staging environment before deploying to production.