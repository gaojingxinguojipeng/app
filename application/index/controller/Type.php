<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use think\Db;

class Type extends Frontend
{
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';
    public function type()
    {
        $data = Db::name('type')->select();
        $data2 = Db::table('fa_user_type')
            ->join('fa_type','fa_user_type.type_id=fa_type.type_id')
            ->field('fa_type.type_name,fa_type.type_id')
            ->select();
        $this->assign('type', $data);
        $this->assign('type2', $data2);
        return $this->view->fetch('type/index');
    }
    public function userType(){
        $type_id = $_POST['type_id'];
        $data=Db::name('user_type')->where(['type_id'=>$type_id])->delete();
        if($data){
            $this->success(__('编辑成功'));
        }else{
            $this->error(__('编辑失败'));
        }
    }
    public function addUsertype(){
        $type_id = $_POST['type_id'];
        $data=[
            'type_id'=>$type_id,
            'user_id'=>1,
        ];
        $arr=Db::name('user_type')->where(['type_id'=>$type_id,'user_id'=>1])->select();
        if($arr){
            $this->error(__('您已经选择过此类型'));
        }else{
            $res=Db::name('user_type')->insert($data);
            if($res){
                $this->success(__('添加成功'));
            }else{
                $this->error(__('添加失败'));
            }
        }
    }
    public function uploadImageText(){
        $data = Db::name('type')->select();
        $this->assign('type', $data);
        return $this->view->fetch('type/imagetext');
    }

}