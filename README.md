Catpow Mailer
===

<p align="center">
  <img src="https://img.shields.io/badge/PHP-5.4-45A?logo=php">
</p>

入力ステップの追加・分岐や、入力タイプ・入力検証の拡張の仕組みを備える、柔軟で拡張性の高い、中・上級者向けPHPのメールフォームです。
Ajaxでページを遷移せずに動作します。

使い方
--

### インストール

サイトのディレクトリに移動してこのリポジトリを任意の空ディレクトリにクローン、またはサブモジュールとして追加します。


リポジトリをクローン
 ```command
git clone --recursive https://github.com/synchrovision/catpow-mailer.git mailform/mailer
 ```

サブモジュールとして追加
 ```command
git submodule add https://github.com/synchrovision/catpow-mailer.git mailform/mailer
 ```

### セットアップ

setup.phpを実行して、各種設定ファイルをインストールしたディレクトリに生成します。

```command
php mailform/mailer/setup.php
```

### 設定

生成された各種設定ファイルを書き換えて、フォームの送信先・入力項目、入力画面・確認画面・送信画面、メールの文面等の設定を行います。

### 配置

メールフォームを配置したい場所に埋め込み用のhtmlを挿入します。
ディレクトリ名は適宜変更してください。

```html
<form class="cmf-form"></form>
<script src="mailform/mailer/mailer.php"></script>
<link rel="stylesheet" href="mailform/css/theme-standard.css"/>
```


設定概要
--

### config.php

メールフォームで利用する入力項目、SMTPサーバー、
デフォルトのメールヘッダの設定等を記述するファイルです。

### form

APIの各actionに対応した処理のファイルを置くフォルダです。
フォーム読み込み時、最初にこのフォルダ内の``init.php``が実行されます。
フォームからのリクエストによる各ファイルの実行時は``Catpow\MailForm``クラスのインスタンスである
``$form``が定義されており、このオブジェクトの各メソッド用いて
入力検証、メール送信等の処理を実行します。
``Catpow\REST_Response``クラスのインスタンスである
``$res``のメソッドでAPIのレスポンスの内容をセットします。
ファイル内の出力はレスポンスの``html``パラメータにセットされます。

### mail

メールの送信先・送信元、文面を設定ファイルを置くフォルダです。
``$form``の``send``メソッド実行時に
引数に対応したファイルが呼び出されます。  
当該の実行ファイル内で$from,$to,$subject,$isHTMLの
変数の値を設定することでメールの各種ヘッダなどを設定をすることができます。
ファイル内の出力はメールの``Body``として利用されます。
``*-alt.php``のファイルが存在する場合は``AltBody``として利用されます。

### classes

PHPのクラスのオートロードの対象となるフォルダです。
フォームで用いることができる入力タイプや入力検証は
クラスを定義することで拡張することができます。

### log

送信されたメールの履歴のCSVを保存するディレクトリです。
初回メール送信時に自動で生成され、config.phpに設定した
``user``と``password``の値によってBasic認証を設置します。