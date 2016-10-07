## backup-log-tables
- database tabels 之中有些是存放 logs, 該程式用來 dump logs-tables
- 輸出格式為 SQL file, 日後有需要可以手動重新匯入至一個 table
- 以 一個月 為單位建立 sql backup
- 注意, 備份的 sql 裡面包含的語句敘述
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