CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255),
    login VARCHAR(255) UNIQUE,
    email VARCHAR(255) UNIQUE,
    phone VARCHAR(255)
);