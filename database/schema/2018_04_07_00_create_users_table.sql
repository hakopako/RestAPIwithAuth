CREATE TABLE IF NOT EXISTS users (
    id BIGSERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    token VARCHAR(50) NULL,
    expire_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT current_timestamp,
    updated_at TIMESTAMP DEFAULT current_timestamp,
    CONSTRAINT unique_users_username UNIQUE (username),
    CONSTRAINT unique_users_token UNIQUE (token)
);

-- UPSERT recipe:recipe
INSERT INTO users (username, password) VALUES ('recipe', '$2y$10$pKnbmVY3kkQ24uqrO6avkuZJHrmUsyhtfL5x0tt1ukfpN1VfoEiAu')
ON CONFLICT (username)
DO UPDATE SET updated_at = now()
where users.username = 'recipe';
