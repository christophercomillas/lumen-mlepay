<?php

namespace App\Http\Controllers;

class MLEpayController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public $secure_key = "ODJkMzA5MGYtZTUzYS00NjNjLWJjNzItMzE2MzA0YjA3ZWFiYzc4YThjYzEtYTE1Yi00ZDAxLWE4NGUtYjcyNTVmZDg4MGMw";

    public function __construct()
    {
        //
        
    }

    //

    public function index(){

        try {

            $timestamp = time(); 
            $expiry = time() + 60*60; 
    
            $request_body = array(
                "receiver_email"=> "comillaschristopher@gmail.com",
                "sender_email"=> "itest@example.com",
                "sender_name"=> "Juan dela Cruz",
                "sender_phone"=> "+6390000000",
                "sender_address"=> "Blk 1 Lot 2, Sitio Doon, Katabi ng Daan, Pilipinas",
                "amount"=> 100000,
                "currency"=> "PHP",
                "nonce"=> "a1s2d3f4g5h6j7k8y",
                "timestamp"=> $timestamp,
                "expiry"=> $expiry,
                "payload" => "id-91288481772412",
                "description" => "White Denim Tee Shirt - Female - Small"
            );
    
            $data_string = json_encode($request_body);
            $base_string = "POST";
            $base_string .= "&" . 'https%3A//www.mlepay.com/api/v2/transaction/create';
            $base_string .= "&" . rawurlencode($data_string);
            $secret_key = $this->secure_key;
            $signature = base64_encode(hash_hmac("sha256", $base_string, $secret_key, true));
            $ch = curl_init('https://www.mlepay.com/api/v2/transaction/create');  
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string); 
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);                                                                 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
                'Content-Type: application/json',                                                                                
                'X-Signature: ' . $signature)                                                                     
            );                                                                                                                   
             
            $result = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $result = json_decode($result, true);
            echo '<pre>'; print_r( $result);
            //echo '<div id="mlepay_transaction_code_wrapper"><span id="mlepay_transaction_code_label">ML ePay Transaction Code:</span> <div id="mlepay_transaction_code">'. $result['transaction']['code'] . '</div><div id="mlepay_transaction_code_instructions">' . $this->instructions . '</div></div>' ;

        }
        catch(Exception $e) {
            echo $e;
        
        }
    }

    public function sample(){

        global $woocommerce;
        
        try{
          $order = new WC_Order( $order_id );
          $randStrLen = 16;
          $nonce = $this->randString($randStrLen);
          $timestamp = time();
          $expiry = $this->due_time($this->expiration_hour);
          $payload_id = $order->id;
          $product = $order->get_items();
          $product_name = array();
          foreach ( $order->get_items() as $item ) {
           
            if ( $item['qty'] ) {
                $item_loop++;
                $product = $order->get_product_from_item( $item );
                $item_name  = $item['name'];
                $item_name = $item_loop . ". " . $item_name . " ";
            }
            array_push($product_name, $item_name);
          }
          $request_body = array(
                  "receiver_email"=> $this->merchant_email, 
                  "sender_email"=> $order->billing_email,
                  "sender_name"=> $order->billing_first_name.' '.$order->billing_last_name,
                  "sender_phone"=> $order->billing_phone,
                  "sender_address"=> $order->billing_address_1.' '.$order->billing_address_2,
                  "amount"=> (int)($order->get_total() * 100),
                  "currency"=> "PHP",
                  "nonce"=> $nonce,
                  "timestamp"=> $timestamp,
                  "expiry"=> $expiry,
                  "payload"=> $payload_id,
                  "description"=> join(" ",$product_name)
              );
          $data_string = json_encode($request_body);
          $base_string = "POST";
          $base_string .= "&" . 'https%3A//www.mlepay.com/api/v2/transaction/create';
          $base_string .= "&" . rawurlencode($data_string);
          $secret_key = $this->secure_key;
          $signature = base64_encode(hash_hmac("sha256", $base_string, $secret_key, true));
          $ch = curl_init('https://www.mlepay.com/api/v2/transaction/create');  
          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
          curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string); 
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);                                                                 
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
              'Content-Type: application/json',                                                                                
              'X-Signature: ' . $signature)                                                                     
          );                                                                                                                   
           
          $result = curl_exec($ch);
          $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
          $result = json_decode($result, true);
          echo '<div id="mlepay_transaction_code_wrapper"><span id="mlepay_transaction_code_label">ML ePay Transaction Code:</span> <div id="mlepay_transaction_code">'. $result['transaction']['code'] . '</div><div id="mlepay_transaction_code_instructions">' . $this->instructions . '</div></div>' ;
      
        }
        catch(Exception $e) {
          echo '<div id="mlepay_transaction_code_wrapper"><span id="mlepay_transaction_code_error">An Error occurred. Please try again.</span></div>';
        
        }


    }
}
