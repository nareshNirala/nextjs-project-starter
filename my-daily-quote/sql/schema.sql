-- My Daily Quote Database Schema
CREATE DATABASE IF NOT EXISTS daily_quotes;
USE daily_quotes;

-- Users table for Google OAuth users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    google_id VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    picture_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Quotes table for daily quotes
CREATE TABLE IF NOT EXISTS quotes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quote_text TEXT NOT NULL,
    author VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- Insert sample quotes
INSERT INTO quotes (quote_text, author) VALUES
('The only way to do great work is to love what you do.', 'Steve Jobs'),
('Innovation distinguishes between a leader and a follower.', 'Steve Jobs'),
('Life is what happens to you while you''re busy making other plans.', 'John Lennon'),
('The future belongs to those who believe in the beauty of their dreams.', 'Eleanor Roosevelt'),
('Be yourself; everyone else is already taken.', 'Oscar Wilde'),
('Two things are infinite: the universe and human stupidity; and I''m not sure about the universe.', 'Albert Einstein'),
('You only live once, but if you do it right, once is enough.', 'Mae West'),
('Be the change that you wish to see in the world.', 'Mahatma Gandhi'),
('In three words I can sum up everything I''ve learned about life: it goes on.', 'Robert Frost'),
('If you tell the truth, you don''t have to remember anything.', 'Mark Twain');
