<?php
namespace Common\Object\Rate;
/**
 * @author sks
 *
 */
class OneMap{
	public $dsfzr;//第三方责任险
	public $clss;//车损险	车辆损失险	【基础保费】--【费率】
	public $qcdq;//盗抢险	全车盗抢险	【基础保费】--【费率】
	public $sjzwzr;//司机座位责任           【基础保费】--【费率】
	public $ckzwzr;//乘客座位责任	【基础保费】--【费率】
	public $bl;//玻璃险			【国产玻璃】--【进口玻璃】
																//del// 	const SS//涉水险   //必须购买车损险；车损险保费X5%					
	public $hh;//划痕险
			
	public $zrjy;//自燃险 家庭自用车 		1年以下	1-2年	2-6年	6年以上
	public $zrqt;//自然险 其他车		2年以下	2-3年	3-4年	4年以上
			
	public $cszj;//车身折旧费率
			
	public $cxzkxs_lxwcx;//出险折扣系数,连续未出险
	public $cxzkxs_snycx;//出险折扣系数,连续未出险
	public $cxzkxs_xchyd;//出险折扣系数,新车或异地转入车辆
			
	public $jqxjcflb;//交强险基础费率表
}