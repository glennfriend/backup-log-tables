## backup-log-tables-tool
- database tabels 之中有些是存放 logs, 該程式用來 dump logs-tables
- 輸出格式為 SQL file, 日後有需要可以手動匯入至您的 database 之中 (table name 已固定)
- 固定以 一個月 為單位建立一個 SQL backup 檔案
- 程式本身只收集、管理需要備份的資訊, 真正的備份方式是, 外部直接呼叫 mysqldump 指令
- 即使當月沒有內容, 還是會輸出帶有 schema 格式的 SQL file backup

### Environment Request
- [x] PHP 5.6 ~ PHP 7
- [x] composer (https://getcomposer.org/)

### Installation
```sh
cp example.config.php config.php
vi config.php
php autorun.php
```

### Execute
```sh
php backup.php
```
