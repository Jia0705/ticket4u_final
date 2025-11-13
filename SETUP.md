# QUICK SETUP GUIDE

Follow these steps to set up Ticket4U on your local machine:

## Step 1: Import Database

1. Open your web browser and go to: **http://localhost/phpmyadmin**
2. Click "Import" tab in the top menu
3. Click "Choose File" and select: `database.sql`
4. Scroll down and click "Go" button
5. Wait for success message: "Import has been successfully finished"

## Step 2: Access the Website

### Homepage
**URL:** http://localhost/ticket4u_final/

### Login as Admin
**URL:** http://localhost/ticket4u_final/auth/login.php
- **Email:** admin@ticket4u.com
- **Password:** admin123

### Login as User
**URL:** http://localhost/ticket4u_final/auth/login.php
- **Email:** john@example.com  
- **Password:** user123

### Register New Account
**URL:** http://localhost/ticket4u_final/auth/register.php

## Step 3: Test the System

### Test User Features:
1. ✅ Browse events on homepage
2. ✅ Filter events by category
3. ✅ Search for events
4. ✅ View event details
5. ✅ Register a new account
6. ✅ Login to your account
7. ✅ Book event tickets (once admin adds events)

### Test Admin Features:
1. ✅ Login as admin
2. ✅ Access admin dashboard
3. ✅ Manage events (Add/Edit/Delete)
4. ✅ Manage users
5. ✅ View booking reports

## Troubleshooting

### Problem: "Database connection failed"
**Solution:** 
- Make sure XAMPP Apache and MySQL are running
- Check if database `ticket4u` exists in phpMyAdmin
- Verify credentials in `config/config.php`

### Problem: "Images not showing"
**Solution:** 
- Place placeholder images in `uploads/events/` folder
- Or update image paths in the database

### Problem: "Can't access admin dashboard"
**Solution:**
- Make sure you're logged in as admin
- Use email: admin@ticket4u.com, password: admin123

### Problem: "Page not found"
**Solution:**
- Check project is in correct folder: `C:\xampp\htdocs\ticket4u_final\`
- Make sure Apache is running in XAMPP

## Default Sample Data

The database comes with:
- ✅ 8 Event Categories
- ✅ 6 Sample Events with details
- ✅ Multiple ticket types for each event
- ✅ 1 Admin account
- ✅ 1 Test user account

## Next Steps

1. **Customize the design**: Edit `assets/css/style.css`
2. **Add real events**: Login as admin and add your events
3. **Upload event images**: Place images in `uploads/events/` folder
4. **Test booking flow**: Try booking tickets as a user
5. **Explore admin features**: Manage events, users, and bookings

## Important Notes

⚠️ **Security**: Change default passwords in production!
⚠️ **Images**: Add real event images to `uploads/events/` folder
⚠️ **Email**: Configure SMTP settings for email notifications (future enhancement)

## Need Help?

1. Check `README.md` for detailed documentation
2. Review code comments in PHP files
3. Test with default accounts first

---

**You're all set! 🎉**

Visit: http://localhost/ticket4u_final/
