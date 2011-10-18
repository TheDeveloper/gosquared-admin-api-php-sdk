<?php

class func_set_account_plan extends GS_ADMIN_SDK_request_model {
	public $mode = 'set_account_plan';
	protected $params = array(
	    'user_id',
	    'plan_name'
	);
	public function parse_response($data){
		return new set_account_plan_response($data);
	}
}

class set_account_plan_response extends GS_ADMIN_SDK_Response{
	function __construct($response_data){
		parent::__construct($response_data);
		$this->succeeded = $response_data['success'];
		if(!$this->succeeded){ 
			//throw new GS_ADMIN_SDK_Exception(join("\n\n",$this->errors));
		}else{
      $this->user_id = $response_data['data']['user_id'];
      $this->plan_settings = $response_data['data']['plan_settings'];
		}
	}
}
