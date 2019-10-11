<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use think\Db;

class Type extends Frontend
{
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    //首页分类展示
    public function type()
    {
          $data = Db::name('type')->select();
        $data2 = Db::table('fa_user_type')
            ->join('fa_type', 'fa_user_type.type_id=fa_type.type_id')
            ->field('fa_type.type_name,fa_type.type_id')
            ->select();
        $this->assign('type', $data);
        $this->assign('type2', $data2);
        return $this->view->fetch('type/index');
    }

    //首页分类编辑
    public function userType()
    {
        $type_id = $_POST['type_id'];
        $data = Db::name('user_type')->where(['type_id' => $type_id])->delete();
        if ($data) {
            $this->success(__('编辑成功'));
        } else {
            $this->error(__('编辑失败'));
        }
    }

    //首页分类添加
    public function addUsertype()
    {
        $user_id = $_POST['user_id'];
        if (empty($user_id)) {
            $this->success(__('未登录'));
        } else {
            $type_id = $_POST['type_id'];
            $data = [
                'type_id' => $type_id,
                'user_id' => $user_id,
            ];
            $arr = Db::name('user_type')->where(['type_id' => $type_id, 'user_id' => 1])->select();
            if ($arr) {
                $this->error(__('您已经选择过此类型'));
            } else {
                $res = Db::name('user_type')->insert($data);
                if ($res) {
                    $this->success(__('添加成功'));
                } else {
                    $this->error(__('添加失败'));
                }
            }
        }

    }

    //点击首页添加图文页面
    public function uploadImageText()
    {
        $data = Db::name('type')->select();
        $this->assign('type', $data);
        return $this->view->fetch('type/imagetext');
    }

//多张图片base64上传
    public function uploadPics()
    {
        $pics = $_POST;
        $pics = $pics['pics'];
        $arr = [];
        $up_dir = "./uploads/publish/";
        $i = 0;
        $crr = [];
        $str = '';
        if (empty($pics)) {
            $arr = ['code' => 1001, 'msg' => '图片上传不能为空'];
            echo json_encode($arr);
        }
        foreach ($pics as $key => $value) {
            if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $pics["$key"], $result)) {
                $type = $result[2];
                if (!file_exists($up_dir)) {
                    mkdir($up_dir, 0777);
                }
                if (in_array($type, array('pjpeg', 'jpeg', 'jpg', 'gif', 'bmp', 'png'))) {
                    $new_file = $up_dir . mt_rand(1000, 9999) . date('YmdHis_') . $i . '.' . $type;
                    $i++;
                    if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $pics["$key"])))) {
                        $img_path = str_replace('../../..', '', $new_file);
                        $pic = str_replace('./uploads/', '', $img_path);
                        array_push($crr, $pic);
                        $str .= $pic . ";";
                    } else {
                        $arr = ['code' => '4030', 'msg' => '图片上传失败'];
                        echo json_encode($arr);
                    }
                } else {
                    $arr = ['code' => '4031', 'msg' => '图片上传类型错误'];
                    echo json_encode($arr);
                }
            } else {
                $arr = ['code' => '4032', 'msg' => '文件错误'];
                echo json_encode($arr);
            }
        }
        $drr = ['code' => 200, 'str' => $str, 'pic_arr' => $crr];
        echo json_encode($drr);
    }

//    点击发布图文按钮
    public function uploadIsT()
    {
        $user_id = $_POST['user_id'];
        if (empty($user_id)) {
            $this->success(__('未登录'));
        } else {
            $images = $_POST['images'];
            $text = $_POST['text'];
            $type_id = $_POST['type_id'];
            $data = [
                'images' => $images,
                'text' => $text,
                'upload_status' => 1,
                'type_id' => $type_id,
                'user_id' => $user_id
            ];
            $arr = DB::table('fa_upload')->insert($data);
            if ($arr) {
                $this->success(__('上传图文成功'));
            } else {
                $this->error(__('上传图文失败'));
            }
        }
    }

//    上传文章
    public function uploadIT()
    {
        $user_id = $_POST['user_id'];
        if (empty($user_id)) {
            $this->success(__('未登录'));
        } else {
            $image = $_POST['image'];
            $images = $_POST['images'];
            $text = $_POST['text'];
            $type_id = $_POST['type_id'];
            $data = [
                'image' => $image,
                'images' => $images,
                'text' => $text,
                'upload_status' => 2,
                'type_id' => $type_id,
                'user_id' => $user_id
            ];
            $arr = DB::table('fa_upload')->insert($data);
            if ($arr) {
                $this->success(__('上传文章成功'));
            } else {
                $this->error(__('上传文章失败'));
            }
        }
    }

//用户上传视频
    public function uploadvedio()
    {
        $user_id = $_POST['user_id'];
        if (empty($user_id)) {
            $this->success(__('未登录'));
        } else {
            $file = $_POST['file'];
            $type_id = $_POST['type_id'];
            $text = $_POST['text'];
            $content = $_POST['content'];
            $data = [
                'user_id' => $user_id,
                'file' => $file,
                'text' => $text,
                'content' => $content,
                'upload_status' => 2,
                'type_id' => $type_id
            ];
            $arr = DB::table('fa_upload')->insert($data);
            if ($arr) {
                $this->success(__('上传视频成功'));
            } else {
                $this->error(__('上传视频失败'));
            }
        }

    }

//首页视频图文文章展示
    public function myFun($pics){
    //      $new_pics = rtrim($pics,',');
      $arr = explode(',',$pics);
      return $arr;
    }
    public function showUpload()
    {
        $data = DB::table("fa_upload")
            ->order(['look'=>'desc'])
            ->limit(10)
            ->select();
        $brr = [];
        foreach($data as $key=>$value){
            $brr[$key]['upload_status'] = $value['upload_status'];
            $brr[$key]['text'] = $value['text'];
            $brr[$key]['user_id'] = $value['user_id'];
            $brr[$key]['image'] = $value['image'];
            $brr[$key]['look'] = $value['look'];
            $brr[$key]['images'] = $this->myFun($value['images']);
            $brr[$key]['count'] = count($this->myFun($value['images']));
        }
       $arr=[
           'code'=>1,
           'msg'=>$brr,
       ];
        echo json_encode($arr);
    }
//    文章详情页
        public function textDetail()
    {

        return $this->view->fetch('type/textdetail');
    }
//    图文详情页
    public function imagetextDetail()
    {

        return $this->view->fetch('type/imagetextdetail');
    }
    //图文详情页接口
    public function showimagetextDetail(){
        $image=$_POST['image'];
        $text=$_POST['text'];
        $user_id=$_POST['user_id'];
        $upload_status=$_POST['upload_status'];
        $where=[
            'user_id'=>$user_id,
            'text'=>$text,
            'upload_status'=>$upload_status,
            'image'=>$image,
        ];
        $arr=DB::table("fa_upload")
            ->where($where)
            ->find();
        if($arr){
            $where=[
                'upload_id'=>$arr['upload_id']
            ];
            $res=DB::table("fa_upload")->where($where)->update(['look'=>$arr['look']+1]);
        }
        $arr=[
            'code'=>1,
            'msg'=>$arr,
        ];
        echo json_encode($arr);
    }
    //文章详情页接口
    public function showtextDetail(){
        $images=$_POST['images'];
        $user_id=$_POST['user_id'];
        $text=$_POST['text'];
        $upload_status=$_POST['upload_status'];
        $where=[
            'user_id'=>$user_id,
            'text'=>$text,
            'upload_status'=>$upload_status,
        ];
        $arr=DB::table("fa_upload")
            ->where($where)
            ->whereOr('images','like',"%{$images}%")
            ->find();
        if($arr){
            $where=[
                'upload_id'=>$arr['upload_id']
            ];
            $res=DB::table("fa_upload")->where($where)->update(['look'=>$arr['look']+1]);
        }
        $arr=[
            'code'=>1,
            'msg'=>$arr,
        ];
        echo json_encode($arr);
    }
//    用户发布评论接口
    public function disscuss(){
        $upload_id=$_POST['upload_id'];
        $discuss_content=$_POST['discuss_content'];
        $add_time=time();
        $data=[
            'upload_id'=>$upload_id,
            'discss_content'=>$discuss_content,
            'add_time'=>$add_time
        ];
        $res=DB::table("fa_discuss")->insert($data);
        if($res){
            $this->success(__('评论成功'));
        }else{
            $this->error(__('评论失败'));
        }
    }
    //用户评论回复接口
    public function reply(){
        $discuss_id=$_POST['discuss_id'];
        $reply_content=$_POST['replay_content'];
        $add_time=time();
        $data=[
            'discuss_id'=>$discuss_id,
            'reply_content'=>$reply_content,
            'add_time'=>$add_time
        ];
        $res=DB::table("fa_reply")->insert($data);
        if($res){
            $this->success(__('回复评论成功'));
        }else{
            $this->error(__('回复评论失败'));
        }
    }
    //详情页面展示一级评论接口
    public function showDiscuss(){
        $upload_id=$_POST['upload_id'];
        $arr=DB::table("fa_discuss")->where(['upload_id'=>$upload_id])->select();
        $brr = [];
        foreach($arr as $key=>$value) {
            $brr[$key]['discuss_id'] = $value['discuss_id'];
            $brr[$key]['discss_content'] = $value['discss_content'];
            $brr[$key]['add_time'] = $value['add_time'];
        }
        $arr=[
            'code'=>1,
            'msg'=>$brr,
        ];
        echo json_encode($arr);
    }
    //详情页面展示二级评论接口
    public  function showReply(){
        $discuss_id=$_POST['discuss_id'];
        $arr=DB::table("fa_reply")->where(['discuss_id'=>$discuss_id])->select();
        $brr = [];
        foreach($arr as $key=>$value){
            $brr[$key]['replay_content'] = $value['replay_content'];
            $brr[$key]['add_time'] = $value['add_time'];
        }
        $arr=[
            'code'=>1,
            'msg'=>$brr,
        ];
        echo json_encode($arr);
    }
//    搜索热搜页面
    public function ssrs()
    {
        return $this->view->fetch('type/ssrs');
    }
    //热点榜页面
    public function rdb()
    {
        return $this->view->fetch('type/rdb');
    }
    //热点榜页面数据
    public function rdbData(){
        $res=Db::table("fa_upload")->order(['look'=>'desc'])->limit(15)->select();
        if($res){
            $arr=[
                'code'=>1,
                'msg'=>$res
            ];
            echo json_encode($arr);
        }
    }
    //手机验证码登录后进行个人信息完善
    public  function userInfo(){
        $image=$_POST['user_image'];
        $name=$_POST['user_name'];
        $ID=mt_rand(10000000, 99999999);
        if(empty($image)||empty($name)){
            $data=[
                'user_image'=>"/images/appimg.png",
                'user_name'=>$ID,
                'userID'=>$ID
            ];
            $res=DB::table("fa_userinfo")->insert($data);
            if($res){

                $this->success(__('成功'));
            }else{
                $this->error(__('失败'));
            }
        }else{
            $data=[
                'user_image'=>$image,
                'user_name'=>$name,
                'userID'=>$ID
            ];
            $res=DB::table("fa_userinfo")->insert($data);
            if($res){
                $this->success(__('成功'));
            }else{
                $this->error(__('失败'));
            }
        }
    }
//    个人中心用户信息修改
    public function userinfoUpdate(){
        
}
//    个人中心用户信息修改执行
    public function userinfoUpdateDo(){

    }
}