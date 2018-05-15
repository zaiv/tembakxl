<?php
/**
 * Name:    XlRequest
 * Author:  Adipati arya
 *           aryaadipati2@gmail.com
 * @adipati
 *
 * Added Awesomeness: Adipati arya
 *
 * Created:  11.10.2017
 *
 * Description:  Modified auth system based on Guzzle with extensive customization. This is basically what guzzle should be.
 * Original Author name has been kept but that does not mean that the method has not been modified.
 *
 * Requirements: PHP5 or above
 *
 * @package		Xlrequest
 * @author		aryaadipati2@gmail.com
 * @link		http://sshcepat.com/xl
 * @filesource	https://github.com/adipatiarya/XLRequest
 */
require 'vendor/autoload.php';
use GuzzleHttp\Client;

class XlRequest {
	
	private $imei; 
	
	private $msisdn;
	
	private $client;
	
	private $header;
	
	private $session;
	
	private $date;
	
	public function __construct() {
		
		$this->client =new Client(['base_uri' => 'https://my.xl.co.id']); 
		
		$this->imei = '303975796'; 
		
		$this->date = date('Ymdhis');
		
		$this->header = [
			'Host' => 'my.xl.co.id',
			'User-Agent'=>'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:56.0) Gecko/20100101 Firefox/56.0',
			'Accept'=> 'application/json, text/plain, */*',
			'Accept-Language'=> 'en-US,en;q=0.5',
			'Accept-Encoding'=> 'gzip, deflate, br',
			'Content-Type'=> 'application/json',
		];
	}
	public function login($msisdn, $passwd) {
		$this->msisdn = $msisdn;
		
		$payload = [
			'Header'=>null,
			'Body'=> [
				'Header'=>[
					'IMEI'=>$this->imei,
					'ReqID'=>$this->date,
				],
				'LoginV2Rq'=>[
					'msisdn'=>$msisdn,
					'pass'=>$passwd
				]
			]
		];
		try {
			$response = $this->client->post('/pre/LoginV2Rq',
				[
					'debug' => FALSE,
					'json' => $payload,
					'headers' => $this->header
				]
			);
			$body= $response->getBody();
			
			if (json_decode((string) $body)->responseCode !== '01') {
				$this->session = json_decode((string) $body)->sessionId; //dapatkan session id
			}
			
			else {
				return false; //jika login gagal 
			}
			
		}
		catch(Exception $e) {}
		
	}
	public function register($idService) {
		$payload = [
			'Header'=>null,
			'Body'=> [
				'HeaderRequest'=>[
					'applicationID'=>'3',
					'applicationSubID'=>'1',
					'touchpoint'=>'MYXL',
					'requestID'=>$this->date,
					'msisdn'=>$this->msisdn,
					'serviceID'=>$idService	
				],
				'opPurchase'=>[
					'msisdn'=>$this->msisdn,
					'serviceid'=>$idService,
				],
				'Header' => [
					'IMEI'=>$this->imei,
					'ReqID'=>$this->date
				]
			],
			'sessionId' => $this->session
		];
		try {
			$response = $this->client->post('/pre/opPurchase',[
					'debug' => FALSE,
					'json' => $payload,
					'headers' => $this->header
			]);
			$status = json_decode((string) $response->getBody());
			if (isset($status->responseCode))
				return $status;
			
			return $this->cek($idService);	
		}
		catch(Exception $e) {}
	}
	private function cek($idService) {
		$payload = [
            'type'=>'thankyou',
            'param'=>'service_id=&package_service_id='.$idService,
            'lang'=>'bahasa',
            'msisdn'=>$this->msisdn,
            'IMEI'=>$this->imei,
            'sessionId'=>$this->session,
            'staySigned'=>'False',
            'platform'=>'04',
            'ReqID'=>$this->date,
            "serviceId"=>'',
            'packageAmt'=>'',
            "reloadType"=>'',
            "reloadAmt"=>'',
            'packageRegUnreg'=>'',
            'onNetLogin'=>'NO',
            'appVersion'=>'3.5.2',
            'sourceName'=>'Firefox',
            'sourceVersion'=>''
        ];
		try {
			$response = $this->client->post('/pre/CMS',[
				'debug' => FALSE,
				'json' => $payload,
				'headers' => $this->header
			]);
			
			return json_decode((string) $response->getBody());
		} 
		catch(Exception $e) {}
	}
}
?>