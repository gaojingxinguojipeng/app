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
            ->order('look desc')
            ->limit(10)
            ->select();


        $brr = [];
//        $upload_status=$data['upload_status'];
        foreach($data as $key=>$value){


            $brr[$key]['upload_status'] = $value['upload_status'];
            $brr[$key]['pics'] = $this->myFun($value['image']);

        }
        echo "<pre>";
        var_dump($brr);
        echo "</pre>";
        var_dump($brr);
//        echo $upload_status;


    }
}