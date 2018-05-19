<?php
namespace app\index\model;
use think\Model;
use think\Db;
class Ranking extends \think\Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'ranking_list';
    
    // 设置当前模型的数据库连接
    protected $connection = [
        // 数据库类型
        'type'        => 'mysql',
        // 服务器地址
        'hostname'    => '127.0.0.1',
        // 数据库名
        'database'    => 'school',
        // 数据库用户名
        'username'    => 'root',
        // 数据库密码
        'password'    => 'root',
        // 数据库编码默认采用utf8
        'charset'     => 'utf8',
        
        // 数据库调试模式
        'debug'       => false,
    ];
    
    public function get_ranking(){
    	$array_id=Db::query('select teacher_id from teacher');
    	$use=new Ranking();
    	$array_res=array();
    	//查询每一个老师的工号
    	foreach($array_id as $id_p){
    		$sum=0;
    		$sum_t=0;
    		$sum_a=0;
    		$sum_q=0;
    		$res=$use->where('teacher_id', $id_p['teacher_id'])->select();
    		//$res=$use->where('teacher_id', 't12345')->select();
    		//对每一个工号进行分析
    		//if(!empty($res)){
    			foreach ($res as $data){
	    			$sum_t+=$data['time'];
	    			$sum_a+=$data['attitude'];
	    			$sum_q+=$data['trouble_shooting'];
	    			$sum=$sum_a+$sum_q+$sum_t;
    			}
    		
    			array_push($array_res,array(
    				'teacher_id'=>$id_p['teacher_id'],
	    			'time'=>$sum_t,
	    			'a'=>$sum_a,
	    			'q'=>$sum_q,
	    			'sum'=>$sum
    			)
    			);
    		//}
    		
    		//echo $sum.' '.$sum_a.' '.$sum_q.' '.$sum_t.'</br>';
    	
    	}
    	//print_r($array_res);
    	return $array_res;
    }
    
}
?>