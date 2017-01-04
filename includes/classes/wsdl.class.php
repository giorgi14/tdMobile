<?php

class wsdl {
	
	private $client;		
	private $su ; 
	private $sp ;	
	private $waybill_id;
	
	/**
	 * @param string $url : სერვისის მისამართი
	 * @param string $su  : სისტემის მომხმარებელი
	 * @param srting $sp  : მომხმარებლის პაროლი
	 */ 
	public function wsdl($url, $su, $sp){
		
		$this->su       = $su;                         
		$this->sp       = $sp;
		$this->client	= new  SoapClient($url);
		
	}
	
	/**
	 * @param int $waybill_id : ზედნადების ID
	 */
	public function set_waybill_id($waybill_id){
		
		$this->waybill_id = $waybill_id;
		
	}
	
	/**
	 * კლიენტის IP 
	 */
	public function what_is_my_ip(){

		$results  = $this->client->what_is_my_ip();
		return 		$results->what_is_my_ipResult;
		
	}
	
	/**
	 * სისტემის მომხმარებლის შექმნა
	 *  
	 * @param string $user_name      	ელექტრონული დეკლარირების მომხმარებლის  სახელი;
	 * @param string $user_password  	ელ. დეკლარირების მომხმარებლის პაროლი;
	 * @param string $ip			 	საიდანც მოხდება სერვისების გამოყენება;
	 * @param string $name           	ობიექტის სახელი;
	 * @param string $su             	სერვისის მომხმარებელი;
	 * @param string $sp             	სერვისის მომხმარებლის პაროლი;
	 */
	public function create_service_user($user_name, $user_password, $ip, $su_name, $su, $sp){
	
		$params 				= new stdClass();
		$params->user_name 		= $user_name;
		$params->user_password 	= $user_password;
		$params->ip 			= $ip;
		$params->name 			= $su_name;
		$params->su 			= $su;
		$params->sp 			= $sp;
		
		$results    = $this->client->create_service_user($params);		
		return $results->create_service_userResult;
		
	}
	
	/**
	 * სისტემის მომხმარებლის შემოწმება
	 */
	public function chek_service_user(){
		
		$params 				= new stdClass();
		$params->su 			= $this->su;
		$params->sp 			= $this->sp;
	
		$results       = $this->client->chek_service_user($params);
		
		$arr = array('un_id' => $results->un_id, 's_user_id' => $results->s_user_id);

		return $arr;
	
	}
	
	/**
	 * სისტემის მომხმარებლის განახლება
	 *  
	 * @param string $user_name      	ელექტრონული დეკლარირების მომხმარებლის  სახელი;
	 * @param string $user_password  	ელ. დეკლარირების მომხმარებლის პაროლი;
	 * @param string $ip			 	საიდანც მოხდება სერვისების გამოყენება;
	 * @param string $name           	ობიექტის სახელი;
	 * @param string $su             	სერვისის მომხმარებელი;
	 * @param string $sp             	სერვისის მომხმარებლის პაროლი;
	 */
	public function update_service_user($user_name, $user_password, $ip, $su_name, $su, $sp){
	
		$params 				= new stdClass();
		$params->user_name 		= $user_name;
		$params->user_password 	= $user_password;
		$params->ip 			= $ip;
		$params->su_name 		= $su_name;
		$params->su 			= $su;
		$params->sp 			= $sp;
		
		$results    = $this->client->update_service_user($params);		
		return $results->update_service_userResult;
		
	}
	
	/**
	 * შემოწმება დღგ-ზე
	 * 
	 * @param string $un_id       პარტნიორის უნიკალური კოდი;
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
	 * ქვე-ზედნადებების სია
	 */
  	public function get_sub_waybills(){
		
		$results       = $this->client->get_waybill(array('su'=> $this->su, 'sp' => $this->sp, 'waybill_id' => $this->waybill_id));
		 
		$xmlString     = $results->get_waybillResult->any;
		$obj           = new  SimpleXMLElement($xmlString);
		
		$count         = count($obj -> SUB_WAYBILLS-> SUB_WAYBILL);
		
		for($i = 0; $i < $count; $i++){
		   
			echo  $obj -> SUB_WAYBILLS -> SUB_WAYBILL[$i] -> ID . '</br>';
			echo  $obj -> SUB_WAYBILLS -> SUB_WAYBILL[$i] -> WAYBILL_NUMBER . '</br>';
		
		}
	}
	
	/**
	 * ზედნადებში შემავალი პროდუქტების სია
	 */
	public function get_waybill($stream){
		
		$results       = $this->client->get_waybill(array('su'=> $this->su, 'sp' => $this->sp, 'waybill_id' => $this->waybill_id));
			
		$xmlString     = $results->get_waybillResult->any;
		$obj           = new  SimpleXMLElement($xmlString);
		
		$overhead_id 		= $obj -> ID;
		$type 				= $obj -> TYPE;
		$create_date 		= $obj -> CREATE_DATE;
		$buyer_tin 			= $obj -> BUYER_TIN;
	    $check_buyer_tin 	= $obj -> CHEK_BUYER_TIN;
		$buyer_name 		= $obj -> BUYER_NAME;
		$start_address 		= $obj -> START_ADDRESS;
		$end_address 		= $obj -> END_ADDRESS;
		$driver_tin 		= $obj -> DRIVER_TIN;
		$check_driver_tin 	= $obj -> CHEK_DRIVER_TIN;
		$driver_name 		= $obj -> DRIVER_NAME;
		$transport_cost 	= $obj -> TRANSPORT_COAST;
		$reception_info 	= $obj -> RECEPTION_INFO;
		$receiver_info 		= $obj -> RECEIVER_INFO;
		$delivery_date 		= $obj -> DELIVERY_DATE;
		$status 			= $obj -> STATUS;
		$seler_un_id 		= $obj -> SELER_UN_ID;
		$activate_date 		= $obj -> ACTIVATE_DATE;
		$par_id 			= $obj -> PAR_ID;
		$full_amount 		= $obj -> FULL_AMOUNT;
		$full_amount_txt 	= $obj -> FULL_AMOUNT_TXT;
		$car_number 		= $obj -> CAR_NUMBER;
		$waybill_number 	= $obj -> WAYBILL_NUMBER;
		$close_date 		= $obj -> CLOSE_DATE;
		$s_user_id 			= $obj -> S_USER_ID;
		$begin_date			= $obj -> BEGIN_DATE;
		$tran_cost_payer 	= $obj -> TRAN_COST_PAYER;
		$trans_id 			= $obj -> TRANS_ID;
		$trans_txt 			= $obj -> TRANS_TXT;
		$comment 			= $obj -> COMMENT;
		$is_confirmed 		= $obj -> IS_CONFIRMED;
		$confirmation_date 	= $obj -> CONFIRMATION_DATE;
		$seller_tin 		= $obj -> SELLER_TIN;
		$seller_name 		= $obj -> SELLER_NAME;
		$category 			= $obj -> CATEGORY;
		$origin_type 		= $obj -> ORIGIN_TYPE;
		$origin_text 		= $obj -> ORIGIN_TEXT;
		
		$row = mysql_query("SELECT `rs_id` FROM `overhead` WHERE `rs_id` = '$overhead_id'");
		if(mysql_num_rows($row) == 0){
			
			mysql_query("INSERT INTO overhead	( 	
												stream, rs_id, type, create_date, buyer_tin,  check_buyer_tin,
												buyer_name, start_address, end_address, driver_tin, 
												check_driver_tin, driver_name, transport_coast, reception_info,
												receiver_info, delivery_date, status, seller_un_id,
												activate_date, par_id, full_amount, full_amount_txt, 
												car_number, waybill_number, close_date, s_user_id,
												begin_date,tran_cost_payer, trans_id, trans_txt, 
												comment, is_confirmed, confirmation_date, seller_tin, 
												seller_name, category, origin_type, origin_text											
											 	)
										VALUES	( 	
												$stream, '$overhead_id', '$type', '$create_date', '$buyer_tin', 
												'$check_buyer_tin', '$buyer_name', '$start_address', '$end_address', 
												'$driver_tin', '$check_driver_tin', '$driver_name', '$transport_cost', 
												'$reception_info', '$receiver_info', '$delivery_date', '$status', 
												'$seler_un_id', '$activate_date', '$par_id', '$full_amount', 
												'$full_amount_txt', '$car_number', '$waybill_number', '$close_date', 
												'$s_user_id', '$begin_date', '$tran_cost_payer', '$trans_id',
												'$trans_txt', '$comment', '$is_confirmed', '$confirmation_date', 
												'$seller_tin', '$seller_name', '$category', '$origin_type',
												'$origin_text'
												)
						
						");
		
		
			$last_id = mysql_insert_id();						
			
			$count         = count($obj -> GOODS_LIST-> GOODS);
	
			for($i = 0; $i < $count; $i++){
				 
				$goods_id 	= $obj -> GOODS_LIST -> GOODS[$i] -> ID ;
				$w_name 	= $obj -> GOODS_LIST -> GOODS[$i] -> W_NAME;
				$unit_id 	= $obj -> GOODS_LIST -> GOODS[$i] -> UNIT_ID;
				$unit_txt 	= $obj -> GOODS_LIST -> GOODS[$i] -> UNIT_TXT;
				$quantity 	= $obj -> GOODS_LIST -> GOODS[$i] -> QUANTITY;
				$price 		= $obj -> GOODS_LIST -> GOODS[$i] -> PRICE;
				$amount 	= $obj -> GOODS_LIST -> GOODS[$i] -> AMOUNT;
				$bar_code 	= $obj -> GOODS_LIST -> GOODS[$i] -> BAR_CODE;
				$a_id 		= $obj -> GOODS_LIST -> GOODS[$i] -> A_ID;
				$vat_type 	= $obj -> GOODS_LIST -> GOODS[$i] -> VAT_TYPE;
				$status 	= $obj -> GOODS_LIST -> GOODS[$i] -> STATUS;
				$quantity_f = $obj -> GOODS_LIST -> GOODS[$i] -> QUANTITY_F;
				
				$w_name		= htmlspecialchars($w_name, ENT_QUOTES);
				$unit_txt	= htmlspecialchars($unit_txt, ENT_QUOTES);
				
				/**
				 * INSERT IN overhead details
				 */
				mysql_query("INSERT INTO overhead_detail ( overhead_id, goods_id, name, unit_id, unit_txt, quantity, 
															price, amount, bar_code, a_id, vat_type, status, quantity_f
														 )
											VALUES		 ( '$last_id', '$goods_id', '$w_name', '$unit_id', '$unit_txt', '$quantity', '$price',
														    '$amount', '$bar_code', '$a_id', '$vat_type', '$status', '$quantity_f' 
														 )
							");
				
				/**
				 * INSERT IN  PRODUCTION IDENTITY
				 */
				$row = mysql_query("SELECT `rs_name` FROM `production_identity` WHERE `rs_name` = '$w_name'");
				if(mysql_num_rows($row) == 0){
					
					mysql_query(" INSERT 	INTO 	production_identity 	(rs_name)
											VALUES							('$w_name')
								");	
				}
				
				/**
				 *  INSERT IN PARTNERS
				 */
				$row = mysql_query("SELECT 	`partners`.`rs_id`
									FROM    `partners` 
									WHERE 	`partners`.`rs_id` = '$seller_tin'");
				if(mysql_num_rows($row) == 0){
					
					mysql_query(" INSERT 	INTO partners 	
														(`name`, `rs_id`, `un_id`)
											VALUES		
														('$seller_name', '$seller_tin', $seler_un_id)
					");
				}
			}
		}
	}
	
	/**
	 * გამავალი ზედნადებები
	 */
	public function get_waybills($params){
		$params->su = $this->su;
		$params->sp = $this->sp;
		
		$results       = $this->client->get_waybills($params);
			
		$xmlString     = $results->get_waybillsResult->any;

		$obj           = new  SimpleXMLElement($xmlString);
	
		$count         = count($obj -> WAYBILL);
		$arr = array();
		for($i = 0; $i < $count; $i++){				
			if( $obj ->WAYBILL[$i] ->  TYPE != '1' ){
		    	array_push($arr, $obj ->WAYBILL[$i] -> ID );
			}
		}
		
		return $arr;
	}
	
	/**
	 * შემომავალი ზედნადებები
	 */
	public function get_buyer_waybills($params){		
		$params->su = $this->su;
		$params->sp = $this->sp;
			
		$results       = $this->client->get_buyer_waybills($params);
			
		$xmlString     = $results->get_buyer_waybillsResult->any;

		$obj           = new  SimpleXMLElement($xmlString);
	
		$count         = count($obj -> WAYBILL);
		$arr = array();
		for($i = 0; $i < $count; $i++){
			if( $obj ->WAYBILL[$i] ->  TYPE != '1' ){
				    array_push($arr, $obj ->WAYBILL[$i] -> ID );
			}
		}
		
		return $arr;
	}
	
	/**
	 * ზედნადების დადასტურება
	 */
	public function confirm_waybill(){
	
		$results       = $this->client->confirm_waybill(array('su'=> $this->su, 'sp' => $this->sp, 'waybill_id' => $this->waybill_id));
		$xmlString     = $results->confirm_waybillResult;
	
		return $xmlString;
	
	}
	
	/**
	 * ზედნადების გაუქმება
	 */
	public function ref_waybill(){
		
		$results       = $this->client->ref_waybill(array('su'=> $this->su, 'sp' => $this->sp, 'waybill_id' => $this->waybill_id));
		$xmlString     = $results->ref_waybillResult;
		
		switch ($xmlString) {
			case 1 :
				echo "წარმატებით გაუქმდა";
				break;
			
			case -1 :
				echo "წარუმატებლად გაუქმდა";
				break;
			
			case -101 :
				echo "სხვისი ზედნადებია";
				break;
			
			case -100 :
				echo "არასწორი მომხმარებელი ან პაროლი";
				break;
			
			default :
				echo "unknow error!!!";
				break;
		}
	
	}
	
	/**
	 * აქტიური ზედნადების  დასრულება
	 */
	public function close_waybill(){
		$message	= "";	
		$results	= $this->client->close_waybill(array('su'=> $this->su, 'sp' => $this->sp, 'waybill_id' => $this->waybill_id));
		$xmlString	= $results->close_waybillResult;

		switch ($xmlString) {
			case 1 :
				$message = 1; //"წარმატებით დასრულდა"
				break;
					
			case -1 :
				$message = "წარუმატებლად დასრულდა";
				break;
					
			case -101 :
				$message = "სხვისი ზედნადებია";
				break;
					
			case -100 :
				$message = "არასწორი მომხმარებელი ან პაროლი";
				break;
					
			default :
				$message = "unknow error!!!";
				break;
		}
		return $message;	
	}
	
	/**
	 * ზედნადების გაგზავნა
	 */
	public function save_waybill($xml){
		

		$params 			= new stdClass();
		$params->su 		= $this->su;
		$params->sp 		= $this->sp;		
		$params->waybill 	= array(
								'any' => new SoapVar($xml, XSD_ANYXML),
								'includeSuccessStatus' => true,
							  );

		$results    = $this->client->save_waybill($params);	
		$xmlString  = $results->save_waybillResult->any;	
		$obj        = new  SimpleXMLElement($xmlString);
		
		$count		= count($obj -> GOODS_LIST -> GOODS);
		$sub_array 	= array();
		for($i = 0; $i < $count; $i++){
			$sub_array[$i] = array(
					"id" 		=> 	$obj -> GOODS_LIST -> GOODS[$i] -> ID,
					"bar_code" 	=>	$obj -> GOODS_LIST -> GOODS[$i] -> BAR_CODE
			);
		}
		
		$array = array(
				"rs_id" 			=> $obj  -> ID,
				"waybill_number" 	=> $obj  -> WAYBILL_NUMBER,
				"status" 			=> $obj  -> STATUS,
				"goods_list"		=> $sub_array
		);
		
		return $array;
	}
	
	/**
	 * შენახული ზედნადების გააქტიურება
	 */	
	public function send_waybill(){
	
		$results       = $this->client->send_waybill(array('su'=> $this->su, 'sp' => $this->sp, 'waybill_id' => $this->waybill_id));
		$xmlString     = $results->send_waybillResult;
		echo $xmlString;
	}
	
	/**
	 * შეცდომების კოდები
	 * @param 	int 	$code
	 * @return 	error 	string
	 */
	public function get_error($code){
		
		switch ($code) {
			case -1001:
				return 'ზედნადების ტიპი არასწორია';
				break;
			
			case -1028:
				return 'წაშლილ ზედნადებში ვერ დაარედაქტირებთ და ვერც წაშლით';
				break;
			
			case -1002:
				return 'ტრანსპორტირების ტიპი არასწორია';
				break;
			
			case -1014:
				return 'მძღოლი არ მოიძებნა;  chek_driver_tin=1';
				break;
				
		 	case -1015:
				return 'reception_info დიდია';
				break;
			
			case -1016:
				return 'receiver_info დიდია';
				break;
						
			case -1017:
				return 'delivery_date მეტია მინდინარე თარიღზე';
				break;
							
			case -1003:
				return 'buyer_name აუცილებელია chek_buyer_tin=0';
				break;
			
			case -1018:
				return 'delivery_date ნაკლებია begin_date თარიღზე';
				break;
					
			case -1004:
				return 'მყიდველი აუცილებელია ყოველთვის გარდა შიდაგადაზიდვისას';
				break;
					
			case -1019:
				return 'სტატუსი არასწორია';
				break;
					
			case -1020:
				return 'შეუნახავს ვერ წაშლი';
				break;
			
			case -1021:
				return 'შეუნახავს ვერ გააუქმებ';
				break;
					
			case -1022:
				return 'გამყიდველი ლიკვიდირებულია';
				break;
			
			case -1005:
				return 'მყიდვევლი არ მოიძებნა (საქართველო)';
				break;
					
			case -1006:
				return 'მყიდვევლი ლიკვიდირებულია ან კოდი გაუქმებულია';
				break;

			case -1007:
				return 'start_address დიდია';
				break;
					
			case -1023:
				return 'ქვეზედნადებისთვის მშობელი აუცილებელია';
				break;
					
			case -1008:
				return 'driver_tin დიდია';
				break;
					
			case -1027:
				return 'დასრულებულის რედაქტირებას ვე გააკეთებთ';
				break;
			
			case -1015:
				return 'reception_info დიდია';
				break;
					
			case -1009:
				return 'start_address აუცილებელია';
				break;
			
			case -1010:
				return 'end_address დიდია';
				break;
					
			case -1029:
				return 'გაუქმებულს ზედნადებში ვერ დაარედაქტირებთ და ვერც წაშლით';
				break;
					
			case -1030:
				return 'მშობელ ზედნადებს ვე გააუქმებთ თუ ქვე ზედნადები აქვს';
				break;
					
			case -1024:
				return 'მშობელი არ მოიძებნა ან მშობელი აქტივირებული არ არის';
				break;
					
			case -1025:
				return 'ქვე ზედნადები მარტო დისტრიბუციაზე იწერება';
				break;
					
			case -1026:
				return 'მანქანია ნომერი არასწორია';
				break;
					
			case -1012:
				return 'მძღოლი აუცილებელია ტრანსპორტირების ტიპის ყველა ზედნადებზე';
				break;
					
			case -1013:
				return 'მძღოლის სახელი აუცილებელია  chek_buyer_tin=0  ტრანსპორტირების ტიპის ყველა ზედნადებზე';
				break;
					
			case -2003:
				return 'unit_id არასწორია';
				break;
					
			case -2004:
				return 'unit_txt აუცილებელია როცა unit_id = 99';
				break;
				
			case -2005:
				return 'რაოდენობა 0-ზე მედი უნდა';
				break;
					
			case -2006:
				return 'ქონების სტატუსი არასწორია';
				break;
					
			case -2007:
				return 'price არის აუცილებელი გარდა შიდაგადაზიდვის';
				break;
					
			case -1027:
				return 'დასრულებულის რედაქტირებას ვე გააკეთებთ';
				break;
					
			case -2008:
				return 'აქციზის ID არასწორია';
				break;
					
			case -2002:
				return 'vat_type არასწორია';
				break;
					
			case -2001:
				return 'პროდუქტის დასახელება დიდია';
				break;
					
			case -3006:
				return 'ანგარიშფაქტურა და ზედნადები სხვადასხვა გადამხდელზეა გამოწერილი';
				break;
					
			case -3004:
				return 'მყიდველი არ არის გადამხელი ან რეგისრირებული არ არის დეკლარირებაში';
				break;
					
			case -3003:
				return 'ზედნადები არ არის დახურული';
				break;
					
			case -3002:
				return 'გამყიდველი არ არის დეცლარირებაში რეგისტრირებული';
				break;
					
			case -3001:
				return 'ზედნადები არ მოიძებნა';
				break;
					
			case -1034:
				return 'აქტივაციის თარიღი  ნაკლებია შექმნის თარიღზე';
				break;
					
			case -2:
				return 'ზოგადი შეცდომა (არამთელი რიცხვებს გამყოფად წერტილი უნდა ქონდეთ)';
				break;
					
			case -1036:
				return 'ტრანსპორტირების გარეშე  შემთხვევაში დაწყების და დარულების ადგილი უნდა ემთხვეოდეს';
				break;
					
			case -1038:
				return 'აქტივაციის თარიღი 3 დღეზე მეტია მიმდინარე თარიღზე';
				break;
				
			case -1031:
				return 'გადაგზავნილს ვერ წაშლით';
				break;
					
			case -1032:
				return 'ქვე ზედდებულს ძირითადად ვერ გახდი';
				break;
					
			case -1033:
				return 'გამოწერილ ზედნადებს ქვე ზედნადებად ვერ გადააკეთებ';
				break;
					
			case -1035:
				return 'შიდაგადაზიდვის დროს მყიდველის საიდენტიფიკაციო კოდი ცარიელი ან გამყიდველის კოდი უნდა იყოს მითითებული';
				break;
					
			case -1039:
				return 'მყიდველი უნდა განსხვავდებოდეს გამყიდველისგან თუ შიდა გადაზიდვა არ არის';
				break;
					
			case -3007:
				return 'ფაქტურაში საქონლის ჩამონათვალში მოხდა შეცდომა';
				break;
					
			case -3008:
				return 'ფაქტურეის შენახვისას მოხდა შედომა';
				break;
					
			case -1:
				return 'გაურკვეველი შეცდომა (გადაამოწმეთ თარიღების ფორმატი)';
				break;
					
			case -100:
				return 'სერვისის მომხმარებელი ან პაროლი არასწორია';
				break;
					
			case -101:
				return 'გამომწერის un_id განსხვავდება XML-ში მითითებული seler_un_id -ის გან';
				break;
					
			case -3009:
				return 'ფაქტურაში ზედნადებების ჩამონათვალში მოხდა შეცდომა';
				break;
					
			case -1040:
				return 'მანქანის ნომერი აუცილებელია';
				break;
					
			case -1037:
				return 'თუ ტრანსპორტირების ტიპი არის სხვა მიუთითეთ TRANS_TXT';
				break;
					
			case -4001:
				return 'ზოგადი შეცდომა (არამთელი რიცხვებს გამყოფად წერტილი უნდა ქონდეთ)';
				break;
					
			case -1036:
				return 'შეცდომა მოხდა სასაქონლო მატერიალული ფასეულობების სიაში';
				break;
					
			case -4001:
				return 'შეცდომა მოხდა სასაქონლო მატერიალული ფასეულობების სიაში';
				break;
			
			case -3010:
				return 'ანგარიშ-ფაქტურის შენახვა ვერ მოხერხდა';
				break;
			
			case -2009:
				return 'მშობელ ზედნადებში არ არის შესაბამისი საქონელი, საქონლის ერთეული ან შტრიხკოდი';
				break;
			
			case -102:
				return 'შეცდომა მოხდა XML-ის პარსირებისას ან SELLER_UN_ID ტეგი არ გაქვთ';
				break;
			
			case -3011:
				return 'ანგარიშ-ფაქტურის კორექტირება ვერ მოხერხდა';
				break;
			
			case -3012:
				return 'ანგარიშ-ფაქტურის პროდუქციის შენახვა ვერ მოხერხდა';
				break;
			
			case -3013:
				return 'ანგარიშ-ფაქტურის ზედნადებზე მიბმა ვერ განხორციელდა';
				break;
			
			case -3014:
				return 'ანგარიშ-ფაქტურის სტატუსის შეცვლა ვერ მოხერხდა';
				break;
			
			case -3015:
				return 'ანგარიშ-ფაქტურის დადასტურება ვერ მოხერხდა';
				break;
			
			case -4002:
				return 'სატესტო კოდებიდან მხოლოდ სატესტოზე გამოწერეთ : სატესტო კოდებია :206322102,12345678910';
				break;
			
			default:
				return 'შეცდომის იდენტიფიცირება ვერ მოხერხდა (' . $code . ')';
			break;
		}
	}	
}

?>