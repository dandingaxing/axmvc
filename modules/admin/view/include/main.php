<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <title>后台统一地址</title>
    <!-- <link rel="stylesheet" type="text/css" href="/adminui/bootstrap/css/bootstrap.min.css"> -->
    <link rel="stylesheet" type="text/css" href="/adminui/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/adminui/bootstrap/css/style.css">
    
    <script type="text/javascript" src="/adminui/bootstrap/js/jquery-1.10.1.min.js"></script>
    <script type="text/javascript" src="/adminui/bootstrap/js/bootstrap.min.js"></script>
</head>
<body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand" href="<?php echo $this->createUrl('/admin/index/index'); ?>">文章管理首页</a>
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav">
            <li  class="dropdown">
                <a href="<?php echo $this->createUrl('/admin/article/list'); ?>">文章管理</a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="<?php echo $this->createUrl('/admin/article/list'); ?>">文章列表</a>
                        <a href="<?php echo $this->createUrl('/admin/article/add'); ?>">添加文章</a>
                        <a href="<?php echo $this->createUrl('/admin/article/recycle'); ?>">回收站</a>
                    </li>
                </ul>
            </li>
            <li  class="dropdown">
                <a href="<?php echo $this->createUrl('/admin/tags/list'); ?>">栏目管理</a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="<?php echo $this->createUrl('/admin/tags/list'); ?>">栏目列表</a>
                        <a href="<?php echo $this->createUrl('/admin/tags/add'); ?>">添加栏目</a>
                    </li>
                </ul>
            </li>
            <li  class="dropdown">
                <a href="<?php echo $this->createUrl('/admin/advert/list'); ?>">广告位管理</a>
                <ul class="dropdown-menu">
                    <li>
                      <a href="<?php echo $this->createUrl('/admin/advert/list'); ?>">广告位列表</a>
                      <a href="<?php echo $this->createUrl('/admin/advert/add'); ?>">添加广告位</a>
                    </li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="<?php echo $this->createUrl('/admin/words/list'); ?>">标签管理</a>
                <ul class="dropdown-menu">
                    <li>
                      <a href="<?php echo $this->createUrl('/admin/words/list'); ?>">标签列表</a>
                      <a href="<?php echo $this->createUrl('/admin/words/add'); ?>">添加标签</a>
                    </li>
                </ul>
            </li>
            <li  class="dropdown">
                <a href="<?php echo $this->createUrl('/admin/pinglun/list'); ?>">评论管理</a>
                <ul class="dropdown-menu">
                    <li>
                      <a href="<?php echo $this->createUrl('/admin/pinglun/list'); ?>">评论列表</a>
                    </li>
                    <li>
                      <a href="<?php echo $this->createUrl('/admin/pinglun/list', array('status'=>'0')); ?>">评论待审核列表</a>
                    </li>
                </ul>
            </li>

          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="javascript:void(0)">欢迎：管理员<?php // echo $this->userName; ?></a></li>
            <li>
                <li><a href="<?php echo $this->createUrl('/admin/default/logout'); ?>">登出</a></li>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <script style="text/javascript">
        $('li.dropdown').mouseover(function() {
            $(this).addClass('open');    }).mouseout(function() {        $(this).removeClass('open');
        }); 
    </script>
    
    <div class="container" style="margin-top:50px"></div>


    <?php echo $content; ?>
</body>
</html>

<script type="text/javascript">
  function adminFormGet(obj){
    var formGetUrl = $(obj).attr('action');
    var formArr = $(obj).serializeArray();
    $.each(formArr, function() {
      formGetUrl += '&' + [this.name] + '=' + this.value;
    });
    window.location.href=formGetUrl;
    return false;
  }
</script>