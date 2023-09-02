1. PHP + PostgreSQL/MySQL
Реализовать REST-сервис:
1) Метод “Создание пользователя” - записывает в БД пользователя, генерирует
   пароль, передаваемые атрибуты: name, login, email, phone, address
2) Метод “Редактирование пользователя” - редактирует в БД атрибуты
3) Метод “Получение пользователя по login” - отдает пользователя по login 
Условие - не использовать фреймворки
Перед началом работы нужно сделать несколько команд:
composer install
make up
make migrate
make dump
# Документация по API

Этот документ описывает доступные эндпоинты и их использование для вашего API.<br>

Получение пользователя по логину<br>
make getByLogin<br>
Получение всех пользователей<br>
make getAll<br>
Создание пользователя<br>
make createUser<br>
Обновление пользователя<br>
make updateUser<br>
Частичное обновление пользователя<br>
make partialUpdate<br>
Удаление пользователя<br>
make deleteUser<br>

2. SQL<br>
   Таблица<br>
   create table objects (<br>
   id int, – ID объекта<br>
   code varchar, - Код атрибута<br>
   value varchar, - Значение<br>
   primary key (id, code)<br>
   );<br>
   Хранение в БД<br>
   id code value<br>
   1 name Петров<br>
   1 login Petrov<br>
   Написать запрос, который группирует строки по id и code - становится колонкой<br>
   ID name login<br>
   1 Петров Petrov<br>
   2 Сидоров Sidorov<br>

make sql_part2<br>

3. Python<br>
   Сделать скрипт, который читает файл лог nginx и выводит топ 3 запроса по<br>
   количеству за указанный промежуток времени. Файл лога nginx может быть очень<br>
   большим<br>
   Условие - реализовать стандартной библиотекой<br>
   
python3 task3.py