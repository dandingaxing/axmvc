<?php

namespace modules\admin\controller;

use common\base;
use modules\admin\common\Controller;

use Medoo\Medoo;

class articleController extends Controller{

    // 初始实例化
    public function init(){
        parent::init();
    }

    // 列表
    public function list(){
        $getParams = $this->getParams();
        $getParams['page'] = isset($getParams['page']) ? intval($getParams['page']) : 1;
        $getParams['tags'] = $getParams['tags'] ?? array();
        $getParams['keywords'] = $getParams['keywords'] ?? "";
        $getParams['order'] = $getParams['order'] ?? "id";
        $getParams['by'] = $getParams['by'] ?? "DESC";

        $where = array();
        if ($getParams['keywords']) {
            $where["OR"] = array(
                'a.title[~]' => $getParams['keywords'],
                'a.keywords[~]' => $getParams['keywords'],
                'a.subtitle[~]' => $getParams['keywords'],
                );
        }
        $where["AND"]['a.status[<]'] = 9;
        if (!empty($getParams['tags'])) {
            $where["AND"]['t.tagsid'] = $getParams['tags'];
        }

        // 分页查询
        $count = base::app('db/mysql')->count('art_article(a)', ["[>]art_tags_article(t)"=>['a.id'=>'artid']], 'DISTINCT a.id', $where);

        $page = base::app('page')->setTotal($count)->setLimit(5)->setNowpage($getParams['page']);

        $optional = array();
        $optional['ORDER'] = ['a.'.strtolower($getParams['order'])=>strtoupper($getParams['by'])];
        $optional['LIMIT'] = [$page->getOffset(), $page->getLimit()];
        $optional['GROUP'] = ['a.id'];
        if (!empty($where)) {
            $optional['AND'] = $where;
        }

        // 文章列表
        $dataList = base::app('db/mysql')->select('art_article(a)', ["[>]art_tags_article(t)"=>['a.id'=>'artid']], '*', $optional);

        // 筛选
        $cateArr = array();
        $cateOne = base::app('db/mysql')->select('art_tags', '*', ['AND'=>['pid'=>0], 'ORDER'=>['id'=>'DESC']] );
        foreach ($cateOne as $key => $one) {
            $cateArr[$one['id']] = $one;
            $cateArr[$one['id']]['children'] = base::app('db/mysql')->select('art_tags', '*', ['AND'=>['pid'=>$one['id']], 'ORDER'=>['id'=>'DESC']] );
        }

        $this->renderout('list', array('dataList'=>$dataList, 'page'=>$page, 'cateArr'=>$cateArr, 'getParams'=>$getParams));
    }

    public function add(){
        $wordslist = base::app('db/mysql')->select('art_wordslist', '*');
        // 筛选
        $taglist = array();
        $cateOne = base::app('db/mysql')->select('art_tags', '*', ['AND'=>['pid'=>0], 'ORDER'=>['id'=>'DESC']] );
        foreach ($cateOne as $key => $one) {
            $taglist[$one['id']]['con'] = $one;
            $taglist[$one['id']]['children'] = base::app('db/mysql')->select('art_tags', '*', ['AND'=>['pid'=>$one['id']], 'ORDER'=>['id'=>'DESC']] );
        }
        return $this->renderout('add', array('wordslist'=>$wordslist, 'taglist'=>$taglist));
    }

    public function insert(){
        $body = $this->get('_request')->getParsedBody();
        // print_r($body['tags']);
        // exit();
        preg_match("/<[img|IMG].*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/i", $body['content'], $thumb);
        $description = mb_substr(strip_tags($body['content']), 0, 500, 'utf-8');
        $data = array(
            'title' => $body['title'],
            'subtitle' => $body['subtitle'],
            'content' => $body['content'],
            'description' => mb_substr(strip_tags($body['content']), 0, 250, 'utf-8'),
            'thumbs' => isset($thumb[1]) ? $thumb[1] : '',
            'keywords' => $body['keywords'],
            'status' => $body['status'],
            );
        $insert = base::app('db/mysql')->insert('art_article', $data);
        $account_id = $insert->rowCount();
        $insertId = base::app('db/mysql')->id();
        if ( $insertId ) {
            $tagArr = array();
            foreach ($body['tags'] as $topid => $tag) {
                foreach ($tag as $k => $v) {
                    $tagArr[] = array(
                        'artid' => $insertId,
                        'tagsid' => $v,
                        'tagspid' => $topid,
                        );
                }
            }
            $tagInsert = base::app('db/mysql')->insert('art_tags_article', $tagArr);
            $a = $tagInsert->rowCount();
            $insertIds = base::app('db/mysql')->id();
        }

        $this->redirect($this->createUrl('list'), 3, '新增成功');
    }


    public function edit(){
        $gets = $this->getParams();
        $id = isset($gets['id']) ? intval($gets['id']) : 0;
        if (empty($id)) {
            exit('error');
        }
        $data = base::app('db/mysql')->get('art_article', '*', ['id'=>$id]);
        $dataTags = base::app('db/mysql')->select('art_tags_article', '*', ['artid'=>$data['id']]);
        $artTags = array();
        foreach ($dataTags as $k => $v) {
            $artTags[] = $v['tagsid'];
        }
        // 筛选
        $taglist = array();
        $cateOne = base::app('db/mysql')->select('art_tags', '*', ['AND'=>['pid'=>0], 'ORDER'=>['id'=>'DESC']] );
        foreach ($cateOne as $key => $one) {
            $taglist[$one['id']]['con'] = $one;
            $taglist[$one['id']]['children'] = base::app('db/mysql')->select('art_tags', '*', ['AND'=>['pid'=>$one['id']], 'ORDER'=>['id'=>'DESC']] );
        }
        $this->renderout('edit', array('data'=>$data, 'artTags'=>$artTags, 'taglist'=>$taglist));
    }

    public function update(){
        $body = $this->get('_request')->getParsedBody();
        preg_match("/<[img|IMG].*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/i", $body['content'], $thumb);
        $description = mb_substr(strip_tags($body['content']), 0, 500, 'utf-8');        
        $data = array(
            'title' => $body['title'],
            'subtitle' => $body['subtitle'],
            'content' => $body['content'],
            'description' => mb_substr(strip_tags($body['content']), 0, 250, 'utf-8'),
            'thumbs' => isset($thumb[1]) ? $thumb[1] : '',
            'keywords' => $body['keywords'],
            'status' => $body['status'],
            );
        $update = base::app('db/mysql')->update('art_article', $data, ['id'=>$body['id']])->rowCount();
        // 受影响条数为 0 就查看是否报错，没报错就是没有任何影响的update
        if (empty($update)) {
            $error = base::app('db/mysql')->error();
            if (!empty($error[1]) || !empty($error[2]) ) {
                exit('update error');
            }
        }
        $delete = base::app('db/mysql')->delete('art_tags_article', ['artid'=>$body['id']]);
        $tagArr = array();
        foreach ($body['tags'] as $topid => $tag) {
            foreach ($tag as $k => $v) {
                $tagArr[] = array(
                    'artid' => $body['id'],
                    'tagsid' => $v,
                    'tagspid' => $topid,
                    );
            }
        }
        $tagInsert = base::app('db/mysql')->insert('art_tags_article', $tagArr);
        $a = $tagInsert->rowCount();
        $insertIds = base::app('db/mysql')->id();
        if ($a) {
            $this->redirect($this->createUrl('list'), 3, '修改成功');
        }
    }

    // 回收站列表
    public function recycle(){
        // 分页查询
        $count = base::app('db/mysql')->count('art_article');
        $page = base::app('page')->setTotal($count)->setLimit(20);

        // 文章列表
        $dataList = base::app('db/mysql')->select('art_article', '*', ['status'=>9, 'ORDER'=>['id'=>'DESC'], 'LIMIT'=>[$page->getOffset(), $page->getLimit()]]);

        // 筛选
        $cateArr = array();
        $cateOne = base::app('db/mysql')->select('art_tags', '*', ['AND'=>['pid'=>0], 'ORDER'=>['id'=>'DESC']] );
        foreach ($cateOne as $key => $one) {
            $cateArr[$one['id']] = $one;
            $cateArr[$one['id']]['children'] = base::app('db/mysql')->select('art_tags', '*', ['AND'=>['pid'=>$one['id']], 'ORDER'=>['id'=>'DESC']] );
        }

        $this->renderout('recycle', array('dataList'=>$dataList, 'page'=>$page, 'cateArr'=>$cateArr));

    }

    // 放入回收站
    public function delete(){
        $gets = $this->getParams();
        $id = isset($gets['id']) ? intval($gets['id']) : 0;
        if (empty($id)) {
            exit('error');
        }
        $update = base::app('db/mysql')->update('art_article', ['status'=>9], ['id'=>$id])->rowCount();
        if ($update) {
            $this->redirect($this->createUrl('list'), 3, '放入回收站成功');
        }
    }

    // 恢复
    public function recovery(){
        $gets = $this->getParams();
        $id = isset($gets['id']) ? intval($gets['id']) : 0;
        if (empty($id)) {
            exit('error');
        }
        $update = base::app('db/mysql')->update('art_article', ['status'=>0], ['id'=>$id])->rowCount();
        if ($update) {
            $this->redirect($this->createUrl('list'), 3, '恢复成功');
        }
    }

    // 强制删除
    public function coercion(){
        $gets = $this->getParams();
        $id = isset($gets['id']) ? intval($gets['id']) : 0;
        if (empty($id)) {
            exit('error');
        }
        $delete = base::app('db/mysql')->delete('art_article', ['id'=>$id])->rowCount();
        if ($delete) {
            $deleteTag = base::app('db/mysql')->delete('art_tags_article', ['artid'=>$id]);
            $this->redirect($this->createUrl('list'), 3, '删除成功');
        }
    }




}










