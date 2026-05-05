# 🧪 Online Exam Management System

A full-stack web application for conducting online exams with support for **MCQs, Short Answers, and Coding Questions (with code execution)**.



## 🚀 Features

* User Authentication (Admin & Student)
* Multiple Question Types:

  * MCQs
  * Short Answer Questions
  * Coding Questions (Run Code Feature 💻)
* Exam Creation & Management
* Real-time Exam Attempt
* Auto Result Generation
* Admin Dashboard
* Upcoming:

  * Google Authentication
  * Deployment for real-world usage 🌍



## 🛠️ Tech Stack

* **Backend:** PHP (Laravel)
* **Frontend:** HTML, CSS, JavaScript
* **Database:** MySQL



## ⚙️ Installation Guide

Follow these steps to run the project locally:

 1️⃣ Clone the Repository

bash
git clone https://github.com/your-username/online-exam-system.git
cd online-exam-system


 2️⃣ Install Dependencies

Make sure you have **Composer** installed.

bash
composer install


 3️⃣ Setup Environment File

bash
cp .env.example .env


Now open `.env` file and update database details:

env
DB_DATABASE=your_database_name
DB_USERNAME=root
DB_PASSWORD=


 4️⃣ Generate Application Key

bash
php artisan key:generate


 5️⃣ Run Migrations

bash
php artisan migrate


 6️⃣ Start the Server

bash
php artisan serve


Now open:
http://127.0.0.1:8000

## 🔐 Admin Login Credentials

Use the following credentials to access the admin panel:

* Email:admin@exam.com
* **Password:** admin123

## 📌 Notes

* Ensure MySQL server is running before migration
* For coding questions, make sure your system supports execution (if using any compiler integration)
* Google Authentication feature is under development



## 📈 Future Improvements

* Google OAuth Login
* Cloud Deployment (AWS / VPS)
* Performance Optimization
* Advanced Analytics Dashboard


🤝 Contributing

Feel free to fork this project and improve it!
## 📧 Contact

For any queries or suggestions, feel free to reach out
mansuriijishan@gmail.com
