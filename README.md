## 什么是 Razor ?
Razor 是一个快速，简单，可扩展的 PHP 框架。Razor 使您能够快速方便地构建REST web应用程序。

```php
require 'vendor/import.php';

import::module('route');

route::get('/hello/(:any)', function($request, $response, $razor) {
  return $response->write("hello $razor");
});

route::run();
```

## Requirements

Razor requires PHP 5.3 or greater.

## License

Flight is released under the MIT license.


## 路由
在 Razor 中的路由是通过匹配的URL模式与回调函数。

```php
route::get('/', function($request, $response) {
  echo 'index';
});
```

当然也可以这样表示

```php
route::get('/', 'hello');

function hello($request, $response){
	echo 'index';
}
```
### 请求方式

route 是路由模块的静态类，您可以通过执行route类的各种静态方法来响应特定方法。
```php
route::get('/get', function($request, $response) {
	echo '我收到了一个 GET 请求';
});

route::post('/post', function($request, $response) {
	echo '我收到了一个 POST 请求';
});
```



使用下面的方法，可以响应任意请求不管是GET或者POST.

```php
route::any('/any', function($request, $response) {
	echo sprintf('我收到了一个 %s 请求', $request->method);
});
```
你可能已经注意到了回调函数中的 ```$request``` 和 ```$response```，这两个参数，这两个参数是```request```和```response```类的实例，是回调函数的必须参数。这两个参数很有用，之后会介绍。
### 通配符路由

一个典型的使用通配符的路由规则如下:
```php
route::get('/hello/(:any)', function($request, $response, $name) {
	echo 'hello ' . $name;
});

```
该方法第一个参数是要匹配的路由URL,其中```(:any)``` 通配符用来匹配任意值。在回调函数中我们使用了```$name```参数来接收这个值。

上面典型的路由匹配的是一个，匹配多个值的时候，回调函数中的参数位置对应匹配的值，参数名自定。

```php
route::get('/(:num)/(:num)/(:num)', function($request, $response, $year, $month, $day) {
	echo $year . '/' . $month . '/' . $day;
});
```
```(:num)``` 匹配只含有数字的一段。 ```(:any)``` 匹配含有任意字符的一段,正则匹配暂不支持。

## Request（请求）

得到当前请求的路径、方法、ip 
```php
route::get('/', function($request, $response) {
	echo $request->path .'<br>';
	echo $request->method .'<br>';
	echo $request->ip .'<br>';
});
```
接受get参数
localhost:3000/home?name=razor&age=0
```php
route::get('/home', function($request, $response) {
	var_dump($request->get());
	// or
	echo $request->get('name');
});
```
接受post参数
```php
route::any('/home', function($request, $response) {
	var_dump($request->post());
	// or
	echo $request->post('name');
});
```
判断当前请求类型

```php
route::any('/', function($request, $response) {
	if($request->isGET()) {
		echo '当前是 GET 请求';
	}

	if($request->isPost()) {
		echo '当前是 Post 请求';	
	}
});
```
## Response（响应）
重定向
```php
route::get('/', function($request, $response) {
	return $response->redirect('/home');
});
```
返回HTTP状态码
```php
route::get('/', function($request, $response) {
	return $response->statusCode(404);
});
```
渲染视图
```php
route::get('/', function($request, $response) {
	return $response->view('index');
});
```

或者
```php
route::get('/', function($request, $response) {
	return $response->view('index', ['title' => '首页']);
});
```
## 数据库操作
首先找到根目录下configs.php配置数据库连接信息，然后引入数据库操作对象```db```。
```php
import::module('db');
```
### 原生语句

### 结构化查询

你可以使用 ```db``` 的 ```table``` 方法开始查询。这个 ```table``` 方法针对查询表返回一个查询构造器实例，允许你在查询时链式调用更多约束，并使用 get 方法获取最终结果：
```php
<?php
require '../vendor/import.php';

import::module('route');
import::module('log');
import::module('db');

route::get('/', function($request, $response) {
	 $users = db::table('users')->get();
});

route::run();
```
get 方法有一个参数，默认是```*```,执行成功返回一个数组。

#### 从数据表中获取单个列或行
如果你只需要从数据表中获取一行数据，则可以使用 first 方法。这个方法将返回单个关联数组：
```php
$user = db::table('users')->where('name', 'John')->first();

echo $user['name'];
```
如果你不需要一整行数据，则可以带上参数来从单条记录中取出单个值。此方法将直接返回字段的值：
```php
$name= db::table('users')->where('name', 'John')->first(‘name’);

echo $name;
```
#### find 子句
如果你的某个表主键名正好叫```id```,你可以这样找到它。
```php
db::table('users')->find($id);
```
如果它叫其它什么名
```php
db::table('users')->find($id, 'user_id');
```


#### orderBy 子句
orderBy 方法允许你根据指定字段对查询结果进行排序。orderBy 方法的第一个参数是你想要用来排序的字段，而第二个参数则控制排序的顺序，可以为 asc 或 desc：
```php
 db::table('users')->orderBy('id')->get();
```
#### Where 子句
你可以在查询构造器实例中使用 where 方法从而把 where 子句加入到这个查询中。基本的 where 方法需要3个参数。第一个参数是字段的名称。第二个参数是要对字段进行评估的值。第三个参数是运算符，可选参数默认为```=```,它可以是数据库所支持的任何运算符。
```php
$users = db::table('users')->where('votes', 100)->get();

$users = db::table('users')->where('votes', 100, '>')->get();
```
#### take 子句
你可以使用take 方法来限制查询结果数量，两个参数第一个是起始位置，第二个是取多少条数据：
```php
$users = db::table('users')->take(10, 20)->get();
```
### insert 方法
查询构造器也提供了 insert 方法，用来插入记录到数据表中。insert 方法接收一个包含字段名和值的数组作为参数：
```php
db::table('users')->insert(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
执行成功返回受影响的行，失败返回```false```.
#### 自增 ID
（无）
### Updates 方法

当然，除了在数据库中插入记录外，你也可以使用 update 来更新已存在的记录。update 方法和 insert 方法一样，接收含有字段及值的数组，其中包括要更新的字段。可以使用 where 子句来约束 update 查找：
```php
db::table('users')->where('id', 1)->update(['votes' => 1]);
```

#### 自增或自减
（无）
### Delete 方法
查询构造器也可使用 delete 方法从数据表中删除记录。在 delete 前，还可使用 where 子句来约束 delete 语法：
```php
db::table('users')->delete();

db::table('users')->where('votes', '>', 100)->delete();
```
