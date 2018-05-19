<?php
namespace app\index\controller;
use think\View;
use think\Request;
use think\Db;
use think\Controller;
use think\Session;
class Manager extends Controller{
	 public function _empty()
    {
        
        return '方法不存在';
    }
	public function gchange(Request $request){
		if($request->isPost()){
			//Db::table('student')->where('student_id',$request->post('id_delete'))->delete();
			if($request->post('delete')=='删除'){
				Db::table('student')->where('student_id',$request->post('id_delete'))->delete();
				//echo $request->post('delete');
			}
			else{
				Db::table('student')->where('student_id',$request->post('id_delete'))->update(['email' => $request->post('mail')]);
			}
			
		}
		$array1=Db::query('select * from student');
		//print_r($array1);
		$this->assign('array1',$array1);
		return $this->fetch('gchange');
	}
	
	public function gchanget(Request $request){
		if($request->isPost()){
			//Db::table('student')->where('student_id',$request->post('id_delete'))->delete();
			if($request->post('delete')=='删除'){
				Db::table('teacher')->where('teacher_id',$request->post('id_delete'))->delete();
				//echo $request->post('delete');
			}
			else{
				Db::table('teacher')->where('teacher_id',$request->post('id_delete'))->update(['email' => $request->post('mail')]);
			}
			
		}
		$array1=Db::query('select * from teacher');
		//print_r($array1);
		$this->assign('array1',$array1);
		return $this->fetch('gchanget');
	}
	public function gdelete(Request $request){
		$flag=1;
		$array_show1=array();
		if($request->isPost()){
			if($request->post('delete')=="删除"){
				Db::table('comment')->where('comment_id',$request->post('id_delete'))->delete();
			}
			else{
				$teacher_name=$request->post('DropDownList_exp_number');
				$array_teacher=Db::query('select teacher_id from teacher where name=?',[$teacher_name]);
				foreach($array_teacher as $array_res){
					//print_r($array_res['teacher_id']);
					$array_comment=Db::query('select * from comment where teacher_id=?',[$array_res['teacher_id']]);
					$array_show1=array_merge($array_show1,$array_comment);
				}
				//$this->assign('array_comment',$array_show1);
				$flag=0;
				//print_r($array_show1);
				//$array_comment=Db::query('select * from comment where ');
			}
			
		}
		$array_teacher=Db::query('select name from teacher');
		if($flag==1){
			$array_comment=Db::query('select * from comment');
			$this->assign('array_comment',$array_comment);
		}
		else{
			$this->assign('array_comment',$array_show1);
		}
		//$array_teacher=array_unique($array_teacher[0]);
		$array_t=array();
		$array_t1=array();
		foreach($array_teacher as $res){
			array_push($array_t,$res['name']);
		}
		$array_t=array_unique($array_t);
		foreach($array_t as $res){
			array_push($array_t1,array('name'=>$res));
		}
		//print_r($array_t1);
		$this->assign('array_teacher',$array_t1);
		
		return $this->fetch('gdelete');
	}
}
?>