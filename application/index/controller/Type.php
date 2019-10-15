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
        if(!empty($_GET['type_id'])){
            $type_id=$_GET['type_id'];
            $data = DB::table("fa_upload")
                ->where(['type_id'=>$type_id])
                ->select();
        }else{
            $data = DB::table("fa_upload")
                ->order(['look'=>'desc'])
                ->limit(4)
                ->select();
        }
        $brr = [];
        foreach($data as $key=>$value){
            $brr[$key]['upload_status'] = $value['upload_status'];
            $brr[$key]['upload_id'] = $value['upload_id'];
            $brr[$key]['text'] = $value['text'];
            $brr[$key]['user_id'] = $value['user_id'];
            $brr[$key]['image'] = $value['image'];
            $brr[$key]['file'] = $value['file'];
            $brr[$key]['look'] = $value['look'];
            $brr[$key]['images'] = $this->myFun($value['images']);
            $brr[$key]['count'] = count($this->myFun($value['images']));
        }
//var_dump($brr);die;


          $data = Db::name('type')->select();
        $data2 = Db::table('fa_user_type')
            ->join('fa_type', 'fa_user_type.type_id=fa_type.type_id')
            ->field('fa_type.type_name,fa_type.type_id')
            ->select();
        $this->assign('type', $data);
        $this->assign('type2', $data2);
        $this->assign('brr',$brr);
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
        $user_id=session("user_id");
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
            $createtime=time();
            $data = [
                'user_id' => $user_id,
                'file' => $file,
                'text' => $text,
                'content' => $content,
                'upload_status' => 2,
                'type_id' => $type_id,
                'createtime'=>$createtime
            ];
            $arr = DB::table('fa_upload')->insert($data);
            if ($arr) {
                $this->success(__('上传视频成功'));
            } else {
                $this->error(__('上传视频失败'));
            }
        }

    }

    public function myFun($pics){
        //      $new_pics = rtrim($pics,',');
        $arr = explode(',',$pics);
        return $arr;
    }
//首页视频图文文章展示
    public function showUpload()
    {
        if(!empty($_POST['type_id'])){
            $type_id=$_POST['type_id'];
            $data = DB::table("fa_upload")
                ->where(['type_id'=>$type_id])
                ->select();
        }else{
            $data = DB::table("fa_upload")
                ->order(['look'=>'desc'])
                ->limit(4)
                ->select();
        }
        $brr = [];
        foreach($data as $key=>$value){
            $brr[$key]['upload_status'] = $value['upload_status'];
            $brr[$key]['upload_id'] = $value['upload_id'];
            $brr[$key]['text'] = $value['text'];
            $brr[$key]['user_id'] = $value['user_id'];
            $brr[$key]['image'] = $value['image'];
            $brr[$key]['file'] = $value['file'];
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
//    图文详情页
    public function imagetextDetail()
    {
        $upload_id=$_GET['upload_id'];
        $where=[
            'upload_id'=>$upload_id,
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
        $arr['new_image'] = explode(',',$arr['images']);
        $user_id=$arr['user_id'];
        $res=DB::table("fa_userinfo")->where(['user_id'=>$user_id])->find();

        $this->assign('arr', $arr);
        $this->assign('res', $res);
        return $this->view->fetch('type/imagetextdetail');
    }
    //文章详情页接口
    public function textDetail(){
        $upload_id=$_GET['upload_id'];
        $where=[
            'upload_id'=>$upload_id,
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
        $user_id=$arr['user_id'];
        $res=DB::table("fa_userinfo")->where(['user_id'=>$user_id])->find();

        $this->assign('arr', $arr);
        $this->assign('res', $res);
        return $this->view->fetch('type/textdetail');
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
    //    短视频页面
    public function dsp(){
        return $this->view->fetch('type/dsp');
    }
    //菜单栏影视数据展示
    public function showVedio(){
        $res=DB::table("fa_upload")->where(['upload_status'=>3])->order(['look'=>'desc'])->limit(15)->select();

        if($res){
            $arr=[
                'code'=>1,
                'msg'=>$res
            ];
            echo json_encode($arr);
        }

    }

//    个人中心用户信息修改
    public function userinfoUpdate(){
        $user_id=$_POST['user_id'];
        $res=Db::table("fa_userinfo")->where(['user_id'=>$user_id])->find();
        $arr=[
            'code'=>1,
            'msg'=>$res
        ];
        echo json_encode($arr);
    }
//    个人中心用户信息修改执行
    public function userinfoUpdateDo(){
        $user_mobile=session("user_mobile");
        $where=['user_mobile'=>$user_mobile];
        if(!empty($_POST['userID'])) {
            $userID = $_POST['userID'];
            $res = DB::table("fa_userinfo")->where($where)->update(['user_id' => $userID]);
            if ($res) {
                $this->success(__('修改ID成功'));
            } else {
                $this->error(__('修改ID失败'));
            }
        }else if(!empty($_POST['user_name'])){
            $user_name=$_POST['user_name'];
            $res=DB::table("fa_userinfo")->where($where)->update(['user_name'=>$user_name]);
            if($res){
                $this->success(__('修改昵称成功'));
            }else{
                $this->error(__('修改昵称失败'));
            }
        }else if(!empty($_POST['user_image'])){
            $user_image=$_POST['user_image'];
            $res=DB::table("fa_userinfo")->where($where)->update(['user_image'=>$user_image]);
            if($res){
                $this->success(__('修改头像成功'));
            }else{
                $this->error(__('修改头像失败'));
            }
        }else if(!empty($_POST['user_sex'])){
            $user_sex=$_POST['user_sex'];
            $res=DB::table("fa_userinfo")->where($where)->update(['user_sex'=>$user_sex]);
            if($res){
                $this->success(__('修改性别成功'));
            }else{
                $this->error(__('修改性别失败'));
            }
        }else if(!empty($_POST['user_address'])){
            $user_address=$_POST['user_address'];
            $res=DB::table("fa_userinfo")->where($where)->update(['user_address'=>$user_address]);
            if($res){
                $this->success(__('修改地址成功'));
            }else{
                $this->error(__('修改地址失败'));
            }
        }
    }
//视频播放详情页视频展示接口
public function vedioDetail(){
        $upload_id=$_GET['upload_id'];
        $arr=DB::table("fa_upload")->where(['upload_id'=>$upload_id])->find();
        $user_id=$arr['user_id'];
        $res=DB::table("fa_userinfo")->where(['user_id'=>$user_id])->find();

        $this->assign('arr', $arr);
        $this->assign('res', $res);
        return $this->view->fetch('type/vediodetail');
}
}
//点击关注成为粉丝
public function fans(){
    $user_id=$_POST['user_id'];
    $user_fans=
    DB::table("userinfo")->where(['user_id'=>$user_id])->update(['user_fans'=>$user_fans+1]);
}



