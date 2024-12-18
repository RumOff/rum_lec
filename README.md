[![Testing](https://github.com/NEWONE-INC/oseru-survey/actions/workflows/laravel.yml/badge.svg?branch=main)](https://github.com/NEWONE-INC/oseru-survey/actions/workflows/laravel.yml)

## 開発環境の構築

まずは<u>[実行のための依存パッケージ](https://readouble.com/laravel/10.x/ja/upgrade.html#updating-dependencies)</u>を確認してください。

以下コマンドラインにて順番に実行します。
＊Laravel Sailを使って、Dockerコンテナを管理します。
| 順番 | コマンド |
| --- | --- |
| 1. git clone | `git clone git@github.com:NEWONE-INC/oseru-survey` |
| 2. 移動 | `cd oseru-survey` |
| 3. 設定ファイル作成 | `cp .env.example .env` |
| 4. モジュールinstall | `composer install && npm install` |
| 5. DBの起動 | `./vendor/bin/sail up` |
| 6. マイグレーション | `./vendor/bin/sail artisan migrate` |
| 7. Seed | `./vendor/bin/sail artisan db:seed` |
| 8. サーバー起動 | `./vendor/bin/sail artisan serve` |
### エイリアスの設定
Sailのコマンドを簡単に実行するために、エイリアスを設定することができます。シェルの設定ファイルにエイリアスを追加し、永続的に使用可能にします。
```
ex) ./vendor/bin/sail up --> sail up
```
##### エイリアスの設定手順
- エイリアスを ~/.bashrc に追加
```
echo "alias sail='bash ./vendor/bin/sail'" >> ~/.bashrc
source ~/.bashrc
```
*zshを使用している場合は、.zshrcに追加してください。

## 開発テスト
### Unit/Featureテスト
```
./vendor/bin/sail artisan test
```
### Duskテスト
```
./vendor/bin/sail dusk
```
## ローカル環境でのログイン方法
### 【ユーザー側】

（１）「Table Plus」などのSQLクライアントツールを使って以下の情報でログインする

```
DBホスト名：127.0.0.1
DBポート番号：3306
DB名：oseru-survey
DBユーザーネーム：newone
DBパスワード：secret
```

（２）database::adminsに登録されているメールアドレスを調べる

- [管理画面ログインページ](http://127.0.0.1:8000/admin/login)からメールアドレスとパスワードでログイン
- パスワードは「 password」

（３）必要に応じて自分用の管理者アカウントを作成する
