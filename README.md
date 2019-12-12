
mashape/unirest-php 简单易用的HTTP请求库    官网地址
guzzlehttp/guzzle   功能强大的HTTP请求库    文档
hassankhan/config   轻量级配置加载类,支持多种配置格式PHP, INI, XML, JSON, and YML
desarrolla2/cache   简单的缓存类,提供多种缓存驱动Apc, Apcu, File, Mongo, Memcache, Memcached, Mysql, Mongo, Redis
hashids/hashids 数字ID生成类似优酷视频ID,支持多语言,支持加盐生成 官网地址
sika/sitemap    XML网站地图生成器
catfan/medoo    简单易用数据库操作类 支持各种常见数据库    文档
rize/uri-template   URL生成
jdorn/sql-formatter SQL语句格式化 支持语法高亮
intervention/image  图片处理,提供对图片的各种操作:获取图片信息,上传,格式转换,缩放,裁剪等等等 文档
phpmailer/phpmailer 邮件发送
phpoffice/phpexcel  excel操作类    文档
league/route    路由调度    文档
willdurand/jsonp-callback-validator JSONP callback参数验证 防止XSS攻击
michelf/php-markdown    PHP markdown 解析 官网
erusev/parsedown    PHP markdown 解析 演示 文档
league/html-to-markdown HTML转markdown
monolog/monolog 日志操作 composer官方就是用它做例子  文档
phpcollection/phpcollection PHP 集合操作    文档
seld/jsonlint   JSON 语法检查
geoip2/geoip2   IP地理位置信息
league/csv  CSV操作类  例子
jalle19/php-whitelist-check IP/网址黑白名检查 支持模糊匹配
shark/simple_html_dom   php解析html类库 文档
naux/auto-correct   自动给中英文之间加入合理的空格并纠正专用名词大小写
symfony/var-dumper  PHP打印输出
endroid/qrcode 二维码
tecnickcom/tcpdf 支持中文的pdf生成
dompdf/dompdf HTML 转 pdf
zizaco/entrust RBAC权限管理
noahbuscher/macaw 路由调度（注：太过于简单功能不完善且不好用）
slim/slim: 路由调度,轻量 HTTP framework




修改
vendor\catfan\medoo\src\Medoo.php
中 protected function columnQuote($string) 方法为：
    protected function columnQuote($string)
    {
        if (strpos($string, 'DISTINCT')!==false) {
            return $string;
        }else{
            if (strpos($string, '.') !== false)
            {
                return '"' . $this->prefix . str_replace('.', '"."', $string) . '"';
            }

            return '"' . $string . '"';
        }
    }
增加 对关键字 DISTINCT 的支持，在count统计中尤为管用
