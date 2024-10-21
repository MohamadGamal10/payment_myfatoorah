<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use App\Services\MyFathoorahService;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;

class FatoorahController extends Controller
{
    use GeneralTrait;
    private $fathoorahService;
    public function __construct(MyFathoorahService $myFathoorahService)
    {
        $this->fathoorahService = $myFathoorahService;
    }
    public function payOrder()
    {

        // $actual_link = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
        $paymentData = [
            'NotificationOption' => 'ALL', //'SMS', 'EML', or 'ALL'
            'InvoiceValue'       => '50',
            'CustomerName'       => 'gemy',
            'DisplayCurrencyIso' => 'KWD',
            // 'MobileCountryCode'  => '+20',
            'CustomerMobile'     => '0123456789',
            'CustomerEmail'      => 'gemy@gmail.com',
            'CallBackUrl'        => env('success_url'),
            'ErrorUrl'           => env('error_url'),
            //'Language'           => 'en', //or 'ar'
            //'CustomerReference'  => 'orderId',
            //'CustomerCivilId'    => 'CivilId',
            //'UserDefinedField'   => 'This could be string, number, or array',
            //'ExpiryDate'         => '', //The Invoice expires after 3 days by default. Use 'Y-m-d\TH:i:s' format in the 'Asia/Kuwait' time zone.
            //'SourceInfo'         => 'Pure PHP', //For example: (Laravel/Yii API Ver2.0 integration)
            //'CustomerAddress'    => $customerAddress,
            //'InvoiceItems'       => $invoiceItems,
        ];
        $pay_data =  $this->fathoorahService->sendPayment($paymentData);

        PaymentTransaction::create([
            'invoice_id' => $pay_data['Data']['InvoiceId'],
            'user_id' => 25
        ]);
        return $pay_data;
    }


    public function successPay(Request $request)
    {
        $data = [];
        $data['key'] = $request->paymentId;
        $data['keyType'] = 'paymentId';
        $check_status =  $this->fathoorahService->getPaymentStatus($data);

        return $check_status;
        // if (!$check_status) {
        //     return $this->returnError('206', 'fail');
        // }
        // return $this->returnSuccessMessage('success');
    }

    public function errorPay()
    {
        return $this->returnError('205', 'fail');
    }
}
