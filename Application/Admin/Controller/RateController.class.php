<?php
namespace Admin\Controller;
class RateController extends AdminController{
	public function index(){
		$this->display();
	}
	public function importE(){
		$ret=$this->setupFromExcel();
		if($_FILES){
			if($ret){
				$msg='导入成功';
			}else{
				$msg='导入失败:'.$ret;
			}
		}
// 		var_dump($ret);
		$this->assign('msg',$msg);
		$this->assign('data',$ret);
		$this->assign('cate',I('get.cate'));
		$this->display('index');
	}
	private function setupFromExcel(){
// 		if(empty($_POST)){
// 			$this->show('<form method="post" action="{:U(importE)}" enctype="multipart/form-data">
//     			   		<h3>导入Excel：</h3>
// 	    				<input  type="file" name="fileinfo" />
// 	           			<input type="submit"  value="导入" /></form>');
// 		}
		if($_FILES){
				
  			$tmp_file = $_FILES ['fileinfo'] ['tmp_name'];
			$file_types = explode ( ".", $_FILES ['fileinfo'] ['name'] );
			$file_type = $file_types [count ( $file_types ) - 1];
				
			if (strtolower ( $file_type ) != "xls")
			{
				$this->error ( '不是Excel文件，重新上传' );
			}
				
			$excel=new \Common\Object\Rate\Excel($_FILES ['fileinfo'] ['tmp_name']);
			$rate=new \Common\Object\Rate\RateTable($excel);
		}else{
			$rate=new \Common\Object\Rate\RateTable();
		}
		
		$ret=$rate->getRateMapData();
		
		if($ret){
			return $ret;
		}else{
			return false;
		}
		
		
		
		
		// 		$rate=new \Common\Object\RateTable();
// 		echo "<pre>";
// 		$rateOne=new \Common\Object\Rate\RateMath(1);
// 		// 		new \Common\Object\Rate\RateProvide();
// 		// 		new \ArrayIterator();
// 		var_dump($rateOne->getOneMap());
// 		echo "</pre>";
	}

}