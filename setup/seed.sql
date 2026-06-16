-- AeroTAXI Database Seeding Data
-- Run AFTER schema.sql

USE aerotaxi;

-- ===== ADMIN USER =====
-- Password: admin123 (bcrypt hash)
INSERT IGNORE INTO admins (id, name, email, password) VALUES
(1, 'Admin', 'admin@aerotaxi.com', '$2y$12$afPr0iTAB/nrZYMb5iHcEO/4dn6mqZ4HZLMq5vwjeDxwPzNVoicZK');

-- ===== AIRPORTS (12 UK AIRPORTS) =====
INSERT IGNORE INTO airports (code, name, city, country, description, sort_order) VALUES
('LHR', 'London Heathrow Airport', 'London', 'United Kingdom', 'UK''s busiest airport and major international hub', 1),
('LGW', 'London Gatwick Airport', 'London', 'United Kingdom', 'London''s second-largest airport', 2),
('STN', 'London Stansted Airport', 'London', 'United Kingdom', 'Major hub for European flights', 3),
('LTN', 'London Luton Airport', 'London', 'United Kingdom', 'Popular choice for budget airlines', 4),
('LCY', 'London City Airport', 'London', 'United Kingdom', 'Convenient airport in East London', 5),
('SEN', 'London Southend Airport', 'London', 'United Kingdom', 'Compact airport serving London and SE', 6),
('MAN', 'Manchester Airport', 'Manchester', 'United Kingdom', 'UK''s third-busiest airport', 7),
('EDI', 'Edinburgh Airport', 'Edinburgh', 'United Kingdom', 'Scotland''s busiest airport', 8),
('BHX', 'Birmingham Airport', 'Birmingham', 'United Kingdom', 'Central England''s premier airport', 9),
('BRS', 'Bristol Airport', 'Bristol', 'United Kingdom', 'Gateway to the South West', 10),
('NCL', 'Newcastle Airport', 'Newcastle', 'United Kingdom', 'Northeast England airport', 11),
('BFS', 'Belfast International', 'Belfast', 'United Kingdom', 'Northern Ireland''s main airport', 12);

-- ===== VEHICLES (7 VEHICLE TYPES) =====
INSERT IGNORE INTO vehicles (name, slug, price, short_base, short_per_mile, long_base, long_per_mile, passengers, suitcases, description, car_model, sort_order) VALUES
('Economy', 'economy', 34.00, 32.64, 2.57, 54.14, 1.17, 3, 3, 'Perfect for solo travelers and business trips', 'Toyota Prius or similar', 1),
('Executive', 'executive', 45.00, 43.07, 3.41, 69.89, 1.64, 4, 3, 'Premium comfort for discerning travelers', 'Mercedes E-Class or similar', 2),
('Estate', 'estate', 39.00, 36.96, 2.94, 61.76, 1.35, 4, 4, 'Extra space for luggage and comfort', 'VW Passat or similar', 3),
('People Carrier', 'people-carrier', 45.00, 44.81, 3.32, 68.30, 1.69, 5, 5, 'Ideal for families and small groups', 'Ford Galaxy or similar', 4),
('MPV Minibus', 'mpv-minibus', 55.00, 54.00, 4.10, 89.20, 1.99, 6, 6, 'Comfortable and spacious for groups', 'Mercedes V-Class or similar', 5),
('Coach', 'coach', 120.00, 100.00, 6.00, 160.00, 2.50, 16, 16, 'Large capacity coach for group transfers', 'Yutong Coach or similar', 6),
('Luxury', 'luxury', 65.00, 60.00, 4.50, 95.00, 2.00, 4, 4, 'Ultimate luxury and comfort', 'Range Rover or similar', 7);

-- ===== FAQS =====
INSERT IGNORE INTO faqs (question, answer, sort_order) VALUES
('How do I book a transfer?', 'Simply visit our home page and fill in your from/to locations, date and time. Select your preferred vehicle, enter your details, and proceed to payment.', 1),
('Can I modify my booking?', 'Yes, you can modify bookings up to 24 hours before your scheduled pickup time. Please contact us with your booking reference.', 2),
('What payment methods do you accept?', 'We accept all major credit cards (Visa, Mastercard), Apple Pay, Google Pay, and bank transfers for corporate accounts.', 3),
('Are your drivers professional and trained?', 'Yes, all our drivers are fully trained, background-checked, and experienced. Your safety and comfort is our priority.', 4),
('Do you offer return transfers?', 'Absolutely! Book a return transfer and receive a 10% discount on the return leg.', 5);
