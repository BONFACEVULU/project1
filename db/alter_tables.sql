-- Add new columns to the instructors table
ALTER TABLE instructors
ADD COLUMN bio TEXT AFTER name,
ADD COLUMN specialties VARCHAR(255) AFTER bio,
ADD COLUMN image_url VARCHAR(255) AFTER specialties;

-- Add new columns to the classes table
ALTER TABLE classes
ADD COLUMN instructor_id INT AFTER image_url,
ADD COLUMN class_type VARCHAR(50) AFTER instructor_id,
ADD COLUMN start_date DATE AFTER class_type,
ADD COLUMN end_date DATE AFTER start_date,
ADD CONSTRAINT fk_instructor
    FOREIGN KEY (instructor_id) REFERENCES instructors(id);

-- Create the schedule table
CREATE TABLE schedule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT,
    instructor_id INT,
    day_of_week VARCHAR(10),
    start_time TIME,
    end_time TIME,
    room VARCHAR(50),
    FOREIGN KEY (class_id) REFERENCES classes(id),
    FOREIGN KEY (instructor_id) REFERENCES instructors(id);

-- Add new columns to the users table
ALTER TABLE users
ADD COLUMN phone_number VARCHAR(15) AFTER email,
ADD COLUMN emergency_contact_name VARCHAR(100) AFTER phone_number,
ADD COLUMN emergency_contact_phone VARCHAR(15) AFTER emergency_contact_name,
ADD COLUMN profile_picture VARCHAR(255) AFTER emergency_contact_phone,
ADD COLUMN preferences TEXT AFTER profile_picture,
ADD COLUMN parent_consent BOOLEAN DEFAULT FALSE AFTER preferences;

-- Add new columns for personal info
ALTER TABLE users
ADD COLUMN gender ENUM('Male', 'Female', 'Other') AFTER parent_consent,
ADD COLUMN birthdate DATE AFTER gender,
ADD COLUMN mobile_phone VARCHAR(15) AFTER birthdate,
ADD COLUMN address VARCHAR(255) AFTER mobile_phone,
ADD COLUMN city VARCHAR(100) AFTER address,
ADD COLUMN postal_code VARCHAR(20) AFTER city;

-- Create the schedule table
CREATE TABLE schedule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT,
    instructor_id INT,
    day_of_week VARCHAR(10),
    start_time TIME,
    end_time TIME,
    room VARCHAR(50),
    FOREIGN KEY (class_id) REFERENCES classes(id),
    FOREIGN KEY (instructor_id) REFERENCES instructors(id)
);
