## backup-log-tables
- 用來備份 database 中可以清除掉的 log
- 以 一個月 為單位建立 sql backup
- 你可以手動將單一個 sql backup 恢復至一個 table
- 備份的 sql 裡面包含有的語句敘述
    - drop table 
    - create table schema
    - insert data

### Environment Request
- [x] PHP 5.6
- [x] composer (https://getcomposer.org/)

### Developer Note
- 對搜尋的 日期欄位 做 index 以加速備份時間

### Installation
```sh
composer self-update
composer install
cp example.config.php config.php
vi config.php
```

### Install & update
```sh
php autorun.php
```

### Execute
```sh
php backup.php
```