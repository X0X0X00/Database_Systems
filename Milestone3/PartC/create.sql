-- Create Record table
CREATE TABLE IF NOT EXISTS Record (
    record_id INT AUTO_INCREMENT PRIMARY KEY,
    cno INT NOT NULL,
    bno INT NOT NULL,
    borrow_date DATE,
    return_date DATE,
    manager_id INT,
    location VARCHAR(255)
);

-- Create Card table
CREATE TABLE IF NOT EXISTS Card (
    cno INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    department VARCHAR(255),
    type VARCHAR(50)
);

-- Create Book table
CREATE TABLE IF NOT EXISTS Book (
    bno INT AUTO_INCREMENT PRIMARY KEY,
    b_name VARCHAR(255),
    ISBN VARCHAR(20) UNIQUE,
    press VARCHAR(255),
    category_id INT,
    year INT,
    author VARCHAR(255),
    total INT,
    stock INT,
    location VARCHAR(255),
    description TEXT
);

-- Create Student table
CREATE TABLE IF NOT EXISTS Student (
    s_id VARCHAR(255) PRIMARY KEY,
    cno INT UNIQUE,
    s_password VARCHAR(255),
    s_email VARCHAR(255) UNIQUE,
    s_firstname VARCHAR(255),
    s_middlename VARCHAR(255),
    s_lastname VARCHAR(255),
    age INT,
    gender VARCHAR(10),
    contact VARCHAR(20)
);

-- Create Faculty table
CREATE TABLE IF NOT EXISTS Faculty (
    f_id VARCHAR(255) PRIMARY KEY,
    cno INT UNIQUE,
    f_password VARCHAR(255),
    f_email VARCHAR(255) UNIQUE,
    f_firstname VARCHAR(255),
    f_middlename VARCHAR(255),
    f_lastname VARCHAR(255),
    age INT,
    gender VARCHAR(10),
    contact VARCHAR(20),
    office VARCHAR(255)
);

-- Create Library Manager table
CREATE TABLE IF NOT EXISTS Library_Manager (
    m_id VARCHAR(255) PRIMARY KEY,
    m_password VARCHAR(255),
    m_email VARCHAR(255) UNIQUE,
    m_firstname VARCHAR(255),
    m_middlename VARCHAR(255),
    m_lastname VARCHAR(255),
    age INT,
    gender VARCHAR(10),
    contact VARCHAR(20),
    office VARCHAR(255)
);

-- Create Author table
CREATE TABLE IF NOT EXISTS Author (
    a_id VARCHAR(255) PRIMARY KEY,
    a_firstname VARCHAR(255),
    a_middlename VARCHAR(255),
    a_lastname VARCHAR(255),
    birthday DATE
);

-- Create Category table
CREATE TABLE IF NOT EXISTS Category (
    c_id INT AUTO_INCREMENT PRIMARY KEY,
    c_name VARCHAR(255),
    parent_id INT,
    creation_date DATE,
    last_updated DATE
);
