## Требования
Реализовать методы API для работы с кошельком пользователя. Ограничения:
* У пользователя может быть только один кошелек.
* Поддерживаемые валюты: USD и RUB.
* При вызове метода для изменения кошелька на сумму с отличной валютой от
валюты кошелька, сумма должна конвертироваться по курсу.
* Курсы обновляются периодически.
* Все изменения кошелька должны фиксироваться в БД.

### Метод для изменения баланса
Обязательные параметры метода:
* ID кошелька (например: 241, 242)
* Тип транзакции (debit или credit)
* Сумма, на которую нужно изменить баланс
* Валюта суммы (допустимы значения: USD, RUB)
* Причина изменения счета (например: stock, refund). Список причин фиксирован.

### Метод для получения текущего баланса
Обязательные параметры метода:
* ID кошелька (например: 241, 242)

### SQL запрос
Написать SQL запрос, который вернет сумму, полученную по причине refund за
последние 7 дней.

```postgresql
SELECT SUM(amount) FROM transaction WHERE reason_id = 2 AND created_at > CURRENT_DATE - interval '7' day;
```
Примечание: можно хранить 'reason_id', точнее 'reason' и как VARCHAR (stock, refund). INT был выбран для экономии места занимаемого БД.
Аналогично и для type_id.

Так как эти типы постоянны они описаны константами в src/Entity/Transaction.php

Другой тип используемый в этом приложении - валюты. Они описаны в таблице static_currency. При добавлении новой валюты, ее курс будет обновлен автоматически. И ее можно будет использовать в системе. 

## Технические требования
* Серверная логика должна быть написана на PHP версии >=7.0
* Для хранения данных должна использоваться реляционная СУБД
* Должны быть инструкции для развертывания проекта

## Допущения
* Выбор дополнительных технологий не ограничен;
* Все спорные вопросы в задаче может и должен решать Исполнитель;

## Установка

```shell script
# git clone https://github.com/Zazza/api
# cd ./api
# composer install
# cp .env.default .env
# nano .env
```

В файле .env поправьте _DATABASE_URL_

```shell script
# php bin/console doctrine:database:create
Created database "api" for connection named default

# php bin/console doctrine:migrations:migrate
 WARNING! You are about to execute a database migration that could result in schema changes and data loss. Are you sure you wish to continue? (yes/no) [yes]:
 >

[notice] Migrating up to DoctrineMigrations\Version20201224075334
[notice] finished in 55.8ms, used 14M memory, 1 migrations executed, 18 sql queries
```

Добавьте fixtures для проверки работоспособности приложения:

```shell script
# php bin/console doctrine:fixtures:load
 Careful, database "api" will be purged. Do you want to continue? (yes/no) [no]:
 > yes

   > purging database
   > loading App\DataFixtures\AppFixtures
   > loading App\DataFixtures\StaticCurrencyFixtures
   > loading App\DataFixtures\UserFixtures
   > loading App\DataFixtures\WalletFixtures
```

**Добавить команду:** `/usr/local/php /[APP_PATH]/bin/console app:exchange_rates` в cron для обновления курса валют.

## Использование

`# curl -X POST -H 'Content-Type: application/x-www-form-urlencoded' -i 'http://domain/api/wallet/updateBalance/1' --data 'currency=RUB&type=credit&reason=stock&amount=5'`

```shell script
HTTP/1.1 200 OK
Server: nginx/1.19.5
Content-Type: application/json
Transfer-Encoding: chunked
Connection: keep-alive
Cache-Control: no-cache, private
Date: Thu, 24 Dec 2020 14:05:16 GMT
X-Robots-Tag: noindex

{"result":true}
```

`# curl -X GET -H 'Content-Type: application/x-www-form-urlencoded' -i 'http://domain/api/wallet/getBalance/1'`

```shell script
HTTP/1.1 200 OK
Server: nginx/1.19.5
Content-Type: application/json
Transfer-Encoding: chunked
Connection: keep-alive
Cache-Control: no-cache, private
Date: Thu, 24 Dec 2020 14:05:54 GMT
X-Robots-Tag: noindex

{"currency":"RUB","amount":582.09}
```

## Тесты
```shell script
i# ./bin/phpunit
 PHPUnit 7.5.20 by Sebastian Bergmann and contributors.
 
 Testing Project Test Suite
 ...........                                                       11 / 11 (100%)
 
 Time: 1.12 seconds, Memory: 34.00 MB
 
 OK (11 tests, 18 assertions)
```