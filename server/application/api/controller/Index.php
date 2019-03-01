<?php
namespace app\api\controller;

use think\Config;
use think\Request;

class Index extends App
{
    
    public function index(){

    }
    public function tulingApi(){
    	$arr = array(
			'reqType' => '0',//
			'perception' => array(
				'inputText' => array(
					'text' => '时间'
				)
			),
			'userInfo' => array(
				'apiKey' => '44f49fc37dd8487382f5875d460f138d',
				'userId' => '123'
			)
		);
		$arr = json_encode($arr);
		$msg = $this->call_tuling_api($arr);
		return json_decode($msg,true)['results'][0]['values']['text'];
    }

    function call_tuling_api($array,$posturl="http://openapi.tuling123.com/openapi/api/v2"){
    	$ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $array);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $posturl);
        $return_data = curl_exec($ch);
        curl_close($ch);
        return $return_data;
	}

	public function stockAnalysis($stock_code='000001'){
		$request_url = sprintf("http://doctor.10jqka.com.cn/%s/",$stock_code);
		$data = file_get_contents($request_url);
		$data = iconv("gb2312", "utf-8//IGNORE",$data);
		//匹配title正则
		$title_partern = '/<strong class="title">(.*?)<a href="#" class="more hide">/ies';
		preg_match_all($title_partern,$data,$title_result);
		$result['title'] = $title_result[1][0];
		//匹配趋势则
		$duanqi_qushi_partern = '/<span>短期趋势：<\/span><p>(.*?)<\/p>/ies';
		preg_match($duanqi_qushi_partern,$data,$duanqi_qushi_result);
		$result['duanqi_qushi'] = $duanqi_qushi_result[1];
		$zhongqi_qushi_partern = '/<span>短期趋势：<\/span><p>(.*?)<\/p>/ies';
		preg_match($zhongqi_qushi_partern,$data,$zhongqi_qushi_result);
		$result['zhongqi_qushi'] = $zhongqi_qushi_result[1];
		$zhongqi_qushi_partern = '/<span>中期趋势：<\/span><p>(.*?)<\/p>/ies';
		preg_match($zhongqi_qushi_partern,$data,$zhongqi_qushi_result);
		$result['zhongqi_qushi'] = $zhongqi_qushi_result[1];
		$changqi_qushi_partern = '/<span>长期趋势：<\/span><p>(.*?)<\/p>/ies';
		preg_match($changqi_qushi_partern,$data,$changqi_qushi_result);
		$result['changqi_qushi'] = $changqi_qushi_result[1];
		//近期成本
		$jinqi_chengben_partern = '/<p class="content">(.*?)<\/p>/ies';
		preg_match($jinqi_chengben_partern,$data,$jinqi_chengben_result);
		$result['jinqi_chengben'] = $jinqi_chengben_result[1];
		$result['jinqi_chengben'] = strip_tags($result['jinqi_chengben']);
		//市场表现
		$shichang_biaoxian_partern = '/<h3 class="hd">市场表现<\/h3>(.*?)<\/div>/ies';
		preg_match($shichang_biaoxian_partern,$data,$shichang_biaoxian_result);
		$result['shichang_biaoxian'] = $shichang_biaoxian_result[1];
		$result['shichang_biaoxian'] = strip_tags($result['shichang_biaoxian']);
		//压力支撑
		$yali_zhicheng_partern = '/压力支撑<\/h3>(.*?)<\/div>/ies';
		preg_match($yali_zhicheng_partern,$data,$yali_zhicheng_result);
		$result['yali_zhicheng'] = $yali_zhicheng_result[1];
		$result['yali_zhicheng'] = strip_tags($result['yali_zhicheng']);
		//多空趋势
		$duokong_qushi_partern = '/<h3 class="hd">多空趋势<\/h3>(.*?)<\/div>/ies';
		preg_match($duokong_qushi_partern,$data,$duokong_qushi_result);
		$result['duokong_qushi'] = $duokong_qushi_result[1];
		$result['duokong_qushi'] = strip_tags($result['duokong_qushi']);
		//主力分析
		//资金流向
		$zijin_liuxiang_partern = '/如何查看实时大资金异动？<\/a>(.*?)<\/div>/ies';
		preg_match($zijin_liuxiang_partern,$data,$zijin_liuxiang_result);
		$result['zijin_liuxiang'] = $zijin_liuxiang_result[1];
		$result['zijin_liuxiang'] = strip_tags($result['zijin_liuxiang']);
		//主力控盘
		$zhuli_kongpang_partern = '/主力控盘<\/a>(.*?)<\/div>/ies';
		preg_match($zhuli_kongpang_partern,$data,$zhuli_kongpang_result);
		$result['zhuli_kongpang'] = $zhuli_kongpang_result[1];
		$result['zhuli_kongpang'] = strip_tags($result['zhuli_kongpang']);
		//机构持仓
		$jigou_chicang_partern = '/机构持仓<\/a>(.*?)<\/div>/ies';
		preg_match($jigou_chicang_partern,$data,$jigou_chicang_result);
		$result['jigou_chicang'] = $jigou_chicang_result[1];
		$result['jigou_chicang'] = strip_tags($result['jigou_chicang']);
		//数据拼接
		$dataConnect = "【大数据分析】"."\n".$result['title']."\n"."【持仓建议】"."\n"."短线建议：".$result['duanqi_qushi']."\n"."中线建议：".$result['zhongqi_qushi']."\n"."长线建议：".$result['changqi_qushi']."\n"."【近期表现】"."\n".$result['jinqi_chengben']."\n".$result['shichang_biaoxian']."\n".$result['yali_zhicheng']."\n".$result['duokong_qushi']."\n".$result['zijin_liuxiang']."\n"."【主力分析】"."\n".$result['zhuli_kongpang']."\n".$result['jigou_chicang'];
		// return $data; 
		return $dataConnect;
	}
}
