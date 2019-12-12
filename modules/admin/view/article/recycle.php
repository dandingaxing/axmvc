
<div class="container">
  <form class="form-horizontal" enctype="multipart/form-data" id="myform" method="post" action="/index.php?r=article/index/update" style="">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="input-group input-group-lg">
          <input type="text" class="form-control" placeholder="关键字">
          <span class="input-group-btn">
            <button class="btn btn-primary" type="button"><font class="pleft15 pright15">搜索一下</font></button>
          </span>
        </div>
      </div>
    </div>
    <div class="row mtop15">
      <label class="control-label col-md-1" for="thumbs">标签：</label>
      <div class="col-md-10">
        <?php foreach ($cateArr as $key => $catOne): ?>
          <div class="col-md-1"><?php echo $catOne['tagsname'] ?></div>
          <div class="col-md-11">
            <?php foreach ($catOne['children'] as $k => $catTwo): ?>
              <span class="col-md-2">
                <input type="checkbox" name="tags[<?php $catOne['id'] ?>][<?php echo $catTwo['id'] ?>]" value="<?php echo $catTwo['id'] ?>">
                <?php echo $catTwo['tagsname'] ?>
              </span>
            <?php endforeach ?>
          </div>
        <?php endforeach ?>
      </div>
    </div>
    <div class="row mtop15">
      <div class="col-md-12">
        <div class="alert alert-success" role="alert">
          
          <button class="btn btn-primary btn-sm mright15" type="button">按ID <i class="glyphicon glyphicon-arrow-up"></i></button>

          <button class="btn btn-primary btn-sm mright15" type="button">按更新 <i class="glyphicon glyphicon-arrow-up"></i></button>

          <button class="btn btn-primary btn-sm mright15" type="button">按热度 <i class="glyphicon glyphicon-arrow-up"></i></button>

        </div>
      </div>
    </div>
  </form>
</div>

<div class="container">

  <?php foreach ($dataList as $key => $data): ?>
    <div class="media bs-callout bs-callout-danger">
      <?php if (!empty($data['thumbs'])): ?>
        <div class="pull-left">
          <a href="<?php echo $this->createUrl('detail', array('id'=>$data['id'])) ?>">
            <img class="media-object" src="<?php echo $data['thumbs'] ?>" alt="<?php echo $data['title'] ?>">
          </a>
        </div>
      <?php endif ?>
      <div class="media-body">
        <h4 class="media-heading">
          <b><a class="media-title" href="<?php echo $this->createUrl('detail', array('id'=>$data['id'])) ?>"><?php echo $data['title'] ?></a></b>
        </h4>
        <p><?php echo $data['description'] ?></p>
        <p>
          <span class="mright15">
            <?php echo $data['keywords'] ?>
          </span>
          <span class="mright15">
            <font class="media-social">更新时间：</font>
            <?php echo date('Y-m-d H:i:s', $data['updatetime']); ?>
          </span>
          <span class="mright15">
            <font class="media-social">状态：</font>
            <label class="radio-inline">
              <input type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1">正常
            </label>
            <label class="radio-inline">
              <input type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">禁用
            </label>
          </span>

          <span class="pull-right">
            <a href="<?php echo $this->createUrl('recovery', array('id'=>$data['id'])); ?>" class="btn btn-primary btn-sm">恢复</a>
            <a href="<?php echo $this->createUrl('coercion', array('id'=>$data['id'])); ?>" class="btn btn-danger btn-sm">强制删除</a>
          </span>
      </div>
    </div>
  <?php endforeach ?>


</div>


<div class="container mtop15">
  <?php echo $page->page(); ?>
</div>

