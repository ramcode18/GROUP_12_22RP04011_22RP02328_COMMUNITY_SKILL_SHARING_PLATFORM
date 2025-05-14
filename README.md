Skill Sharing Platform
A web and USSD-based platform that connects users to share knowledge and skills. Users can request lessons, and admins can manage lesson content and interactions. Designed with role-based access control and approval workflows to ensure content quality and secure interactions.

 Features
 General
User registration via Web and USSD

Role-based system with Admin and User roles

Admin approval required before login is enabled

Clean and secure backend with PHP + PDO

👤 User Capabilities
Register via USSD or web interface

View available lessons (approved only)

Request lessons or skill support via USSD

🛠️ Admin Capabilities
Approve or reject user registrations

Create, update, or delete lessons

Approve or reject user-submitted comments

View all user interactions

🗂️ Project Structure
graphql
Copy
Edit
skill-sharing-platform/
│
├── ussd/
│   ├── index.php           
│   └── Menu.php            
│
├── web/
│   ├── index.php           
│   ├── register.php        
│   ├── login.php           
│   ├── dashboard/
│   │   ├── user_dashboard.php
│   │   └── admin_dashboard.php
│
├── includes/
│   ├── db.php             
│   ├── auth.php           
│
├── lessons/
│   ├── create.php
│   ├── edit.php
│   ├── list.php
│   └── view.php
│
├── comments/
│   ├── moderate.php
│   └── list.php
│
└── README.md               
🛠️ Technologies Used
Component	Stack
Backend Language	PHP (with PDO for DB access)
Database	MariaDB / MySQL
USSD Integration	Africa’s Talking API
Authentication	Role-based Access Control
Frontend	HTML + minimal CSS (customizable)

⚙️ Installation
Clone the repo

bash
Copy
Edit
git clone https://github.com/yourusername/skill-sharing-platform.git
Set up your database

Create a new MariaDB database and import the following tables:

users (with roles: admin, user; and is_approved boolean)

lessons (admin-created, includes is_approved)

comments (linked to lessons, requires moderation)

Configure db.php

php
Copy
Edit
$dsn = 'mysql:host=localhost;dbname=your_db_name';
$username = 'your_username';
$password = 'your_password';
Set up Africa's Talking

Sign up at Africa's Talking

Configure your USSD app with your server’s ussd/index.php as the callback URL

Host the project

Use a web server like XAMPP, MAMP, or deploy to a live server (with HTTPS enabled for Africa's Talking)

🔐 User Roles
Role	Access
Admin	Approve users, manage lessons, view all comments
User	Request/view lessons via USSD or web

New registrations are pending until approved by an Admin.

📱 USSD Menu Flow
pgsql
Copy
Edit
User dials: *XYZ# 
→ Welcome screen
→ Register or Login
→ View approved lessons
→ Request new skill/lesson
Menus are handled through Menu.php, using Africa’s Talking’s session and level-based system.

📌 To Do / Roadmap
 Add SMS notifications on approval

 Implement search and filtering of lessons

 Add ratings or feedback for lessons

 Build REST API endpoints for mobile integration

 Enhance UI with Bootstrap or Tailwind CSS

🧑‍💻 Contributing
Fork the repo

Create a new branch (feature/lesson-comments)

Commit your changes

Push to the branch

Create a Pull Request

