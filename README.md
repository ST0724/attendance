# coachtech勤怠管理アプリ

## 環境構築
Dockerビルド
1. git clone git@github.com:ST0724/attendance.git
2. docker-compose up -d --build

Laravel環境構築
1. docker-compose exec php bash
2. composer install
3. cp .env.example .env
4. .envファイルの環境変数を適宜変更
5. php artisan key:generate
6. php artisan migrate
7. php artisan db:seed
8. php artisan storage:link

## 使用技術(実行環境)
+ Laravel 8.83.8

## テストアカウント
### 管理ログイン用
name:管理者  
email:admin@example.com  
password:password  
### 一般ログイン用
name:テストユーザー1   
email:test1@example.com  
password:test_user1  

## ER図
[ER図]

## URL
開発環境：[http://localhost/](http://localhost/)

## 備考
php artisan users:reset-status  
上記をphpコンテナ内で実行することで、全ての一般ユーザーのステータスを『勤務外』に変更します。