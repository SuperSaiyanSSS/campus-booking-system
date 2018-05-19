<?php
namespace app\index\controller;
use think\View;
use think\Request;
use think\Db;
use think\Controller;
use think\Session;
use \think\Validate;

class Index extends Controller
{
	 public function xieyi(Request $request){
	 	return $this->fetch('xieyi');
	 }
    public function index(Request $request)
    {
       
      	if($request->isPost()){
      		//echo "123";
      		$user_id=$request->post('user');
    		$password=$request->post('password');
    		$identype=$request->post('identype');
    		
    		//echo $student;
    		//进行数据库查询确定用户身份并完成登录及页面跳转，教师和学生页面跳转不同
    		$info="teacher li";
    		$view = new View();
    		//根据identity的不同值实现不同页面的跳转
      		#return $view->fetch('success',['name'=>$user_name,'domain'=>$domain_user[0]['domain']]);
      		//定位到test文件夹下的模板  控制器/文件名
      		if($identype=='teacher'){
      			$array1=Db::query('select * from teacher where teacher_id=?',[$user_id]);
      			if(empty($array1)){
      				echo "<script> alert('用户不存在或登录类型错误') </script>";
      				return $this->fetch('index');
      			}
      			else{
      				if($array1[0]['teacher_id']==$user_id&&md5($password)==$array1[0]['password']){
	      				//return $view->fetch('/teacher/3',['name'=>$user_id,'domain'=>$password]);
	      				$ip = $_SERVER["REMOTE_ADDR"];
	      				$myfile = fopen("a.txt", "a+") or die("Unable to open file!");
						fwrite($myfile, $ip);
						$ip = "  ";
						fwrite($myfile, $ip);
						$ip = $user_id;
						fwrite($myfile, $ip);
						$ip = "\n     ";
						fwrite($myfile, $ip);
						fclose($myfile);
	      				$array1=Db::query('select * from classroom limit 6');
							//print_r($array1);
						$this->assign('array1',$array1);
						Session::set('name',$user_id,'think');
	      				return $this->fetch('/teacher/publish',['teacher_id'=>$user_id]);
	      			}
	      			else{
	      				echo "<script> alert('登录失败') </script>";
	      				return $this->fetch('index');
	      			}
      			}
      			
      			//print_r($array1[0]['teacher_id']);
      			//return $view->fetch('/teacher/3',['name'=>$user_id,'domain'=>$password]);
      		}
      		//改
      		else if($identype=='student'){
      			
      			$array1=Db::query('select * from student where student_id=?',[$user_id]);
      			if(empty($array1)){
      				echo "<script> alert('用户不存在或登录类型错误') </script>";
      				return $this->fetch('index');
      			}
      			else{
      				if($array1[0]['student_id']==$user_id&&md5($password)==$array1[0]['password']){
      				//return $view->fetch('/teacher/3',['name'=>$user_id,'domain'=>$password]);
	      				Session::set('name',$user_id,'think');
	      				$ip = $_SERVER["REMOTE_ADDR"];
	      				$myfile = fopen("a.txt", "a+") or die("Unable to open file!");
						fwrite($myfile, $ip);
						$ip = "  ";
						fwrite($myfile, $ip);
						$ip = $user_id;
						fwrite($myfile, $ip);
						$ip = "\n     ";
						fwrite($myfile, $ip);
						fclose($myfile);
	      				$array_teacher1=Db::query('select day_sequence,item_sequence,max_num,now_num from time where teacher_id=?',['t123']);
						$this->assign('array_teacher1',$array_teacher1);
						$array1=Db::query('select name,teacher_id from teacher');
						$this->assign('teacher_name',$array1);
						$array_class=Db::query('select * from classroom limit 6');		
						$this->assign('array_class',$array_class);
	      				return $this->fetch('/student/reservations',['student_id'=>$user_id]);
	      			}
	      			else{
	      				echo "<script> alert('登录失败') </script>";
	      				return $this->fetch('index');
	      			}
      			}
      			
      			
      		}
      		else{
      			
      			if($user_id=='root'&&md5($password)=='63a9f0ea7bb98050796b649e85481845'){
      				Session::set('name','root','think');
      				$array1=Db::query('select * from student');
						//print_r($array1);
					$this->assign('array1',$array1);
      				return $this->fetch('/manager/gchange');
      			}
      			else{
      				echo "<script> alert('请正确输入信息') </script>";
      				return $this->fetch('index');
      			}
      			//return $view->fetch('manager/gchange');
      		}
    		
      	}else{
      		$view = new View();
      		return $view->fetch('index');
      	}
     }
     public function sign(Request $request){
     	if($request->isPost()){
     		//echo '123';
     		$user_id=$request->post('name');
    		$password=$request->post('password');
    		$mail=$request->post('mail');
    		$identype=$request->post('identype');
    		$user_name=$request->post('name1');
    		$pass_save=md5($password);
    		$degree=$request->post('dlst_degree');
    		if($identype=="student"){
    			$data = ['student_id' => $user_id, 'email' => $mail,'password'=>$pass_save,'name'=>$user_name,'degree'=>$degree];
				Db::table('student')->insert($data);
				echo "<script> alert('学生注册成功') </script>";
    		}
    		if($identype=='teacher'){
    			$data = ['teacher_id' => $user_id, 'email' => $mail,'password'=>$pass_save,'name'=>$user_name];
				Db::table('teacher')->insert($data);
				echo "<script> alert('教师注册成功') </script>";
    		}
    		if($identype=='admin'){
    			if(strpos($user_id,'t')===false){
    				echo "<script> alert('此用户不允许注册为管理员') </script>";
    			}
    			else{
    				$data = ['student_id' => $user_id, 'email' => $mail,'password'=>$pass_save];
					Db::table('manager')->insert($data);
					echo "<script> alert('管理员注册成功') </script>";
    			}
    			
    		}
    		//echo $user_name.' '.$password.' '.$mail.' '.$identype;
    		
     	}
     	$view = new View();
     	return $view->fetch('sign');
     }
     
     public function check_id($checkid){
     	$domain = strstr($checkid,'t');
     	//echo $domain;
     	if($domain==$checkid){
     		$array1=Db::query('select * from teacher where teacher_id=?',[$checkid]);
     		//$array2=Db::query('select * from manager where student_id=?',[$checkid]);
     		//echo count($array2[0]['student_id']);
     		if(count($array1)>0){
   				echo 'have';
   			}
     	}
     	else{
     		$array3=Db::query('select * from student where student_id=?',[$checkid]);
     		if(count($array3)>0){
     			//print_r($array3);
   				echo 'have';
   			}
   			//print_r(count($array3));
     	}
     	
     	//echo 'have';
     	//print_r(count($array1));
   		
     }
     
     
     //验证邮箱格式
     public function validate_mail($mail){
     	$validate = new Validate([
		    
		    'email' => 'email'
		]);
		$data = [
		    
		    'email' => $mail
		];
		if (!$validate->check($data)) {
		    echo $validate->getError();
		}
		
     }
    
}
