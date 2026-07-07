-- Add index for better performance
CREATE INDEX idx_company_posts ON posts(company_id, created_at);

INSERT INTO jobs (company_id, title, description, requirements, location, type, salary_range, application_deadline) VALUES
(1, 'Senior Software Engineer', 'We are looking for a skilled Senior Software Engineer to join our dynamic team...', '5+ years of experience, PHP, MySQL, JavaScript', 'New York, NY', 'Full-time', '$120,000 - $150,000', '2024-12-31'),
(1, 'Frontend Developer', 'Join our frontend team to build amazing user experiences...', 'React, TypeScript, CSS, 3+ years experience', 'Remote', 'Remote', '$80,000 - $110,000', '2024-11-30');

UPDATE posts 
SET poster_type = 'company'  -- or 'university' depending on what makes sense
WHERE poster_type IS NULL;

UPDATE companies
SET is_verified = TRUE;

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

ALTER TABLE posts MODIFY company_id INT NULL;

UPDATE users SET is_verified = TRUE;
UPDATE companies SET is_verified = TRUE;

CREATE INDEX idx_company_posts ON posts(company_id, created_at);

select * from companies;
select * from otp_codes;
drop table conversation_participants;
drop table conversations;
drop table messages;
select * from programs;
select * from universities;

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