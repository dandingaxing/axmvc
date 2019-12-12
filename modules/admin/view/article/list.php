
<div class="container">
  <form class="form-horizontal" id="myform" method="get" action="<?php echo $this->createUrl('list') ?>" onsubmit="return adminFormGet(this)">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="input-group input-group-lg">
          <input type="text" name="keywords" class="form-control" placeholder="关键字" value="<?php echo isset($getParams['keywords']) ? $getParams['keywords'] : ""; ?>">
          <span class="input-group-btn">
            <button class="btn btn-primary" type="submit"><font class="pleft15 pright15">搜索一下</font></button>
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
              <label class="col-md-2">
                <input type="checkbox" name="tags[]" value="<?php echo $catTwo['id'] ?>" <?php if(in_array($catTwo['id'], $getParams['tags'])) echo 'checked="checked"' ?>>
                <?php echo $catTwo['tagsname'] ?>
              </label>
            <?php endforeach ?>
          </div>
        <?php endforeach ?>
      </div>
    </div>
    <div class="row mtop15">
      <div class="col-md-12">
        <div class="alert alert-success" role="alert">
          
          <?php $orderParams = $getParams; ?>
          <?php if($getParams['order']=='id' && $getParams['by']='desc') : ?>
            <?php $orderParams['order']='id'; $orderParams['by']='asc'; ?>
            <a href="<?php echo $this->createUrl('list', $orderParams); ?>" class="btn btn-danger btn-sm mright15" >按ID <i class="glyphicon glyphicon-arrow-up"></i></a>
          <?php elseif($getParams['order']=='id' && $getParams['by']='asc'): ?>
            <?php $orderParams['order']='id'; $orderParams['by']='desc'; ?>
            <a href="<?php echo $this->createUrl('list', $orderParams) ?>" class="btn btn-danger btn-sm mright15">按ID <i class="glyphicon glyphicon-arrow-down"></i></a>
          <?php else: ?>
            <?php $orderParams['order']='id'; $orderParams['by']='desc'; ?>
            <a href="<?php echo $this->createUrl('list', $orderParams) ?>" class="btn btn-danger btn-sm mright15">按ID <i class="glyphicon glyphicon-arrow-down"></i></a>
          <?php endif ?>

          <?php if($getParams['order']=='updatetime' && $getParams['by']='desc') : ?>
            <?php $orderParams['order']='updatetime'; $orderParams['by']='asc'; ?>
            <a href="<?php echo $this->createUrl('list', $orderParams); ?>" class="btn btn-danger btn-sm mright15">按更新 <i class="glyphicon glyphicon-arrow-up"></i></a>
          <?php elseif($getParams['order']=='updatetime' && $getParams['by']='asc'): ?>
            <?php $orderParams['order']='updatetime'; $orderParams['by']='desc'; ?>
            <a href="<?php echo $this->createUrl('list', $orderParams) ?>" class="btn btn-danger btn-sm mright15">按更新 <i class="glyphicon glyphicon-arrow-down"></i></a>
          <?php else: ?>
            <?php $orderParams['order']='updatetime'; $orderParams['by']='desc'; ?>
            <a href="<?php echo $this->createUrl('list', $orderParams) ?>" class="btn btn-danger btn-sm mright15">按更新 <i class="glyphicon glyphicon-arrow-down"></i></a>
          <?php endif ?>

          <?php if($getParams['order']=='viewcount' && $getParams['by']='desc') : ?>
            <?php $orderParams['order']='viewcount'; $orderParams['by']='asc'; ?>
            <a href="<?php echo $this->createUrl('list', $orderParams); ?>" class="btn btn-danger btn-sm mright15">按热度 <i class="glyphicon glyphicon-arrow-up"></i></a>
          <?php elseif($getParams['order']=='viewcount' && $getParams['by']='asc'): ?>
            <?php $orderParams['order']='viewcount'; $orderParams['by']='desc'; ?>
            <a href="<?php echo $this->createUrl('list', $orderParams) ?>" class="btn btn-danger btn-sm mright15">按热度 <i class="glyphicon glyphicon-arrow-down"></i></a>
          <?php else: ?>
            <?php $orderParams['order']='viewcount'; $orderParams['by']='desc'; ?>
            <a href="<?php echo $this->createUrl('list', $orderParams) ?>" class="btn btn-danger btn-sm mright15">按热度 <i class="glyphicon glyphicon-arrow-down"></i></a>
          <?php endif ?>

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
            <a href="<?php echo $this->createUrl('edit', array('id'=>$data['id'])); ?>" class="btn btn-primary btn-sm">修改</a>
            <a href="<?php echo $this->createUrl('delete', array('id'=>$data['id'])); ?>" class="btn btn-danger btn-sm">删除</a>
          </span>
      </div>
    </div>
  <?php endforeach ?>


</div>


<div class="container mtop15">
  <?php echo $page->page(); ?>
</div>

