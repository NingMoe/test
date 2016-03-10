<?php
namespace Api\ocr;
class bdocr{
    static function getOcrText($imgPath){
        $ch = curl_init();
        $url = 'http://apis.baidu.com/idl_baidu/baiduocrpay/idlocrpaid';
        $header = array(
            'Content-Type:application/x-www-form-urlencoded',
            'apikey: a40c1d4b0bd400ac8ccff77aae83e862',
        );

        $file =$imgPath;// '文件路径/文件名.jpg';
        $pic_content = file_get_contents($file);
        $pic_content_base64 = urlencode(base64_encode($pic_content));
        $data = "fromdevice=pc&clientip=10.10.10.0&detecttype=LocateRecognize&languagetype=CHN_ENG&imagetype=1&image=".$pic_content_base64;
        // 添加apikey到header
        curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
        // 添加参数
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 执行HTTP请求
        curl_setopt($ch , CURLOPT_URL , $url);
        $res = curl_exec($ch);

       return $res;
    }
    public function getOcrTextD($imgPath){
        $fileBase=$this->getFileContentBase64();
        $ret=$this->call_idl_ocr($fileBase);
        return $ret;
    }
    private function call_idl_ocr(&$pic_content_base64)
    {
        $ch = curl_init();
        $url = 'http://apis.baidu.com/apistore/idlocr/ocr';
        $header = array(
            'Content-Type:application/x-www-form-urlencoded',
            'apikey: 32352374ac0825d0eb7c534c6da579c0',
        );


        $data = 'fromdevice=pc&clientip=10.10.10.0&detecttype=LocateRecognize&languagetype=CHN_ENG&imagetype=1&image='.$pic_content_base64;
        // 添加apikey到header
        curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
        // 添加参数
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 执行HTTP请求
        curl_setopt($ch , CURLOPT_URL , $url);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }
    private function getFileContentBase64(){
        $arr_option = getopt("f:");
        $file = $arr_option['f'];
        $pic_content = file_get_contents($file);
        $pic_content_base64 = urlencode(base64_encode($pic_content));
        return $pic_content_base64;
//        $arr_res = json_decode(call_idl_ocr($pic_content_base64));
    }
}