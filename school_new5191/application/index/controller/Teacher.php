<?php
namespace app\index\controller;
use think\View;
use think\Request;
use think\Db;
use think\Controller;
use think\Session;
use app\index\model\Ranking;
class Teacher extends Controller{
	 public function _empty()
    {
       
        return '方法不存在';
    }
	public function info(Request $request){
		$id_show=Session::get('name','think');
		if($request->isGet()){
			$array1=Db::query('select research_area,email from teacher where teacher_id=?',[$_SERVER['QUERY_STRING']]);
			$this->assign('array1',$array1);
			//echo $_SERVER['QUERY_STRING'];
		}
		if($request->isPost()){
			$abo=$request->post('aboutme');
			$email=$request->post('nickname');
			echo $email;
			Db::table('teacher')->where('teacher_id',$id_show )->update(['email' => $email,'research_area'=>$abo]);
			echo "<script> alert('提交成功') </script>";
			$array1=Db::query('select research_area,email from teacher where teacher_id=?',[$_SERVER['QUERY_STRING']]);
			$this->assign('array1',$array1);
		}
		
		return $this->fetch('info',['$student'=>$id_show]);
		
	}
	public function publish(Request $request){
		//print_r(234);
		if($request->isPost()){
			//print_r(123);
			$date=$request->post('dlst_weekday');
			$class=$request->post('dlst_class');
			$student_num=$request->post('dlst_people');
			$teacher=Session::get('name');
			$data = ['day_sequence' => $date, 'max_num' => $student_num,'item_sequence'=>$class,'is_choosed'=>1,'teacher_id'=>$teacher];
			//Db::table('time')->insert($data);
			//print_r($date.' '.$class.' '.$student_num);
			if($class=='1-2'){
				$array1=Db::query('select one from classroom where date=?',[$date]);
				if($array1[0]['one']!=0){
					echo "<script> alert('本节课已被占用') </script>";
				}
				else{
					Db::table('time')->insert($data);
					$num_new=$array1[0]['one']+$student_num;
					Db::table('classroom')->where('date', $date)->update(['one' => $num_new]);
					echo "<script> alert('发布成功') </script>";
				}
				
			}
			if($class=='3-4'){
				$array1=Db::query('select two from classroom where date=?',[$date]);
				if($array1[0]['two']!=0){
					echo "<script> alert('本节课已被占用') </script>";
				}
				else{
					Db::table('time')->insert($data);
					$num_new=$array1[0]['two']+$student_num;
					Db::table('classroom')->where('date', $date)->update(['two' => $num_new]);
					echo "<script> alert('发布成功') </script>";
				}
			}
			if($class=='5-6'){
				$array1=Db::query('select three from classroom where date=?',[$date]);
				if($array1[0]['three']!=0){
					echo "<script> alert('本节课已被占用') </script>";
				}
				else{
					Db::table('time')->insert($data);
					$num_new=$array1[0]['three']+$student_num;
					Db::table('classroom')->where('date', $date)->update(['three' => $num_new]);
					echo "<script> alert('发布成功') </script>";
				}
			}
			if($class=='7-8'){
				$array1=Db::query('select four from classroom where date=?',[$date]);
				if($array1[0]['four']!=0){
					echo "<script> alert('本节课已被占用') </script>";
				}
				else{
					Db::table('time')->insert($data);
					$num_new=$array1[0]['four']+$student_num;
					Db::table('classroom')->where('date', $date)->update(['four' => $num_new]);
					echo "<script> alert('发布成功') </script>";
				}
			}
			
		}
		$array1=Db::query('select * from classroom limit 6');
		//print_r($array1);
		
		$this->assign('array1',$array1);
		$id_show=Session::get('name','think');
		return $this->fetch('publish',['teacher_id'=>$id_show]);
	}
	public function manager(Request $request){
		
		if($request->isPost()){
			$id_delete=$request->post('id_delete');
			$result1=Db::query('select * from time where time_id=?',[$id_delete]);
			$student_num=$result1[0]['max_num'];
			$date=$result1[0]['day_sequence'];
			$class=$result1[0]['item_sequence'];
			if($class=='1-2'){
				$array1=Db::query('select one from classroom where date=?',[$date]);
				
					$num_new=$array1[0]['one']-$student_num;
					Db::table('classroom')->where('date', $date)->update(['one' => $num_new]);
				
				
				
			}
			if($class=='3-4'){
				$array1=Db::query('select two from classroom where date=?',[$date]);
				
					$num_new=$array1[0]['two']-$student_num;
					Db::table('classroom')->where('date', $date)->update(['two' => $num_new]);
				
			
			}
			if($class=='5-6'){
				
				$array1=Db::query('select three from classroom where date=?',[$date]);
				
					$num_new=$array1[0]['three']-$student_num;
					Db::table('classroom')->where('date', $date)->update(['three' => $num_new]);
				
				
			}
			if($class=='7-8'){
				$array1=Db::query('select four from classroom where date=?',[$date]);
				
					$num_new=$array1[0]['four']-$student_num;
					Db::table('classroom')->where('date', $date)->update(['four' => $num_new]);
				
				
			}
			Db::table('time')->where('time_id',$id_delete)->delete();
			echo "<script> alert('修改成功') </script>";
		}
		$teacher=Session::get('name');
		$array1=Db::query('select day_sequence,max_num,item_sequence,time_id from time where teacher_id=?',[$teacher]);
		//print_r($array1);
		$this->assign('list',$array1);
		$id_show=Session::get('name','think');
		return $this->fetch('manager',['teacher_id'=>$id_show]);
	}
	public function suggestion(){
		//建议这里在学生模块在存储评论时必须同时存储老师的id
		$teacher=Session::get('name');
		$result_comment=Db::query('select comment_id,content,time from comment where teacher_id=?',[$teacher]);
		$this->assign('list',$result_comment);
		$id_show=Session::get('name','think');
		return $this->fetch('suggestion',['teacher_id'=>$id_show]);
	}
	
	
		public function ranking_list(Request $request){
		$ranking = new Ranking();
		//$user = Ranking::get(['teacher_id' => 't12222']);;
		//print_r($user['teacher_id']);
		//print_r($ranking->get_ranking());
		$garde_result=$ranking->get_ranking();
		$this->assign('garde_result',$garde_result);
		$id_show=Session::get('name','think');
		return $this->fetch('ranking_list',['teacher_id'=>$id_show]);
		
	}
	
}
?>