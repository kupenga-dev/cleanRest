1. PHP + PostgreSQL/MySQL
Реализовать REST-сервис:
1) Метод “Создание пользователя” - записывает в БД пользователя, генерирует
   пароль, передаваемые атрибуты: name, login, email, phone, address
2) Метод “Редактирование пользователя” - редактирует в БД атрибуты
3) Метод “Получение пользователя по login” - отдает пользователя по login 
Условие - не использовать фреймворки
Перед началом работы нужно сделать несколько команд:
make migrate
make dump
# Документация по API

Этот документ описывает доступные эндпоинты и их использование для вашего API.

Получение пользователя по логину
make getByLogin
Получение всех пользователей
make getAll
Создание пользователя
make createUser
Обновление пользователя
make updateUser
Частичное обновление пользователя
make partialUpdate
Удаление пользователя
make deleteUser

2. SQL
   Таблица
   create table objects (
   id int, – ID объекта
   code varchar, - Код атрибута
   value varchar, - Значение
   primary key (id, code)
   );
   Хранение в БД
   id code value
   1 name Петров
   1 login Petrov
   Написать запрос, который группирует строки по id и code - становится колонкой
   ID name login
   1 Петров Petrov
   2 Сидоров Sidorov

make sql_part2

3. Python
   Сделать скрипт, который читает файл лог nginx и выводит топ 3 запроса по
   количеству за указанный промежуток времени. Файл лога nginx может быть очень
   большим
   Условие - реализовать стандартной библиотекой
   
python3 task3.py