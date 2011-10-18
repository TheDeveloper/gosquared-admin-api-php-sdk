<?php
require_once(__DIR__.'/../sdk.php');

class GoSquaredAdminSDKTest extends PHPUnit_Framework_TestCase{
    
    public function setUp(){
        $this->_sdk = new GS_ADMIN_SDK();
    }
    
    /**
     */
    public function testCreateAccount(){
      list($args) = $this->providerAccountAttributes();
        $r = call_user_func_array(array($this->_sdk, 'create_account'), $args);
        $this->assertContainsOnly('object', array($r));
        $this->objectHasAttribute('succeeded', $r);
        $this->assertEquals(true, $r->succeeded);
        $this->objectHasAttribute('user_id', $r);
        $this->assertGreaterThanOrEqual(1, $r->user_id);
        $this->user_id = $r->user_id;
        return $r;
    }

    /**
     * @dataProvider providerSiteAttributes
     * @depends testCreateAccount
     */
    public function testCreateSite($site_name, $site_url, $createAccountResponse){
        $r = call_user_func_array(array($this->_sdk, 'create_site'), array($createAccountResponse->user_id, $site_name, $site_url));
        $this->assertContainsOnly('object', array($r));
        $this->objectHasAttribute('succeeded', $r);
        $this->assertTrue($r->succeeded);
        $this->objectHasAttribute('site_token', $r);
        $this->assertRegExp('/GSN-[0-9]{6,7}-[A-Z]{1}/', $r->site_token);
        return $r;
    }
    
    /**
     * @depends testCreateAccount
     */

    public function testGetNoSubscription($createAccountResponse){
      $r = $this->_sdk->get_subscription($createAccountResponse->user_id);
      $this->assertContainsOnly('object', array($r));
      $this->objectHasAttribute('succeeded', $r);
      $this->assertTrue($r->succeeded);
      $this->assertFalse($r->subscription);
    }

    /**
     * @depends testCreateAccount
     */

    public function testSetAccountPlan($createAccountResponse){
      $r = $this->_sdk->set_account_plan($createAccountResponse->user_id, 'standard');
      $this->assertContainsOnly('object', array($r));
      $this->objectHasAttribute('succeeded', $r);
      $this->assertTrue($r->succeeded);
      $this->objectHasAttribute('user_id', $r);
      $this->assertGreaterThanOrEqual(1, $r->user_id);
      $this->objectHasAttribute('plan_settings');
      $this->assertContainsOnly('array', array($r->plan_settings));
    }

  /**
     * @depends testCreateAccount
     */

    public function testModifyAccount($createAccountResponse){
      $r = $this->_sdk->modify_account(
            $createAccountResponse->user_id
          , 'geoff+'.round(mt_rand()).'@gosquared.com'
          , mt_rand()
          , 'Joe'
          , 'Bloggs'
          , 'London, United Kingdom'
          , '@TheDeveloper'
          , '+44123456789'
      );
      $this->assertContainsOnly('object', array($r));
      $this->objectHasAttribute('succeeded', $r);
      $this->assertTrue($r->succeeded);
      $this->objectHasAttribute('user_id', $r);
      $this->assertGreaterThanOrEqual(1, $r->user_id);
    }

    /**
     * @depends testCreateAccount
     */

    public function testGetNoBillingInfo($createAccountResponse){
      $r = $this->_sdk->get_billing_info($createAccountResponse->user_id);
      $this->assertContainsOnly('object', array($r));
      $this->objectHasAttribute('succeeded', $r);
      $this->assertTrue($r->succeeded);
      $this->objectHasAttribute('billing_info', $r);
      $this->assertFalse($r->billing_info);
    }

    /**
     * @depends testCreateAccount
     */

    public function testSetBillingInfo($createAccountResponse){
      $r = $this->_sdk->set_billing_info(
              $createAccountResponse->user_id
              , array(
                'first_name' => 'test'
                , 'last_name' => 'test'
                , 'address1' => '123 Test St'
                , 'city' => 'London'
                , 'state' => 'London'
                , 'country' => 'UK'
                , 'zip' => 'EC1R 5DF'
                , 'credit_card' => array(
                      'number' => 1
                    , 'year' => '2020'
                    , 'month' => '06'
                    , 'verification_value' => 123
                )

              )
      );
      $this->assertContainsOnly('object', array($r));
      $this->objectHasAttribute('succeeded', $r);
      $this->assertTrue($r->succeeded);
    }
   
    /**
     * @depends testCreateAccount
     */

    public function testCreateSubscription($createAccountResponse){
      $r = $this->_sdk->create_subscription(
              $createAccountResponse->user_id
            , 'standard'
      );
      $this->assertContainsOnly('object', array($r));
      $this->objectHasAttribute('succeeded', $r);
      $this->assertTrue($r->succeeded);
    }

    /**
     * @depends testCreateAccount
     */

    public function testModifySubscription($createAccountResponse){
      $r = $this->_sdk->modify_subscription(
              $createAccountResponse->user_id
              , 'pro'
              , 'now'
      );
      $this->assertContainsOnly('object', array($r));
      $this->objectHasAttribute('succeeded', $r);
      $this->assertTrue($r->succeeded);
    }

     /**
     * @depends testCreateAccount
     */

    public function testGetSubscription($createAccountResponse){
      $r = $this->_sdk->get_subscription($createAccountResponse->user_id);
      $this->assertContainsOnly('object', array($r));
      $this->objectHasAttribute('succeeded', $r);
      $this->assertTrue($r->succeeded);
      $this->objectHasAttribute('plan', $r->subscription);
      $this->assertEquals('pro', $r->subscription['plan']['plan_code']);
    }

     /**
     * @depends testCreateAccount
     */

    public function testCancelSubscription($createAccountResponse){
      $r = $this->_sdk->cancel_subscription($createAccountResponse->user_id);
      $this->assertContainsOnly('object', array($r));
      $this->objectHasAttribute('succeeded', $r);
      $this->assertTrue($r->succeeded);
    }

     /**
     * @depends testCreateAccount
     */

    public function testReactivateSubscription($createAccountResponse){
      $r = $this->_sdk->reactivate_subscription($createAccountResponse->user_id);
      $this->assertContainsOnly('object', array($r));
      $this->objectHasAttribute('succeeded', $r);
      $this->assertTrue($r->succeeded);
    }

    /**
     * @depends testCreateAccount
     */

    public function testPurgeAccount($createAccountResponse){
      $r = $this->_sdk->purge_account($createAccountResponse->user_id);
      $this->assertContainsOnly('object', array($r));
      $this->objectHasAttribute('succeeded', $r);
      $this->assertTrue($r->succeeded);
    }


    public function providerAccountAttributes(){
        $email = 'geoff+'.round(mt_rand()*10000).'@gosquared.com';
        $password = 'testpassword';
        $first_name = 'Bob';
        $last_name = 'Jenkins';
        return array(
             array(
                 $email
                ,$password
                ,$first_name
                ,$last_name
                ,null
                ,null
             )
        );
    }
    
    public function providerSiteAttributes(){
        $site_name = 'Test Site';
        $site_url = 'http://www.gosquared.com/';
        return array(
            array(
                 $site_name
                ,$site_url
            )
        );
    }
    
}
?>
