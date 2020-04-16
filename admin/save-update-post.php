<?php
	
	include 'config.php';
	
	// Checking if user uploaded the image or not
	if(empty($_FILES['new_image']['name'])){
		$new_name = $_POST['old_image'];
	}else{

		$errors = array();

		$file_name = $_FILES['new_image']['name'];
		$file_size = $_FILES['new_image']['size'];
		$file_tmp = $_FILES['new_image']['tmp_name'];
		$file_type = $_FILES['new_image']['type'];
		
		$array = explode('.', $file_name);
		$file_ext = strtolower(end($array));

		$extensions = array("jpeg","jpg","png");

		if(in_array($file_ext, $extensions) === false){
			$errors[] = "This file extension is not allowed, Please upload jpg or png images";
		}

		if($file_size > 2097152){
			$errors[] = "File size must me 2mb or lower";
		}

		$new_name = time(). "-" .basename($file_name);
		$target = "upload/" . $new_name;
		$image_name = $new_name;

		if(empty($errors)){
			move_uploaded_file($file_tmp, $target);
		}else{
			print_r($errors);
			die();
		}
	}

	$date = date("d M, Y");
	$sql = "UPDATE post SET title = '{$_POST["post_title"]}', description = '{$_POST["postdesc"]}',
	category = {$_POST['category']}, post_date = '{$date}', post_img = '{$image_name}'
	WHERE post_id = {$_POST['post_id']} ;";

	if($_POST['old_category'] != $_POST['category']){
		$sql .= "UPDATE category SET post = post - 1 WHERE category_id = {$_POST['old_category']};";
		$sql .= "UPDATE category SET post = post + 1 WHERE category_id = {$_POST['category']};";
	}
	
	$result = mysqli_multi_query($conn, $sql) or die("Query Failed");

	if($result){
		header("location: {$hostname}/admin/post.php");
	}else{
		echo "Query Failed";
	}
?>