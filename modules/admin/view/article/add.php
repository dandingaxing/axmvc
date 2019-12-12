
<div class="container">
  <form class="form-horizontal" enctype="multipart/form-data" id="myform" method="post" action="<?php echo $this->createUrl('insert'); ?>">

    <?php echo self::app('http/csrf')->csrf(); ?>

    <div class="list-group-item form-group">
      <label class="control-label col-md-1">文章标题：</label>
      <div class="col-md-5">
        <input class="form-control" name="title" type="text" maxlength="255" value="">
      </div>
      <div class="col-md-3">
        <span class="mright15">
          <span class="pull-left media-social mtop7 mright10">状态：</span>
          <label class="radio-inline">
            <input type="radio" name="status" value="1">正常
          </label>
          <label class="radio-inline">
            <input type="radio" name="status" value="0">禁用
          </label>
        </span>
      </div>
    </div>

    <div class="list-group-item form-group">
      <label class="control-label col-md-1">副标题：</label>
      <div class="col-md-5">
        <input class="form-control" name="subtitle" type="text" maxlength="255" value="">
      </div>
    </div>

    <div class="list-group-item form-group">
      <label class="control-label col-md-1">关键字：</label>
      <div class="col-md-5">
        <input class="form-control" name="keywords" type="text" maxlength="255" value="">
      </div>
    </div>

    <div class="list-group-item form-group">
      <label class="control-label col-md-1" for="thumbs">标签：</label>
      <div class="col-md-10">
        <?php foreach ( (array) $taglist as $key => $tags): ?>
            <div class="col-md-2">
              <?php echo $tags['con']['tagsname'] ?> ：
            </div>
            <div class="col-md-10">
                <?php foreach ( (array) $tags['children'] as $key => $tag): ?>
                  <label class="col-md-2">
                    <input type="checkbox" name="tags[<?php echo $tags['con']['id'] ?>][<?php echo $tag['id'] ?>]" value="<?php echo $tag['id'] ?>"> <?php echo $tag['tagsname'] ?>
                  </label>
                <?php endforeach ?>
            </div>
        <?php endforeach ?>
      </div>
    </div>

    <div class="list-group-item form-group">
      <label class="control-label col-md-1">内容：</label>
      <div class="col-md-11">
        <script type="text/plain" id="content" style="width:100%;height:800px;" name="content"></script>
      </div>
    </div>


    <div class="list-group-item form-group" style="margin-top:-2px">
      <input type="submit" class="col-md-offset-5 right10 btn btn-primary" value="提交">
      <input type="reset" class="btn btn-danger left10" value="重置">
    </div>

  </form>
</div>


<script type="text/javascript" src="/adminui/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="/adminui/ueditor/ueditor.all.js"></script>

<!-- 实例化编辑器 -->
<script type="text/javascript">
    var ue = UE.getEditor('content');
</script>