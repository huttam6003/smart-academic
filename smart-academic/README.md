# 🧠 Smart Academic Result & University Management System
**Team: Bit Brains | CSE 2291 | Software Development II**

---

## 📁 Project Structure

```
smart-academic/
├── index.php                  ← Login Page
├── config/
│   └── db.php                 ← Database Connection
├── auth/
│   ├── login.php              ← Login Handler
│   └── logout.php             ← Logout Handler
├── admin/
│   ├── dashboard.php          ← Admin Dashboard
│   ├── students.php           ← Manage Students
│   ├── courses.php            ← Manage Courses
│   ├── semesters.php          ← Manage Semesters
│   ├── marks.php              ← Enter Marks
│   └── reports.php            ← Reports & Analytics
├── student/
│   ├── dashboard.php          ← Student Dashboard
│   ├── results.php            ← View Results
│   ├── transcript.php         ← Download Transcript
│   └── profile.php            ← Edit Profile
├── includes/
│   ├── header.php             ← Shared Header + Sidebar
│   └── footer.php             ← Shared Footer
└── database/
    └── schema.sql             ← Database Setup SQL
```

---

## ⚙️ Setup Instructions (Step by Step)

### Step 1: Install XAMPP
Download from: https://www.apachefriends.org/
Install করো এবং **Apache** ও **MySQL** চালু করো।

### Step 2: Project Copy করো
পুরো `smart-academic` folder টা এখানে রাখো:
```
C:\xampp\htdocs\smart-academic\
```

### Step 3: Database তৈরি করো
1. Browser এ যাও: `http://localhost/phpmyadmin`
2. উপরে **"SQL"** ট্যাবে click করো
3. `database/schema.sql` ফাইলের সব content copy করো
4. SQL box এ paste করো
5. **"Go"** button এ click করো

### Step 4: Config চেক করো
`config/db.php` ফাইলটা খোলো:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');   // XAMPP default
define('DB_PASS', '');        // XAMPP default (empty)
define('DB_NAME', 'smart_academic');
```
যদি তোমার MySQL password আলাদা হয়, সেটা দাও।

### Step 5: Run করো!
Browser এ যাও: `http://localhost/smart-academic/`

---

## 🔑 Login Credentials

| Role    | Email                    | Password  |
|---------|--------------------------|-----------|
| Admin   | admin@bitbrains.edu      | password  |
| Student | uttam@bitbrains.edu      | password  |
| Student | morium@bitbrains.edu     | password  |

---

## ✨ Features

### Admin Panel
- ✅ Dashboard with stats
- ✅ Add/Delete Students
- ✅ Add/Delete Courses
- ✅ Add Semesters
- ✅ Enter & Publish Marks
- ✅ Reports & Grade Distribution
- ✅ Top Students Ranking

### Student Panel
- ✅ Personal Dashboard with CGPA
- ✅ Semester-wise Results
- ✅ Printable Academic Transcript
- ✅ Profile Management

---

## 🛠️ Technology Stack

| Layer     | Technology              |
|-----------|-------------------------|
| Frontend  | HTML5, CSS3, JavaScript |
| Backend   | PHP                     |
| Database  | MySQL                   |
| Styling   | Custom CSS + Font Awesome |
| Fonts     | Space Grotesk, Syne (Google Fonts) |
| Server    | XAMPP (Apache + MySQL)  |

---

**Team Bit Brains** 🧠
