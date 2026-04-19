-- =============================================
-- Smart Academic Result & University Management
-- Database Schema | Team: Bit Brains
-- =============================================

CREATE DATABASE IF NOT EXISTS smart_academic;
USE smart_academic;

-- Users table (Admin & Students)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Students table
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    student_id VARCHAR(20) UNIQUE NOT NULL,
    department VARCHAR(100),
    batch VARCHAR(20),
    session VARCHAR(20),
    phone VARCHAR(20),
    address TEXT,
    profile_image VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Courses table
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_code VARCHAR(20) UNIQUE NOT NULL,
    course_name VARCHAR(150) NOT NULL,
    credit_hours DECIMAL(3,1) NOT NULL,
    department VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Semesters table
CREATE TABLE semesters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    semester_name VARCHAR(50) NOT NULL,
    semester_number INT NOT NULL,
    year VARCHAR(10) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Results table
CREATE TABLE results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    semester_id INT NOT NULL,
    attendance_marks DECIMAL(5,2) DEFAULT 0,
    class_test_marks DECIMAL(5,2) DEFAULT 0,
    midterm_marks DECIMAL(5,2) DEFAULT 0,
    final_marks DECIMAL(5,2) DEFAULT 0,
    total_marks DECIMAL(5,2) DEFAULT 0,
    grade_point DECIMAL(3,2) DEFAULT 0,
    grade_letter VARCHAR(5),
    is_published TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE
);

-- Activity logs
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- Sample Data
-- =============================================

-- Admin user (password: admin123)
INSERT INTO users (name, email, password, role) VALUES
('Administrator', 'admin@bitbrains.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Uttam Halder', 'uttam@bitbrains.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
('Morium Haque', 'morium@bitbrains.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
('Rakibul Islam', 'rakibul@bitbrains.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student');

INSERT INTO students (user_id, student_id, department, batch, session) VALUES
(2, '42250102317', 'CSE', '2025', '2024-25'),
(3, '42250102353', 'CSE', '2025', '2024-25'),
(4, '42250102337', 'CSE', '2025', '2024-25');

INSERT INTO courses (course_code, course_name, credit_hours, department) VALUES
('CSE2291', 'Software Development II', 3.0, 'CSE'),
('CSE2101', 'Data Structures', 3.0, 'CSE'),
('CSE2201', 'Algorithms', 3.0, 'CSE'),
('CSE2301', 'Database Management', 3.0, 'CSE'),
('MATH201', 'Discrete Mathematics', 3.0, 'CSE');

INSERT INTO semesters (semester_name, semester_number, year) VALUES
('1st Semester', 1, '2024'),
('2nd Semester', 2, '2024'),
('3rd Semester', 3, '2025');

INSERT INTO results (student_id, course_id, semester_id, attendance_marks, class_test_marks, midterm_marks, final_marks, total_marks, grade_point, grade_letter, is_published) VALUES
(1, 1, 1, 9, 14, 28, 52, 88, 4.00, 'A+', 1),
(1, 2, 1, 8, 13, 25, 48, 82, 3.75, 'A', 1),
(1, 3, 1, 9, 12, 27, 50, 85, 3.75, 'A', 1),
(1, 4, 1, 10, 15, 30, 55, 90, 4.00, 'A+', 1),
(1, 5, 1, 8, 12, 26, 49, 80, 3.50, 'A-', 1);
