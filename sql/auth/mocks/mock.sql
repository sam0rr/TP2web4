INSERT INTO userProfile (username, firstname, lastname, email, password, type) VALUES
                                                                                                ('john_doe92', 'Johnathan', 'Doe', 'john.doe92@example.com', '$2y$12$Q2VkaW5nUmVhbGx5U3Ryb25nIQM5z7KjX9kL2mP8vN4rT6uW9yA2C', 'NORMAL'),
                                                                                                ('jane_smith88', 'Jane', 'Smithson', 'jane.smith88@example.com', '$2y$12$U2VjdXJlUGFzc3dvcmQyMDIyISM8nPxKjL2vR5tY9zA3qW6uB9cE', 'PREMIUM'),
                                                                                                ('alice_wonder77', 'Alicia', 'Wonderland', 'alice.wonder77@example.com', '$2y$12$V29uZGVyZnVsQWxpY2UyMyEAs7KjX9mP2vN5rT8uW0yB3cF6gJ9', 'NORMAL'),
                                                                                                ('bob_builder65', 'Robert', 'Builderton', 'bob.builder65@example.com', '$2y$12$QnVpbGRlcjEyM0A3ODlNYW4hQz6KjL9vR2tY5zA8qW0uB3cF9gJ', 'NORMAL'),
                                                                                                ('charlie_brown54', 'Charles', 'Brownfield', 'charlie.brown54@example.com', '$2y$12$Q2hhcmxpZVNlY3VyZTIwMjIhR5KjX8mP9vN2rT6uW0yB3cA5gJ', 'PREMIUM'),
                                                                                                ('emma_watson43', 'Emmaline', 'Watsonville', 'emma.watson43@example.com', '$2y$12$RW1tYUhhc2hQYXNzd29yZCEAz9KjL2vR5tY8zN0qW3uB6cF9gJ', 'NORMAL'),
                                                                                                ('michael_scott32', 'Michael', 'Scottson', 'michael.scott32@example.com', '$2y$12$TWljaGFlbFN0cm9uZzEyMyEHs6KjX9mP2vR5tY8zN0qW3uA6cJ', 'NORMAL'),
                                                                                                ('sarah_connor21', 'Sarah', 'Connors', 'sarah.connor21@example.com', '$2y$12$VGVybWluYXRvcjIwMjNAISM5KjL8vN2rT6uY9zA0qW3cF6gB9', 'PREMIUM'),
                                                                                                ('david_lee10', 'David', 'Leeman', 'david.lee10@example.com', '$2y$12$RGF2aWRFbmNyeXB0MjAyMyEHs9KjX2mP5vR8tY0zN3qW6cA9gJ', 'NORMAL'),
                                                                                                ('linda_kim99', 'Linda', 'Kimberly', 'linda.kim99@example.com', '$2y$12$TGlubmFTZWN1cmUyMDI0IQMz6KjL9vR2tY5zA8qN0uW3cF6gB', 'PREMIUM');

INSERT INTO userToken (userId, token) VALUES
                                                         (1, 'jwt_john_doe92_8f3k9p2m7v4r1t6'),
                                                         (2, 'jwt_jane_smith88_5x8y1a4c7e9g2'),
                                                         (3, 'jwt_alice_wonder77_2j5k8m3p6r9t'),
                                                         (4, 'jwt_bob_builder65_9v2x5y8a1c4e'),
                                                         (5, 'jwt_charlie_brown54_6t9r2p5m8k'),
                                                         (6, 'jwt_emma_watson43_3e6g9j2k5m8'),
                                                         (7, 'jwt_michael_scott32_1c4e7g9j2k'),
                                                         (8, 'jwt_sarah_connor21_8m3p6r9t2v5'),
                                                         (9, 'jwt_david_lee10_5y8a1c4e7g9j'),
                                                         (10, 'jwt_linda_kim99_2k5m8p3r6t9v');

INSERT INTO userWallet (userId, balance, totalspent) VALUES
                                                (1, 150.75, 1000),
                                                (2, 750.00, 1000),
                                                (3, 320.50, 2300),
                                                (4, 80.25, 5000),
                                                (5, 1250.00, 300),
                                                (6, 450.80, 500),
                                                (7, 95.60, 50),
                                                (8, 1800.90, 80),
                                                (9, 275.30, 50),
                                                (10, 990.15, 2003);

INSERT INTO transaction (userId, itemName, price, quantity) VALUES
                                                                   (1, 'Gaming Laptop', 1499.99, 1),
                                                                   (1, 'Wireless Mouse', 29.99, 2),
                                                                   (1, 'USB-C Hub', 45.50, 1),
                                                                   (2, 'iPhone 14 Pro', 999.99, 1),
                                                                   (2, 'Leather Case', 49.99, 1),
                                                                   (2, 'Screen Protector', 15.99, 3),
                                                                   (3, 'iPad Air', 599.99, 1),
                                                                   (3, 'Stylus Pen', 89.99, 1),
                                                                   (3, 'Bluetooth Earbuds', 129.99, 2),
                                                                   (4, 'Noise-Cancelling Headphones', 199.99, 1),
                                                                   (4, 'Audio Cable', 12.99, 2),
                                                                   (5, '4K Monitor', 349.99, 2),
                                                                   (5, 'HDMI Splitter', 25.99, 1),
                                                                   (5, 'Desk Organizer', 39.99, 1),
                                                                   (6, 'Mechanical Keyboard', 89.99, 1),
                                                                   (6, 'RGB Mouse Pad', 19.99, 2),
                                                                   (6, 'Webcam Cover', 5.99, 5),
                                                                   (7, 'Ergonomic Mouse', 34.99, 1),
                                                                   (7, 'Cable Ties', 7.99, 10),
                                                                   (7, 'Monitor Stand', 45.99, 1),
                                                                   (8, 'Laser Printer', 299.99, 1),
                                                                   (8, 'Toner Cartridge', 79.99, 2),
                                                                   (8, 'Paper Ream', 9.99, 5),
                                                                   (9, 'Smartwatch', 249.99, 1),
                                                                   (9, 'Fitness Band', 39.99, 1),
                                                                   (9, 'Charging Dock', 19.99, 2),
                                                                   (10, 'Home Speaker System', 499.99, 1),
                                                                   (10, 'Audio Receiver', 149.99, 1),
                                                                   (10, 'Extension Cord', 14.99, 3);
