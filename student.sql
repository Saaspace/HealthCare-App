CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    usertype ENUM('Student', 'Teacher') NOT NULL
);

CREATE TABLE leave_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    reason TEXT NOT NULL,
    document VARCHAR(255),
    status ENUM('Pending', 'Approved', 'Rejected', 'Auto-Rejected') DEFAULT 'Pending',
    submission_date DATE NOT NULL,
    deadline DATE NOT NULL,
    FOREIGN KEY (student_id) REFERENCES users(id)
);

CREATE TABLE faculty_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    teacher_id INT NOT NULL,
    comment TEXT NOT NULL,
    decision ENUM('Approved', 'Rejected') NOT NULL,
    FOREIGN KEY (application_id) REFERENCES leave_applications(id),
    FOREIGN KEY (teacher_id) REFERENCES users(id)
);
