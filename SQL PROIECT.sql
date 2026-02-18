-- 1. Roles
CREATE TABLE roles (
    id INT PRIMARY KEY IDENTITY(1,1),
    role_name VARCHAR(50) NOT NULL UNIQUE
);

-- 2. Users
CREATE TABLE users (
    id INT PRIMARY KEY IDENTITY(1,1),
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    full_name VARCHAR(100),
    role_id INT FOREIGN KEY REFERENCES roles(id),
    created_at DATETIME DEFAULT GETDATE()
);

-- 3. Stations
CREATE TABLE stations (
    id INT PRIMARY KEY IDENTITY(1,1),
    name VARCHAR(100) NOT NULL,
    code VARCHAR(10) NOT NULL UNIQUE,
    city VARCHAR(100)
);

-- 4. Trains
CREATE TABLE trains (
    id INT PRIMARY KEY IDENTITY(1,1),
    train_number VARCHAR(10) NOT NULL UNIQUE,
    train_name VARCHAR(100) NOT NULL,
    train_type VARCHAR(50),
    capacity INT
);

-- 5. Schedules
CREATE TABLE schedules (
    id INT PRIMARY KEY IDENTITY(1,1),
    train_id INT FOREIGN KEY REFERENCES trains(id),
    departure_station_id INT FOREIGN KEY REFERENCES stations(id),
    arrival_station_id INT FOREIGN KEY REFERENCES stations(id),
    departure_time DATETIME,
    arrival_time DATETIME,
    journey_date DATE
);

-- 6. Seats
CREATE TABLE seats (
    id INT PRIMARY KEY IDENTITY(1,1),
    schedule_id INT FOREIGN KEY REFERENCES schedules(id),
    seat_number VARCHAR(10),
    class VARCHAR(50), -- e.g., AC, Sleeper
    is_available BIT DEFAULT 1
);

-- 7. Bookings
CREATE TABLE bookings (
    id INT PRIMARY KEY IDENTITY(1,1),
    user_id INT FOREIGN KEY REFERENCES users(id),
    schedule_id INT FOREIGN KEY REFERENCES schedules(id),
    booking_date DATETIME DEFAULT GETDATE(),
    total_price DECIMAL(10,2),
    status VARCHAR(20) DEFAULT 'confirmed' -- confirmed, cancelled
);

-- 8. Payments
CREATE TABLE payments (
    id INT PRIMARY KEY IDENTITY(1,1),
    booking_id INT FOREIGN KEY REFERENCES bookings(id),
    payment_method VARCHAR(50),
    payment_status VARCHAR(20), -- paid, failed, refunded
    amount DECIMAL(10,2),
    payment_date DATETIME DEFAULT GETDATE()
);

-- 9. Tickets
CREATE TABLE tickets (
    id INT PRIMARY KEY IDENTITY(1,1),
    booking_id INT FOREIGN KEY REFERENCES bookings(id),
    pnr_number VARCHAR(20) NOT NULL UNIQUE,
    issue_date DATETIME DEFAULT GETDATE(),
    seat_id INT FOREIGN KEY REFERENCES seats(id)
);

-- 10. Feedback
CREATE TABLE feedback (
    id INT PRIMARY KEY IDENTITY(1,1),
    user_id INT FOREIGN KEY REFERENCES users(id),
    train_id INT FOREIGN KEY REFERENCES trains(id),
    comment TEXT,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    feedback_date DATETIME DEFAULT GETDATE()
);


ALTER TABLE users
DROP COLUMN created_at;

SELECT name
FROM sys.default_constraints
WHERE parent_object_id = OBJECT_ID('users')
  AND parent_column_id = (
      SELECT column_id
      FROM sys.columns
      WHERE object_id = OBJECT_ID('users')
        AND name = 'created_at'
  );
  ALTER TABLE users
DROP CONSTRAINT DF__users__created_a__3D5E1FD2;  -- replace with your actual constraint name
ALTER TABLE users
DROP COLUMN created_at;
