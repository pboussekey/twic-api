<?php
namespace ModuleTest\Api\Service;

use ModuleTest\Api\AbstractService;
use Application\Model\Preregistration;

class UserTest extends AbstractService
{
    public static $session;

    public static function setUpBeforeClass()
    {
        system('phing -q reset-db deploy-db');
        parent::setUpBeforeClass();
    }

    public function testInit()
    {
        $this->setIdentity(1, 1);
        $data = $this->jsonRpc(
            'page.add', [
            'title' => 'super title',
            'logo' => 'logo',
            'background' => 'background',
            'description' => 'description',
            'confidentiality' => 1,
            'type' => 'organization',
            'admission' => 'free',
            'start_date' => '2015-00-00 00:00:00',
            'end_date' => '2016-00-00 00:00:00',
            'location' => 'location',
            'organization_id' => 1,
            'domaine' => 'twic.io',
            'users' => [
              ['user_id' => 1,'role' => 'admin', 'state' => 'member'],
              ['user_id' => 2,'role' => 'admin', 'state' => 'member'],
              ['user_id' => 3,'role' => 'admin', 'state' => 'member'],
              ['user_email' => 'contact@paul-boussekey.com','role' => 'user', 'state' => 'pending']
            ],
            ]
        );

        $page_id = $data['id'];
        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'user.addOrganization', [
            'organization_id' => $page_id,
            'user_id' => [1,2,3,4,5,6,7],
            'default' => true
            ]
        );

        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'circle.add', [
            'name' => 'gnam'
            ]
        );

        $circle_id = $data['result'];

        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'circle.addOrganizations', [
            'id' => $circle_id,
            'organizations' => [$page_id]
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 1);
        $this->assertEquals($data['result'][1], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);

        return $page_id;
    }
    
    public function testAcceptCgu()
    {
        $this->setIdentity(1, 1);
        $data = $this->jsonRpc(
            'user.acceptCgu', []
        );
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals($data['result'] , 1); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 
    }
    
    /**
     * @depends testInit
     **/
    /*public function testCanAddUserError($page_id)
    {
        $this->setIdentity(5, 1);
        $data = $this->jsonRpc(
            'user.add', [
            'firstname' => 'Christophe',
            'gender' => 'm',
            'origin' => 1,
            'organization_id' => $page_id,
            'nationality' => 1,
            'lastname' => 'Robert',
            'email' => 'crobert@thestudnet.com',
            'password' => 'studnet',
            'position' => 'une position',
            'interest' => 'un interet',
            'avatar' => 'un_token']
        );
        
       
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -38001); 
        $this->assertEquals($data['error']['message'] , "duplicate email"); 
        $this->assertEquals($data['error']['data'] , null); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }*/

    /**
     * @depends testInit
     **/
    public function testCanAddUser($page_id)
    {
        $this->setIdentity(5, 1);
        $data = $this->jsonRpc(
            'user.add', [
            'firstname' => 'Christophe',
            'gender' => 'm',
            'origin' => 1,
            'organization_id' => $page_id,
            'nationality' => 1,
            'lastname' => 'Robert',
            'email' => 'crobertr@thestudnet.com',
            'password' => 'thestudnet',
            'position' => 'une position',
            'interest' => 'un interet',
            'avatar' => 'un_token',
            'address' => [
              "street_no" => 12,
              "street_type" => "rue",
              "street_name" => "du stade",
              "city" => ["name" => "Monaco"],
              "country" => ["name" => "Monaco"]
            ],
            'sis' => 'sis']
        );
        
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['result'], 9);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
        
        $this->reset();
        $this->setIdentity(5, 1);
        $this->jsonRpc(
            'pageuser.add', [
            'page_id' => $page_id,
            'user_id' => $data['result'],
            'state' => 'member',
            'role' => 'user'
            ]
        );

        return $data['result'];
    }
    
    
    /**
     * @depends testInit
     **/
    public function testCanAddUserError2($page_id)
    {
        $this->setIdentity(5, 1);
        $data = $this->jsonRpc(
            'user.add', [
            'firstname' => 'Christophe',
            'gender' => 'm',
            'origin' => 1,
            'organization_id' => $page_id,
            'nationality' => 1,
            'lastname' => 'Robert',
            'email' => 'crobert2@thestudnet.com',
            'password' => 'thestudnet',
            'position' => 'une position',
            'interest' => 'un interet',
            'avatar' => 'un_token',
            'sis' => 'sis']
        );

        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -38002); 
        $this->assertEquals($data['error']['message'] , "uid email"); 
        $this->assertEquals($data['error']['data'] , null); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 
    }
    
     /**
     * @depends testInit
     **/
    public function testCanAddUserError3($page_id)
    {
        $this->setIdentity(5, 2);
        $data = $this->jsonRpc(
            'user.add', [
            'firstname' => 'Christophe',
            'gender' => 'm',
            'origin' => 1,
            'organization_id' => $page_id,
            'nationality' => 1,
            'lastname' => 'Robert',
            'email' => 'crobert2@thestudnet.com',
            'password' => 'thestudnet',
            'position' => 'une position',
            'interest' => 'un interet',
            'avatar' => 'un_token']
        );

        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -38003); 
        $this->assertEquals($data['error']['message'] , "Unauthorized operation user.add"); 
        $this->assertEquals($data['error']['data'] , null); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }
    
    
      /**
     * @depends testInit
     **/
    public function testCanAddExternal($page_id)
    {
        $this->setIdentity(5, 1);
        $data = $this->jsonRpc(
            'user.add', [
            'firstname' => 'Christophe',
            'gender' => 'm',
            'origin' => 1,
            'organization_id' => $page_id,
            'nationality' => 1,
            'roles' => ['external'],
            'lastname' => 'Robert',
            'email' => 'external@thestudnet.com',
            'password' => 'thestudnet',
            'position' => 'une position',
            'interest' => 'un interet',
            'avatar' => 'un_token']
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['result'], 10);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
        
        $this->reset();
        $this->setIdentity(5, 1);
        $this->jsonRpc(
            'pageuser.add', [
            'page_id' => $page_id,
            'user_id' => $data['result'],
            'state' => 'member',
            'role' => 'user'
            ]
        );
        return $data['result'];
    }
    
    public function testGetListOrgByMail()
    {
        $this->setIdentity(5, 1);
        
        $data = $this->jsonRpc('page.getListByEmail', ['email' => 'toto@twic.io']);
        
        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals(count($data['result']) , 1);
        $this->assertEquals(count($data['result'][0]) , 4);
        $this->assertEquals($data['result'][0]['id'] , 1);
        $this->assertEquals($data['result'][0]['title'] , "super title");
        $this->assertEquals($data['result'][0]['logo'] , "logo");
        $this->assertEquals($data['result'][0]['domaine'] , "twic.io");
        $this->assertEquals($data['jsonrpc'] , 2.0);
    }
    
    public function testPreSignIn()
    {
        $this->setIdentity(5, 1);
        
        //preSignIn($email, $page_id, $firstname = null, $lastname = null)
        $data = $this->jsonRpc('user.preSignIn', [
            'email' => 'christophe@twic.io', 
            'page_id' => 1,
        ]);
        
        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals($data['result'] , true);
        $this->assertEquals($data['jsonrpc'] , 2.0);
    }
    
    public function testSignIn()
    {
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        
        $m_preregistration = new Preregistration();
        $m_preregistration->setEmail("christophe@twic.io")->setOrganizationId(1)->setAccountToken('fake');
        
        
        $mock = $this->getMockBuilder('\Application\Service\Preregistration')
        ->setMethods(['get','delete'])
            ->disableOriginalConstructor()
            ->getMock();
        
        $mock->expects($this->any())
            ->method('get')
            ->will($this->returnValue($m_preregistration));
        
        $serviceManager->setService('app_service_preregistration', $mock);

        $data = $this->jsonRpc('user.signIn', [
            'account_token' => '1234',
            'password' => 1,
            'firstname' => 'chris',
            'lastname' => 'bob'
        ]);

        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals(count($data['result']) , 18);
        $this->assertEquals($data['result']['id'] , 11);
        $this->assertEquals(!empty($data['result']['token']) , true);
        $this->assertEquals(!empty($data['result']['created_date']) , true);
        $this->assertEquals($data['result']['firstname'] , "chris");
        $this->assertEquals($data['result']['lastname'] , "bob");
        $this->assertEquals($data['result']['nickname'] , null);
        $this->assertEquals($data['result']['suspension_date'] , null);
        $this->assertEquals($data['result']['suspension_reason'] , null);
        $this->assertEquals($data['result']['organization_id'] , 1);
        $this->assertEquals($data['result']['email'] , "christophe@twic.io");
        $this->assertEquals($data['result']['avatar'] , null);
        $this->assertEquals($data['result']['expiration_date'] , null);
        $this->assertEquals($data['result']['has_linkedin'] , false);
        $this->assertEquals($data['result']['cgu_accepted'] , 0);
        $this->assertEquals($data['result']['swap_email'] , null);
        $this->assertEquals(count($data['result']['roles']) , 1);
        $this->assertEquals($data['result']['roles'][2] , "user");
        $this->assertEquals(!empty($data['result']['wstoken']) , true);
        $this->assertEquals(!empty($data['result']['fbtoken']) , true);
        $this->assertEquals($data['jsonrpc'] , 2.0);
    }
    

    
    
    
    
    /**
     * @depends testCanAddUser
     */
    public function testLogin()
    {
        $this->mockRbac();


        $data = $this->jsonRpc('user.login', ['user' => 'crobert@thestudnet.com','password' => 'thestudnet']);

        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 18); 
        $this->assertEquals($data['result']['id'] , 3); 
        $this->assertEquals(!empty($data['result']['token']) , true); 
        $this->assertEquals($data['result']['created_date'] , null); 
        $this->assertEquals($data['result']['firstname'] , "Christophe"); 
        $this->assertEquals($data['result']['lastname'] , "Robert"); 
        $this->assertEquals($data['result']['nickname'] , null); 
        $this->assertEquals($data['result']['suspension_date'] , null); 
        $this->assertEquals($data['result']['suspension_reason'] , null); 
        $this->assertEquals($data['result']['organization_id'] , 1); 
        $this->assertEquals($data['result']['email'] , "crobert@thestudnet.com"); 
        $this->assertEquals($data['result']['avatar'] , null); 
        $this->assertEquals($data['result']['expiration_date'] , null); 
        $this->assertEquals($data['result']['has_linkedin'] , false); 
        $this->assertEquals($data['result']['cgu_accepted'] , 0); 
        $this->assertEquals($data['result']['swap_email'] , null);
        $this->assertEquals(count($data['result']['roles']) , 1); 
        $this->assertEquals($data['result']['roles'][2] , "user"); 
        $this->assertEquals(!empty($data['result']['wstoken']) , true); 
        $this->assertEquals(!empty($data['result']['fbtoken']) , true); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 


        return $data['result']['token'];
    }
    
    
     /**
     * @depends testCanAddUser
     */
    public function testSendPassword($id)
    {
        $this->mockRbac();
        $this->mockMail();

        $data = $this->jsonRpc('user.sendPassword', ['id' => [$id, -1]]);
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals($data['result'] , 1); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 


    }
    
    /**
     * @depends testCanAddUser
     */
    public function testAddPreregistration($id)
    {
        $this->mockRbac();


        $data = $this->jsonRpc('preregistration.add', 
            ['account_token' => 'token', 'firstname' => 'Christophe', 'lastname' => 'Robert', 'email' => 'crobertr@thestudnet.com', 'organization_id' => 1, 'user_id' => $id]);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals($data['result'] , "token"); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

        return $data['result'];
    }
    
    
       /**
     * @depends testAddPreregistration
     */
    public function testCheckAccountToken($token)
    {
        $this->mockRbac();


        $data = $this->jsonRpc('user.checkAccountToken', 
            ['token' => $token]);
        
        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals(count($data['result']) , 7);
        $this->assertEquals(count($data['result']['preregistration']) , 6);
        $this->assertEquals($data['result']['preregistration']['email'] , "crobertr@thestudnet.com");
        $this->assertEquals($data['result']['preregistration']['firstname'] , "Christophe");
        $this->assertEquals($data['result']['preregistration']['lastname'] , "Robert");
        $this->assertEquals($data['result']['preregistration']['organization_id'] , 1);
        $this->assertEquals($data['result']['preregistration']['account_token'] , "token");
        $this->assertEquals($data['result']['preregistration']['user_id'] , 9);
        $this->assertEquals($data['result']['firstname'] , "Christophe");
        $this->assertEquals($data['result']['lastname'] , "Robert");
        $this->assertEquals($data['result']['nickname'] , null);
        $this->assertEquals($data['result']['email'] , "crobertr@thestudnet.com");
        $this->assertEquals($data['result']['avatar'] , "un_token");
        $this->assertEquals($data['result']['is_active'] , 0);
        $this->assertEquals($data['jsonrpc'] , 2.0); 

        return $data['result'];
    }
    
     
       /**
     * @depends testAddPreregistration
     */
    public function testCheckEmail($token)
    {
        $this->mockRbac();


        $data = $this->jsonRpc('user.checkEmail', 
            ['email' => 'pboussekey@thestudnet.com']);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 6); 
        $this->assertEquals($data['result']['firstname'] , "Paul"); 
        $this->assertEquals($data['result']['lastname'] , "Boussekey"); 
        $this->assertEquals($data['result']['nickname'] , null); 
        $this->assertEquals($data['result']['email'] , "pboussekey@thestudnet.com"); 
        $this->assertEquals($data['result']['avatar'] , null); 
        $this->assertEquals($data['result']['is_active'] , 1); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 



        return $data['result'];
    }
    
    
    /**
     * @depends testCanAddUser
     * @depends testAddPreregistration
     */
    public function testGetPreregistration($id, $token)
    {
        $this->setIdentity(1,1);
        $data = $this->jsonRpc(
            'preregistration.get', [
            'account_token' => $token
            ]
        );
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 6);
        $this->assertEquals($data['result']['email'], "crobertr@thestudnet.com");
        $this->assertEquals($data['result']['firstname'], "Christophe");
        $this->assertEquals($data['result']['lastname'], "Robert");
        $this->assertEquals($data['result']['organization_id'], 1);
        $this->assertEquals($data['result']['account_token'], $token);
        $this->assertEquals($data['result']['user_id'], $id);
        $this->assertEquals($data['jsonrpc'], 2.0);



    }
    
     /**
     * @depends testCanAddExternal
     */
    public function testSendPwdForExternal($id)
    {
        $this->mockRbac();


        $data = $this->jsonRpc('user.sendPassword', ['id' => $id]);
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals($data['result'] , 0); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }
    
       /**
     * @depends testCanAddExternal
     */
    public function testAddPreregistration2($id)
    {
        $this->mockRbac();


        $data = $this->jsonRpc('preregistration.add', 
            ['account_token' => 'token2', 'firstname' => 'Christophe', 'lastname' => 'Robert', 'email' => 'external@thestudnet.com', 'organization_id' => 1, 'user_id' => $id]);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals($data['result'] , "token2"); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 


    }
     
    /**
     * @depends testCanAddExternal
     */
    public function testSignInExternal($id)
    {
        $this->mockRbac();


        $data = $this->jsonRpc('user.signIn', 
            ['account_token' => 'token2',  'password' => 'thestudnet']);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -32000); 
        $this->assertEquals(!empty($data['error']['message']) , true); 

    }
    
    public function testSignIn3()
    {
        $this->mockRbac();


        $this->jsonRpc('preregistration.add', 
            ['account_token' => 'testtoken', 'firstname' => 'Test', 'lastname' => 'Test', 'email' => 'test@thestudnet.com', 'organization_id' => 1]);
      
        $this->reset();
        $data = $this->jsonRpc('user.signIn', 
            ['account_token' => 'testtoken',  'password' => 'thestudnet']);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 18); 
        $this->assertEquals($data['result']['id'] , 12); 
        $this->assertEquals(!empty($data['result']['token']) , true); 
        $this->assertEquals(!empty($data['result']['created_date']) , true); 
        $this->assertEquals($data['result']['firstname'] , "Test"); 
        $this->assertEquals($data['result']['lastname'] , "Test"); 
        $this->assertEquals($data['result']['nickname'] , null); 
        $this->assertEquals($data['result']['suspension_date'] , null); 
        $this->assertEquals($data['result']['suspension_reason'] , null); 
        $this->assertEquals($data['result']['organization_id'] , 1); 
        $this->assertEquals($data['result']['email'] , "test@thestudnet.com"); 
        $this->assertEquals($data['result']['avatar'] , null); 
        $this->assertEquals($data['result']['expiration_date'] , null); 
        $this->assertEquals($data['result']['has_linkedin'] , false); 
        $this->assertEquals($data['result']['cgu_accepted'] , 0); 
        $this->assertEquals($data['result']['swap_email'] , null);
        $this->assertEquals(count($data['result']['roles']) , 1); 
        $this->assertEquals($data['result']['roles'][2] , "user"); 
        $this->assertEquals(!empty($data['result']['wstoken']) , true); 
        $this->assertEquals(!empty($data['result']['fbtoken']) , true); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 
    }
     
    public function testSignInError()
    {
        $this->mockRbac();


        $data = $this->jsonRpc('user.signIn', 
            ['account_token' => 'unknowntoken',  'password' => 'thestudnet']);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -32000); 
        $this->assertEquals(!empty($data['error']['message']) , true); 
    }
    
    
    /**
     * @depends testCanAddExternal
     */
     public function testExternalLogin()
    {
        $this->mockRbac();


        $data = $this->jsonRpc('user.login', ['user' => 'external@thestudnet.com','password' => 'thestudnet']);
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -32000); 
        $this->assertEquals(!empty($data['error']['message']) , true); 


    }
    
    
    public function testWrongLogin()
    {
        $this->mockRbac();


        $data = $this->jsonRpc('user.login', ['user' => 'unknown@thestudnet.com','password' => 'thestudnet']);
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -32033); 
        $this->assertEquals($data['error']['message'] , "A record with the supplied identity could not be found."); 
        $this->assertEquals($data['error']['data'] , null); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 
    }
    
    
    /**
     * @depends testCanAddUser
     */
    public function testWrongPWD()
    {
        $this->mockRbac();


        $data = $this->jsonRpc('user.login', ['user' => 'crobert@thestudnet.com','password' => 'wrongpwd']);
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -32032); 
        $this->assertEquals($data['error']['message'] , "A record with the supplied identity could not be found."); 
        $this->assertEquals($data['error']['data'] , null); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }
    
    
    /**
     * @depends testCanAddUser
     */
    public function testSuspendError()
    {
        $this->mockRbac();


        $this->setIdentity(1,2);
        $data = $this->jsonRpc('user.suspend', ['id' => 9,'suspend' => 1]);
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -38003); 
        $this->assertEquals($data['error']['message'] , "Unauthorized operation user.suspend"); 
        $this->assertEquals($data['error']['data'] , null); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }

    /**
     * @depends testCanAddUser
     */
    public function testRegisterFcm($id)
    {
        $this->setIdentity(3);
        $data = $this->jsonRpc(
            'user.registerFcm', [
            'uuid' => 3,
            'token' => 'azertyuiop',
            'package' => 'azertyuiop'
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    
    /**
     * @depends testCanAddUser
     */
    public function testCanGetCustomTokenfb($id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'user.getCustomTokenfb', [
            'id' => $id
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(!empty($data['result']), true);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    /**
     * @depends testCanAddUser
     */
    public function testUpdate($id)
    {
        $this->setIdentity($id);

        $data = $this->jsonRpc(
            'user.update', [
            'id' => $id,
            'firstname' => 'Jean',
            'lastname' => 'Paul',
            'nickname' => 'JP',
            'email' => 'jpaul@thestudnet.com',
            'password' => 'studnetnew',
            'position' => 'une position new',
            'interest' => 'un interet new',
            'avatar' => 'un_token_new',
            'birth_date' => '01-01-1970',
            'address' => [
                "street_no"   => "11",
                "street_name" => "Allée des Chênes",
                "city"        => ["name" => "Villefontaine", "libelle" => "VILLEFONTAINE"],
                "division"    => ["name" => "Auvergne-Rhône-Alpes"],
                "country"     => ["short_name" => "France"],
                "latitude"    => 45.601569,
                "longitude"   => 5.178744499999993,
                ]
            ]
        );
        
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
        
        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'user.update', [
            'id' => 1,
            'nickname' => 'pboussekey',
            'resetpassword' => true
            ]
        );
    }
    
      /**
     * @depends testCanAddUser
     */
    public function testUpdateError($id)
    {
        $this->setIdentity($id);

        $data = $this->jsonRpc(
            'user.update', [
            'id' => $id - 1,
            'email' => 'jpaul@thestudnet.com'
            ]
         
        );
        
        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals(count($data['error']) , 3);
        $this->assertEquals($data['error']['code'] , -38003);
        $this->assertEquals($data['error']['message'] , "Unauthorized operation user.update");
        $this->assertEquals($data['error']['data'] , null);
        $this->assertEquals($data['jsonrpc'] , 2.0);
    }
    
      /**
     * @depends testCanAddUser
     */
    public function testUpdateError2($id)
    {
        $this->setIdentity(1,2);

        $data = $this->jsonRpc(
            'user.update', [
            'id' => $id ,
            'email' => 'jpaul2@thestudnet.com'
            ]
         
        );
        
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -38003); 
        $this->assertEquals($data['error']['message'] , "Unauthorized operation user.update"); 
        $this->assertEquals($data['error']['data'] , null); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }
    
    /**
     * @depends testCanAddUser
     */
    public function testUpdateMyself($id)
    {
        $this->setIdentity(1,2);

        $data = $this->jsonRpc(
            'user.update', [
            'nickname' => 'Me',
            'address' => 'null',
            'roles'=> ['admin'],
            'resetPassword' => true 
            ]
         
        );
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals($data['result'] , 1); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }
    
    
    /**
     * @depends testCanAddUser
     */
    public function testUpdateMyself2($id)
    {
        $this->setIdentity(4,1);

        $data = $this->jsonRpc(
            'user.update', [
            'roles'=> ['admin'],
            'suspend' => 0,
            'nickname' => 'Me 2',
            'organization_id' => 'null'
            ]
         
        );
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals($data['result'] , 1); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }
    
    public function testLostPasswordError()
    {
        $this->setIdentity(1);

        $data = $this->jsonRpc(
            'user.lostPassword', [
            'email' => ''
            ]
         
        );
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -32000); 
        $this->assertEquals(!empty($data['error']['message']) , true); 


    }
    
    public function testLostPasswordError2()
    {
        $this->setIdentity(1);

        $data = $this->jsonRpc(
            'user.lostPassword', [
            'email' => 'unknownemail'
            ]
         
        );
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -32000); 
        $this->assertEquals(!empty($data['error']['message']) , true); 


    }
    
    /**
     * @depends testCanAddUser
     */
    public function testLostPassword($id)
    {
        $this->setIdentity(1);

        $data = $this->jsonRpc(
            'user.lostPassword', [
            'email' => 'pboussekey@thestudnet.com'
            ]
         
        );
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals($data['result'] , true); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }
    
    
    /**
     * @depends testCanAddUser
     */
    public function testUpdatePassword($id)
    {
        $this->setIdentity(1);

        $data = $this->jsonRpc(
            'user.updatePassword', [
            'oldpassword' => 'thestudnet',
            'password' => 'thestudnet'
            ]
         
        );
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals($data['result'] , 0); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 
    }
    
    /**
     * @depends testCanAddUser
     */
    public function testGetIdByEmail($id)
    {
        $this->setIdentity(1);

        $data = $this->jsonRpc(
            'user.getListIdByEmail', [
            'email' => 'gmasmejean@thestudnet.com'
            ]
         
        );
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 1); 
        $this->assertEquals($data['result']['gmasmejean@thestudnet.com'] , 6); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }
    
     /**
     * @depends testCanAddUser
     */
    public function testGetIdByEmail2($id)
    {
        $this->setIdentity(1);

        $data = $this->jsonRpc(
            'user.getListIdByEmail', [
            'email' => []
            ]
         
        );
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals($data['result'] , null); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }




    /**
     * @depends testCanAddUser
     */
    public function testUserGet($id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'user.get', [
            'id' => [$id]
            ]
        );
        
        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals(count($data['result']) , 1);
        $this->assertEquals(count($data['result'][9]) , 24);
        $this->assertEquals(count($data['result'][9]['origin']) , 2);
        $this->assertEquals($data['result'][9]['origin']['id'] , 1);
        $this->assertEquals($data['result'][9]['origin']['short_name'] , "Afghanistan");
        $this->assertEquals(count($data['result'][9]['nationality']) , 2);
        $this->assertEquals($data['result'][9]['nationality']['id'] , 1);
        $this->assertEquals($data['result'][9]['nationality']['short_name'] , "Afghanistan");
        $this->assertEquals($data['result'][9]['gender'] , "m");
        $this->assertEquals($data['result'][9]['contact_state'] , 0);
        $this->assertEquals($data['result'][9]['contacts_count'] , 0);
        $this->assertEquals(count($data['result'][9]['address']) , 14);
        $this->assertEquals(count($data['result'][9]['address']['city']) , 1);
        $this->assertEquals($data['result'][9]['address']['city']['name'] , "Villefontaine");
        $this->assertEquals(count($data['result'][9]['address']['division']) , 2);
        $this->assertEquals($data['result'][9]['address']['division']['id'] , 54);
        $this->assertEquals($data['result'][9]['address']['division']['name'] , "Auvergne-Rhône-Alpes");
        $this->assertEquals($data['result'][9]['address']['country'] , null);
        $this->assertEquals($data['result'][9]['address']['id'] , 2);
        $this->assertEquals($data['result'][9]['address']['street_no'] , 11);
        $this->assertEquals($data['result'][9]['address']['street_type'] , null);
        $this->assertEquals($data['result'][9]['address']['street_name'] , "Allée des Chênes");
        $this->assertEquals($data['result'][9]['address']['longitude'] , 5.1787445);
        $this->assertEquals($data['result'][9]['address']['latitude'] , 45.601569);
        $this->assertEquals($data['result'][9]['address']['door'] , null);
        $this->assertEquals($data['result'][9]['address']['building'] , null);
        $this->assertEquals($data['result'][9]['address']['apartment'] , null);
        $this->assertEquals($data['result'][9]['address']['floor'] , null);
        $this->assertEquals($data['result'][9]['address']['timezone'] , "Europe/Paris");
        $this->assertEquals($data['result'][9]['id'] , 9);
        $this->assertEquals($data['result'][9]['firstname'] , "Jean");
        $this->assertEquals($data['result'][9]['lastname'] , "Paul");
        $this->assertEquals($data['result'][9]['nickname'] , "JP");
        $this->assertEquals($data['result'][9]['email'] , "crobertr@thestudnet.com");
        $this->assertEquals(!empty($data['result'][9]['birth_date']) , true);
        $this->assertEquals($data['result'][9]['position'] , "une position new");
        $this->assertEquals($data['result'][9]['organization_id'] , 1);
        $this->assertEquals($data['result'][9]['interest'] , "un interet new");
        $this->assertEquals($data['result'][9]['avatar'] , "un_token_new");
        $this->assertEquals($data['result'][9]['has_email_notifier'] , 1);
        $this->assertEquals($data['result'][9]['background'] , null);
        $this->assertEquals($data['result'][9]['ambassador'] , null);
        $this->assertEquals(!empty($data['result'][9]['created_date']) , true);
        $this->assertEquals($data['result'][9]['email_sent'] , 1);
        $this->assertEquals($data['result'][9]['welcome_date'] , null);
        $this->assertEquals(!empty($data['result'][9]['invitation_date']) , true);
        $this->assertEquals(count($data['result'][9]['roles']) , 1);
        $this->assertEquals($data['result'][9]['roles'][0] , "user");
        $this->assertEquals($data['jsonrpc'] , 2.0);
    }
    
    public function testUserGet2()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'user.get', []
        );
        
        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals(count($data['result']) , 25);
        $this->assertEquals($data['result']['origin'] , null);
        $this->assertEquals($data['result']['nationality'] , null);
        $this->assertEquals($data['result']['gender'] , null);
        $this->assertEquals($data['result']['contact_state'] , 0);
        $this->assertEquals($data['result']['contacts_count'] , 0);
        $this->assertEquals($data['result']['address'] , null);
        $this->assertEquals($data['result']['id'] , 1);
        $this->assertEquals($data['result']['firstname'] , "Paul");
        $this->assertEquals($data['result']['lastname'] , "Boussekey");
        $this->assertEquals($data['result']['nickname'] , "Me");
        $this->assertEquals($data['result']['email'] , "pboussekey@thestudnet.com");
        $this->assertEquals($data['result']['birth_date'] , null);
        $this->assertEquals($data['result']['position'] , null);
        $this->assertEquals($data['result']['organization_id'] , 1);
        $this->assertEquals($data['result']['interest'] , null);
        $this->assertEquals($data['result']['avatar'] , null);
        $this->assertEquals($data['result']['has_email_notifier'] , 1);
        $this->assertEquals($data['result']['background'] , null);
        $this->assertEquals($data['result']['ambassador'] , null);
        $this->assertEquals($data['result']['created_date'] , null);
        $this->assertEquals($data['result']['email_sent'] , 1);
        $this->assertEquals($data['result']['welcome_date'] , null);
        $this->assertEquals($data['result']['swap_email'] , null);
        $this->assertEquals($data['result']['invitation_date'] , null);
        $this->assertEquals(count($data['result']['roles']) , 1);
        $this->assertEquals($data['result']['roles'][0] , "user");
        $this->assertEquals($data['jsonrpc'] , 2.0); 
    }
    
    
       /**
     * @depends testCanAddUser
     */
    public function testSuspend()
    {
        $this->mockRbac();


        $this->setIdentity(1,1);
        $data = $this->jsonRpc('user.suspend', ['id' => 9,'suspend' => 1]);
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals($data['result'] , 1); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 
    }
    
    /**
     * @depends testCanAddUser
     */
    public function testLoginSuspendedUser()
    {
        $this->mockRbac();

        $data = $this->jsonRpc('user.login', ['user' => 'jpaul@thestudnet.com','password' => 'studnetnew']);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -32033); 
        $this->assertEquals($data['error']['message'] , "A record with the supplied identity could not be found."); 
        $this->assertEquals($data['error']['data'] , null); 
        $this->assertEquals($data['jsonrpc'] , 2.0);
    }
    
      /**
     * @depends testCanAddUser
     */
    public function testUserGetError($id)
    {
        
        $this->setIdentity(4,1);
         $data = $this->jsonRpc(
            'user.update', [
            'organization_id' => 'null'
            ]
         
        );
        $this->reset();
        $this->setIdentity(4,2);
        $data = $this->jsonRpc(
            'user.get', [
            'id' => [$id]
            ]
        );
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -32000); 
        $this->assertEquals(!empty($data['error']['message']) , true);
    }   

    public function testExceptionAddContact()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('contact.add', array('user' => 1));
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['error']), 3);
        $this->assertEquals($data['error']['code'], -32000);
    }
    
    public function testAddContact()
    {
        $this->setIdentity(3);
        $data = $this->jsonRpc('contact.add', array('user' => 1));

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    public function testGetListRequest()
    {
        $this->setIdentity(3);
        $data = $this->jsonRpc(
            'contact.getListRequestId', [
            'user_id' => [1],
            ]
        );
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 1);
        $this->assertEquals(count($data['result'][1]), 1);
        $this->assertEquals($data['result'][1][0], 3);
        $this->assertEquals($data['jsonrpc'], 2.0);

        
        $this->reset();
        $this->setIdentity(3);
        $data = $this->jsonRpc(
            'contact.getListRequestId', [
            'user_id' => 1,
            ]
        );
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 1);
        $this->assertEquals($data['result'][0], 3);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    public function testGetListRequestContact()
    {
        $this->setIdentity(3);
        $data = $this->jsonRpc(
            'contact.getListRequestId', [
            'contact_id' => [3],
            ]
        );
        
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 1);
        $this->assertEquals(count($data['result'][3]), 1);
        $this->assertEquals($data['result'][3][0], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);

        
        $this->reset();
        $this->setIdentity(3);
        $data = $this->jsonRpc(
            'contact.getListRequestId', [
            'contact_id' => 3,
            ]
        );
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 1);
        $this->assertEquals($data['result'][0], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }
    
    
    public function testErrorRequestContact()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'contact.getListRequestId', [
            'contact_id' => [1],
            'user_id' => [1],
            ]
        );
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['error']), 3);
        $this->assertEquals($data['error']['code'], 1);
        $this->assertEquals(!empty($data['error']['message']), true);
    }

    public function testAcceptContact()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('contact.accept', array('user' => 3));
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    public function testGetListContact()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'contact.getListId', [
            'user_id' => [1],
            'exclude' => 2,
            'search' => 'robert'
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 1);
        $this->assertEquals(count($data['result'][1]), 1);
        $this->assertEquals($data['result'][1][0], 3);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }
    
    public function testGetListContact2()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'contact.getListId', [
            'filter' => [ 'n' => 10, 'p' => 1]
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 2);
        $this->assertEquals(count($data['result']['list']), 1);
        $this->assertEquals($data['result']['list'][0], 3);
        $this->assertEquals($data['result']['count'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }
    
     public function testGetListContact3()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'user.getListId', [
            'contact_state' => 0
            ]
        );

        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals(count($data['result']) , 9);
        $this->assertEquals($data['result'][0] , 12);
        $this->assertEquals($data['result'][1] , 11);
        $this->assertEquals($data['result'][2] , 10);
        $this->assertEquals($data['result'][3] , 9);
        $this->assertEquals($data['result'][4] , 7);
        $this->assertEquals($data['result'][5] , 6);
        $this->assertEquals($data['result'][6] , 5);
        $this->assertEquals($data['result'][7] , 2);
        $this->assertEquals($data['result'][8] , 1);
        $this->assertEquals($data['jsonrpc'] , 2.0); 
    }

    public function testDeleteContact()
    {
        $this->setIdentity(1);

        $data = $this->jsonRpc('contact.remove', ['user' => 3]);

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    public function testReAddContact()
    {
        $this->setIdentity(1);

        $data = $this->jsonRpc('contact.add', ['user' => 3]);
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);

        $this->reset();
        $this->setIdentity(3);
        $data = $this->jsonRpc('contact.accept', array('user' => 1));
    }
    

    public function testContactCount()
    {
        $this->setIdentity(1);

        $data = $this->jsonRpc('contact.getRequestsCount', [
             'start_date' => '2015-10-10T06:00:00Z',
            'end_date' => '2099-10-10T06:00:00Z',
            'organization_id' => 1
        ]);
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 1);
        $this->assertEquals(count($data['result'][0]), 2);
        $this->assertEquals(!empty($data['result'][0]['request_date']), true);
        $this->assertEquals($data['result'][0]['requested'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
        
        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc('contact.getAcceptedCount', [
             'start_date' => '2015-10-10T06:00:00Z',
            'end_date' => '2099-10-10T06:00:00Z',
            'organization_id' => 1]);
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 1);
        $this->assertEquals(count($data['result'][0]), 2);
        $this->assertEquals(!empty($data['result'][0]['accepted_date']), true);
        $this->assertEquals($data['result'][0]['accepted'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }
    
    
    public function testCanGetListId()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'user.getListId', [
            'exclude' => [2],
            'search' => 'robert',
            'filter' => ['p' => 1, 'n' => 10],
            'order' => ['type' => 'firstname']
            //  'page_id' => 1
            ]
        );
        
        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals(count($data['result']) , 2);
        $this->assertEquals(count($data['result']['list']) , 3);
        $this->assertEquals($data['result']['list'][0] , 3);
        $this->assertEquals($data['result']['list'][1] , 10);
        $this->assertEquals($data['result']['list'][2] , 9);
        $this->assertEquals($data['result']['count'] , 3);
        $this->assertEquals($data['jsonrpc'] , 2.0);

    }

     public function testCanGetListIdAdmin()
    {
        $this->setIdentity(1,1);
        $data = $this->jsonRpc(
            'user.getListId', [
            'exclude' => [2],
            'search' => 'robert',
            'filter' => ['p' => 1, 'n' => 10],
            'order' => ['type' => 'random', 'seed' => 1]
            //  'page_id' => 1
            ]
        );
        
        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals(count($data['result']) , 2);
        $this->assertEquals(count($data['result']['list']) , 3);
        $this->assertEquals($data['result']['list'][0] , 10);
        $this->assertEquals($data['result']['list'][1] , 3);
        $this->assertEquals($data['result']['list'][2] , 9);
        $this->assertEquals($data['result']['count'] , 3);
        $this->assertEquals($data['jsonrpc'] , 2.0);
        

    }
    
    public function testCanLanguageGetList()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'language.getList', [
            'search' => 'fr',
            'filter' => [
            'n' => 2,
            'p' => 1
            ]
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 2);
        $this->assertEquals(count($data['result']['list']), 2);
        $this->assertEquals(count($data['result']['list'][0]), 2);
        $this->assertEquals($data['result']['list'][0]['id'], 144);
        $this->assertEquals($data['result']['list'][0]['libelle'], "French");
        $this->assertEquals(count($data['result']['list'][1]), 2);
        $this->assertEquals($data['result']['list'][1]['id'], 151);
        $this->assertEquals($data['result']['list'][1]['libelle'], "Friulian");
        $this->assertEquals($data['result']['count'], 2);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }


    /**
     * @depends testCanAddUser
     */
    public function testLogout()
    {
        $this->mockRbac();

        $this->setIdentity(3);
        $data = $this->jsonRpc('user.logout', []);

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], true);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }
    
    
        
    /**
     * @depends testCanAddUser
     */
    public function testRegisterFcm2()
    {
        $data = $this->jsonRpc('user.login', ['user' => 'crobert@thestudnet.com','password' => 'thestudnet']);
        $this->reset();
        $this->setIdentity(3);
        $data = $this->jsonRpc(
            'user.registerFcm', [
            'uuid' => 3,
            'token' => 'azertyuiop2',
            'package' => 'azertyuiop2'
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    /**
     * @TODO Check test
     * @depends testCanAddUser
     *
    public function testUserDeleteError($id)
    {
        $this->setIdentity(3,2);
        $data = $this->jsonRpc('user.delete', ['id' => $id]);
        
        $this->printCreateTest($data);
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -38003); 
        $this->assertEquals($data['error']['message'] , "Unauthorized operation user.delete"); 
        $this->assertEquals($data['error']['data'] , null); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }*/
    
      /**
     * @depends testCanAddUser
     */
    public function testLinkedinSignInError($id)
    {
        
        
        $this->mockRbac();
        $this->mockLinkedin();
        $this->mockLibrary();
        
        $data = $this->jsonRpc('user.linkedinSignIn', 
            ['account_token' => 'token3',  
                'code' => 'code']);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -32000); 
        $this->assertEquals(!empty($data['error']['message']) , true); 
   }
   
      /**
     * @depends testCanAddUser
     */
    public function testLinkedinSignInError2($id)
    {
        
        
        $this->mockRbac();
        $this->mockLinkedin(1);
        $this->mockLibrary();
        
        $data = $this->jsonRpc('user.linkedinSignIn', 
            ['account_token' => 'token3',  
                'code' => 'code']);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -32000); 
        $this->assertEquals(!empty($data['error']['message']) , true); 
   }
    
    
     
      /**
     * @depends testCanAddUser
     */
    public function testLinkedinSignInWhenConnected($id)
    {
       
        $this->reset();
        $this->mockRbac();
        $this->mockLinkedin("NewID");
        $this->mockLibrary();
        $this->mockIdentity([
            'id' => 1,
            'firstname' => 'Paul',
            'avatar' => 'avatar',
            'lastname' => 'BOUSSEKEY',
            'nickname' => '',
            'email' => 'pboussekey@thestudnet.com',
            'created_date' => '01-01-1970',
            'token' => 'token',
            'organization_id' => 1,
            'roles' => [2 => 'user']
        ]);
        $data = $this->jsonRpc('user.linkedinSignIn', 
            ['code' => 'newcode']);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 11); 
        $this->assertEquals($data['result']['id'] , 1); 
        $this->assertEquals($data['result']['firstname'] , "Paul"); 
        $this->assertEquals($data['result']['avatar'] , "avatar"); 
        $this->assertEquals($data['result']['lastname'] , "BOUSSEKEY"); 
        $this->assertEquals($data['result']['nickname'] , ""); 
        $this->assertEquals($data['result']['email'] , "pboussekey@thestudnet.com"); 
        $this->assertEquals($data['result']['created_date'] , "01-01-1970"); 
        $this->assertEquals($data['result']['token'] , "token"); 
        $this->assertEquals($data['result']['organization_id'] , 1); 
        $this->assertEquals(count($data['result']['roles']) , 1); 
        $this->assertEquals($data['result']['roles'][2] , "user"); 
        $this->assertEquals($data['result']['has_linkedin'] , true); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 


    }
    
    
     /**
     * @depends testCanAddUser
     * @TODO check organization_id null
     */
    public function testLinkedinSignIn($id)
    {
        $this->mockRbac();
        $data = $this->jsonRpc('preregistration.add', 
            ['account_token' => 'token3', 'firstname' => '', 'lastname' => '', 'email' => 'contact@paul-boussekey.com', 'organization_id' => 1, 'user_id' => 8]);
        
        $this->reset();
        
        $this->mockRbac();
        $this->mockLinkedin();
        $this->mockLibrary();
        $data = $this->jsonRpc('user.linkedinSignIn', 
            ['account_token' => 'token3',  
                'code' => 'code']);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 18); 
        $this->assertEquals($data['result']['id'] , 8); 
        $this->assertEquals(!empty($data['result']['token']) , true); 
        $this->assertEquals(!empty($data['result']['created_date']) , true); 
        $this->assertEquals($data['result']['firstname'] , "Paul"); 
        $this->assertEquals($data['result']['lastname'] , "BOUSSEKEY"); 
        $this->assertEquals($data['result']['nickname'] , null); 
        $this->assertEquals($data['result']['suspension_date'] , null); 
        $this->assertEquals($data['result']['suspension_reason'] , null); 
        $this->assertEquals($data['result']['organization_id'] , null); 
        $this->assertEquals($data['result']['email'] , "contact@paul-boussekey.com"); 
        $this->assertEquals($data['result']['avatar'] , "token"); 
        $this->assertEquals($data['result']['expiration_date'] , null); 
        $this->assertEquals($data['result']['has_linkedin'] , true); 
        $this->assertEquals($data['result']['cgu_accepted'] , 0); 
        $this->assertEquals($data['result']['swap_email'] , null);
        $this->assertEquals(count($data['result']['roles']) , 1); 
        $this->assertEquals($data['result']['roles'][2] , "user"); 
        $this->assertEquals(!empty($data['result']['wstoken']) , true); 
        $this->assertEquals(!empty($data['result']['fbtoken']) , true); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 






    }
  
    /**
     * @depends testCanAddUser
     * @TODO check organization_id null
     */
    public function testLinkedinLogIn($id)
    {
        $this->mockRbac();
      
        $data = $this->jsonRpc('user.linkedinLogIn', 
            ['linkedin_id' => 'ID']);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 18); 
        $this->assertEquals($data['result']['id'] , 8); 
        $this->assertEquals(!empty($data['result']['token']) , true); 
        $this->assertEquals(!empty($data['result']['created_date']) , true); 
        $this->assertEquals($data['result']['firstname'] , "Paul"); 
        $this->assertEquals($data['result']['lastname'] , "BOUSSEKEY"); 
        $this->assertEquals($data['result']['nickname'] , null); 
        $this->assertEquals($data['result']['suspension_date'] , null); 
        $this->assertEquals($data['result']['suspension_reason'] , null); 
        $this->assertEquals($data['result']['organization_id'] , null); 
        $this->assertEquals($data['result']['email'] , "contact@paul-boussekey.com"); 
        $this->assertEquals($data['result']['avatar'] , "token"); 
        $this->assertEquals($data['result']['expiration_date'] , null); 
        $this->assertEquals($data['result']['has_linkedin'] , true); 
        $this->assertEquals($data['result']['cgu_accepted'] , 0); 
        $this->assertEquals($data['result']['swap_email'] , null);
        $this->assertEquals(count($data['result']['roles']) , 1); 
        $this->assertEquals($data['result']['roles'][2] , "user"); 
        $this->assertEquals(!empty($data['result']['wstoken']) , true); 
        $this->assertEquals(!empty($data['result']['fbtoken']) , true); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 
    }
    
    
    /**
     * @depends testCanAddUser
     */
    public function testLinkedinLogInError($id)
    {
        $this->mockRbac();
      
        $data = $this->jsonRpc('user.linkedinLogIn', 
            ['linkedin_id' => 'unknowntoken']);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -32000); 
        $this->assertEquals(!empty($data['error']['message']) , true); 

    }
    
    public function testLinkedinLogInExternal()
    {
        $this->setIdentity(1,1);
        $data = $this->jsonRpc('user.update', ['id' => 8,'roles' => 'external']);
        
        $this->reset();
        $this->mockRbac();
        $data = $this->jsonRpc('user.linkedinLogIn', 
            ['linkedin_id' => 'ID']);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -32000); 
        $this->assertEquals(!empty($data['error']['message']) , true); 


    }
    
    
     public function testLinkedinLogInSuspendedUser()
    {
        $this->setIdentity(1,1);
        $data = $this->jsonRpc('user.suspend', ['id' => 8,'suspend' => 1]);
        
        $this->reset();
        $this->mockRbac();
        $data = $this->jsonRpc('user.linkedinLogIn', 
            ['linkedin_id' => 'ID']);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -32031); 
        $this->assertEquals($data['error']['message'] , ""); 
        $this->assertEquals($data['error']['data'] , null); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }
    
    /**
     * @depends testCanAddUser
     */
    public function testUserDelete($id)
    {
        $this->setIdentity(3,1);
        $data = $this->jsonRpc('user.delete', ['id' => 8]);
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 1); 
        $this->assertEquals($data['result'][8] , 1); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }
    
     /**
     * @depends testCanAddUser
     */
    public function testLinkedinSignIn2($id)
    {
        $this->mockRbac();
        $data = $this->jsonRpc('preregistration.add', 
            ['account_token' => 'newtoken', 'firstname' => '', 'lastname' => '', 'email' => 'newuser@thestudnet.com', 'organization_id' => 1]);
        
        $this->reset();
        
        $this->mockRbac();
        $this->mockLinkedin();
        $this->mockLibrary();
        
        $data = $this->jsonRpc('user.linkedinSignIn', 
            ['account_token' => 'newtoken',  
                'code' => 'code']);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 18); 
        $this->assertEquals($data['result']['id'] , 13); 
        $this->assertEquals(!empty($data['result']['token']) , true); 
        $this->assertEquals(!empty($data['result']['created_date']) , true); 
        $this->assertEquals($data['result']['firstname'] , "Paul"); 
        $this->assertEquals($data['result']['lastname'] , "BOUSSEKEY"); 
        $this->assertEquals($data['result']['nickname'] , null); 
        $this->assertEquals($data['result']['suspension_date'] , null); 
        $this->assertEquals($data['result']['suspension_reason'] , null); 
        $this->assertEquals($data['result']['organization_id'] , 1); 
        $this->assertEquals($data['result']['email'] , "newuser@thestudnet.com"); 
        $this->assertEquals($data['result']['avatar'] , null); 
        $this->assertEquals($data['result']['expiration_date'] , null); 
        $this->assertEquals($data['result']['has_linkedin'] , true); 
        $this->assertEquals($data['result']['cgu_accepted'] , 0); 
        $this->assertEquals($data['result']['swap_email'] , null);
        $this->assertEquals(count($data['result']['roles']) , 1); 
        $this->assertEquals($data['result']['roles'][2] , "user"); 
        $this->assertEquals(!empty($data['result']['wstoken']) , true); 
        $this->assertEquals(!empty($data['result']['fbtoken']) , true); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 


    }
    
   
    
    
   
}
