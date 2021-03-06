Catpow Mailer
===

<p align="center">
  <img src="https://img.shields.io/badge/PHP-5.4-45A?logo=php">
</p>

Ajax×APIのメールフォームのためのライブラリ。
Nonceや入力検証などメールフォームに必要なAPIを提供します。

Catpow-Mailformにてサブモジュールとして利用されます。
このリポジトリは直接クローンされることを想定しません。
Catpow-Mailformのテンプレートリポジトリをクローンしてください。

概要
--

``mailer.php``にリクエストを投げることで各種処理を行います。
リクエストの``action``に対応した``form``フォルダ内のファイルを実行するのが
Catpow mailer APIの基本動作となります。
各ファイル・ディレクトリの役割は以下の通りです。

### config.php

メールフォームで利用する入力項目、SMTPサーバー、
デフォルトのメールヘッダの設定等を記述するファイルです。

### form

APIの各actionに対応した処理のファイルを置くフォルダです。
当該の実行ファイル内では``Catpow\MailForm``クラスのインスタンスである
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