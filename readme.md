## backup-log-tables
- database tabels 之中有些是存放 logs, 該程式用來 dump logs-tables
- 輸出格式為 SQL file, 日後有需要可以手動重新匯入至一個新的 table
- 固定以 一個月 為單位建立一個 SQL backup 檔案
- 程式本身只收集、管理需要備份的資訊, 真正的備份方式是, 外部直接呼叫 mysqldump 指令

### Environment Request
- [x] PHP 5.6 ~ PHP 7
- [x] composer (https://getcomposer.org/)

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

### 備份的 SQL file 裡面包含的語句敘述
- drop table 
- create table schema
- insert data

### 加快備份的速度
- 對搜尋的 日期欄位 做 index 以加速備份時間

### 日期欄位的測試
- [x] timestamp
- [ ] datetime
- [ ] date

### 備份的檔案名稱
```
備份進行中 => table_name.200012.running
備份已完成 => table_name.200012.sql
```

### 已知問題
```
備份的內容預設會包含 drop table & create table schema 語句
如果要同時匯入 2 個以上的 備份 請自行移除這些語句
如果剛好遇上 2 個 schema 有差界
必須先自行處理, 讓資料相同之後再匯入
```
