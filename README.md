# Ticket4U - Event Ticketing Platform

A modern, professional event ticketing system built with PHP, MySQL, and vanilla JavaScript. Inspired by Ticket2U Malaysia but with enhanced UI/UX and features.

## Features

### User Features
- ✅ Beautiful, responsive modern UI
- ✅ Event browsing with advanced filters (category, location, date, price)
- ✅ Search functionality
- ✅ Event details with image gallery
- ✅ User authentication (login/register)
- ✅ Wishlist functionality
- ✅ Booking management
- ✅ User profile with booking history
- ✅ Secure payment integration (ready for Stripe/PayPal)

### Admin Features
- ✅ Admin dashboard
- ✅ Event management (CRUD operations)
- ✅ User management
- ✅ Booking overview and reporting
- ✅ Category management

### Technical Features
- Clean, modern codebase
- Responsive design (mobile-first)
- Secure authentication with password hashing
- SQL injection protection
- XSS protection
- Session management
- Flash messages
- Form validation

## Setup Instructions

### Prerequisites
- XAMPP/WAMP/LAMP (PHP 8.0+, MySQL 5.7+)
- Modern web browser

### Installation Steps

1. **Copy Project to XAMPP**
   ```bash
   Copy the entire `ticket4u_final` folder to: C:\xampp\htdocs\
   ```

2. **Create Database**
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Import the database:
     - Click "New" to create database (or use import)
     - Click "Import" tab
     - Choose file: `database.sql`
     - Click "Go"

3. **Configure Database Connection**
   - Open `config/config.php`
   - Update if needed (default settings work with XAMPP):
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_NAME', 'ticket4u');
     ```

4. **Create Upload Directories**
   Create these folders with write permissions:
   ```
   uploads/
   ├── events/
   ├── profiles/
   └── tickets/
   ```

5. **Set Folder Permissions**
   Make sure the `uploads/` folder is writable by the web server.

6. **Access the Application**
   - Homepage: http://localhost/ticket4u_final/
   - Admin Login: http://localhost/ticket4u_final/auth/login.php
     - Email: admin@ticket4u.com
     - Password: admin123
   - User Login: http://localhost/ticket4u_final/auth/login.php
     - Email: john@example.com
     - Password: user123

## Default Accounts

### Admin Account
- **Email:** admin@ticket4u.com
- **Password:** admin123

### Test User Account
- **Email:** john@example.com
- **Password:** user123

**Important:** Change these passwords in production!

## Project Structure

```
ticket4u_final/
├── assets/
│   ├── css/
│   │   └── style.css          # Main stylesheet
│   ├── js/
│   │   └── main.js            # Main JavaScript
│   └── images/
├── auth/
│   ├── login.php              # Login page
│   ├── register.php           # Registration page
│   └── logout.php             # Logout handler
├── admin/
│   ├── dashboard.php          # Admin dashboard
│   ├── events/                # Event management
│   ├── users/                 # User management
│   └── bookings/              # Booking management
├── profile/
│   ├── dashboard.php          # User dashboard
│   ├── profile.php            # Profile settings
│   └── wishlist.php           # User wishlist
├── config/
│   └── config.php             # Configuration & DB connection
├── includes/
│   ├── header.php             # Global header
│   └── footer.php             # Global footer
├── uploads/                   # User uploaded files
├── index.php                  # Homepage
├── events.php                 # Events listing
├── event-details.php          # Event details
├── booking.php                # Booking process
├── database.sql               # Database schema
└── README.md                  # This file
```

## Technology Stack

- **Backend:** PHP 8.0+
- **Database:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **Libraries:** 
  - jQuery 3.6.0
  - Font Awesome 6.4.0
  - Google Fonts (Inter)

## Features Overview

### Homepage
- Hero section with search
- Featured events carousel
- Category browsing
- Upcoming events grid
- Statistics display

### Events Listing
- Advanced filtering (category, city, date range)
- Sorting options
- Pagination
- Search functionality
- Wishlist integration

### Event Details
- Image gallery
- Complete event information
- Ticket type selection
- Booking button
- Share functionality

### User Dashboard
- Booking history
- Upcoming bookings
- Past bookings
- Ticket downloads (PDF)
- Profile management

### Admin Dashboard
- Overview statistics
- Event management
- User management
- Booking reports
- Revenue analytics

## Security Features

- Password hashing with bcrypt
- SQL injection protection (prepared statements)
- XSS protection (htmlspecialchars)
- CSRF protection (session tokens)
- Input validation and sanitization
- Secure session management

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Future Enhancements

- [ ] Payment gateway integration (Stripe/PayPal)
- [ ] QR code ticket generation
- [ ] Email notifications
- [ ] SMS notifications
- [ ] Multi-language support
- [ ] Event reviews and ratings
- [ ] Social media integration
- [ ] Analytics dashboard
- [ ] Export functionality (PDF reports)
- [ ] Calendar integration

## Customization

### Changing Colors
Edit `assets/css/style.css` and modify the CSS variables:
```css
:root {
    --primary-color: #6366f1;
    --primary-dark: #4f46e5;
    --primary-light: #818cf8;
    /* ... */
}
```

### Adding New Event Categories
1. Add category in database (categories table)
2. Icon uses Font Awesome classes
3. Category will appear automatically

### Modifying Booking Fee
Edit in `config/config.php`:
```php
define('BOOKING_FEE_PERCENTAGE', 0.05); // 5%
```

## Troubleshooting

### Database Connection Error
- Check MySQL is running in XAMPP
- Verify database credentials in `config/config.php`
- Ensure database `ticket4u` exists

### Images Not Displaying
- Check `uploads/` folder exists
- Verify folder permissions (writable)
- Check image paths in database

### Session Issues
- Clear browser cookies
- Check `session_start()` in config.php
- Verify PHP session settings

## Support

For issues or questions:
- Check this README
- Review code comments
- Test with default data

## License

This project is for educational/portfolio purposes.

## Credits

- Design inspired by Ticket2U Malaysia
- Icons by Font Awesome
- Fonts by Google Fonts

---
