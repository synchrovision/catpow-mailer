Catpow Mailer
===

<p align="center">
  <img src="https://img.shields.io/badge/PHP-7.4-45A?logo=php">
</p>

Ajax×APIのメールフォームのためのライブラリ。
Nonceや入力検証などメールフォームに必要なAPIを提供します。

メールフォームのディレクトリに移動して

 ```command
git clone --recursive https://github.com/synchrovision/catpow-mailer.git mailer
 ```
 
または、サブモジュールとしてサイトのリポジトリにて

 ```command
git submodule add https://github.com/synchrovision/catpow-mailer.git contact/mailform/mailer
 ```
 
でインストール

CLIで``setup.php``を実行すると親フォルダに各種設定ファイルとテンプレートの雛形を生成します。

概要
--

``mailer.php``にリクエストを投げることで各種処理を行います。
リクエストの``action``に対応した``action``フォルダ内のファイルを実行するのが
Catpow mailer APIの基本動作となります。
各ファイル・ディレクトリの役割は以下の通りです。

### config.php

メールフォームで利用する入力項目、SMTPサーバー、
デフォルトのメールヘッダの設定等を記述するファイルです。

### action

APIの各actionに対応した処理のファイルを置くフォルダです。
当該の実行ファイル内では``Catpow\Mailer``クラスのインスタンスである
``$mailer``が定義されており、このオブジェクトの各メソッド用いて
Nonce発行・検証、入力検証、メール送信等の処理を実行します。
``Catpow\REST_Response``クラスのインスタンスである
``$res``のメソッドでAPIのレスポンスの内容をセットします。
ファイル内の出力はレスポンスの``html``パラメータにセットされます。

### mail

メールの送信先・送信元、文面を設定ファイルを置くフォルダです。
``$mailer``の``send_mail``メソッド実行時に
引数に対応したファイルが呼び出されます。  
当該の実行ファイル内でfrom,to,subject,isHTML等の値を設定した
``$conf``の連想配列を定義することで
メールの各種ヘッダなどを設定をすることができます。
ファイル内の出力はメールの``Body``として利用されます。


### classes

PHPのクラスのオートロードの対象となるフォルダです。
フォームで用いることができる入力タイプや入力検証は
クラスを定義することで拡張することができます。

### log

送信されたメールの履歴を保存するディレクトリです。