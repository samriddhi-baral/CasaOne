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
- User login: **sessions**; optional **remember me** → secure cookie + `remember_tokens` (user_id = u_id).  
- Admin login: session only (no remember cookie).  
- All DB access via **PDO** prepared statements.
#CasaOne
