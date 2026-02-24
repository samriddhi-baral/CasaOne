# Hostel Management System

Full working backend: **login**, **signup**, **admin panel**, **user panel**, **booking**, **user registration**, **session**, **cookie**, **hashing**.

## Database

- **database/project.sql** – Single schema file: all tables (admin, users, hostel, room, roomtype, complaint, payment, booking, feedback, faq, etc.).

### Setup

1. **XAMPP**: Start **Apache** and **MySQL**.
2. **phpMyAdmin**: Create database `project` (or use existing).
3. Import **database/project.sql** (creates database and all tables).
4. **config/database.php**: Set `DB_NAME` to `project` (already set). Edit host/user/pass if needed.

## Features (unchanged from your frontend)

- **Info** – Home, facilities, study hall, FAQ  
- **Facilities** – Toilet, food routine, laundry, 24hrs electricity, WiFi  
- **Rooms** – Room list (from `room` + `roomtype` + `hostel`), fee by type  
- **Fees** – Fee table from room/roomtype  
- **Admission** – User registration (inserts `userregistration` + `users`, **password hashing**)  
- **Login** – **User** or **Admin** (session + **Remember me** cookie for users)  
- **Session & cookie** – PHP sessions; optional remember-me uses `remember_tokens`  
- **Hashing** – `password_hash()` / `password_verify()` for users and admin (admin plain password is hashed on first login)  
- **Booking** – User books room (table `booking`), Pay & Confirm → Check-in → Check-out in My Bookings  
- **Complaint** – User submits (uses `complaint.u_id`)  
- **Feedback** – User feedback (table `feedback`)  

## Admin panel

- **URL**: `http://localhost/project/admin/` or `admin/login.php`  
- **Login**: Select “Admin” on main login, or go to **admin/login.php**.  
- **Default admin**: `admin@gmail.com` / `admin@123` (first login hashes this password).  
- **Dashboard** – Counts: hostels, rooms, users, students, bookings, complaints, payments  
- **Hostels** – Add/delete hostels  
- **Rooms** – Add rooms (hostel, room_no, price, availability, type, capacity), update availability, delete  
- **Students** – Add/delete students (optional room assignment)  
- **Bookings** – List all user bookings  
- **Complaints** – List complaints, update status (open / in_progress / resolved)  
- **Payments** – List payments, total  
- **Users** – List registered users (userregistration + users)  

## User panel

- **Signup**: Admission form → inserts `userregistration` (reg_date, gender) + `users` (u_name, u_email, u_phone, u_address, u_password hashed).  
- **Login**: User login uses `users.u_email` + `users.u_password` (verify with `password_verify`).  
- **Booking**: Select room (from `room` where availability), check-in date → insert `booking`.  
- **My Bookings**: Pay & Confirm (inserts `payment`), Check-in, Check-out.  
- **Complaint**: Insert into `complaint` (u_id if column exists).  
- **Feedback**: Insert into `feedback`.  

## Structure

```
project/
├── config/database.php       # DB name: project
├── database/
│   └── project.sql            # Complete schema (single import)
├── includes/
│   ├── auth.php               # Session, cookie, hashing; isAdmin(), isLoggedInAsUser()
│   ├── header.php
│   └── footer.php
├── admin/
│   ├── login.php              # Admin login
│   ├── index.php              # Dashboard
│   ├── hostels.php, rooms.php, students.php
│   ├── bookings.php, complaints.php, payments.php, users.php
│   └── includes/header.php, footer.php
├── index.php, facilities.php, rooms.php, study-hall.php, fees.php, faq.php
├── admission.php              # User registration (signup)
├── login.php                  # User or Admin login
├── logout.php
├── booking.php, my-bookings.php
├── complaint.php, feedback.php
└── assets/
```

## Security

- Passwords: **password_hash(PASSWORD_DEFAULT)** / **password_verify()**.  
- User login: **sessions**; cookie
- Admin login: session only (no remember cookie).  
- All DB access via **PDO** prepared statements.
# CasaOne — Student Hostel Management

Simple PHP hostel management system intended to run on a local XAMPP (Apache + MySQL) stack.

## Overview

This repo contains a small PHP application for managing hostel rooms, bookings, complaints, feedback, and an admin panel.

## Prerequisites

- XAMPP (Apache + MySQL) or any PHP 7.4+ + MySQL/MariaDB environment
- A working browser to access the app at `http://localhost/CasaOne` (when placed in XAMPP `htdocs`)

## Quick Install

1. Copy the project folder into your XAMPP `htdocs` directory (or configure your webroot to this folder).
2. Start Apache and MySQL via the XAMPP control panel.
3. Import the database schema: open phpMyAdmin and import `database/project.sql`.
4. Update database credentials in [config/database.php](config/database.php).
5. Ensure the `uploads/rooms/` directory is writable by the webserver (for file uploads).
6. Open the app in your browser: `http://localhost/CasaOne` and the admin panel at `http://localhost/CasaOne/admin`.

## File / Folder Structure

Top-level files
- [index.php](index.php): Public homepage / landing page
- [login.php](login.php): User login page
- [logout.php](logout.php): Logout script
- [rooms.php](rooms.php): Rooms listing
- [booking.php](booking.php): Booking submission page
- [my-bookings.php](my-bookings.php): User's bookings
- [admission.php](admission.php), [complaint.php](complaint.php), [feedback.php](feedback.php), [faq.php](faq.php), [fees.php](fees.php), [study-hall.php](study-hall.php), [facilities.php](facilities.php)
- [README.md](README.md): This file

Configuration
- [config/database.php](config/database.php): Database connection settings

Includes
- [includes/header.php](includes/header.php) and [includes/footer.php](includes/footer.php): Public site header/footer
- [auth.php](includes/auth.php): Authentication helper

Admin panel (protected)
- [admin/index.php](admin/index.php): Admin dashboard
- [admin/login.php](admin/login.php): Admin login
- [admin/bookings.php](admin/bookings.php): Manage bookings
- [admin/complaints.php](admin/complaints.php): Manage complaints
- [admin/feedback.php](admin/feedback.php): View feedback
- [admin/hostels.php](admin/hostels.php): Hostels management
- [admin/payments.php](admin/payments.php): Payments management
- [admin/rooms.php](admin/rooms.php): Admin rooms CRUD
- [admin/users.php](admin/users.php): Admin users management
- [admin/includes/header.php](admin/includes/header.php) and [admin/includes/footer.php](admin/includes/footer.php)

Assets and uploads
- `assets/css/style.css`: Main stylesheet ([assets/css/style.css](assets/css/style.css))
- `assets/js/main.js`: Frontend JS ([assets/js/main.js](assets/js/main.js))
- `assets/images/`: Images used by the site
- `uploads/rooms/`: Uploaded room images and media

Database
- `database/project.sql`: Database schema and sample data

## Notes & Maintenance

- After importing the SQL, check the `users` or `admin` table for seeded admin credentials. If none exist, create an admin user directly in the database.
- Keep `config/database.php` out of version control if you add real credentials.
- Back up `uploads/` before clearing or migrating data.

## Next steps

- Customize `config/database.php` and import `database/project.sql` to start using the app.

---
Generated README reflecting the repository layout and basic setup instructions.
