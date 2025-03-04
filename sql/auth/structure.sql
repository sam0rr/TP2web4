CREATE TABLE userProfile
(
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    username TEXT NOT NULL UNIQUE,
    firstname TEXT NOT NULL,
    lastname TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    type TEXT NOT NULL DEFAULT 'NORMAL'
       CHECK (type IN ('NORMAL', 'PREMIUM'))
);

CREATE TABLE userToken
(
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    userId INT NOT NULL,
    token TEXT NOT NULL UNIQUE,
    createdAt TIMESTAMP DEFAULT NOW(),
    FOREIGN KEY (userid) REFERENCES userProfile(id) ON DELETE CASCADE
);

CREATE TABLE userWallet (
    userId INT PRIMARY KEY,
    balance NUMERIC(10,2) DEFAULT 0 CHECK (balance >= 0),
    totalSpent NUMERIC(10,2) DEFAULT 0,
    FOREIGN KEY (userId) REFERENCES userProfile(id) ON DELETE CASCADE
);

CREATE TABLE transaction (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    userId INT NOT NULL,
    itemName TEXT NOT NULL,
    price NUMERIC(10,2) NOT NULL CHECK (price >= 0),
    quantity INT NOT NULL CHECK (quantity > 0),
    totalPrice NUMERIC(10,2) GENERATED ALWAYS AS (price * quantity) STORED,
    createdAt TIMESTAMP DEFAULT NOW(),
    FOREIGN KEY (userId) REFERENCES userProfile(id) ON DELETE CASCADE
);