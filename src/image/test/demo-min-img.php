<?php
use qpf\image\lib\Image;

include __DIR__ . '/../../unit.php';

// 获取token
$session_input_token = QPF::$app->session->get('input_token');

// 资源目录
$imagePath = __DIR__ . '/image';

// 防止刷新重复提交
if (isset($_POST['input_token']) && $session_input_token == $_POST['input_token']
    && !empty($_FILES) && isset($_POST['width']) && isset($_POST['height'])) {
    
    // 宽高最小50x50
    $width = max(50, intval($_POST['width']));
    $height = max(50, intval($_POST['height']));
    
    $file = QPF::$app->request->file('image');
    
    // 由于测试, 固定保存同一个文件名.tmp
    $info = $file->validate(['size' => 2*1024*1024, 'ext' => 'gif,png,jpg'])->move($imagePath, '');
    
    echor($info);
    
    if ($info) {
        // 上传图片完整路径
        $upImage = $info->getPathname();

        $image = new Image($upImage);
        
        echor($image->info);
        
        
        $image->thumbnail($imagePath . '/demo-min-img' . $image->info['ext'], $width, $height);
        
        echo '<h1>裁剪结果</h1>';
        echo '<img src="'. dirname(QPF::$app->request->baseUrl()) . '/image/demo-min-img' . $image->info['ext'] .'" width="'.$width.'" height="'.$height.'">';
        
        
        // 删除上传的文件
        $up_file = $info->getPathname();
        if (is_file($up_file)) {
            unset($info);// 需要先释放资源
            unlink($up_file);
        }
        
        // 如果设置成固定的值. 黑客可能会 提交固定的值, (''|null) == $_POST['input_token'] 始终未true
        $input_token = QPF::$app->session->set('input_token', mt_rand());
        
        exit(1);
    } else {
        echor($info->getError());
    }
}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>裁剪图片</title>
	</head>
	<body>
		<div id="">
			
			<form action="" method="post" enctype="multipart/form-data">
				<label>
				选择要裁剪的图片:
				<input type="file" name="image" />
				</label>
				<br>	
				<label>
				宽度:
				<input type="text" name ="width" />
				</label>
				<br>	
				<label>
				高度:
				<input type="text" name ="height" />
				</label>
				 <br>	
				 <input type="hidden" name="input_token" value="<?php 
				    // 生成令牌, 并保存再session
				    echo $input_token = mt_rand(0, 99999);
				    QPF::$app->session->set('input_token', $input_token);
				 ?>"/> 
				<button type="submit">上传</button>
			</form>
		</div>
	</body>
</html>
