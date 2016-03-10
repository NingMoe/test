<?php
/*
登录
http://120.27.139.117/home/user/login
post  验证码:phoneCode--电话:phone--密码:password
------------------------------------------------------
行驶证上传
http://120.27.139.117/home/Upload/updriving
首页-first--副页-second
------------------------------------------------------
选择投保 套餐
http://120.27.139.117/home/Package/
操作 ：和去年一样-lastYear--自定义套餐-diy--通过问题搭配-helpMe
------------------------------------------------------
报价单列表
http://120.27.139.117/home/Pricesheet/index
{"Pricesheet":{"id":"1","platenumber":"\u6caaa12345","insurancestyle":null,"quoteovertime":"2015-12-27 20:28","state":"0","companyname":[{"name":"\u592a\u5e73\u6d0b\u4fdd\u9669","prize":7271.1005,"quoteid":"1"},{"name":"\u5e73\u5b89\u8f66\u9669","prize":15307.58,"quoteid":"2"},{"name":"\u4eba\u4fdd\u8f66\u9669","prize":9184.548,"quoteid":"3"},{"name":"\u65b0\u7684\u516c\u53f8","prize":9643.7754,"quoteid":"4"},{"name":"\u65b0\u7684\u516c\u53f82","prize":9184.548,"quoteid":"6"}]}}
------------------------------------------------------
报价单详情：
http://120.27.139.117/home/Pricesheet/detail   /Pricedetail
get   companyid   drivingid
{"companyId":"2","company":"\u5e73\u5b89\u8f66\u9669","allPrice":15307.58,"allPriceDis":15307.58,"catePrice":"13297","catePriceDis":13297,"jq_price":"985.56","cc_price":"1025.02","cateList":{"dsfzr":{"name":"\u7b2c\u4e09\u8005\u8d23\u4efb\u9669","level":150,"price":2134.368,"disPrice":2134.368,"noPayPrice":null,"disNoPayPrice":0},"clss":{"name":"\u8f66\u8f86\u635f\u5931\u9669","level":"","price":6115.368984,"disPrice":6115.368984,"noPayPrice":null,"disNoPayPrice":0},"qcdq":{"name":"\u5168\u8f66\u76d7\u62a2\u9669","level":"","price":1493.10769344,"disPrice":1493.10769344,"noPayPrice":null,"disNoPayPrice":0},"sjzwzr":{"name":"\u53f8\u673a\u4e58\u5750\u8d23\u4efb\u9669","level":20,"price":883.2,"disPrice":883.2,"noPayPrice":null,"disNoPayPrice":0},"ckzwzr":{"name":"\u4e58\u5ba2\u5ea7\u4f4d\u8d23\u4efb\u9669","level":20,"price":499.2,"disPrice":499.2,"noPayPrice":null,"disNoPayPrice":0},"bl":{"name":"\u73bb\u7483\u5355\u72ec\u7834\u788e\u9669","level":"","price":794.926368,"disPrice":794.926368,"noPayPrice":null,"disNoPayPrice":0},"ss":{"name":"\u6d89\u6c34\u9669","level":"","price":30.57684492,"disPrice":30.57684492,"noPayPrice":null,"disNoPayPrice":0},"hh":{"name":"\u8f66\u8eab\u5212\u75d5\u9669","level":5000,"price":993.6,"disPrice":993.6,"noPayPrice":null,"disNoPayPrice":0},"zr":{"name":"\u81ea\u7136\u635f\u5931\u9669","level":"","price":353.362051584,"disPrice":353.362051584,"noPayPrice":null,"disNoPayPrice":0}},"redamount":765.379}
增加车信息
    ["owner"]=>
    string(6) "王菲"
    ["car_model"]=>
    string(29) "梅塞德斯奔驰WDDNG54X17A"
    ["vin"]=>
    string(17) "WDDNG54X17A167127"
    ["engine_no"]=>
    string(10) "2729463070"
    ["register_time"]=>
    string(10) "1262275200"
    ["id"]=>
    string(1) "1"
    ["license_number"]=>
    string(9) "沪a12345"
--------------------------
$result['jqccstarttime']  交强车船税的生效时间
$result['jqccendtime']  交强车船税的到期时间
-----------------------------------------
用户中心
行驶证信息
home/center/index
{"chepailist":[{"platenumber":null,"username":null,"carinfo":null,"chejianumber":null,"fadongjinumber":null,"regtime":null,"cankaoprize":null}]}
------------------------------------------
订单支付 与未支付
Home/order/getUncompleted-----getComplete  ordergetUncompleted      ordergetComplete
去内容[{"id":"2","number":"160105569849","status":"1","amount":"6944.30","pay_amount":null,"red_amount":null,"paytime":null}]
------
详情
Home/order/detail  
post参数  number 订单号码
含订单内容
{"id":"2","number":"160105569849","content":"{\"companyId\":\"1\",\"company\":\"\\u592a\\u5e73\\u6d0b\\u4fdd\\u9669\",\"allPrice\":14619.58,\"allPriceDis\":6944.3005,\"catePrice\":\"12609\",\"catePriceDis\":5989.275,\"jq_price\":\"985.56\",\"cc_price\":\"1025.02\",\"cateList\":{\"dsfzr\":{\"name\":\"\\u7b2c\\u4e09\\u8005\\u8d23\\u4efb\\u9669\",\"level\":150,\"price\":2133.408,\"disPrice\":1013.3688,\"noPayPrice\":0,\"disNoPayPrice\":0},\"clss\":{\"name\":\"\\u8f66\\u8f86\\u635f\\u5931\\u9669\",\"level\":\"\",\"price\":5003.776032,\"disPrice\":2376.7936152,\"noPayPrice\":750.5664048,\"disNoPayPrice\":356.51904228},\"qcdq\":{\"name\":\"\\u5168\\u8f66\\u76d7\\u62a2\\u9669\",\"level\":\"\",\"price\":1194.486154752,\"disPrice\":567.3809235072,\"noPayPrice\":238.8972309504,\"disNoPayPrice\":113.47618470144},\"sjzwzr\":{\"name\":\"\\u53f8\\u673a\\u4e58\\u5750\\u8d23\\u4efb\\u9669\",\"level\":5,\"price\":192,\"disPrice\":91.2,\"noPayPrice\":28.8,\"disNoPayPrice\":13.68},\"ckzwzr\":{\"name\":\"\\u4e58\\u5ba2\\u5ea7\\u4f4d\\u8d23\\u4efb\\u9669\",\"level\":5,\"price\":499.2,\"disPrice\":237.12,\"noPayPrice\":0,\"disNoPayPrice\":0},\"bl\":{\"name\":\"\\u73bb\\u7483\\u5355\\u72ec\\u7834\\u788e\\u9669\",\"level\":\"\",\"price\":656.678304,\"disPrice\":311.9221944,\"noPayPrice\":null,\"disNoPayPrice\":0},\"ss\":{\"name\":\"\\u6d89\\u6c34\\u9669\",\"level\":\"\",\"price\":25.01888016,\"disPrice\":11.883968076,\"noPayPrice\":3.752832024,\"disNoPayPrice\":1.7825952114},\"hh\":{\"name\":\"\\u8f66\\u8eab\\u5212\\u75d5\\u9669\",\"level\":5000,\"price\":1296,\"disPrice\":615.6,\"noPayPrice\":194.4,\"disNoPayPrice\":92.34},\"zr\":{\"name\":\"\\u81ea\\u7136\\u635f\\u5931\\u9669\",\"level\":\"\",\"price\":392.62450176,\"disPrice\":186.496638336,\"noPayPrice\":0,\"disNoPayPrice\":0}},\"redamount\":347.215025}","status":"1","amount":"6944.30","pay_amount":null,"red_amount":null,"paytime":null}
--------------------------------
分享列表
home/red/index
原始数据数据array (
0 => 
array (
'amount' => '7',
'nickname' => '七分十空',
'headimgurl' => 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM5fZz8eeZjGA9eBLcaFK8Rn0JjKhHKMich5MaOovfIFkJgxib22CtlCDlVKKR0TUYtJIqbJcuFkHv7w/0',
),
1 => 
array (
'amount' => '25',
'nickname' => '七分十空',
'headimgurl' => 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM5fZz8eeZjGA9eBLcaFK8Rn0JjKhHKMich5MaOovfIFkJgxib22CtlCDlVKKR0TUYtJIqbJcuFkHv7w/0',
),
--------------------
获取分享地址
red/getShareUrl
get share_string 用户标识字符串
$result=array('shareUrl'=>$url);
---------------------------------------------
用户分享出去链接
userred/share
array('userShareUrl'=>$shareUrl);
---------------------------------------------
首页车牌
home/index/getcars
[{"id":"1","carnumber":"\u6caaa12345"}]
-------------------------------
砍价帮
home/red/toplist
{"toplist":[{"sharetotal":"126","client_id":"2","headimgurl":"http://wx.qlogo.cn/mmopen/Q3auHgzwzM50s1TQ5eorjRTeZSZicSgp5Uxag77JWjZnZPv2HR7N67xrzgoPTn0mXeYQeRsKiamkpytqztazhVSw/0","nickname":"七分十空"}]}
-----------------------
支付
home/pay/pay
get orderid 换成 get ordernum   精简数据为   {"ordernum":"160105569849","amount":"160105569849","redUsable":"0"}
{"id":"2","number":"160105569849","content":"{\"companyId\":\"1\",\"company\":\"\\u592a\\u5e73\\u6d0b\\u4fdd\\u9669\",\"allPrice\":14619.58,\"allPriceDis\":6944.3005,\"catePrice\":\"12609\",\"catePriceDis\":5989.275,\"jq_price\":\"985.56\",\"cc_price\":\"1025.02\",\"cateList\":{\"dsfzr\":{\"name\":\"\\u7b2c\\u4e09\\u8005\\u8d23\\u4efb\\u9669\",\"level\":150,\"price\":2133.408,\"disPrice\":1013.3688,\"noPayPrice\":0,\"disNoPayPrice\":0},\"clss\":{\"name\":\"\\u8f66\\u8f86\\u635f\\u5931\\u9669\",\"level\":\"\",\"price\":5003.776032,\"disPrice\":2376.7936152,\"noPayPrice\":750.5664048,\"disNoPayPrice\":356.51904228},\"qcdq\":{\"name\":\"\\u5168\\u8f66\\u76d7\\u62a2\\u9669\",\"level\":\"\",\"price\":1194.486154752,\"disPrice\":567.3809235072,\"noPayPrice\":238.8972309504,\"disNoPayPrice\":113.47618470144},\"sjzwzr\":{\"name\":\"\\u53f8\\u673a\\u4e58\\u5750\\u8d23\\u4efb\\u9669\",\"level\":5,\"price\":192,\"disPrice\":91.2,\"noPayPrice\":28.8,\"disNoPayPrice\":13.68},\"ckzwzr\":{\"name\":\"\\u4e58\\u5ba2\\u5ea7\\u4f4d\\u8d23\\u4efb\\u9669\",\"level\":5,\"price\":499.2,\"disPrice\":237.12,\"noPayPrice\":0,\"disNoPayPrice\":0},\"bl\":{\"name\":\"\\u73bb\\u7483\\u5355\\u72ec\\u7834\\u788e\\u9669\",\"level\":\"\",\"price\":656.678304,\"disPrice\":311.9221944,\"noPayPrice\":null,\"disNoPayPrice\":0},\"ss\":{\"name\":\"\\u6d89\\u6c34\\u9669\",\"level\":\"\",\"price\":25.01888016,\"disPrice\":11.883968076,\"noPayPrice\":3.752832024,\"disNoPayPrice\":1.7825952114},\"hh\":{\"name\":\"\\u8f66\\u8eab\\u5212\\u75d5\\u9669\",\"level\":5000,\"price\":1296,\"disPrice\":615.6,\"noPayPrice\":194.4,\"disNoPayPrice\":92.34},\"zr\":{\"name\":\"\\u81ea\\u7136\\u635f\\u5931\\u9669\",\"level\":\"\",\"price\":392.62450176,\"disPrice\":186.496638336,\"noPayPrice\":0,\"disNoPayPrice\":0}},\"redamount\":347.215025}","status":"1","amount":"6944.30","pay_amount":null,"red_amount":null,"updatetime":"1451963447","paytime":null,"deadtime":"0"}
-------------------------
自己分享的列表
Home/userred/sharedlist
[{"amount":"7","nickname":"\u4e03\u5206\u5341\u7a7a","headimgurl":"http:\/\/wx.qlogo.cn\/mmopen\/Q3auHgzwzM5fZz8eeZjGA9eBLcaFK8Rn0JjKhHKMich5MaOovfIFkJgxib22CtlCDlVKKR0TUYtJIqbJcuFkHv7w\/0"},{"amount":"25","nickname":"\u4e03\u5206\u5341\u7a7a","headimgurl":"http:\/\/wx.qlogo.cn\/mmopen\/Q3auHgzwzM5fZz8eeZjGA9eBLcaFK8Rn0JjKhHKMich5MaOovfIFkJgxib22CtlCDlVKKR0TUYtJIqbJcuFkHv7w\/0"},{"amount":"12","nickname":"\u4e03\u5206\u5341\u7a7a","headimgurl":"http:\/\/wx.qlogo.cn\/mmopen\/Q3auHgzwzM5fZz8eeZjGA9eBLcaFK8Rn0JjKhHKMich5MaOovfIFkJgxib22CtlCDlVKKR0TUYtJIqbJcuFkHv7w\/0"},{"amount":"36","nickname":"\u4e03\u5206\u5341\u7a7a","headimgurl":"http:\/\/wx.qlogo.cn\/mmopen\/Q3auHgzwzM5fZz8eeZjGA9eBLcaFK8Rn0JjKhHKMich5MaOovfIFkJgxib22CtlCDlVKKR0TUYtJIqbJcuFkHv7w\/0"},{"amount":"46","nickname":"\u4e03\u5206\u5341\u7a7a","headimgurl":"http:\/\/wx.qlogo.cn\/mmopen\/Q3auHgzwzM5fZz8eeZjGA9eBLcaFK8Rn0JjKhHKMich5MaOovfIFkJgxib22CtlCDlVKKR0TUYtJIqbJcuFkHv7w\/0"}]
-------------------------------
所有订单
home/order/getAll
需要的话用这个，或者前面的，未完成的和完成的集合起来
字段名都一样。
----------------------------------------------

绑定手机   还用register  post参数里多个用户id  
--------------------------------------------------
得到授权地址
oneonebao.com/home/user/getAuthUrl
-----------------------------------------------------
地址
home/address/edit  保存
字段 name	phone	address	province	city	district
操作 add edit  提交数据
getCurrentAddress
得到当前用户地址信息
这里现在没有数据都是空的，添加后才有。
------------------------------------------------------
订单状态
订单状态0已报价生空，-1过期失效，1有内容生成，2已支付
------------------------------------------
套餐问题选择
package/helpme
一步步提交时，post   个step标识就可以。最后提交时，不要step，我就处理数据。
---------------------------------
有订单无订单
order/ishave
orderhave  -1 没有， 1有
-------------------------------
身份证上传
upload/upIdentityCard
首页firstcard副页secondcard
----------------------------
取状态
center/getStatus
{"actionstatus":"0"};
目前状态说明
	static $init=1;  //初始状态,未审核
	static $upDriving=2;//行驶证上传
	static $commitPackage=3;//提交套餐
	static $drivingInvalid=4;//报价失败,行驶证无效，
	static $packageInvalid=5;//报价失败,套餐无效
	static $priceOrOrderFailure=6;//报价或订单 失效
	static $createPrice=7;//报价完成
	static $createOrderContent=8;//生成未支付订单,查看详情，订单有数据
	static $paySuccess=9;//订单已支付
	static $insureEffect=10;//保单生效
	static $pastDue=11;//订单过期
------------------------------------------
失效后重新请求报价
pricesheet/requote
post carid车辆id
-----------------------------------------
失效后重选套餐
package/getCurrentPackage
post carid车辆id
字段名和选择套餐那里的一样
{"dsfzr":{"_isSelected":true,"_level":"1","_noPay":false},"clss":{"_isSelected":true,"_level":null,"_noPay":false},
"qcdq":{"_isSelected":false,"_level":null,"_noPay":false},"sjzwzr":{"_isSelected":false,"_level":"1","_noPay":false},
"ckzwzr":{"_isSelected":false,"_level":"1","_noPay":false},"bl":{"_isSelected":false,"_level":"1","_noPay":false},
"ss":{"_isSelected":false,"_level":null,"_noPay":false},"hh":{"_isSelected":false,"_level":"1","_noPay":false},
"zr":{"_isSelected":false,"_level":null,"_noPay":false},"zd":{"_isSelected":null,"_level":null,"_noPay":null}}
---------------------------------------








*/