-- Ticket4U Database Schema
-- Professional Event Ticketing System

DROP DATABASE IF EXISTS ticket4u;
CREATE DATABASE ticket4u CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ticket4u;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
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

-- Reviews table
CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    booking_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    UNIQUE KEY unique_booking_review (booking_id),
    INDEX idx_event (event_id)
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
INSERT INTO users (email, password, full_name, role, status) VALUES
('admin@ticket4u.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin', 'active');

-- Insert sample user (password: user123)
INSERT INTO users (email, password, full_name, phone, role) VALUES
('john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', '0123456789', 'user');

-- Insert sample events
INSERT INTO events (category_id, title, slug, description, featured_image, venue_name, venue_address, venue_city, event_date, event_time, organizer_name, featured, total_seats, available_seats, min_price, max_price) VALUES
(1, 'Rock Nation 2026 - Malaysia Tour', 'rock-nation-2026', 'Experience the biggest rock festival in Southeast Asia featuring international and local rock bands. A night filled with electrifying performances and amazing energy!', 'rock-concert.jpg', 'Stadium Merdeka', 'Jalan Stadium, Kuala Lumpur', 'Kuala Lumpur', '2026-03-15', '19:00:00', 'Rock Events Malaysia', TRUE, 5000, 5000, 150.00, 500.00),
(1, 'Jazz Under The Stars', 'jazz-under-stars', 'An intimate evening of smooth jazz performances by renowned Malaysian and international jazz artists.', 'jazz-concert.jpg', 'Istana Budaya', 'Jalan Tun Razak, Kuala Lumpur', 'Kuala Lumpur', '2026-02-20', '20:00:00', 'Jazz Society Malaysia', TRUE, 800, 800, 80.00, 250.00),
(2, 'Malaysia Football League Finals 2026', 'football-finals-2026', 'Witness the exciting finals of Malaysia Football League. Support your favorite team!', 'football.jpg', 'Bukit Jalil National Stadium', 'Bukit Jalil, Kuala Lumpur', 'Kuala Lumpur', '2026-04-10', '20:45:00', 'Football Association Malaysia', TRUE, 87000, 87000, 50.00, 300.00),
(3, 'Shakespeare In The Park: Romeo & Juliet', 'shakespeare-romeo-juliet', 'A modern adaptation of the classic love story performed under the stars.', 'theatre.jpg', 'Taman Botani Perdana', 'Jalan Kebun Bunga, Kuala Lumpur', 'Kuala Lumpur', '2026-03-05', '19:30:00', 'KL Theatre Society', FALSE, 500, 500, 60.00, 120.00),
(4, 'Rainforest Music Festival 2026', 'rainforest-music-2026', 'Three days of world music, cultural performances, and workshops set in the stunning Sarawak Cultural Village.', 'festival.jpg', 'Sarawak Cultural Village', 'Pantai Damai, Santubong', 'Kuching', '2026-07-18', '15:00:00', 'Sarawak Tourism Board', TRUE, 3000, 3000, 200.00, 600.00),
(5, 'Comedy Night Live with Harith Iskander', 'comedy-harith-2026', 'Laugh out loud with Malaysia\'s King of Comedy in an evening of hilarious stand-up.', 'comedy.jpg', 'Pavilion Theatre', 'Pavilion KL, Jalan Bukit Bintang', 'Kuala Lumpur', '2026-02-28', '20:00:00', 'Comedy Central Asia', FALSE, 600, 600, 88.00, 188.00);

-- Insert sample ticket types
INSERT INTO ticket_types (event_id, name, description, price, quantity, available, display_order) VALUES
(1, 'VIP Standing', 'Front row standing area with exclusive VIP lounge access', 500.00, 500, 500, 1),
(1, 'Premium Standing', 'Premium standing area with great view', 250.00, 1500, 1500, 2),
(1, 'General Admission', 'General standing area', 150.00, 3000, 3000, 3),
(2, 'VIP Seating', 'Premium seating with complimentary drinks', 250.00, 100, 100, 1),
(2, 'Category A', 'Excellent view seating', 150.00, 300, 300, 2),
(2, 'Category B', 'Good view seating', 80.00, 400, 400, 3),
(3, 'VIP Box', 'Private box for 4 people', 300.00, 200, 200, 1),
(3, 'Category 1', 'Best view seats', 150.00, 5000, 5000, 2),
(3, 'Category 2', 'Good seats', 100.00, 20000, 20000, 3),
(3, 'Category 3', 'Standard seats', 50.00, 61800, 61800, 4);
