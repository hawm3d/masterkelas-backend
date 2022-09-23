<?php

namespace MasterKelas\Queue\Job;

use Carbon\Carbon;
use MasterKelas\Database\OTP_Query;

/**
 * Send OTP to recipient email address
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class OTP_Email {
  public static function process($otp_id) {
    $max_attempts = 3;

    $otp = (new OTP_Query())->get_item($otp_id);

    if (!$otp || empty($otp))
      throw new \Exception("otp.invalid");

    if (!$otp->expire_at || $otp->is_expired)
      throw new \Exception("otp.expired");

    if ($otp->is_success)
      throw new \Exception("otp.ended");

    if ($otp->data_prop("provider-attempts") >= $max_attempts)
      throw new \Exception("otp.reached.max");

    $new_otp = new OTP_Query([
      'number' => 1,
      'fields' => 'ids',
      "id__not_in" => [$otp->id],
      "type" => $otp->type,
      "recipient" => $otp->recipient,
      "created_at_query" => [
        "after" => $otp->created_at
      ],
    ]);

    if (!empty($new_otp->items))
      throw new \Exception("otp.replaced");

    $status = 0;
    $provider = 'test-provider';
    $data = array_merge($otp->data, [
      "provider" => $provider,
      "provider-attempts" => intval($otp->data_prop('provider-attempts')) + 1,
      "provider-status" => 0,
      "provider-response" => null,
    ]);

    try {
      $subject = "MasterKelas Email OTP Code";
      $body = "Code: {$otp->token}";
      $headers = [
        'Content-type: text/plain; charset=utf-8',
        'From:' . "testing@gmail.com"
      ];

      wp_mail($otp->recipient, $subject, $body, $headers);

      // Send request to provider
      // throw new \Exception("provider.fail.msg");

      $status = 1;
    } catch (\Throwable $th) {
      error_log($th);
      $status = 2;
      $data['provider-status'] = 2;
      $data['provider-response'] = $th->getMessage();
    }

    $data['time'] = time();

    $model = $otp->model();
    $model->update([
      "data" => $data,
      "status" => $status,
    ]);

    if ($data['provider-status'] === 2) {
      $model->dispatch(false, false, Carbon::now()->addSeconds(5)->timestamp);

      throw new \Exception($data['provider-response']);
    }

    return;
  }
}
