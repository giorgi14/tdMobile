<?php

class invoice {
	
	private $su; 
	private $sp;	
	private $user_id;
	private $un_id;
	private $invoice_id;
	private $client;
	
	/**
	 * CONSTRUCTOR
	 * 
	 * @param string $url : სერვისის მისამართი
	 * @param string $su  : სისტემის მომხმარებელი
	 * @param srting $sp  : მომხმარებლის პაროლი
	 */ 
	public function invoice($url, $su, $sp, $user_id, $un_id){
		
		$this->su       = $su;                         
		$this->sp       = $sp;
		$this->user_id	= $user_id;
		$this->un_id	= $un_id;
		$this->client	= new  SoapClient($url);
		
	}
	
	/**
	 * @param int $waybill_id : ზედნადების ID
	 */
	public function set_invoice_id($invoice_id){
		
		$this->invoice_id = $invoice_id;
		
	}
	
	/**
	 * შემოწმება დღგ-ზე
	 * 
	 * @param string $un_id : პარტნიორის უნიკალური კოდი;
	 */
	public function is_vat_payer($un_id){
	
		$params 				= new stdClass();
		$params->su 			= $this->su;
		$params->sp 			= $this->sp;
		$params->un_id			= $un_id;
	
		$results       			= $this->client->is_vat_payer($params);
		return 		$results->is_vat_payerResult;
		
	}	
	
	/**
	 * ახალი ინვოისის შექმნა
	 * 
	 * @param 	string 	$operation_date : ოპერაციის თარიღი
	 * @param	int 	$buyer_un_id    : მყიდველის უნიკალური ID
	 * @return 	array  	$resultsArray
	 */
	public function create_invoice( $operation_date, $buyer_un_id){

		$params 				= new stdClass();

		$params->user_id		= $this->user_id;
		$params->invois_id		= $this->invoice_id;
		$params->operation_date = $operation_date;
		$params->seller_un_id 	= $this->un_id;
		$params->buyer_un_id	= $buyer_un_id;
		$params->overhead_no	= '';
		$params->overhead_dt	= $operation_date;
		$params->b_s_user_id	= 0;
		
		$params->su 			= $this->su;
		$params->sp 			= $this->sp;
		
		$results    = $this->client->save_invoice($params);
		
		$resultsArray = array(
			'results' => $results->save_invoiceResult ,
			'invoice_id' => $results->invois_id
		);
		
		return $resultsArray;
	
	}
	
	/**
	 * ინვოისში სერვისების დამატება
	 * 
	 * @param string 	$service		: სერვისის დასახელება
	 * @param int		$quantity		: რაოდენობა
	 * @param int		$full_amount	: თანხა სულ
	 */
	public function add_services_in_invoice($service, $quantity, $full_amount){
		
		$params 				= new stdClass();
	
		$params->user_id		= $this->user_id;
		$params->id				= 0;
		$params->su 			= $this->su;
		$params->sp 			= $this->sp;
		
		$params->invois_id		= $this->invoice_id;
		
		$params->goods 			= $service;
		$params->g_unit 		= 'ცალი';
		$params->g_number		= $quantity;
		$params->full_amount	= $full_amount;
		$params->drg_amount		= 1;
		$params->aqcizi_amount	= 0;
		$params->akciz_id		= 0;
	
		$results    = $this->client->save_invoice_desc($params);

		return $results->save_invoice_descResult;
		
	}
	
	/**
	 * ინვოისში სერვისების დამატება
	 *
	 * @param string 	$service		: სერვისის დასახელება
	 * @param int		$quantity		: რაოდენობა
	 * @param int		$full_amount	: თანხა სულ
	 */
	public function send_invoice(){
	
		$params 				= new stdClass();
	
		$params->user_id		= $this->user_id;
		$params->inv_id			= $this->invoice_id;
		$params->status			= 1;		
		$params->su 			= $this->su;
		$params->sp 			= $this->sp;
	
		$results    = $this->client->change_invoice_status($params);
	
		return $results->change_invoice_statusResult;
	
	}
	
	public function get_invoice(){
		
		$params 				= new stdClass();
		
		$params->user_id		= $this->user_id;
		$params->invois_id		= $this->invoice_id;
		$params->su 			= $this->su;
		$params->sp 			= $this->sp;
		
		$results    	= $this->client->get_invoice($params);	

		mysql_query("INSERT INTO invoice (id, user_id, status, serial_number, buyer_un_id, start_date, end_date)
								 VALUES  ($params->invois_id, 2, '$results->status', '$results->f_series', '$results->buyer_un_id','$results->operation_dt', '$results->reg_dt')");
		
		$data = $this->client->get_invoice_desc($params);
		 
		$data = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
				   <soap:Body>
				      <get_invoice_descResponse xmlns="http://tempuri.org/">
				         <get_invoice_descResult>
												'.substr(serialize($data), 84, -4).'
						 </get_invoice_descResult>
				      </get_invoice_descResponse>
				   </soap:Body>
		  		</soap:Envelope>';
		
		$xml = new SimpleXMLElement($data);
		$xml->registerXPathNamespace("diffgr", "urn:schemas-microsoft-com:xml-diffgram-v1");
		$data = $xml->xpath("//diffgr:diffgram") ;
		$data = $data[0];
		
		foreach($data->DocumentElement->invoices_descs as $result)
		{
			mysql_query("INSERT INTO invoice_detail (invoice_id, service, unit, amount, tax, vat)
											 VALUES ($result->INV_ID, '$result->GOODS', '$result->G_UNIT', $result->G_NUMBER, $result->FULL_AMOUNT, $result->DRG_AMOUNT)");
		}	

	}
		
}
?>