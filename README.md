🚗 RENTAL. | Premium Car Subscription Platform
RENTAL. is a high-end web application designed for luxury car rentals and subscriptions. Moving away from the "utility-only" rental model, this platform focuses on the experience of driving premium vehicles, offering a seamless interface for both customers and car owners.

✨ Key Features
🏢 For Customers
Experience-Driven Search: A streamlined search bar focused on car models and performance types.

Premium Fleet Access: A curated gallery of high-performance vehicles with AOS (Animate On Scroll) effects.

Simplified Booking: One-click navigation from search to authentication to booking.

Modern Auth UX: Secure Login and Registration with real-time password visibility toggles and glassmorphism design.

🔑 For Car Owners & Admins
Role-Based Access Control (RBAC): Dedicated dashboards for Customers, Owners, and Administrators.

Secure Authentication: Password hashing using PASSWORD_DEFAULT and protection against SQL injection.

Fleet Management: (In progress) Interface for owners to list and manage their vehicles.

🛠️ Tech Stack
Frontend: HTML5, CSS3 (Custom Landing Styles), Bootstrap 5.3.

Interactivity: Remix Icon (SVG Icons), AOS (Animate on Scroll), JavaScript (ES6).

Backend: PHP (Procedural/Functional Hybrid).

Database: MySQL with MySQLi Prepared Statements for security.

Design Philosophy: Modern, bold typography, asymmetric layouts, and premium dark/yellow contrast.

📂 Project Structure
Plaintext

├── assets/             # Images, car photos, and brand logos
├── auth/               # login.php, register.php
├── config/             # db.php (Database connection)
├── css/                # landing.css (Main design file)
├── dashboard/          # Role-specific dashboard logic
│   ├── admin_dashboard.php
│   ├── customer_dashboard.php
│   └── owner_dashboard.php
└── index.php           # Main landing page
🚀 Installation & Setup
Clone the repository:

Bash

git clone https://github.com/mgarikalfn/rental-car-system.git
Database Setup:

Create a database named rental_db.

Import the provided SQL schema (if applicable) or create a users table with fields: id, name, email, password, role.

Configure Environment:

Open config/db.php and update your database credentials:

PHP

// Example credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'rental_db');
Run the project:

Move the folder to your htdocs (XAMPP) or www (WAMP) directory.

Open http://localhost/rental-car-system in your browser.

💡 Philosophy: "Not Just a Car"
The slogan "Drive the Experience, Not Just a Car" drives the UI/UX of this project. It implies:

Prestige: Every visual element is designed to feel expensive and exclusive.

Ease: The user shouldn't struggle with forms; the path from choosing a car to "keys in hand" is minimal.

Emotion: High-quality imagery and smooth transitions evoke the feeling of speed and luxury.

📜 License
This project is licensed under the MIT License - see the LICENSE file for details.