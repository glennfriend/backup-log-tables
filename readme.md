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
cp example.config.php config.php
vi config.php
```

### Execute
```sh
php backup.php
```
