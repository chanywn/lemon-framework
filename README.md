# lemon-framework
🍋 是一个现代的 PHP 框架
#### 相关项目

基于 lemon 框架的博客项目 

https://blog.codefun.cn/

#### 如何使用

1.安装
```
composer require chanywn/lemon dev-master
```
2.新建index.php文件
```
require 'vendor/autoload.php';

use Lemon\Route;

Route::get('/', function($request, $response){
	return $response->write('Hello lemon');
});

Route::run();
```
3.执行内置服务器

```
php -S localhost:4000
```
4.访问 localhost:4000