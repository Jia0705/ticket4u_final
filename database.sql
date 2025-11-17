-- Ticket4U Database Schema
-- Event Ticketing System

DROP DATABASE IF EXISTS ticket4u;
CREATE DATABASE ticket4u CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ticket4u;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('user', 'admin') DEFAULT 'user',
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    profile_image VARCHAR(255) DEFAULT 'default-avatar.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB;

-- Categories table
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    icon VARCHAR(50),
    description TEXT,
    display_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    INDEX idx_slug (slug)
) ENGINE=InnoDB;

-- Events table
CREATE TABLE events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    featured_image VARCHAR(255),
    gallery JSON,
    venue_name VARCHAR(255) NOT NULL,
    venue_address TEXT,
    venue_city VARCHAR(100),
    venue_state VARCHAR(100),
    venue_country VARCHAR(100) DEFAULT 'Malaysia',
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    end_date DATE,
    end_time TIME,
    organizer_name VARCHAR(255),
    organizer_email VARCHAR(255),
    organizer_phone VARCHAR(20),
    status ENUM('draft', 'published', 'cancelled', 'completed') DEFAULT 'published',
    featured BOOLEAN DEFAULT FALSE,
    total_seats INT DEFAULT 0,
    available_seats INT DEFAULT 0,
    min_price DECIMAL(10,2),
    max_price DECIMAL(10,2),
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    INDEX idx_slug (slug),
    INDEX idx_category (category_id),
    INDEX idx_date (event_date),
    INDEX idx_featured (featured),
    INDEX idx_status (status),
    FULLTEXT idx_search (title, description, venue_name)
) ENGINE=InnoDB;

-- Ticket types table
CREATE TABLE ticket_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    available INT NOT NULL,
    max_per_order INT DEFAULT 10,
    sales_start DATETIME,
    sales_end DATETIME,
    display_order INT DEFAULT 0,
    status ENUM('active', 'inactive', 'sold_out') DEFAULT 'active',
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    INDEX idx_event (event_id)
) ENGINE=InnoDB;

-- Bookings table
CREATE TABLE bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    booking_reference VARCHAR(50) UNIQUE NOT NULL,
    total_tickets INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    booking_fee DECIMAL(10,2) DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('card', 'fpx', 'ewallet') NOT NULL,
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    payment_reference VARCHAR(255),
    booking_status ENUM('confirmed', 'cancelled', 'attended') DEFAULT 'confirmed',
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE RESTRICT,
    INDEX idx_user (user_id),
    INDEX idx_event (event_id),
    INDEX idx_reference (booking_reference),
    INDEX idx_payment_status (payment_status)
) ENGINE=InnoDB;

-- Booking items table
CREATE TABLE booking_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    ticket_type_id INT NOT NULL,
    ticket_number VARCHAR(100) UNIQUE NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    attendee_name VARCHAR(255),
    attendee_email VARCHAR(255),
    checked_in BOOLEAN DEFAULT FALSE,
    checked_in_at TIMESTAMP NULL,
    qr_code VARCHAR(255),
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (ticket_type_id) REFERENCES ticket_types(id) ON DELETE RESTRICT,
    INDEX idx_booking (booking_id),
    INDEX idx_ticket_number (ticket_number)
) ENGINE=InnoDB;

-- Wishlists table
CREATE TABLE wishlists (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist (user_id, event_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB;

-- Insert default categories
INSERT INTO categories (name, slug, icon, description, display_order) VALUES
('Concerts', 'concerts', 'fa-music', 'Live music performances and concerts', 1),
('Sports', 'sports', 'fa-football', 'Sports events and tournaments', 2),
('Theatre & Arts', 'theatre-arts', 'fa-theater-masks', 'Theatre shows, plays, and art exhibitions', 3),
('Festivals', 'festivals', 'fa-calendar-week', 'Music festivals and cultural celebrations', 4),
('Comedy', 'comedy', 'fa-laugh', 'Stand-up comedy and entertainment shows', 5),
('Family', 'family', 'fa-users', 'Family-friendly events and activities', 6),
('Conferences', 'conferences', 'fa-users-cog', 'Business conferences and seminars', 7),
('Exhibitions', 'exhibitions', 'fa-building', 'Trade shows and exhibitions', 8);

-- Insert default admin user (password: admin123)
INSERT INTO users (email, password, name, role, status) VALUES
('admin@ticket4u.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin', 'active');

-- Insert sample user (password: user123)
INSERT INTO users (email, password, name, phone, role) VALUES
('john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', '0123456789', 'user');

-- Insert sample events
INSERT INTO events (category_id, title, slug, description, featured_image, venue_name, venue_address, venue_city, event_date, event_time, organizer_name, featured, total_seats, available_seats, min_price, max_price) VALUES
(1, 'Coldplay: Music Of The Spheres World Tour', 'coldplay-malaysia-2026', 'Experience the magic of Coldplay live in Malaysia! The British rock band brings their spectacular Music Of The Spheres World Tour featuring hits like Yellow, Fix You, Viva La Vida, and more. A night filled with stunning visuals, eco-friendly production, and unforgettable music.', 'coldplay.jpg', 'Bukit Jalil National Stadium', 'Bukit Jalil, Kuala Lumpur', 'Kuala Lumpur', '2026-03-22', '20:00:00', 'Live Nation Malaysia', TRUE, 87000, 87000, 298.00, 888.00),
(1, 'Justin Bieber: Justice World Tour Malaysia', 'justin-bieber-2026', 'The global pop sensation Justin Bieber returns to Malaysia! Sing along to Peaches, Stay, Love Yourself, Sorry, and all your favorite hits. Don\'t miss this incredible once-in-a-lifetime performance!', 'justin-bieber.jpg', 'Axiata Arena', 'Jalan 3/155B, Bukit Jalil', 'Kuala Lumpur', '2026-04-15', '20:00:00', 'PR Worldwide', TRUE, 12000, 12000, 388.00, 1288.00),
(1, 'Maroon 5 Live in Kuala Lumpur', 'maroon-5-malaysia', 'Adam Levine and Maroon 5 are coming to Malaysia! Get ready for an amazing night with hits like Sugar, Moves Like Jagger, Girls Like You, Memories, and more. Their signature pop-rock sound will light up the night!', 'maroon5.jpg', 'Merdeka Stadium', 'Jalan Stadium, Kuala Lumpur', 'Kuala Lumpur', '2026-05-10', '20:00:00', 'IME Malaysia', TRUE, 45000, 45000, 268.00, 788.00),
(1, 'Keshi: HELL/HEAVEN Tour Malaysia', 'keshi-malaysia-2026', 'Vietnamese-American singer-songwriter Keshi brings his intimate and emotional performance to Malaysia. Experience live renditions of LIMBO, 2 soon, drunk, like i need u, and more from his soulful R&B catalog.', 'keshi.jpg', 'Zepp Kuala Lumpur', 'Jalan Tunku Abdul Rahman, KL', 'Kuala Lumpur', '2026-02-28', '20:00:00', 'Unusual Entertainment', TRUE, 3500, 3500, 188.00, 488.00),
(1, 'Ed Sheeran: Mathematics Tour 2026', 'ed-sheeran-malaysia', 'Ed Sheeran returns to Malaysia with his Mathematics Tour! The Grammy-winning artist performs Shape of You, Perfect, Thinking Out Loud, Bad Habits, and more. Just him, his guitar, and his iconic loop pedal.', 'ed-sheeran.jpg', 'Bukit Jalil National Stadium', 'Bukit Jalil, Kuala Lumpur', 'Kuala Lumpur', '2026-06-20', '20:00:00', 'AEG Presents Asia', TRUE, 87000, 87000, 328.00, 988.00),
(1, 'The Weeknd: After Hours til Dawn Tour', 'the-weeknd-malaysia', 'Abel Tesfaye aka The Weeknd brings his cinematic After Hours til Dawn stadium tour to Malaysia! Experience Blinding Lights, Starboy, Save Your Tears, and The Hills with jaw-dropping production and visuals.', 'the-weeknd.jpg', 'Bukit Jalil National Stadium', 'Bukit Jalil, Kuala Lumpur', 'Kuala Lumpur', '2026-07-18', '20:00:00', 'Live Nation Asia', TRUE, 87000, 87000, 358.00, 1088.00),
(1, 'Bruno Mars: An Evening with Silk Sonic', 'bruno-mars-malaysia', 'Bruno Mars is bringing the party to Malaysia! Get ready for non-stop energy with 24K Magic, Uptown Funk, Just The Way You Are, Locked Out of Heaven, and more. The ultimate feel-good concert experience!', 'bruno-mars.jpg', 'Axiata Arena', 'Jalan 3/155B, Bukit Jalil', 'Kuala Lumpur', '2026-08-15', '20:00:00', 'AEG Presents', TRUE, 12000, 12000, 398.00, 1188.00);

-- Insert sample ticket types
INSERT INTO ticket_types (event_id, name, description, price, quantity, available, display_order) VALUES
(1, 'A Reserve', 'Best view seats - front sections with perfect stage view', 888.00, 5000, 5000, 1),
(1, 'B Reserve', 'Great view seats - excellent sightlines', 588.00, 15000, 15000, 2),
(1, 'C Reserve', 'Good view seats - standard stadium seating', 398.00, 30000, 30000, 3),
(1, 'D Reserve', 'Economy seats - upper level with full stage view', 298.00, 37000, 37000, 4),
(2, 'VIP Package', 'Premium seating, exclusive merchandise, and meet & greet opportunity', 1288.00, 500, 500, 1),
(2, 'Rock Zone', 'Standing area close to stage - best energy!', 788.00, 3000, 3000, 2),
(2, 'Platinum Seating', 'Premium seated section with excellent view', 588.00, 2500, 2500, 3),
(2, 'Gold Seating', 'Great seated section', 388.00, 6000, 6000, 4),
(3, 'Rock Pit', 'Standing area in front of stage - closest to the band!', 788.00, 5000, 5000, 1),
(3, 'Premium Seating', 'Best seated sections with perfect view', 488.00, 10000, 10000, 2),
(3, 'Standard Seating', 'Good seated sections', 368.00, 15000, 15000, 3),
(3, 'Gallery', 'Upper level seating - full stage view', 268.00, 15000, 15000, 4),
(4, 'VIP Experience', 'Priority entry, exclusive merch, best viewing area', 488.00, 300, 300, 1),
(4, 'Standing GA', 'General admission standing - close to stage', 288.00, 1700, 1700, 2),
(4, 'Seated', 'Seated section with good view', 188.00, 1500, 1500, 3),
(5, 'Diamond', 'Closest to stage - ultimate experience', 988.00, 5000, 5000, 1),
(5, 'Platinum', 'Premium lower bowl seating', 688.00, 15000, 15000, 2),
(5, 'Gold', 'Great mid-level seating', 488.00, 30000, 30000, 3),
(5, 'Silver', 'Upper level with full view', 328.00, 37000, 37000, 4),
(6, 'VIP Floor', 'Floor standing closest to stage with VIP entry', 1088.00, 5000, 5000, 1),
(6, 'Floor Standing', 'General floor standing area', 688.00, 15000, 15000, 2),
(6, 'Lower Bowl', 'Seated lower bowl sections', 488.00, 30000, 30000, 3),
(6, 'Upper Bowl', 'Seated upper sections', 358.00, 37000, 37000, 4),
(7, 'VIP Package', 'Floor seats, merchandise, VIP lounge access', 1188.00, 1000, 1000, 1),
(7, 'Floor A', 'Premium floor seating - front sections', 788.00, 2500, 2500, 2),
(7, 'Floor B', 'Floor seating - mid sections', 588.00, 2500, 2500, 3),
(7, 'Lower Tier', 'Stadium lower tier seating', 398.00, 6000, 6000, 4);
