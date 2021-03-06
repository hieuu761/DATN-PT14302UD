<?php
ob_start();
session_start();
?>
@extends('templates.shoes.master')
@section('title')Thông tin thanh toán @endsection
@section('content')
@if (Auth::user() == null)
<script>
    window.location.href = "/";
</script>
@else
<?php
$vnp_SecureHash =$request->vnp_SecureHash;
$inputData = array();
$vnp_HashSecret = $request->vnp_HashSecret; //Chuỗi bí mật
foreach ($_GET as $key => $value) {
    if (substr($key, 0, 4) == "vnp_") {
        $inputData[$key] = $value;
    }
}

unset($inputData['vnp_SecureHashType']);
unset($inputData['vnp_SecureHash']);
ksort($inputData);
$i = 0;
$hashData = "";
foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashData = $hashData . '&' . $key . "=" . $value;
    } else {
        $hashData = $hashData . $key . "=" . $value;
        $i = 1;
    }
}

//$secureHash = md5($vnp_HashSecret . $hashData);
$secureHash = hash('sha256', $vnp_HashSecret . $hashData);
?>
<div class="pay">
    <div class="container margin-res-top" style="margin-top: 150px;">
        <div class="col-sm-9" style="left: 30%">
            <div class="header clearfix ">
                <h3 class=" text-left">{{__('sentence.information_line')}}</h3>
            </div>
            <div class="table">
                <div class="form-group row">
                    <label for="colFormLabelSm" class="col-sm-3 col-form-label col-form-label-sm">{{__('sentence.order_code')}}:</label>
                    <div class="col-sm-3">
                        <label for="colFormLabelSm" id="order_desc" name="order_desc">{{$request->vnp_TxnRef}}</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="colFormLabelSm" class="col-sm-3 col-form-label col-form-label-sm">{{__('sentence.amount_of_money')}}:</label>
                    <div class="col-sm-3">
                        <label for="colFormLabelSm" id="order_desc" name="order_desc"><?= number_format($_GET['vnp_Amount'] / 100) ?> VNĐ</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="colFormLabelSm" class="col-sm-3 col-form-label col-form-label-sm">{{__('sentence.content_billing')}}:</label>
                    <div class="col-sm-6">
                        <label for="colFormLabelSm" id="order_desc" name="order_desc">{{$request->vnp_OrderInfo}}</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="colFormLabelSm" class="col-sm-3 col-form-label col-form-label-sm">{{__('sentence.response_code')}}:</label>
                    <div class="col-sm-3">
                        <label for="colFormLabelSm" id="order_desc" name="order_desc">{{$request->vnp_ResponseCode}}</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="colFormLabelSm" class="col-sm-3 col-form-label col-form-label-sm">{{__('sentence.transaction_code_at')}} VNPAY:</label>
                    <div class="col-sm-3">
                        <label for="colFormLabelSm" id="order_desc" name="order_desc">{{$request->vnp_TransactionNo}}</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="colFormLabelSm" class="col-sm-3 col-form-label col-form-label-sm">{{__('sentence.code_bank')}}:</label>
                    <div class="col-sm-3">
                        <label for="colFormLabelSm" id="order_desc" name="order_desc">{{$request->vnp_BankCode}}</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="colFormLabelSm" class="col-sm-3 col-form-label col-form-label-sm">{{__('sentence.payment_time')}}:</label>
                    <div class="col-sm-3">
                        <label for="colFormLabelSm" id="order_desc" name="order_desc">{{$request->vnp_PayDate}}</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="colFormLabelSm" class="col-sm-3 col-form-label col-form-label-sm">{{__('sentence.result')}}:</label>
                    <div class="col-sm-3">
                        <label for="colFormLabelSm" id="order_desc" name="order_desc">
                            <?php
                            if ($secureHash == $vnp_SecureHash) {
                                if ($request->vnp_ResponseCode == '00') {
                                    $order_id =$request->vnp_TxnRef;
                                    $money =$request->vnp_Amount / 100;
                                    $note =$request->vnp_OrderInfo;
                                    $vnp_response_code =$request->vnp_ResponseCode;
                                    $code_vnpay =$request->vnp_TransactionNo;
                                    $code_bank =$request->vnp_BankCode;
                                    $time =$request->vnp_PayDate ;
                                    $date_time = substr($time, 0, 4) . '-' . substr($time, 4, 2) . '-' . substr($time, 6, 2) . ' ' . substr($time, 8, 2) . ' ' . substr($time, 10, 2) . ' ' . substr($time, 12, 2);
                                    $servername = 'localhost';
                                    $username = 'root';
                                    $password = '';
                                    $dbname = "sneaker";
                                    $conn = mysqli_connect($servername, $username, $password, "$dbname");
                                    $sql = "SELECT * FROM payments WHERE order_id = '$order_id'";
                                    $query = mysqli_query($conn, $sql);
                                    $row = mysqli_num_rows($query);
                                    $taikhoan = Auth::id();
                                    if ($row > 0) {
                                        $sql = "UPDATE payments SET order_id = '$order_id', money = '$money', note = '$note', vnp_response_code = '$vnp_response_code', code_vnpay = '$code_vnpay', code_bank = '$code_bank' WHERE order_id = '$order_id'";

                                        mysqli_query($conn, $sql);
                                    } else {
                                        $sql = "INSERT INTO payments(order_id, thanh_vien, money, note, vnp_response_code, code_vnpay, code_bank, time) VALUES ('$order_id', '$taikhoan', '$money', '$note', '$vnp_response_code', '$code_vnpay', '$code_bank','$date_time')";
                                        mysqli_query($conn, $sql);
                                        $sql = "UPDATE transaction SET status = '1'  WHERE id_transaction = '$order_id'";
                                        mysqli_query($conn, $sql);
                                    }

                                    echo "GD Thanh cong";
                                } else {
                                    echo "GD Khong thanh cong";
                                }
                            } else {
                                echo "Chu ky khong hop le";
                            }
                            ?>
                        </label>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @endsection
    @section('src-public')
    <script src="https://www.paypal.com/sdk/js?client-id=sb&currency=USD"></script>
    <script src="{{asset('shoes/js/ajax/gift.code.order.js')}}"></script>
    <script src="{{asset('shoes/js/ajax/order.pay.js')}}"></script>
    @endsection
    @endif