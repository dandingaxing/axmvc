AliyunOSS
---------

```
    ___     __    _                                    ____    _____   _____
   /   |   / /   (_)   __  __  __  __   ____          / __ \  / ___/  / ___/
  / /| |  / /   / /   / / / / / / / /  / __ \        / / / /  \__ \   \__ \
 / ___ | / /   / /   / /_/ / / /_/ /  / / / /       / /_/ /  ___/ /  ___/ /
/_/  |_|/_/   /_/    \__, /  \__,_/  /_/ /_/        \____/  /____/  /____/
                    /____/
```

AliyunOSS 是阿里云 OSS 官方 SDK 的 Composer 封装，支持任何 PHP 项目，包括 Laravel、Symfony、TinyLara 等等。


## 更新记录

* 2016-09-12 `Release v1.3.5` 加入文件元信息的设置功能
* 2016-07-20 `Release v1.3.4` 加入文件元信息的获取功能
* 2016-01-31 `Release v1.3.2` 获取指定虚拟文件夹下的所有文件
* 2015-10-23 `Release v1.3` 增加删除、复制、移动文件功能
* 2015-08-07 `Release v1.2` 修复内存泄露 bug
* 2015-01-12 `Release v1.1` 增加内外网配置分离
* 2015-01-09 `Release v1.0` 完善功能，增加 Laravel 框架详细使用教程及代码

## 安装

安装有两种方式：

### ① 直接编辑配置文件

将以下内容增加到 composer.json：

```json
require: {
    "johnlui/aliyun-oss": "~1.3"
}
```

然后运行 `composer update`。

### ② 执行命令安装

运行命令：

```bash
composer require johnlui/aliyun-oss:~1.3
```

## 使用（以 Laravel 为例）

### 构建 Service 文件

新建 `app/services/OSS.php`，内容可参考：[OSSExample.php](https://github.com/johnlui/AliyunOSS/blob/master/OSSExample.php)：

```php
<?php

namespace App\Services;

use JohnLui\AliyunOSS\AliyunOSS;

use Config;

class OSS {

  private $ossClient;

  public function __construct($isInternal = false)
  {
    $serverAddress = $isInternal ? Config::get('app.ossServerInternal') : Config::get('app.ossServer');
    $this->ossClient = AliyunOSS::boot(
      $serverAddress,
      Config::get('app.AccessKeyId'),
      Config::get('app.AccessKeySecret')
    );
  }

  public static function upload($ossKey, $filePath)
  {
    $oss = new OSS(true); // 上传文件使用内网，免流量费
    $oss->ossClient->setBucket('你的 bucket 名称');
    return $oss->ossClient->uploadFile($ossKey, $filePath);
  }
  /**
   * 直接把变量内容上传到oss
   * @param $osskey
   * @param $content
   */
  public static function uploadContent($osskey,$content)
  {
    $oss = new OSS(true); // 上传文件使用内网，免流量费
    $oss->ossClient->setBucket('你的 bucket 名称');
    return $oss->ossClient->uploadContent($osskey,$content);
  }

  /**
   * 删除存储在oss中的文件
   *
   * @param string $ossKey 存储的key（文件路径和文件名）
   * @return
   */
  public static function deleteObject($ossKey)
  {
      $oss = new OSS(true); // 上传文件使用内网，免流量费

      return $oss->ossClient->deleteObject('你的 bucket 名称', $ossKey);
  }

  /**
   * 复制存储在阿里云OSS中的Object
   *
   * @param string $sourceBuckt 复制的源Bucket
   * @param string $sourceKey - 复制的的源Object的Key
   * @param string $destBucket - 复制的目的Bucket
   * @param string $destKey - 复制的目的Object的Key
   * @return Models\CopyObjectResult
   */
  public function copyObject($sourceBuckt, $sourceKey, $destBucket, $destKey)
  {
      $oss = new OSS(true); // 上传文件使用内网，免流量费

      return $oss->ossClient->copyObject($sourceBuckt, $sourceKey, $destBucket, $destKey);
  }

  /**
   * 移动存储在阿里云OSS中的Object
   *
   * @param string $sourceBuckt 复制的源Bucket
   * @param string $sourceKey - 复制的的源Object的Key
   * @param string $destBucket - 复制的目的Bucket
   * @param string $destKey - 复制的目的Object的Key
   * @return Models\CopyObjectResult
   */
  public function moveObject($sourceBuckt, $sourceKey, $destBucket, $destKey)
  {
      $oss = new OSS(true); // 上传文件使用内网，免流量费

      return $oss->ossClient->moveObject($sourceBuckt, $sourceKey, $destBucket, $destKey);
  }

  public static function getUrl($ossKey)
  {
    $oss = new OSS();
    $oss->ossClient->setBucket('你的 bucket 名称');
    return $oss->ossClient->getUrl($ossKey, new \DateTime("+1 day"));
  }

  public static function createBucket($bucketName)
  {
    $oss = new OSS();
    return $oss->ossClient->createBucket($bucketName);
  }

  public static function getAllObjectKey($bucketName)
  {
    $oss = new OSS();
    return $oss->ossClient->getAllObjectKey($bucketName);
  }

  /**
   * 获取指定Object的元信息
   * 
   * @param  string $bucketName 源Bucket名称
   * @param  string $key 存储的key（文件路径和文件名）
   * @return object 元信息
   */
  public static function getObjectMeta($bucketName, $osskey)
  {
      $oss = new OSS();
      return $oss->ossClient->getObjectMeta($bucketName, $osskey);
  }
}
```

### 放入自动加载

#### 遵循 psr-0 的项目（如Laravel 4、CodeIgniter、TinyLara）中：
在 `composer.json` 中 `autoload -> classmap` 处增加配置：

```json
"autoload": {
    "classmap": [
      "app/services"
    ]
  }
```
然后运行 `composer dump-autoload`。

#### 遵循 psr-4 的项目（如 Laravel 5、Symfony）中：

无需配置，保证目录 `App/Services` 和命名空间 `namespace App\Services;` 一致即可自动加载。

### 增加相关配置
在 app/config/app.php 中增加四项配置：

```php
# 注意在设置『内网』和『外网』的时候，地址不要加上 bucketName

'ossServer' => 'http://服务器外网地址', //青岛为 http://oss-cn-qingdao.aliyuncs.com
'ossServerInternal' => 'http://服务器内网地址', //青岛为 http://oss-cn-qingdao-internal.aliyuncs.com
'AccessKeyId' => '阿里云给的AccessKeyId',
'AccessKeySecret' => '阿里云给的AccessKeySecret',
```

### 使用

```php
use App\Services\OSS;

// 上传一个文件
OSS::upload('文件名', '本地路径');

// 打印出某个文件的外网链接
echo OSS::getUrl('某个文件的名称');

// 新增一个 Bucket。注意，Bucket 名称具有全局唯一性，也就是说跟其他人的 Bucket 名称也不能相同。
OSS::createBucket('一个字符串');

// 获取该 Bucket 中所有文件的文件名，返回 Array。
OSS::getAllObjectKey('某个 Bucket 名称'); 

// 指定 options 如：Content-Type 类型
OSS::upload('文件名', '文件路径', [
    'ContentType' => 'application/pdf',
    // ...
    
])
```

> 更多上传参数见：[这里](https://github.com/johnlui/AliyunOSS/blob/master/oss/src/Aliyun/OSS/OSSClient.php#L142-L148)
> 更多使用方法见：[这里](https://github.com/johnlui/AliyunOSS/blob/master/OSSExample.php)

## 反馈

有问题请到 http://lvwenhan.com/laravel/425.html 下面留言。

## License
除 “版权所有（C）阿里云计算有限公司” 的代码文件外，遵循 [MIT license](http://opensource.org/licenses/MIT) 开源。


