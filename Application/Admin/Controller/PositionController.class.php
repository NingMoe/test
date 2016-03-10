<?php
namespace Admin\Controller;
use Common\Model\PositionModel;
class PositionController extends AdminController{
	public function getList(){
		$userid=I('get.userid');
		$mp=new PositionModel();
		$list=$mp->getClientAllPositions($userid);
// 		ct($list); 取值 {$list['']}  格式化时间 {:ftime($time)}
		$this->assign('list',$list);
		$this->display();
	}
}