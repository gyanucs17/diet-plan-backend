# Dietplan Project Backend

This repository contains the backend for the Diet Management System. The backend is built with PHP and uses Composer for dependency management. 
Follow the steps below to set up and run the project locally.

## Prerequisites

Ensure you have the following installed on your machine:

- *PHP 8+*
- *Composer* (Dependency Manager for PHP)
- *MySQL* 

## Getting Started

### 1. Clone the repository

Open your terminal and run the following command to clone the repository:

bash
git clone https://github.com/gyanucs17/diet-plan-backend.git


### 2. Navigate to project directory
bash
cd diet-plan-backend


### 3. Install Dependencies
Run the following commands to install the necessary dependencies:
bash
composer install
composer dump-autoload


### 4. Configure environment variables
Update the .env  with your database credentials and other necessary configurations.

DB_SERVER=localhost
DB_USER=
DB_PASSWORD=
DB_NAME=test_db

SECRET_KEY=secret_key_for_this_project


### 5. Import database tables and using .sql file located at root named test_db.sql

### 6. Start your php server
Run the following commands to start server
php -S localhost:8080 //you can use any port 

## CSV Uploads

1. Food Items - use CSV with header columns Resident name, IDSSI Level & Category, please look at sampleFood.csv sample file for your reference
2. Residents - use CSV with header columns Resident name & IDSSI Level, please look at sampleResident.csv sample file for your reference

## Credentials to login
username - admin
password - admin@123
  
