include .env

up:
	docker-compose up -d --build
down:
	docker-compose down
app:
	docker exec -it ${COMPOSE_PROJECT_NAME}-app-1 bash
db:
	docker exec -it ${COMPOSE_PROJECT_NAME}-pgsql-1 psql -U ${DB_USERNAME} -d ${DB_DATABASE_NAME}
run_tests:
	docker exec -it ${COMPOSE_PROJECT_NAME}-app-1 vendor/bin/phpunit src/tests/user/UserServiceTest.php
migrate:
	docker exec -i ${COMPOSE_PROJECT_NAME}-pgsql-1 psql -U ${DB_USERNAME} -d ${DB_DATABASE_NAME} < src/Database/Migrations/20230902055000_create_users_table.sql
	docker exec -i ${COMPOSE_PROJECT_NAME}-pgsql-1 psql -U ${DB_USERNAME} -d ${DB_DATABASE_NAME} < src/Database/Migrations/20230902163200_create_objects_table.sql
drop:
	docker exec -i ${COMPOSE_PROJECT_NAME}-pgsql-1 psql -U ${DB_USERNAME} -d ${DB_DATABASE_NAME} < src/Database/Migrations/20230902055000_create_users_table_down.sql
	docker exec -i ${COMPOSE_PROJECT_NAME}-pgsql-1 psql -U ${DB_USERNAME} -d ${DB_DATABASE_NAME} < src/Database/Migrations/20230902163200_create_objects_table_down.sql
dump:
	docker exec -i ${COMPOSE_PROJECT_NAME}-pgsql-1 psql -U ${DB_USERNAME} -d ${DB_DATABASE_NAME} < src/Database/Data/20230902055000_dump.sql
	docker exec -i ${COMPOSE_PROJECT_NAME}-pgsql-1 psql -U ${DB_USERNAME} -d ${DB_DATABASE_NAME} < src/Database/Data/20230902163200_dump.sql
getByLogin:
	curl -X GET http://127.0.0.1:80/api/v1/users/ivanpetrov
getAll:
	curl -X GET http://127.0.0.1:80/api/v1/users
createUser:
	curl -X POST http://127.0.0.1:80/api/v1/users/create -H "Content-Type: application/json" -d '{"name": "Ivan", "login": "vanetrov1", "email": "ivn2@example.com", "phone": "+79111234567"}'
updateUser:
	curl -X PUT http://127.0.0.1:80/api/v1/users/update -H "Content-Type: application/json" -d '{"name": "Ivan", "login": "vanetrov1", "email": "ivn2@example.com", "phone": "+79111234567"}'
partialUpdate:
	curl -X PATCH http://127.0.0.1:80/api/v1/users/update -H "Content-Type: application/json" -d '{"login": "vanetrov1", "email": "ivn2@example.com", "phone": "+79111234567"}'
deleteUser:
	curl -X DELETE http://127.0.0.1:80/api/v1/users/vanetrov1
sql_part2:
	docker exec -i ${COMPOSE_PROJECT_NAME}-pgsql-1 psql -U ${DB_USERNAME} -d ${DB_DATABASE_NAME} < src/Database/Queries/task2.sql
python_part3:
	python3 task3.py