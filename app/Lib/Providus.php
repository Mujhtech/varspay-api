<?php

namespace App\Lib;
use App\Models\Settings;

class Providus {
    
    private $api_key;
    
    private $secret_key;
    
    private $contract_code;
    
    private $wallet_no;
    
    private $currencyCode = "NGN";
    
    private $base_url = "https://api.monnify.com";
    
    
    public function __construct(){
        
        $set = Settings::find(1);
        
        $this->api_key = $set->providus_api_key;
        
        $this->secret_key = $set->providus_secret_key;
        
        $this->wallet_no = $set->providus_source_acct_no;
        
        $this->contract_code = $set->providus_contract_code;
        
    }
    
    public function generateAccount($ref, $name, $email){
        
        
        //accountReference has to be unique
        $params["accountReference"] = $ref;
        $params["accountName"] = $name;
        $params["currencyCode"] = $this->currencyCode;
        $params["contractCode"] = $this->contract_code;
        $params["customerEmail"] = $email;
        $params["customerName"] = $name;
        
        $request = "POST";
        
        $method = "v1/bank-transfer/reserved-accounts";
        
        return $this->request($method, $request, $params);
        
    }
    
    public function generateSubAccount($subRef, $ref, $name, $email){
        
        
        //accountReference has to be unique
        $params["accountReference"] = $ref;
        $params["accountName"] = $name;
        $params["currencyCode"] = $this->currencyCode;
        $params["contractCode"] = $this->contract_code;
        $params["customerEmail"] = $email;
        $params["customerName"] = $name;
        $params["incomeSplitConfig"] = array( 
                                            array(
                                                "subAccountCode" => $subRef,
                                                "feePercentage" => 10.5,
                                                "splitPercentage" => 20,
                                                "feeBearer" => true
                                            )
                                        );

        $request = "POST";
        
        $method = "v1/bank-transfer/reserved-accounts";
        
        return $this->request($method, $request, $params);
        
    }
    
    public function getWalletBalance(){
        
        if (empty($this->api_key) || empty($this->secret_key)) {
            
			return "Error: API Key or API Secret Key are not set.";
			
		}
        
        $curl = curl_init();
  
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->base_url."/v2/disbursements/wallet-balance?accountNumber=".$this->wallet_no,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "UTF-8",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
              $this->encryptToken(),
              "Content-Type: application/json"
            ),
        ));
          
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
          
          
        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return json_decode($response, true);
        }
        
    }
    
    public function verifyAcctNo($acct_no, $bank){
        
        if (empty($this->api_key) || empty($this->secret_key)) {
            
			return "Error: API Key or API Secret Key are not set.";
			
		}
        
        $curl = curl_init();
  
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->base_url."/v1/disbursements/account/validate?accountNumber=".$acct_no."&bankCode=".$bank."",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "UTF-8",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
              $this->encryptToken(),
              "Content-Type: application/json"
            ),
        ));
          
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
          
          
        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return json_decode($response, true);
        }
        
    }
    
    public function getResAcctTransaction($acct_no){
        
        if (empty($this->api_key) || empty($this->secret_key)) {
            
			return "Error: API Key or API Secret Key are not set.";
			
		}
        
        $curl = curl_init();
  
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->base_url."/v1/bank-transfer/reserved-accounts/transactions?accountReference=".$acct_no."&page=0&size=10",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "UTF-8",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
              $this->authToken(),
              "Content-Type: application/json"
            ),
        ));
          
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
          
          
        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return json_decode($response, true);
        }
        
    }
    
    public function verifyBvnNo(){
        
        if (empty($this->api_key) || empty($this->secret_key)) {
            
			return "Error: API Key or API Secret Key are not set.";
			
		}
        
        $curl = curl_init();
  
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->base_url."/v1/verify-bvn-on-account?accountNumber=0242623583&bankCode=058&bvn=22437391124",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "UTF-8",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
              $this->authToken(),
              "Content-Type: application/json"
            ),
        ));
          
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
          
          
        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return json_decode($response, true);
        }
        
    }
    
    public function singleTransfer($amount, $destination, $token, $naration, $acct_no){
        
        //reference has to be unique
        $params["amount"] = $amount;
        $params["destinationBankCode"] = $destination;
        $params["destinationAccountNumber"] = $acct_no;
        $params["currency"] = $this->currencyCode;
        $params["sourceAccountNumber"] = $this->wallet_no;
        $params["reference"] = $token;
        $params["narration"] = $naration;
        
        $method = "v2/disbursements/single";
        
        if (empty($this->api_key) || empty($this->secret_key)) {
		    
			return "Error: API Key or API Secret Key are not set.";
			
		}

		$url = $this->base_url . '/' . $method;
		
		$data = json_encode($params, 320);


		$curl = curl_init();
		
		curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "UTF-8",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
              $this->encryptToken(),
              "Content-Type: application/json"
            ),
        ));
		$response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
          
          
        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return json_decode($response, true);
        }
        
    }
    
    public function bulkTransfer($bulkref, $bulknar, $bulkList){
        
        
        //batchReference has to be unique
        $params["title"] = "Bulk Transfer";
        $params["onValidationFailure"] = "CONTINUE";
        $params["notificationInterval"] = 100;
        $params["currency"] = $this->currencyCode;
        $params["sourceAccountNumber"] = $this->wallet_no;
        $params["batchReference"] = $bulkref;
        $params["narration"] = $bulknar;
        $params["transactionList"] = $bulkList;
        
        $method = "v2/disbursements/batch";
        
        if (empty($this->api_key) || empty($this->secret_key)) {
		    
			return "Error: API Key or API Secret Key are not set.";
			
		}

		$url = $this->base_url . '/' . $method;
		
		$data = json_encode($params, 320);


		$curl = curl_init();
		
		curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "UTF-8",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
              $this->encryptToken(),
              "Content-Type: application/json"
            ),
        ));
		$response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
          
          
        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return json_decode($response, true);
        }
        
    }
    
    public function singleTransferAuth(){
        
        //reference has to be unique
        $params["reference"] = "Seunmsdos33@gmail.com";
        $params["authorizationCode"] = "John Doe";
        
        $request = "POST";
        
        $method = "v2/disbursements/single/validate-otp";
        
        return $this->request($method, $request, $params);
        
    }
    
    public function singleBulkAuth(){
        
        //reference has to be unique
        $params["reference"] = "Seunmsdos33@gmail.com";
        $params["authorizationCode"] = "John Doe";
        
        $request = "POST";
        
        $method = "v2/disbursements/single/validate-otp";
        
        return $this->request($method, $request, $params);
        
    }
    
    public function singleTransferDetails($ref){
        
        
        $reference = $ref;
        
        $request = "GET";
        
        $method = "v2/disbursements/single/summary?reference=".$reference."";
        
        if (empty($this->api_key) || empty($this->secret_key)) {
		    
			return "Error: API Key or API Secret Key are not set.";
			
		}

		$url = $this->base_url . '/' . $method;

		$curl = curl_init();
		
		curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "UTF-8",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
              $this->encryptToken(),
              "Content-Type: application/json"
            ),
        ));
		
		$response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
          
          
        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return json_decode($response, true);
        }
        
    }
    
    public function bulkTransferDetails($ref){
        
        
        $reference = $ref;
        
        $request = "GET";
        
        $method = "v2/disbursements/batch/summary?reference=".$reference."";
        
        if (empty($this->api_key) || empty($this->secret_key)) {
		    
			return "Error: API Key or API Secret Key are not set.";
			
		}

		$url = $this->base_url . '/' . $method;

		$curl = curl_init();
		
		curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "UTF-8",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
              $this->encryptToken(),
              "Content-Type: application/json"
            ),
        ));
		
		$response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
          
          
        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return json_decode($response, true);
        }
        
    }
    
    public function checkAccountDetails($ref){
        
        $request = "GET";
        
        $method = "v1/bank-transfer/reserved-accounts/".$ref;
        
        return $this->request($method, $request);
        
    }
    
    public function updateAccountDetails(){
        
        $currentCustomerEmail = "Seunmsdos33@gmail.com";
        
        $params["customerEmail"] = "mujhtech@gmail.com";
        $params["customerName"] = "John Doe";
        
        $request = "GET";
        
        $method = "v1/customer/update/".$currentCustomerEmail;
        
        return $this->request($method, $request, $params);
        
    }
    
    public function getBankUssd(){

        
        $request = "GET";
        
        $method = "v1/banks";
        
        return $this->request($method, $request);
        
    }
    
    public function bankName($code){
        
        $bankName = "";
        
        foreach($this->getBankUssd()["responseBody"] as $bank){
            
            if($bank["code"] == $code){
                
                $bankName = $bank["name"];
                
            }
        }
        
        return $bankName;
    }
    
    public function bankCode($code){
        
        $bankCode = "";
        
        $pattern = "/".$code."/i";
        
        foreach($this->getBankUssd()["responseBody"] as $bank){
            
            if(preg_match($pattern, $bank["name"])){
                
                $bankCode = $bank["code"];
                
            }
        }
        
        return $bankCode;
    }
    
    public function queryTransaction(){
        
        $transactionReference = "MNFY|20190809123429|000000";
        
        $request = "GET";
        
        $method = "v1/merchant/transactions/query?transactionReference=".$transactionReference;
        
        return $this->request($method, $request, $params);
        
    }
    
    private function request($method, $request, $params = []) {
        
		if (empty($this->api_key) || empty($this->secret_key)) {
		    
			return "Error: API Key or API Secret Key are not set.";
			
		}

		$url = $this->base_url . '/' . $method;
		
		$data = json_encode($params, 320);


		$curl = curl_init();
		
		if($request == "POST"){
		    
		    curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "UTF-8",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => array(
                  $this->authToken(),
                  "Content-Type: application/json"
                ),
            ));
          
		} else {
		    
		    curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "UTF-8",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => array(
                  $this->authToken(),
                  "Content-Type: application/json"
                ),
            ));
            
		}
		
		$response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
          
          
        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return json_decode($response, true);
        }
        
	}
	
	public function auth(){
	   
	    if (empty($this->api_key) || empty($this->secret_key)) {
		    
			return "Error: API Key or API Secret Key are not set.";
			
		}
	    
	    $curl = curl_init();
  
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->base_url."/v1/auth/login",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "UTF-8",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => array(
              $this->encryptToken(),
              "Content-Type: application/json"
            ),
          ));
          
          $response = curl_exec($curl);
          $err = curl_error($curl);
          curl_close($curl);
          
          
          if ($err) {
            return "cURL Error #:" . $err;
          } else {
            return json_decode($response, true);
          }
  
  
	}
	
	private function authToken(){
	    
	    return "Authorization: Bearer ".$this->auth()["responseBody"]["accessToken"];
	    
	}
	
	private function encryptToken() {
	    //apiKey:clientSecret
	    $str = $this->api_key.":".$this->secret_key;
	    
		return "Authorization: Basic ".base64_encode($str);
	}
	
	private function encryptTrasactionHash($clientSecret, $paymentReference, $amountPaid, $paidOn, $transactionReference) {
	    
	    $str = "{$clientSecret} | {$paymentReference} | {$amountPaid} | {$paidOn} | {$transactionReference}";
	    
		return hash('sha512', $str);
	}
}