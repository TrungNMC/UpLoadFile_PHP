<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File</title>
    <style type="text/css">
        .alert {
            border: 1px solid red;
            padding: 10px 15px;
            color: red;
        }
    </style>
</head>
<body>
<?php
    error_reporting(0);
    if ($_SERVER['REQUEST_METHOD']=='POST'){
        $file = $_FILES['filename'];

        echo '<pre>';
        print_r($file);
        echo '</pre>';

        $filename_arr = $file['name'];

        $size_allow = 100; //Tối đa 10MB

        $ext_allow = ['raz', 'zip', 'docx', 'doc', 'xls', 'xlsx', 'png', 'jpg', 'gif', 'jpeg', 'ppt', 'pptx'];

        $errors = [];

        $upload_succ = [];

        if(!empty($filename_arr)){
            foreach ($filename_arr as $key=>$item){

                $ext = end(explode('.', $item));

                $new_file = md5(uniqid());

                $new_file.='.'.$ext;

                $size = $file['size'][$key];

                $size = $size/1024/1024;

                $file_tmp = $file['tmp_name'][$key];



                if (in_array($ext, $ext_allow)){
                    if($size<=$size_allow){
                        //Thỏa mã dk upload
                        $upload = @move_uploaded_file($file_tmp, 'uploads/'.$new_file);
                        if(!$upload){
                            $errors[][$key] = 'upload_err';
                        }else{
                            $upload_succ[] = ['file'=>$item, 'new_file'=>$new_file];

                        }
                    }
                    else{
                        $errors[][$key] = 'size_err';
                    }
                }else {
                    $errors[][$key] = 'ext_err';
                }
            }
        }
        $count = count($upload_succ);
        if ($count>0){
            ?>
            <div class="alert">
                Đã upload thành công <?php echo $count; ?> files: <br/>
                <?php
                foreach ($upload_succ as $item){
                    echo '<a href="uploads/'.$item['new_file'].'">'.$item['file'].'</a><br/>';
                }
                ?>
            </div>
            <?php
        }else{
            if(!empty($errors)){
                $mess = '';
                foreach($errors as $error){
                    foreach($error as $index=>$err_name){
                        if($err_name=='ext_err'){
                            $mess.= 'Định dạng không hợp lệ với file: '.$file['name'][$index].'<br/>';
                        }else if($err_name=='size_er'){
                            $mess.= 'Dung lượng không được quá '.$size_allow.'MB, kiểm tra lại file '.$file['name'][$index].'<br/>';
                        }else {
                            $mess.= 'Không thể upload file '.$file['name'][$index].' tại thời điểm này <br/>';
                        }
                    }
                }
                if(!empty($mess)){
                    ?>
                    <div class="alert"><?php echo $mess; ?></div>
                    <?php
                }
            }else{
                ?>
                <div class="alert">Vui lòng chon file để upload</div>
                <?php
            }
        }
    }
?>
    <form method="post" enctype="multipart/form-data">
        <div>
            Chọn file cần upload<br/>
            <input type="file" name="filename[]" multiple/>
        </div>
        <div>
            <button type="submit">Upload</button>
        </div>
    </form>
</body>
</html>