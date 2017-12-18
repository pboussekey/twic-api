<?php
namespace ModuleTest\Api\Service;

use ModuleTest\Api\AbstractService;

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
            'users' => [
              ['user_id' => 1,'role' => 'admin', 'state' => 'member'],
              ['user_id' => 2,'role' => 'admin', 'state' => 'member'],
              ['user_id' => 3,'role' => 'admin', 'state' => 'member'],
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
    
       /**
     * @depends testInit
     **/
    public function testCanAddUserError($page_id)
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

    }

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
        $this->assertEquals($data['result'], 8);
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
     * @depends testCanAddUser
     */
    public function testLogin()
    {
        $this->mockRbac();


        $data = $this->jsonRpc('user.login', ['user' => 'crobert@thestudnet.com','password' => 'thestudnet']);
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 16);
        $this->assertEquals($data['result']['id'], 3);
        $this->assertEquals(!empty($data['result']['token']), true);
        $this->assertEquals($data['result']['created_date'], null);
        $this->assertEquals($data['result']['firstname'], "Christophe");
        $this->assertEquals($data['result']['lastname'], "Robert");
        $this->assertEquals($data['result']['nickname'], null);
        $this->assertEquals($data['result']['suspension_date'], null);
        $this->assertEquals($data['result']['suspension_reason'], null);
        $this->assertEquals($data['result']['organization_id'], 1);
        $this->assertEquals($data['result']['email'], "crobert@thestudnet.com");
        $this->assertEquals($data['result']['avatar'], null);
        $this->assertEquals($data['result']['expiration_date'], null);
        $this->assertEquals($data['result']['has_linkedin'], false);
        $this->assertEquals(count($data['result']['roles']), 1);
        $this->assertEquals($data['result']['roles'][2], "user");
        $this->assertEquals(!empty($data['result']['wstoken']), true);
        $this->assertEquals(!empty($data['result']['fbtoken']), true);
        $this->assertEquals($data['jsonrpc'], 2.0);

        return $data['result']['token'];
    }
    
    
     /**
     * @depends testCanAddUser
     */
    public function testSendPwd($id)
    {
        $this->mockRbac();


        $data = $this->jsonRpc('user.sendPassword', ['id' => $id]);
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals($data['result'] , 0); 
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


    }
    
        /**
     * @depends testCanAddUser
     */
    public function testSignIn($id)
    {
        $this->mockRbac();


        $data = $this->jsonRpc('user.signIn', 
            ['account_token' => 'token',  'password' => 'thestudnet']);
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 16); 
        $this->assertEquals($data['result']['id'] , 8); 
        $this->assertEquals(!empty($data['result']['token']) , true); 
        $this->assertEquals(!empty($data['result']['created_date']) , true); 
        $this->assertEquals($data['result']['firstname'] , "Christophe"); 
        $this->assertEquals($data['result']['lastname'] , "Robert"); 
        $this->assertEquals($data['result']['nickname'] , null); 
        $this->assertEquals($data['result']['suspension_date'] , null); 
        $this->assertEquals($data['result']['suspension_reason'] , null); 
        $this->assertEquals($data['result']['organization_id'] , 1); 
        $this->assertEquals($data['result']['email'] , "crobertr@thestudnet.com"); 
        $this->assertEquals($data['result']['avatar'] , "un_token"); 
        $this->assertEquals($data['result']['expiration_date'] , null); 
        $this->assertEquals($data['result']['has_linkedin'] , false); 
        $this->assertEquals(count($data['result']['roles']) , 1); 
        $this->assertEquals($data['result']['roles'][2] , "user"); 
        $this->assertEquals(!empty($data['result']['wstoken']) , true); 
        $this->assertEquals(!empty($data['result']['fbtoken']) , true); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 



    }
    
     /**
     * @depends testCanAddExternal
     */
    public function testSendPwd2($id)
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
    public function testSignIn2($id)
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
        $data = $this->jsonRpc('user.suspend', ['id' => 8,'suspend' => true]);
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
            'birthdate' => '01-01-1970',
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
        $this->assertEquals($data['error']['code'] , -38001); 
        $this->assertEquals($data['error']['message'] , "duplicate email"); 
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
        $this->assertEquals(count($data['result'][8]) , 21); 
        $this->assertEquals(count($data['result'][8]['origin']) , 2); 
        $this->assertEquals($data['result'][8]['origin']['id'] , 1); 
        $this->assertEquals($data['result'][8]['origin']['short_name'] , "Afghanistan"); 
        $this->assertEquals(count($data['result'][8]['nationality']) , 2); 
        $this->assertEquals($data['result'][8]['nationality']['id'] , 1); 
        $this->assertEquals($data['result'][8]['nationality']['short_name'] , "Afghanistan"); 
        $this->assertEquals($data['result'][8]['gender'] , "m"); 
        $this->assertEquals($data['result'][8]['contact_state'] , 0); 
        $this->assertEquals($data['result'][8]['contacts_count'] , 0); 
        $this->assertEquals(count($data['result'][8]['address']) , 14); 
        $this->assertEquals(count($data['result'][8]['address']['city']) , 1); 
        $this->assertEquals($data['result'][8]['address']['city']['name'] , "Villefontaine"); 
        $this->assertEquals(count($data['result'][8]['address']['division']) , 2); 
        $this->assertEquals($data['result'][8]['address']['division']['id'] , 54); 
        $this->assertEquals($data['result'][8]['address']['division']['name'] , "Auvergne-Rhône-Alpes"); 
        $this->assertEquals($data['result'][8]['address']['country'] , null); 
        $this->assertEquals($data['result'][8]['address']['id'] , 2); 
        $this->assertEquals($data['result'][8]['address']['street_no'] , 11); 
        $this->assertEquals($data['result'][8]['address']['street_type'] , null); 
        $this->assertEquals($data['result'][8]['address']['street_name'] , "Allée des Chênes"); 
        $this->assertEquals($data['result'][8]['address']['longitude'] , 5.1787445); 
        $this->assertEquals($data['result'][8]['address']['latitude'] , 45.601569); 
        $this->assertEquals($data['result'][8]['address']['door'] , null); 
        $this->assertEquals($data['result'][8]['address']['building'] , null); 
        $this->assertEquals($data['result'][8]['address']['apartment'] , null); 
        $this->assertEquals($data['result'][8]['address']['floor'] , null); 
        $this->assertEquals($data['result'][8]['address']['timezone'] , "Europe/Paris"); 
        $this->assertEquals($data['result'][8]['id'] , 8); 
        $this->assertEquals($data['result'][8]['firstname'] , "Jean"); 
        $this->assertEquals($data['result'][8]['lastname'] , "Paul"); 
        $this->assertEquals($data['result'][8]['nickname'] , "JP"); 
        $this->assertEquals($data['result'][8]['email'] , "jpaul@thestudnet.com"); 
        $this->assertEquals($data['result'][8]['birth_date'] , null); 
        $this->assertEquals($data['result'][8]['position'] , "une position new"); 
        $this->assertEquals($data['result'][8]['organization_id'] , 1); 
        $this->assertEquals($data['result'][8]['interest'] , "un interet new"); 
        $this->assertEquals($data['result'][8]['avatar'] , "un_token_new"); 
        $this->assertEquals($data['result'][8]['has_email_notifier'] , 1); 
        $this->assertEquals($data['result'][8]['background'] , null); 
        $this->assertEquals($data['result'][8]['ambassador'] , null); 
        $this->assertEquals($data['result'][8]['email_sent'] , 0); 
        $this->assertEquals(count($data['result'][8]['roles']) , 1); 
        $this->assertEquals($data['result'][8]['roles'][0] , "user"); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }
    
       /**
     * @depends testCanAddUser
     */
    public function testSuspend()
    {
        $this->mockRbac();


        $this->setIdentity(1,1);
        $data = $this->jsonRpc('user.suspend', ['id' => 8,'suspend' => 1]);
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

        $data = $this->jsonRpc('user.login', ['user' => 'crobertr@thestudnet.com','password' => 'thestudnet']);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -32033); 
        $this->assertEquals(!empty($data['error']['message']) , true); 
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
    

    /**
     * @TODO MOCK
     * @depends testCanAddUser
     */
    public function testSendPassword($id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('user.sendPassword', ['id' => $id]);

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 0);
        $this->assertEquals($data['jsonrpc'], 2.0);
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

        $data = $this->jsonRpc('contact.getRequestsCount', []);
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 1);
        $this->assertEquals(count($data['result'][0]), 2);
        $this->assertEquals(!empty($data['result'][0]['request_date']), true);
        $this->assertEquals($data['result'][0]['requested'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
        
        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc('contact.getAcceptedCount', []);
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
            //  'page_id' => 1
            ]
        );
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 2); 
        $this->assertEquals(count($data['result']['list']) , 2); 
        $this->assertEquals($data['result']['list'][0] , 9); 
        $this->assertEquals($data['result']['list'][1] , 3); 
        $this->assertEquals($data['result']['count'] , 2); 
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
     * @depends testCanAddUser
     */
    public function testUserDeleteError($id)
    {
        $this->setIdentity(3,2);
        $data = $this->jsonRpc('user.delete', ['id' => $id]);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -38003); 
        $this->assertEquals($data['error']['message'] , "Unauthorized operation user.delete"); 
        $this->assertEquals($data['error']['data'] , null); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }

    /**
     * @depends testCanAddUser
     */
    public function testUserDelete($id)
    {
        $this->setIdentity(3,1);
        $data = $this->jsonRpc('user.delete', ['id' => $id]);
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 1); 
        $this->assertEquals($data['result'][8] , 1); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }
   
}
