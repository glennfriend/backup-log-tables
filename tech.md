## backup-log-tables

### Install & update
```sh
composer self-update
composer install
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
