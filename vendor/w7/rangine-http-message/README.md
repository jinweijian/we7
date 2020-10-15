# we7HttpMessage

swoole request / response 的 psr 标准的实现，fork 自  Swoft Http-message 组件，感谢 Swoft 团队。

支持下载文件及发送文件Chunk。兼容Fpm模式和Swoole Server模式，根据不同的来源来构造 Request 和 Response

# use 

#### 初始化 Swoole Request Response对象

```php
$server = new \Swoole\Http\Server('0.0.0.0', 88, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);

$server->on('request', function ($request, $response) {
	$psr7Request = \W7\Http\Message\Server\Request::loadFromSwooleRequest($request);
	$psr7Response = new \W7\Http\Message\Server\Response();
	$psr7Response->setOutputer(new \W7\Http\Message\Outputer\SwooleResponseOutputer($response));
	
	//获取 Post 
	$code = $psr7Request->post('code');

	// 发送一个文件
	// $filePath 下载的文件物理路径
	// $startPos 需要分片下载时，指定文件的开始位置 
	// $chunkFileSize 需要分片下载时，每个分片的大小
	$psr7Response->withFile(new File($filePath, $startPos, $chunkFileSize));
});

$server->start();
```

#### 初始化 Fpm Request Response对象

```php
$psr7Request = \W7\Http\Message\Server\Request::loadFromFpmRequest();
$psr7Response = new \W7\Http\Message\Server\Response();
$psr7Response->setOutputer(new \W7\Http\Message\Outputer\FpmResponseOutputer());
```

其它使用方法参考软擎开发文档 

[请求 Request](https://wiki.w7.cc/chapter/1?id=106#)
[响应 Response](https://wiki.w7.cc/chapter/1?id=110#)