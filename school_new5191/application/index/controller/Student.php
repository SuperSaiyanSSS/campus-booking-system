<?php
namespace app\index\controller;
use think\View;
use think\Request;
use think\Db;
use think\Controller;
use think\Session;
use app\index\model\Ranking;
class Student extends Controller{
	 public function _empty()
    {
        
        return '方法不存在';
    }
	public function t_view(Request $request){
		$array1=Db::query('select * from teacher');
		$this->assign('array1',$array1);
		return $this->fetch('t_view');
	}
	public function sinfo(Request $request){
		//可在此处添加post请求处理 用于更新学生信息
		$id_show=Session::get('name','think');
		$array1=Db::query('select * from student where student_id=?',[$id_show]);
		$this->assign('array1',$array1);
		return $this->fetch('sinfo',['student_id'=>$id_show]);
	}
	public function assess(Request $request){
		if($request->isPost()){
			$teacher_id=$request->post('teacher_name');
			$grade1=$request->post('DropDownList_grade1');
			$grade2=$request->post('DropDownList_grade2');
			$grade3=$request->post('DropDownList_grade3');
			$data = ['teacher_id' => $teacher_id, 'time' => $grade1,'attitude'=>$grade2,'trouble_shooting'=>$grade3];
			Db::table('ranking_list')->insert($data);
		}
		$id_show=Session::get('name','think');
		//echo $id_show;
		$time_id_a=Db::query('select time_id from tts where student_id=?',[$id_show]);
		$reault=array();
		#print_r($teacher_id_a);
		if(!empty($time_id_a)){
			
			foreach($time_id_a as $time_a){
				//print_r($time_a);
				
				$res_a=Db::query('select day_sequence,item_sequence,teacher_id from time where time_id=?',[$time_a['time_id']]);
				if(!empty($res_a)){
					$t_id=Db::query('select name from teacher where teacher_id=?',[$res_a[0]['teacher_id']]);
					$res_a[0]['name']=$t_id[0]['name'];
		//			print_r($res_a);
		//			echo '</br>';
					array_push($reault,$res_a);
				}
				else{
					array_push($reault,array(array('day_sequence' => 'null' ,'item_sequence' => 'null' ,'teacher_id' => 'null','name'=> 'null' )));
					echo "<script> alert('出现错误') </script>";
				}
				
			}
			//print_r($reault);
			
		}
		else{			
			echo "<script> alert('目前没有预约') </script>";
			array_push($reault,array(array('day_sequence' => 'null' ,'item_sequence' => 'null' ,'teacher_id' => 'null','name'=> 'null' )));
		}
		$array1=Db::query('select name,teacher_id from teacher');
		$this->assign('res_show',$reault);
		$this->assign('teacher_name',$array1);
		
		return $this->fetch('assess',['student_id'=>$id_show]);
	}
	public function ranking_list(Request $request){
		$ranking = new Ranking();
		//$user = Ranking::get(['teacher_id' => 't12222']);;
		//print_r($user['teacher_id']);
		//print_r($ranking->get_ranking());
		$garde_result=$ranking->get_ranking();
		$this->assign('garde_result',$garde_result);
		$id_show=Session::get('name','think');
		return $this->fetch('ranking_list',['student_id'=>$id_show]);
		
	}
	public function reservation_query(Request $request){
		if($request->isPost()){
			$id_delete=$request->post('id_delete');
			Db::table('tts')->where('time_id',$id_delete)->delete();
		}
		$time_id_choose=Db::query('select time_id,teacher_id from tts where student_id=?',[Session::get('name')]);
		//$time_choose=array();
		$choose=array();
		foreach($time_id_choose as $id_use){
//			print_r($id_use);
//			echo '</br>';
			$time_choose=Db::query('select * from time where time_id=?',[$id_use['time_id']]);
			$teacher_name=Db::query('select name from teacher where teacher_id=?',[$id_use['teacher_id']]);
			if(empty($time_choose)){
				echo "<script> alert('目前没有预约') </script>";
			}
			else{
				$time_choose[0]['name']=$teacher_name[0]['name'];
				array_push($choose,$time_choose);
			}
			
		}
		//print_r($choose);
		$this->assign('choose',$choose);
		$id_show=Session::get('name','think');
		return $this->fetch('reservation_query',['student_id'=>$id_show]);
		
	}
	public function reservations(Request $request){
		$array_teacher1=Db::query('select day_sequence,item_sequence,max_num,now_num from time where teacher_id=?',['t123']);
		$this->assign('array_teacher1',$array_teacher1);
		
		if($request->isPost()){
			if($request->post('btn_submit')=='查询'){
				$array_teacher1=Db::query('select day_sequence,item_sequence,max_num,now_num from time where teacher_id=?',[$request->post('DropDownList_exp_number')]);
				$this->assign('array_teacher1',$array_teacher1);
				//Db::table('time')->where('teacher_id', $request->post('DropDownList_exp_number'))->update(['now_num' => $array_teacher1[0]['now_num']+1]);
			}
			if($request->post('btn_booking')=='预约'){
				
				$day=$request->post('dlst_weekday');
				$item=$request->post('dlst_class');
				//echo$day.' '.$item;
				$now_num=Db::query('select now_num,time_id,teacher_id from time where day_sequence=? and item_sequence=?',[$day,$item]);
				//print_r($now_num);
				if(!empty($now_num)){
					$now_person=Db::query('select * from tts where time_id=? and teacher_id=? and student_id=?',[$now_num[0]['time_id'],$now_num[0]['teacher_id'],Session::get('name')]);
					if(!empty($now_person)){
						echo "<script> alert('你已预约本节课程') </script>";
					}
					else{
						
						Db::table('time')->where('day_sequence',$day )->where('item_sequence',$item)->update(['now_num'=>$now_num[0]['now_num']+1]);
						$array_teacher1=Db::query('select day_sequence,item_sequence,max_num,now_num from time where day_sequence=? and item_sequence=?',[$day,$item]);
						$data_tts = ['teacher_id' => $now_num[0]['teacher_id'],'time_id' => $now_num[0]['time_id'],'student_id'=>Session::get('name')];
						Db::table('tts')->insert($data_tts);
						$this->assign('array_teacher1',$array_teacher1);
					}
				}
				else{
					echo "<script> alert('本节课无安排') </script>";
					
				}
				
				
			}
		}
		$array_class=Db::query('select * from classroom limit 6');
		
		$this->assign('array_class',$array_class);
		$array1=Db::query('select name,teacher_id from teacher');
		$this->assign('teacher_name',$array1);
		$id_show=Session::get('name','think');
		return $this->fetch('reservations',['student_id'=>$id_show]);
	}
	public function suggestion_box(Request $request){
		if($request->isPost()){
			$suggestion=$request->post('a123');
			$teacher_id=$request->post('DropDownList_exp_number');
			$time=date("Y-m-d");
			$student_id=Session::get('name');
			$data = ['time' => $time, 'content' => $suggestion,'author_name'=>$student_id,'teacher_id'=>$teacher_id];
			Db::table('comment')->insert($data);
			echo "<script> alert('评价成功') </script>";
		}
		$array1=Db::query('select name,teacher_id from teacher');
		$this->assign('teacher_name',$array1);
		$id_show=Session::get('name','think');
		return $this->fetch('suggestion_box',['student_id'=>$id_show]);
	}
	
	
}
?>