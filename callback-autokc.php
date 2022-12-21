<?php
require $_SERVER['DOCUMENT_ROOT'] . '/Core.php';
require $_SERVER['DOCUMENT_ROOT'] . '/lib/Pusher/Pusher.php';
// API ĐƯỢC LÀM BỚI HIEUVIP22
// VUI LÒNG KHÔNG XÓA
$kun = new System();
//$user = $kun->user();
date_default_timezone_set('Asia/Ho_Chi_Minh');
// Lưu log
$log = $_GET;
$log['date'] = date("d-m-Y h:i:s");
$myfile = fopen("log-kc.txt", "a+");
fwrite($myfile, json_encode($_GET).PHP_EOL);
fclose($myfile);
// API ĐƯỢC LÀM BỚI HIEUVIP22
// VUI LÒNG KHÔNG XÓA
$hash = @$_GET['content'];
if (is_numeric($hash) && strlen($hash) > 15){
    $check = $kun->connect_db()->query("SELECT * FROM `rut_kim_cuong` WHERE `hash` = '$hash'");
    if($check->num_rows > 0){
        $kc_info = $check->fetch_assoc();
        //Kiểm tra xem đơn đã duyệt chưa
        if($kc_info['status'] != 2) die("[3] BAD REQUEST!");
        if(@$_GET['status'] === 'thanhcong'){
            //Thành công
            $kun->connect_db()->query("UPDATE `rut_kim_cuong` SET `status` = '1' WHERE `hash` = '$hash'");
            die("RÚT THÀNH CÔNG");
        } else if(@$_GET['status'] === 'thatbai') {
            //Thất bại
            $kun->connect_db()->query("UPDATE `rut_kim_cuong` SET `status` = '0' WHERE `hash` = '$hash'");
            //Hoàn tiền
            $hoan_kc = $kc_info['tru_kc'];
            $user_rut = $kc_info['username'];
            $kun->connect_db()->query("UPDATE `users` SET `kimcuong` = `kimcuong` + '$hoan_kc' WHERE `username` = '$user_rut'");
            die("RÚT THẤT BẠI");
        } else {
            die("[4] BAD REQUEST!");
        }
    } else {
        die("[2] BAD REQUEST!");
    }
} else {
    die("[1] BAD REQUEST!");
}