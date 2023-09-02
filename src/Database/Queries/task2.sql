SELECT
    id,
    MAX(CASE WHEN code = 'name' THEN value END) AS name,
    MAX(CASE WHEN code = 'login' THEN value END) AS login
FROM objects
GROUP BY id;