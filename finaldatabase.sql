-- Drop database career_finder;
create database career_finder;
use career_finder;

-- SET SQL_SAFE_UPDATES = 0;
-- SET SQL_SAFE_UPDATES = 1;



CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- desc users;

Alter table users Add column otp_code VARCHAR(6);
-- select * from users;

-- delete from users where id > 10;


-- User profiles table
CREATE TABLE user_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    designation VARCHAR(100),
    about TEXT,
    cover_photo VARCHAR(255),
    profile_photo VARCHAR(255),
    location VARCHAR(100),
    website VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- select * from user_profiles;

ALTER TABLE user_profiles 
ADD COLUMN university VARCHAR(255) AFTER designation,
ADD COLUMN degree VARCHAR(100) AFTER university,
ADD COLUMN field_of_study VARCHAR(100) AFTER degree,
ADD COLUMN graduation_year YEAR AFTER field_of_study;

-- User experiences table
CREATE TABLE user_experiences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    company VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    description TEXT,
    start_date DATE NOT NULL,
    end_date DATE,
    current_job BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- select * from user_experiences;

-- User skills table
CREATE TABLE user_skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    skill_name VARCHAR(50) NOT NULL,
    proficiency ENUM('Beginner', 'Intermediate', 'Advanced', 'Expert') DEFAULT 'Intermediate',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- select * from user_skills;


-- Posts table
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    poster_id INT NOT NULL,
    poster_type ENUM('company', 'university') NOT NULL,
    poster_name VARCHAR(255) NOT NULL,
    poster_avatar VARCHAR(255),
    post_text TEXT,
    post_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- select * from posts;
-- DELETE FROM posts WHERE id > 0;
-- drop table posts;
-- truncate posts;

-- Saved posts table
CREATE TABLE saved_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    saved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    UNIQUE KEY unique_save (user_id, post_id)
);

-- select * from saved_posts;
-- drop table saved_posts;

-- Update posts table to link with companies
ALTER TABLE posts 
ADD COLUMN company_id INT AFTER poster_id,
ADD FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE;

-- Insert dummy posts
-- INSERT INTO posts (poster_id, poster_type, poster_name, poster_avatar, post_text, post_image) VALUES
-- (1, 'company', 'Tech Innovations Inc.', 'company1.jpg', 'We are excited to announce our new AI-powered platform that will revolutionize the way businesses operate! 🚀 #Innovation #Tech', 'post1.jpg'),
-- (2, 'university', 'Stanford University', 'stanford.jpg', 'Congratulations to all our graduates! The class of 2024 has shown exceptional resilience and achievement. 🎓 #Graduation2024 #Proud', 'graduation.jpg'),
-- (3, 'company', 'Google Careers', 'google.jpg', 'Looking for talented software engineers to join our team! We offer competitive packages and amazing work culture. Apply now! 💼 #Hiring #TechJobs', 'careers.jpg'),
-- (4, 'university', 'MIT', 'mit.jpg', 'Our research team has made a breakthrough in quantum computing. Read more about this exciting development in our latest publication. 🔬 #Research #Quantum', 'research.jpg'),
-- (5, 'company', 'Microsoft', 'microsoft.jpg', 'New Windows update brings enhanced security features and improved performance. Update now to experience the latest innovations! 💻 #WindowsUpdate', 'windows.jpg');


-- Companies table
CREATE TABLE companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    website VARCHAR(255),
    phone VARCHAR(20),
    founded_year YEAR,
    company_size ENUM('1-10', '11-50', '51-200', '201-500', '501-1000', '1000+'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- desc companies;

select * from companies;
-- delete from companies where id > 0;

UPDATE companies
SET is_verified = TRUE;


ALTER TABLE companies 
ADD COLUMN logo VARCHAR(255) AFTER website,
ADD COLUMN about TEXT AFTER logo;

-- Company profiles table
CREATE TABLE company_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    logo VARCHAR(255),
    cover_photo VARCHAR(255),
    about TEXT,
    location VARCHAR(255),
    industry VARCHAR(100),
    specialization TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

-- select * from company_profiles;

-- Update company_profiles table
ALTER TABLE company_profiles 
ADD COLUMN category VARCHAR(100) AFTER industry,
ADD COLUMN portfolio_link VARCHAR(255) ,
ADD COLUMN projects JSON AFTER specialization;



-- Jobs table (for future use)
CREATE TABLE jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    requirements TEXT,
    location VARCHAR(100),
    type ENUM('Full-time', 'Part-time', 'Contract', 'Internship'),
    salary_range VARCHAR(100),
    application_deadline DATE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

-- select * from jobs;



-- Or create a separate projects table (recommended)
CREATE TABLE company_projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    project_title VARCHAR(255) NOT NULL,
    project_description TEXT,
    project_thumbnail VARCHAR(255),
    project_link VARCHAR(255),
    technologies TEXT,
    project_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);


-- Jobs table (if not exists)
CREATE TABLE IF NOT EXISTS jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    requirements TEXT,
    location VARCHAR(100),
    type ENUM('Full-time', 'Part-time', 'Contract', 'Internship', 'Remote') DEFAULT 'Full-time',
    salary_range VARCHAR(100),
    application_deadline DATE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

-- select * from jobs;

-- Job applications table
CREATE TABLE IF NOT EXISTS job_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    user_id INT NOT NULL,
    cv_file VARCHAR(255) NOT NULL,
    cover_letter TEXT,
    status ENUM('pending', 'reviewed', 'accepted', 'rejected') DEFAULT 'pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_application (job_id, user_id)
);

-- drop table job_applications;

CREATE TABLE IF NOT EXISTS saved_jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    job_id INT NOT NULL,
    saved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    UNIQUE KEY unique_save (user_id, job_id)
);


-- Add some sample jobs for testing
INSERT INTO jobs (company_id, title, description, requirements, location, type, salary_range, application_deadline) VALUES
(1, 'Senior Software Engineer', 'We are looking for a skilled Senior Software Engineer to join our dynamic team...', '5+ years of experience, PHP, MySQL, JavaScript', 'New York, NY', 'Full-time', '$120,000 - $150,000', '2024-12-31'),
(1, 'Frontend Developer', 'Join our frontend team to build amazing user experiences...', 'React, TypeScript, CSS, 3+ years experience', 'Remote', 'Remote', '$80,000 - $110,000', '2024-11-30');


UPDATE posts 
SET poster_type = 'company'  -- or 'university' depending on what makes sense
WHERE poster_type IS NULL;


-- Make sure posts table has proper structure for company posts
ALTER TABLE posts 
MODIFY COLUMN company_id INT NOT NULL AFTER poster_id,
ADD COLUMN post_title VARCHAR(255) AFTER post_image,
ADD COLUMN is_published BOOLEAN DEFAULT TRUE,
ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Add index for better performance
CREATE INDEX idx_company_posts ON posts(company_id, created_at);


-- Universities table
CREATE TABLE universities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    university_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255),
    category VARCHAR(100),
    campuses JSON,
    contact_info JSON,
    logo VARCHAR(255),
    established_year YEAR,
    sector ENUM('Public', 'Private'),
    chartered_by VARCHAR(255),
    city VARCHAR(100),
    province VARCHAR(100),
    is_recognized BOOLEAN DEFAULT TRUE,
    otp_code VARCHAR(6),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


INSERT INTO universities (
    university_name,
    email,
    password,
    category,
    campuses,
    contact_info,
    logo,
    established_year,
    sector,
    chartered_by,
    city,
    province,
    is_recognized,
    otp_code,
    otp_expiry,
    is_active
) VALUES
(
    'National University of Sciences and Technology (NUST)',
    'ahmedikram567@gmail.com',
    NULL,
    'Engineering and Technology',
    JSON_ARRAY('Islamabad Campus', 'Rawalpindi Campus', 'Karachi Campus'),
    JSON_OBJECT('phone', '+92-51-9085-1341', 'website', 'https://nust.edu.pk', 'address', 'H-12, Islamabad'),
    'https://nust.edu.pk/assets/logo.png',
    1991,
    'Public',
    'Government of Pakistan',
    'Islamabad',
    'Islamabad Capital Territory',
    TRUE,
    NULL,
    NULL,
    TRUE
),
(
    'Lahore University of Management Sciences (LUMS)',
    'info.personalmoon@gmail.com',
    NULL,
    'Business and Management',
    JSON_ARRAY('Main Campus'),
    JSON_OBJECT('phone', '+92-42-3560-8000', 'website', 'https://lums.edu.pk', 'address', 'Opposite Sector U, DHA, Lahore'),
    'https://lums.edu.pk/assets/logo.png',
    1984,
    'Private',
    'Higher Education Commission (HEC) Pakistan',
    'Lahore',
    'Punjab',
    TRUE,
    NULL,
    NULL,
    TRUE
);

-- select * from universities; 
-- truncate universities;

-- drop table universities;


-- University profiles table
CREATE TABLE university_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    university_id INT NOT NULL,
    about TEXT,
    vision TEXT,
    mission TEXT,
    facilities JSON,
    accreditation TEXT,
    ranking_info TEXT,
    social_links JSON,
    cover_photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE CASCADE
);

-- drop table university_profiles;

-- University posts table
CREATE TABLE university_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    university_id INT NOT NULL,
    post_text TEXT,
    post_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE CASCADE
);

-- drop table university_posts;


-- Create conversations table
CREATE TABLE conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    created_by_type ENUM('user', 'company', 'university') NOT NULL,
    created_by_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create conversation participants table
CREATE TABLE conversation_participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    participant_type ENUM('user', 'company', 'university') NOT NULL,
    participant_id INT NOT NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE
);

-- Create messages table
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    sender_type ENUM('user', 'company', 'university') NOT NULL,
    sender_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE
);

-- DESCRIBE posts;
ALTER TABLE posts MODIFY company_id INT NULL;

-- Add OTP fields to users table
ALTER TABLE users 
ADD COLUMN is_verified BOOLEAN DEFAULT FALSE;

-- Add OTP fields to companies table  
ALTER TABLE companies 
ADD COLUMN otp_code VARCHAR(6),
ADD COLUMN is_verified BOOLEAN DEFAULT FALSE;

-- Update existing users and companies to be verified
UPDATE users SET is_verified = TRUE;
UPDATE companies SET is_verified = TRUE;

-- Add OTP fields to users table
ALTER TABLE users 
ADD COLUMN otp_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;


-- Add OTP fields to companies table  
ALTER TABLE companies 
ADD COLUMN otp_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;


CREATE TABLE otp_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    otp_code CHAR(6) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (email)
);

-- drop table otp_codes;
select * from otp_codes;

drop table conversation_participants;
drop table conversations;
drop table messages;


-- Sessions table
CREATE TABLE sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    user_type ENUM('user', 'company', 'university') NOT NULL,
    session_token VARCHAR(255) UNIQUE NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Messages table
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id VARCHAR(100) NOT NULL,
    sender_id INT NOT NULL,
    sender_type ENUM('user', 'company', 'university') NOT NULL,
    receiver_id INT NOT NULL,
    receiver_type ENUM('user', 'company', 'university') NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (conversation_id),
    INDEX (sender_id, sender_type),
    INDEX (receiver_id, receiver_type)
);

-- Conversations table for better performance
CREATE TABLE conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id VARCHAR(100) UNIQUE NOT NULL,
    participant1_id INT NOT NULL,
    participant1_type ENUM('user', 'company', 'university') NOT NULL,
    participant2_id INT NOT NULL,
    participant2_type ENUM('user', 'company', 'university') NOT NULL,
    last_message TEXT,
    last_message_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (participant1_id, participant1_type),
    INDEX (participant2_id, participant2_type)
);

CREATE TABLE programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    university_id INT NOT NULL,
    program_name VARCHAR(255) NOT NULL,
    program_category VARCHAR(100) NOT NULL,
    closing_merit DECIMAL(5,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE CASCADE,
    INDEX idx_university_id (university_id),
    INDEX idx_category (program_category)
);

select * from programs;

select * from universities;

-- Insert 5 Pakistani universities with campuses and contact info
INSERT INTO universities (
    university_name, 
    email, 
    password, 
    category, 
    campuses, 
    contact_info, 
    established_year, 
    sector, 
    chartered_by, 
    city, 
    province, 
    is_recognized, 
    is_active
) VALUES
(
    'National University of Sciences & Technology (NUST)',
    'ahmedikram567@gmail.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Engineering & Technology',
    '[
        {"name": "Main Campus", "address": "Sector H-12, Islamabad", "phone": "+92-51-90851000"},
        {"name": "EME Campus", "address": "Peshawar Road, Rawalpindi", "phone": "+92-51-9272201"},
        {"name": "MCS Campus", "address": "The Mall, Rawalpindi", "phone": "+92-51-9270270"}
    ]',
    '{
        "phone": "+92-51-90851000",
        "website": "https://nust.edu.pk",
        "address": "Sector H-12, Islamabad, Pakistan"
    }',
    2005,
    'Public',
    'Federal Government',
    'Islamabad',
    'Islamabad Capital Territory',
    TRUE,
    TRUE
),
(
    'Lahore University of Management Sciences (LUMS)',
    'admissions@lums.edu.pk',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Business & Management',
    '[
        {"name": "Main Campus", "address": "DHA, Lahore Cantt", "phone": "+92-42-35608000"}
    ]',
    '{
        "phone": "+92-42-35608000",
        "website": "https://lums.edu.pk",
        "address": "DHA, Lahore Cantt, Lahore, Pakistan"
    }',
    2005,
    'Private',
    'Provincial Assembly of Punjab',
    'Lahore',
    'Punjab',
    TRUE,
    TRUE
),
(
    'University of the Punjab',
    'info@pu.edu.pk',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'General',
    '[
        {"name": "Quaid-e-Azam Campus", "address": "Canal Road, Lahore", "phone": "+92-42-99231145"},
        {"name": "Allama Iqbal Campus", "address": "The Mall, Lahore", "phone": "+92-42-99231129"}
    ]',
    '{
        "phone": "+92-42-99231145",
        "website": "https://pu.edu.pk",
        "address": "Quaid-e-Azam Campus, Canal Road, Lahore, Pakistan"
    }',
    2005,
    'Public',
    'Government of Pakistan',
    'Lahore',
    'Punjab',
    TRUE,
    TRUE
),
(
    'University of Engineering and Technology (UET) Lahore',
    'admissions@uet.edu.pk',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Engineering & Technology',
    '[
        {"name": "Main Campus", "address": "Grand Trunk Road, Lahore", "phone": "+92-42-99029227"},
        {"name": "Kala Shah Kaku Campus", "address": "Kala Shah Kaku, Lahore", "phone": "+92-42-99029228"}
    ]',
    '{
        "phone": "+92-42-99029227",
        "website": "https://uet.edu.pk",
        "address": "Grand Trunk Road, Lahore, Pakistan"
    }',
    2005,
    'Public',
    'Government of Punjab',
    'Lahore',
    'Punjab',
    TRUE,
    TRUE
),
(
    'Aga Khan University',
    'admissions@aku.edu',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Medical & Health Sciences',
    '[
        {"name": "Main Campus", "address": "Stadium Road, Karachi", "phone": "+92-21-34860000"},
        {"name": "Faculty of Arts & Sciences", "address": "Karachi", "phone": "+92-21-34860000"}
    ]',
    '{
        "phone": "+92-21-34860000",
        "website": "https://aku.edu",
        "address": "Stadium Road, Karachi, Pakistan"
    }',
    2005,
    'Private',
    'Government of Pakistan',
    'Karachi',
    'Sindh',
    TRUE,
    TRUE
);


-- Insert dummy programs for Pakistani universities
INSERT INTO programs (university_id, program_name, program_category, closing_merit) VALUES

-- NUST (National University of Sciences & Technology)
(1, 'Bachelor of Computer Science', 'Computer Science & IT', 82.50),
(1, 'Bachelor of Electrical Engineering', 'Engineering & Technology', 85.75),
(1, 'Bachelor of Business Administration', 'Business & Management', 78.25),

-- LUMS (Lahore University of Management Sciences)
(2, 'BS Computer Science', 'Computer Science & IT', 88.90),
(2, 'BSc Economics', 'Social Sciences', 84.60),
(2, 'Bachelor of Business Administration', 'Business & Management', 86.75),

-- University of the Punjab
(3, 'Bachelor of Medicine, Bachelor of Surgery (MBBS)', 'Medical & Health Sciences', 92.15),
(3, 'Bachelor of Laws (LLB)', 'Law & Legal Studies', 76.80),
(3, 'Bachelor of Arts in English Literature', 'Arts & Humanities', 72.45),

-- UET Lahore (University of Engineering and Technology)
(4, 'Bachelor of Civil Engineering', 'Engineering & Technology', 83.20),
(4, 'Bachelor of Mechanical Engineering', 'Engineering & Technology', 81.95),
(4, 'Bachelor of Architecture', 'Architecture & Planning', 79.60),

-- Aga Khan University
(5, 'Bachelor of Science in Nursing', 'Medical & Health Sciences', 80.25),
(5, 'Doctor of Medicine (MD)', 'Medical & Health Sciences', 94.50),
(5, 'Bachelor of Science in Midwifery', 'Medical & Health Sciences', 77.85);