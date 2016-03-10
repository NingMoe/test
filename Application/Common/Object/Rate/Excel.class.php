<?php
namespace Common\Object\Rate;
class Excel{
	protected $objPhpexcel;
	Public function __construct($fileName=""){
		import("PHPExcel.Classes.PHPExcel");
		if(empty($fileName)){
			$this->objPhpexcel=new \PHPExcel();
		}else{
			$this->readExcel($fileName);
		}
	}
	/*
	 * 读取新的excel
	 */
	public function readExcel($fileName){
		$objReader=\PHPExcel_IOFactory::createReader('Excel5');
		$objReader->setReadDataOnly(true);
		$this->objPhpexcel=$objReader->load($fileName);
	}
	/*
	 * 根据x，y坐标得到单元格的值。
	 */
	public function getCellValue($numRow,$numCol){
		$objWorkSheet=$this->objPhpexcel->getActiveSheet();
		return $objWorkSheet->getCellByColumnAndRow($numRow,$numCol)->getValue();
	}
	/*
	 * @param attrubutes 属性数组
	 * @param rs 数据
	 */
	public function exportRs($attributes,$rs,$sname="sheetName",$ename="excelName"){
		if(!$attributes || !$rs){
			return false;
		}
		$objSheet=$this->objPhpexcel->getActiveSheet()->setTitle($sname);
		$this->setSheetAttributes(&$objSheet);
		$i=1;
		foreach ($rs as $row){
			$j=0;
			foreach ($attributes as $att){
				if($i==1){
					$objSheet->setCellValue($this->getCell($j).$i,$att['title']);//设置标题
					$objSheet->setCellValue($this->getCell($j).($i+1),$row[$att['field'][0]]);//设置标题
				}else {
					$objSheet->setCellValue($this->getCell($j).$i,$row[$att['field'][0]]);
				}
				$j++;
			}
			if($i==1){
				$i++;
			}
			$i++;
		}
		
// 		$this->writeExcel($ename);

		$this->browseExport($ename);
// 		return true;
	}
	private function setSheetAttributes(&$objSheet){
		$objSheet->getDefaultStyle()->getFont()->setSize(12)->setName("楷体");
		$objSheet->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objSheet->getStyle("A1:Z1")->getFont()->setSize(13)->setBold(true);
		$objSheet->getDefaultRowDimension()->setRowHeight(30);//设置默认行高
	}
	private function writeExcel($ename="excelName"){
		$objWrite=\PHPExcel_IOFactory::createWriter($this->objPhpexcel,'excel2007');
		$file="./".$ename.'.xls';
		$objWrite->save($file);
	}
	private function browseExport($ename="excelName"){
		$type='excel5';
// 		$type='excel2007';
		$objWrite=\PHPExcel_IOFactory::createWriter($this->objPhpexcel,$type);
		if($type=="Excel5"){
			header('Content-Type: application/vnd.ms-excel');//告诉浏览器将要输出excel03文件
		}else{
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//告诉浏览器数据excel07文件
		}
		header('Content-Disposition: attachment;filename="'.$ename.'.xls"');//告诉浏览器将输出文件的名称
		header('Cache-Control: max-age=0');//禁止缓存
		
		$objWrite->save("php://output");
	}
	
	public function test(){
		$path="./";
		$name="test.xls";
		$file=$path.$name;
		$objSheet=$this->objPhpexcel->getActiveSheet();
		$objSheet->setCellValue('a1',"fefe1");
		$objSheet->setCellValue('b1',"fefe2");
		$objSheet->setCellValue('a2',"a2");
		$objSheet->setCellValue('b2','b2beebb');
		$objWrite=\PHPExcel_IOFactory::createWriter($this->objPhpexcel,'excel2007');
		$objWrite->save($file);
	}
	
	private function getCell($index){
		$arr=range('A','Z');
		return $arr[$index];
	}
	function __destruct(){
		unset($this->objPhpexcel);
	}
}














