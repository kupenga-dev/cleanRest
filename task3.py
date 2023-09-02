import re
import os
from collections import Counter
from datetime import datetime, timezone, timedelta

# Функция для парсинга времени из строки лога nginx
def parse_nginx_time(log_time):
    try:
        # Пример формата времени в логе: "17/Aug/2023:12:34:56 +0000"
        return datetime.strptime(log_time, "%d/%b/%Y:%H:%M:%S %z")
    except ValueError:
        return None

# Функция для чтения лога nginx и подсчета запросов за указанный промежуток времени
def top_requests_by_time(log_file, start_time, end_time, top_n=3):
    requests_counter = Counter()

    with open(log_file, 'r') as file:
        for line in file:
            # Пример строки лога nginx: "127.0.0.1 - - [17/Aug/2023:12:34:56 +0000] "GET /page HTTP/1.1" 200 ..."
            match = re.search(r'\[([^]]+)\]', line)
            if match:
                log_time_str = match.group(1)
                log_time = parse_nginx_time(log_time_str)
                if log_time and start_time <= log_time <= end_time:
                    request_line = line.split('"')[1]
                    requests_counter[request_line] += 1

    top_requests = requests_counter.most_common(top_n)
    return top_requests

if __name__ == "__main__":
    log_file = "system/nginx/access.log"  # Путь к файлу лога nginx
    if not os.path.exists(log_file):
        print("Файл лога '{}' не существует.".format(log_file))
        exit(1)
    current_time = datetime.now(timezone.utc)
    start_time = current_time - timedelta(days=1)  # Начальное время
    end_time = current_time  # Конечное время

    top_n = 3  # Топ N запросов

    top_requests = top_requests_by_time(log_file, start_time, end_time, top_n)
    print("Топ {} запросов за указанный промежуток времени:".format(top_n))
    for i, (request_line, count) in enumerate(top_requests, 1):
        print("{}. Запрос: '{}', Количество: {}".format(i, request_line, count))