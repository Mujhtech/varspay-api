<?php

namespace App\Lib;
use App\Models\Settings;

class Rubies {
    
    private $api_key;
    
    private $secret_key;
    
    private $base_url = "https://openapi.rubiesbank.io/v1";
    
    
    public function __construct(){
        
        $set = Settings::find(1);
        
        $this->api_key = $set->rubies_api_key;
        
        $this->secret_key = $set->rubies_secret_key;
        
    }
    
    
    public function generateVirtualAccount($name){
        
        if (empty($this->api_key) || empty($this->secret_key)) {
            
			return "Error: API Key or API Secret Key are not set.";
			
		}
		
		$params["virtualaccountname"] = $name;
        $params["amount"] = 1;
        $params["amountcontrol"] = "VARIABLEAMOUNT";
        $params["daysactive"] = 10000;
        $params["minutesactive"] = 0;
        $params["callbackurl"] = "https://varspay.com";
        
        $data = json_encode($params, 320);
        
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->base_url."/createvirtualaccount",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS =>$data,
          CURLOPT_HTTPHEADER => array(
            "Authorization: ".$this->secret_key."",
            "Content-Type: application/json",
            "Accept: application/json"
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
    
    
    
    public function airtimePurchase($number,$amount,$net,$ref){
        
        if (empty($this->api_key) || empty($this->secret_key)) {
            
			return "Error: API Key or API Secret Key are not set.";
			
		}
		
		$params["mobilenumber"] = $number;
        $params["amount"] = $amount;
        $params["telco"] = $net;
        $params["reference"] = $ref;
        
        $data = json_encode($params, 320);
        
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->base_url."/ctairtimepurchase",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS =>$data,
          CURLOPT_HTTPHEADER => array(
            "Authorization: ".$this->secret_key."",
            "Content-Type: application/json",
            "Accept: application/json"
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
    
    
    public function airtimeQuery($ref){
        
        if (empty($this->api_key) || empty($this->secret_key)) {
            
			return "Error: API Key or API Secret Key are not set.";
			
		}
		
        $params["reference"] = $ref;
        
        $data = json_encode($params, 320);
        
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->base_url."/ctairtimequery",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS =>$data,
          CURLOPT_HTTPHEADER => array(
            "Authorization: ".$this->secret_key."",
            "Content-Type: application/json",
            "Accept: application/json"
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
    
    
    public function dataPurchase($number,$amount,$net,$code,$ref){
        
        if (empty($this->api_key) || empty($this->secret_key)) {
            
			return "Error: API Key or API Secret Key are not set.";
			
		}
		
		$params["mobilenumber"] = $number;
        $params["amount"] = $amount;
        $params["telco"] = $net;
        $params["reference"] = $ref;
        $params["productcode"] = $code;
        
        $data = json_encode($params, 320);
        
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->base_url."/ctmobiledatapurchase",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS =>$data,
          CURLOPT_HTTPHEADER => array(
            "Authorization: ".$this->secret_key."",
            "Content-Type: application/json",
            "Accept: application/json"
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
    
    
    public function listVirtualAccountTransaction($virtualno){
        
        if (empty($this->api_key) || empty($this->secret_key)) {
            
			return "Error: API Key or API Secret Key are not set.";
			
		}
		
		$params["virtualaccount"] = $virtualno;
        $params["page"] = 1;
        
        $data = json_encode($params, 320);
        
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->base_url."/listtransactions",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS =>$data,
          CURLOPT_HTTPHEADER => array(
            "Authorization: ".$this->secret_key."",
            "Content-Type: application/json",
            "Accept: application/json"
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
    
    public function verifyBVN($bvn,$ref){
        
        if (empty($this->api_key) || empty($this->secret_key)) {
            
			return "Error: API Key or API Secret Key are not set.";
			
		}
		
		$params["bvn"] = $bvn;
        $params["reference"] = $ref;
        
        $data = json_encode($params, 320);
        
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->base_url."/verifybvn",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS =>$data,
          CURLOPT_HTTPHEADER => array(
            "Authorization: ".$this->secret_key."",
            "Content-Type: application/json",
            "Accept: application/json"
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
    
    
    public function withdrawWithPayCode($channel,$ref,$amount,$pin){
        
        if (empty($this->api_key) || empty($this->secret_key)) {
            
			return "Error: API Key or API Secret Key are not set.";
			
		}
		
		$params["withdrawalchannel"] = $channel;
        $params["reference"] = $ref;
        $params["amount"] = $amount;
        $params["withdrawalpin"] = $pin;
        $params["tokenexpiryminutes"] = "180";
        
        $data = json_encode($params, 320);
        
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->base_url."/paycodegenerate",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS =>$data,
          CURLOPT_HTTPHEADER => array(
            "Authorization: ".$this->secret_key."",
            "Content-Type: application/json",
            "Accept: application/json"
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
    
    
    
    public function singleTransfer($amount,$nar,$cracctname,$bankname,$dracctname,$craccount,$bankcode,$ref){
        
        if (empty($this->api_key) || empty($this->secret_key)) {
            
			return "Error: API Key or API Secret Key are not set.";
			
		}
		
		$params["amount"] = $amount;
		$params["narration"] = $nar;
		$params["craccountname"] = $cracctname;
		$params["bankname"] = $bankname;
		$params["draccountname"] = $dracctname;
		$params["craccount"] = $craccount;
		$params["bankcode"] = $bankcode;
        $params["reference"] = $ref;
        
        
        $data = json_encode($params, 320);
        
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->base_url."/fundtransfer",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS =>$data,
          CURLOPT_HTTPHEADER => array(
            "Authorization: ".$this->secret_key."",
            "Content-Type: application/json",
            "Accept: application/json"
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
    
    
    public function bulkTransfer($ref, $transList){
        
        if (empty($this->api_key) || empty($this->secret_key)) {
            
			return "Error: API Key or API Secret Key are not set.";
			
		}
		
		$params["transactions"] = $transList;
        $params["batchref"] = $ref;
        
        
        $data = json_encode($params, 320);
        
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->base_url."/bulktransfer",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS =>$data,
          CURLOPT_HTTPHEADER => array(
            "Authorization: ".$this->secret_key."",
            "Content-Type: application/json",
            "Accept: application/json"
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
    
    
    public function bulkTransferValidation($ref, $transList){
        
        if (empty($this->api_key) || empty($this->secret_key)) {
            
			return "Error: API Key or API Secret Key are not set.";
			
		}
		
		$params["transactions"] = $transList;
        $params["batchref"] = $ref;
        
        
        $data = json_encode($params, 320);
        
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->base_url."/bulktransfervalidation",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS =>$data,
          CURLOPT_HTTPHEADER => array(
            "Authorization: ".$this->secret_key."",
            "Content-Type: application/json",
            "Accept: application/json"
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
        
        if (empty($this->api_key) || empty($this->secret_key)) {
            
			return "Error: API Key or API Secret Key are not set.";
			
		}
		
        $params["batchref"] = $ref;
        
        
        $data = json_encode($params, 320);
        
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->base_url."/bulktransferquery",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS =>$data,
          CURLOPT_HTTPHEADER => array(
            "Authorization: ".$this->secret_key."",
            "Content-Type: application/json",
            "Accept: application/json"
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
    
    
    public function transactionQuery($ref){
        
        if (empty($this->api_key) || empty($this->secret_key)) {
            
			return "Error: API Key or API Secret Key are not set.";
			
		}
		
        $params["reference"] = $ref;
        
        
        $data = json_encode($params, 320);
        
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->base_url."/transactionquery",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS =>$data,
          CURLOPT_HTTPHEADER => array(
            "Authorization: ".$this->secret_key."",
            "Content-Type: application/json",
            "Accept: application/json"
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
    
    
    
    public function balanceEnquries($acctno){
        
        if (empty($this->api_key) || empty($this->secret_key)) {
            
			return "Error: API Key or API Secret Key are not set.";
			
		}
		
        $params["accountnumber"] = $acctno;
        
        
        $data = json_encode($params, 320);
        
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->base_url."/balanceenquiry",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS =>$data,
          CURLOPT_HTTPHEADER => array(
            "Authorization: ".$this->secret_key."",
            "Content-Type: application/json",
            "Accept: application/json"
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
    
    public function nameEnquries($acctno, $code){
        
        if (empty($this->api_key) || empty($this->secret_key)) {
            
			return "Error: API Key or API Secret Key are not set.";
			
		}
		
        $params["accountnumber"] = $acctno;
        $params["bankcode"] = $code;
        
        
        $data = json_encode($params, 320);
        
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->base_url."/nameenquiry",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS =>$data,
          CURLOPT_HTTPHEADER => array(
            "Authorization: ".$this->secret_key."",
            "Content-Type: application/json",
            "Accept: application/json"
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
    
    public function bankName($code){
        
        $bankName = "";
        
        foreach($this->bankList()["banklist"] as $bank){
            
            if($bank["bankcode"] == $code){
                
                $bankName = $bank["bankname"];
                
            }
        }
        
        return $bankName;
    }
    
    public function acctName($acct_no,$bank){
        
        $acct_name = $this->nameEnquries($acct_no,$bank);
        
        if($acct_name["responsecode"] == 00 && $acct_name["responsemessage"] == "success"){
            
            return $acct_name["accountname"];
            
        } else {
            
            return "error";
            
        }
        
    }
    
    
    public function bankList(){
        
        if (empty($this->api_key) || empty($this->secret_key)) {
            
			return "Error: API Key or API Secret Key are not set.";
			
		}
		
        $params["request"] = "banklist";
        
        
        $data = json_encode($params, 320);
        
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->base_url."/banklist",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS =>$data,
          CURLOPT_HTTPHEADER => array(
            "Authorization: ".$this->secret_key."",
            "Content-Type: application/json",
            "Accept: application/json"
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
    
    
}