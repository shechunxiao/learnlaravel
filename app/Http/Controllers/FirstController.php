<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FirstController extends Controller
{
    public function upload()
    {
        return view('upload');
    }

    public function index()
    {
        //需要先删除原来的数组
        $filename = public_path() . '/file/110/ceshi.zip';
        $this->extractZipToFile($filename, public_path() . '/file/110/up');
    }

    function zip($zipName,$dir){
        $zipName = iconv("utf-8","gb2312",$zipName);
        $zip = new \ZipArchive;
        if ($zip->open($zipName) === true) {
//            $docnum = $zip->numFiles;
//            var_dump($docnum);
            //获取索引为0的文件名称
            $docnum = $zip->numFiles;
            for ($i = 0; $i < $docnum; $i++) {
                $statInfo = $zip->statIndex($i);
//                $encoding = mb_detect_encoding($statInfo['name']);
//                $filename = mb_convert_encoding($statInfo['name'], 'utf-8', 'gbk');
////                $filename = iconv($encoding, 'GBK', $statInfo['name']);
                $name = $statInfo['name'];
                $name = $this->transcoding($name);
                echo '****************';
                var_dump($name);
                var_dump(mb_detect_encoding($name, ['UTF-8', 'GBK', 'BIG5', 'CP936']));
                echo '****************';
//                var_dump($encoding);
//                var_dump($filename);
//                $name = $this->transcoding($statInfo[0]);
            }

            //将压缩包文件解压到test目录下
//            $zip->extractTo(public_path());

            // 关闭zip文件
            $zip->close();
        }
    }
    function extractZipToFileTwo($zipName, $toDir)
    {
        $zip = new \ZipArchive;//新建一个ZipArchive的对象
        /*
        通过ZipArchive的对象处理zip文件
        $zip->open这个方法的参数表示处理的zip文件名。
        如果对zip文件对象操作成功，$zip->open这个方法会返回TRUE
        */
        $res = $zip->open(iconv ( 'UTF-8', 'GBK', $zipName));
        if ($res === TRUE){
            if (!is_dir($this->transcoding($toDir))){
            mkdir($this->transcoding($toDir), 0777, true);
            }
            //$zip->extractTo($toDir);
            $docnum = $zip->numFiles;
            for($i = 0; $i < $docnum; $i++) {
                continue;
                $statInfo = $zip->statIndex($i);
                dump($statInfo);
                if($statInfo['crc'] == 0) {
                    //新建目录
                    mkdir(iconv ( 'UTF-8', 'GBK', $toDir.'/'.$statInfo['name']), 0777, true);
                } else {
                    //拷贝文件,特别的改动，iconv的位置决定copy能不能work
                    if(copy('zip://'.iconv ( 'UTF-8', 'GBK', $zipName).'#'.$statInfo['name'], iconv ( 'UTF-8', 'GBK', $toDir.'/'.$statInfo['name'])) == false){
                        echo 'faild to copy';
                    }
                }
            }


            print_r(scandir(iconv ( 'UTF-8', 'GBK',$toDir)));
            $zip->close();//关闭处理的zip文件
        }
        else{
            echo 'failed, code:'.$res.'<br>';
        }
    }

    /**
     * 解压
     * @param $zipName
     * @param $dir
     * @return bool
     */
    function extractZipToFile($zipName, $dir)
    {
        $zip = new \ZipArchive;
        if ($zip->open($zipName) === TRUE) {
            if (!is_dir($dir)) mkdir($dir, 0775, true);
            $docnum = $zip->numFiles;
            for ($i = 0; $i < $docnum; $i++) {
                $statInfo = $zip->statIndex($i);
                $filename = $zip->getNameIndex($i, \ZipArchive::FL_ENC_RAW);
                $filename = $this->transcoding($filename);
                if ($statInfo['crc'] == 0) {
                    //新建目录
                    if (!is_dir($dir . '/' . substr($filename, 0, -1))) mkdir($dir . '/' . substr($filename, 0, -1), 0775, true);

                } else {
                    //拷贝文件
                    $name = explode('/',$filename);
                    $count = count($name);
                    $name = $name[$count-1];
                    if (is_file('zip://' . $zipName . '#' . $zip->getNameIndex($i))){
                        echo '是';die();
                    }
                    var_dump('zip://' . $zipName . '#' . $zip->getNameIndex($i));die();
                    copy('zip://' . $zipName . '#' . $zip->getNameIndex($i), $dir . '/' . $filename);
                }
            }
            $name = $zip->getNameIndex(0);
            $name = substr($name, 0, -1);
            $name = $this->transcoding($name);
            $zip->close();
            @rename($dir . $name, $dir . 'extract'); //重新命名，方法取数据
            return true;
        } else {
            return false;
        }
    }

    /**
     * 转换字符编码
     * @param $fileName
     * @return false|string
     */
    function transcoding($fileName)
    {
//        $encoding = mb_detect_encoding($fileName, ['UTF-8', 'GBK', 'BIG5', 'CP936']);
        $encoding = mb_detect_encoding($fileName);
        if (DIRECTORY_SEPARATOR == '/') {    //linux
            var_dump('*****************');
//            $filename = iconv($encoding, 'UTF-8', $fileName);
            $filename = mb_convert_encoding($fileName, 'UTF-8', $encoding);


        } else {  //win
//            $filename = iconv($encoding, 'GBK', $fileName);
            $filename = mb_convert_encoding($fileName, 'GBK', $encoding);
        }
        return $filename;
    }


    /**
     * 导入
     * @param Request $request
     * @return array
     */
    public function import(Request $request)
    {
        //上传zip文件
        $uploadFile = $this->uploadFile($request);
        if ($uploadFile['status'] == 0) return $uploadFile;
        //解压zip文件
        $filename = public_path() . '/file/110/upload.zip';
        $this->extractZipToFileTwo($filename, public_path() . '/file/110/');
//        $this->unzip($filename, public_path() .'cao',true,false);
//        $this->zip($filename, public_path() . '/file/110/');
//        //验证解压后的文件的正确性
//        $path = public_path() . '/file/110/extract';
//        $verifyFile = $this->verifyFile($path);
//        if ($verifyFile['status'] == 0) return $verifyFile;
        //导入数据

        //清理上传文件和解压文件


    }

    /**
     *  验证文件的正确性
     * @param $path
     * @return array
     */
    public function verifyFile($path)
    {
        $isVerify = true;
        //验证图片
        $verifyPic = $this->verifyPic($path);
        if ($verifyPic['status'] == 0) {
            return $verifyPic;
        }
        //验证商品列表的数据
        $verifyExcel = $this->verifyExcel($path);
        if ($verifyExcel['status'] == 0) {
            return $verifyExcel;
        }
        return ['status' => 1, 'msg' => '验证通过'];

    }

    /**
     * 验证图片
     * @param $path
     * @return array
     */
    public function verifyPic($path)
    {
        $picPath = $path . '/' . $this->transcoding('图片2');
        $barCodeArr = []; //条形码数组
        $picArray = []; //图片数组
        //判断图片文件夹是否为空
        if ($handle = @opendir($picPath)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $pathInfo = pathinfo($picPath . '/' . $entry);
                    $barCode = explode('-', $pathInfo['filename'])[0];
                    if (!in_array($barCode, $barCodeArr)) {
                        $barCodeArr[] = $barCode;
                    }
                    $picArray[$barCode][] = $pathInfo['basename'];
                }
            }
            closedir($handle);
        }
        /*****查询数据库，验证******/


        /*****查询数据库，验证*******/
        //假设验证成功
        return ['status' => 1, 'msg' => '验证成功'];
    }

    /**
     *  验证excel文件
     * @param $path
     * @return array
     */
    public function verifyExcel($path)
    {
        if (file_exists($path . '/' . $this->transcoding('商品列表.xlsx'))) {
            $excelPath = $path . '/' . $this->transcoding('商品列表.xlsx');
        } elseif (file_exists($path . '/' . $this->transcoding('商品列表.xls'))) {
            $excelPath = $path . '/' . $this->transcoding('商品列表.xls');
        } else {
            return ['status' => 0, 'msg' => 'Excel文件格式不正确'];
        }
        //读取数据信息，验证是否正确
        var_dump($excelPath);


    }

    /**
     * 上传文件
     * @param Request $request
     * @return array
     */
    public function uploadFile($request)
    {
//        $savePath = $request->input('path'); //暂时不用，最后再用
        if (!$request->hasFile('file')) {
            return ['status' => 0, 'msg' => '缺少文件'];
        }
        $file = $request->file('file');
        if (!$file->isValid()) {
            return ['status' => 0, 'msg' => '上传出错'];
        }
        $extension = $file->getClientOriginalExtension();
        if ($extension != 'zip') {
            return ['status' => 0, 'msg' => '上传格式错误'];
        }
        $saveName = 'upload.' . $extension;
        $savePath = public_path() . '/file/110/';
        $target = $file->move($savePath, $saveName);
        if ($target) {
            return ['status' => 1, 'msg' => '上传成功'];
        }
    }

    public function unzip($src_file, $dest_dir=false, $create_zip_name_dir=true, $overwrite=true){

        header("Content-type: text/html; charset=gbk");
        if ($zip = zip_open($src_file)){
            if ($zip){
                $splitter = ($create_zip_name_dir === true) ? "." : "/";
                if($dest_dir === false){
                    $dest_dir = substr($src_file, 0, strrpos($src_file, $splitter))."/";
                }

                // 如果不存在 创建目标解压目录
                $this->create_dirs($dest_dir);

                // 对每个文件进行解压
                while ($zip_entry = zip_read($zip)){
                    // 文件不在根目录
                    $pos_last_slash = strrpos(zip_entry_name($zip_entry), "/");
                    if ($pos_last_slash !== false){
                        // 创建目录 在末尾带 /
                        $this->create_dirs($dest_dir.substr(zip_entry_name($zip_entry), 0, $pos_last_slash+1));
                    }

                    // 打开包
                    if (zip_entry_open($zip,$zip_entry,"r")){

                        // 文件名保存在磁盘上
                        $file_name = $dest_dir.zip_entry_name($zip_entry);
                        $info = pathinfo($file_name);
                        var_dump($info);
//                        var_dump(zip_entry_name($zip_entry));
                        // 检查文件是否需要重写
                        if ($overwrite === true || $overwrite === false && !is_file($file_name)){
                            // 读取压缩文件的内容
                            $fstream = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

                            @file_put_contents($file_name, $fstream);
                            // 设置权限
                            chmod($file_name, 0777);
                            echo "save: ".$file_name."<br />";
                        }

                        // 关闭入口
                        zip_entry_close($zip_entry);
                    }
                }
                // 关闭压缩包
                zip_close($zip);
            }
        }else{
            return false;
        }
        return true;
    }

    /**
     * 创建目录
     */
    public function create_dirs($path){
        if (!is_dir($path)){
            $directory_path = "";
            $directories = explode("/",$path);
            array_pop($directories);

            foreach($directories as $directory){
                $directory_path .= $directory."/";
                if (!is_dir($directory_path)){
                    mkdir($directory_path);
                    chmod($directory_path, 0777);
                }
            }
        }
    }

}