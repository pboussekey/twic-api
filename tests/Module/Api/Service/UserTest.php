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
            'avatar' => 'un_token']
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
        $this->assertEquals($data['error']['code'] , -32031); 
        $this->assertEquals($data['error']['message'] , ""); 
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
            'address' => [
            "street_no"   => "11",
            "street_name" => "Allée des Chênes",
            "city"        => ["name" => "Villefontaine", "libelle" => "VILLEFONTAINE"],
            "division"    => ["name" => "Auvergne-Rhône-Alpes"],
            "country"     => ["short_name" => "France"],
            "latitude"    => 45.601569,
            "longitude"   => 5.178744499999993,
            ]]
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
    public function testUserGet($id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'user.get', [
            'id' => [$id]
            ]
        );
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 1);
        $this->assertEquals(count($data['result'][8]), 21);
        $this->assertEquals(count($data['result'][8]['origin']), 2);
        $this->assertEquals($data['result'][8]['origin']['id'], 1);
        $this->assertEquals($data['result'][8]['origin']['short_name'], "Afghanistan");
        $this->assertEquals(count($data['result'][8]['nationality']), 2);
        $this->assertEquals($data['result'][8]['nationality']['id'], 1);
        $this->assertEquals($data['result'][8]['nationality']['short_name'], "Afghanistan");
        $this->assertEquals($data['result'][8]['gender'], "m");
        $this->assertEquals($data['result'][8]['contact_state'], 0);
        $this->assertEquals($data['result'][8]['contacts_count'], 0);
        $this->assertEquals(count($data['result'][8]['address']), 14);
        $this->assertEquals(count($data['result'][8]['address']['city']), 1);
        $this->assertEquals($data['result'][8]['address']['city']['name'], "Villefontaine");
        $this->assertEquals(count($data['result'][8]['address']['division']), 2);
        $this->assertEquals($data['result'][8]['address']['division']['id'], 54);
        $this->assertEquals($data['result'][8]['address']['division']['name'], "Auvergne-Rhône-Alpes");
        $this->assertEquals($data['result'][8]['address']['country'], null);
        $this->assertEquals($data['result'][8]['address']['id'], 1);
        $this->assertEquals($data['result'][8]['address']['street_no'], 11);
        $this->assertEquals($data['result'][8]['address']['street_type'], null);
        $this->assertEquals($data['result'][8]['address']['street_name'], "Allée des Chênes");
        $this->assertEquals($data['result'][8]['address']['longitude'], 5.1787445);
        $this->assertEquals($data['result'][8]['address']['latitude'], 45.601569);
        $this->assertEquals($data['result'][8]['address']['door'], null);
        $this->assertEquals($data['result'][8]['address']['building'], null);
        $this->assertEquals($data['result'][8]['address']['apartment'], null);
        $this->assertEquals($data['result'][8]['address']['floor'], null);
        $this->assertEquals($data['result'][8]['address']['timezone'], "Europe/Paris");
        $this->assertEquals($data['result'][8]['id'], 8);
        $this->assertEquals($data['result'][8]['firstname'], "Jean");
        $this->assertEquals($data['result'][8]['lastname'], "Paul");
        $this->assertEquals($data['result'][8]['nickname'], "JP");
        $this->assertEquals($data['result'][8]['email'], "jpaul@thestudnet.com");
        $this->assertEquals($data['result'][8]['birth_date'], null);
        $this->assertEquals($data['result'][8]['position'], "une position new");
        $this->assertEquals($data['result'][8]['organization_id'], 1);
        $this->assertEquals($data['result'][8]['interest'], "un interet new");
        $this->assertEquals($data['result'][8]['avatar'], "un_token_new");
        $this->assertEquals($data['result'][8]['has_email_notifier'], 1);
        $this->assertEquals($data['result'][8]['background'], null);
        $this->assertEquals($data['result'][8]['ambassador'], null);
        $this->assertEquals($data['result'][8]['email_sent'], 0);
        $this->assertEquals(count($data['result'][8]['roles']), 1);
        $this->assertEquals($data['result'][8]['roles'][0], "user");
        $this->assertEquals($data['jsonrpc'], 2.0);
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

    public function testCanImport()
    {
        $this->setIdentity(1, 1);
        $data = $this->jsonRpc(
            'page.add', [
            'title' => 'super title',
            'logo' => 'logo',
            'background' => 'background',
            'description' => 'description',
            'confidentiality' => 1,
            'type' => 'school',
            'admission' => 'free',
            'location' => 'location'
            ]
        );

        $this->reset();

        $this->setIdentity(5, 1);
        $data = $this->jsonRpc(
            'user.import', [
            'data' => [[
            'firstname' => 'Christopher',
            'lastname' => 'Robert',
            'nickname' => 'mnickname',
            'email' => 'crobertr@thestudnet.com',
            'uid' => 'uid',
            'role' => 'user'
            ]],
            'page_id' => $data['id'],
            ]
        );
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 0);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }
    /*
    
        /**
         * @depends testCanAddUser
         */
    /*public function testLostPassword($id)
    {
        $this->mockRbac();
        $data = $this->jsonRpc('user.lostPassword', array('email' => 'jpaul@thestudnet.com'));

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    /**
     * @depends testCanAddUser
     */
    /*public function testGetMulti($id)
    {
        $this->setIdentity(1);

        $ar = [['method' => 'user.get','id' => 1,'params' => ['id' => 1]],['method' => 'user.get','id' => 2,'params' => ['id' => 2]]];

        $data = $this->jsonRpcRequest($ar);

        $this->assertEquals(count($data), 2);
        $this->assertEquals(count($data[0]), 3);
        $this->assertEquals($data[0]['id'], 1);
        $this->assertEquals(count($data[0]['result']), 21);
        $this->assertEquals(count($data[0]['result']['origin']), 2);
        $this->assertEquals($data[0]['result']['origin']['id'], null);
        $this->assertEquals($data[0]['result']['origin']['short_name'], null);
        $this->assertEquals(count($data[0]['result']['nationality']), 2);
        $this->assertEquals($data[0]['result']['nationality']['id'], null);
        $this->assertEquals($data[0]['result']['nationality']['short_name'], null);
        $this->assertEquals($data[0]['result']['gender'], null);
        $this->assertEquals($data[0]['result']['contact_state'], 0);
        $this->assertEquals($data[0]['result']['contacts_count'], 7);
        $this->assertEquals(count($data[0]['result']['school']), 5);
        $this->assertEquals($data[0]['result']['school']['id'], 1);
        $this->assertEquals($data[0]['result']['school']['name'], "Morbi Corporation");
        $this->assertEquals($data[0]['result']['school']['short_name'], "turpis");
        $this->assertEquals($data[0]['result']['school']['logo'], null);
        $this->assertEquals($data[0]['result']['school']['background'], null);
        $this->assertEquals($data[0]['result']['id'], 1);
        $this->assertEquals($data[0]['result']['firstname'], "Paul");
        $this->assertEquals($data[0]['result']['lastname'], "Boussekey");
        $this->assertEquals($data[0]['result']['nickname'], null);
        $this->assertEquals($data[0]['result']['email'], "pboussekey@thestudnet.com");
        $this->assertEquals($data[0]['result']['birth_date'], null);
        $this->assertEquals($data[0]['result']['position'], null);
        $this->assertEquals($data[0]['result']['school_id'], 1);
        $this->assertEquals($data[0]['result']['interest'], null);
        $this->assertEquals($data[0]['result']['avatar'], null);
        $this->assertEquals($data[0]['result']['has_email_notifier'], 1);
        $this->assertEquals($data[0]['result']['background'], null);
        $this->assertEquals($data[0]['result']['ambassador'], null);
        $this->assertEquals(count($data[0]['result']['roles']), 1);
        $this->assertEquals($data[0]['result']['roles'][0], "super_admin");
        $this->assertEquals(count($data[0]['result']['program']), 0);
        $this->assertEquals($data[0]['jsonrpc'], 2.0);
        $this->assertEquals(count($data[1]), 3);
        $this->assertEquals($data[1]['id'], 2);
        $this->assertEquals(count($data[1]['result']), 21);
        $this->assertEquals(count($data[1]['result']['origin']), 2);
        $this->assertEquals($data[1]['result']['origin']['id'], null);
        $this->assertEquals($data[1]['result']['origin']['short_name'], null);
        $this->assertEquals(count($data[1]['result']['nationality']), 2);
        $this->assertEquals($data[1]['result']['nationality']['id'], null);
        $this->assertEquals($data[1]['result']['nationality']['short_name'], null);
        $this->assertEquals($data[1]['result']['gender'], null);
        $this->assertEquals($data[1]['result']['contact_state'], 3);
        $this->assertEquals($data[1]['result']['contacts_count'], 7);
        $this->assertEquals(count($data[1]['result']['school']), 5);
        $this->assertEquals($data[1]['result']['school']['id'], 1);
        $this->assertEquals($data[1]['result']['school']['name'], "Morbi Corporation");
        $this->assertEquals($data[1]['result']['school']['short_name'], "turpis");
        $this->assertEquals($data[1]['result']['school']['logo'], null);
        $this->assertEquals($data[1]['result']['school']['background'], null);
        $this->assertEquals($data[1]['result']['id'], 2);
        $this->assertEquals($data[1]['result']['firstname'], "Xuan-Anh");
        $this->assertEquals($data[1]['result']['lastname'], "Hoang");
        $this->assertEquals($data[1]['result']['nickname'], null);
        $this->assertEquals($data[1]['result']['email'], "xhoang@thestudnet.com");
        $this->assertEquals($data[1]['result']['birth_date'], null);
        $this->assertEquals($data[1]['result']['position'], null);
        $this->assertEquals($data[1]['result']['school_id'], 1);
        $this->assertEquals($data[1]['result']['interest'], null);
        $this->assertEquals($data[1]['result']['avatar'], null);
        $this->assertEquals($data[1]['result']['has_email_notifier'], 1);
        $this->assertEquals($data[1]['result']['background'], null);
        $this->assertEquals($data[1]['result']['ambassador'], null);
        $this->assertEquals(count($data[1]['result']['roles']), 1);
        $this->assertEquals($data[1]['result']['roles'][0], "admin");
        $this->assertEquals(count($data[1]['result']['program']), 0);
        $this->assertEquals($data[1]['jsonrpc'], 2.0);
    }

    /**
     * @depends testCanAddUser
     */
    /*public function testGetListAttendees($id)
    {
        $this->setIdentity(1);

        $data = $this->jsonRpc('user.getListAttendees', [
           // 'course' => [1],
           // 'program' => [1],
           'school' => [1],
           'exclude_course' => [2,3],
           'exclude_program' => [99],
           'exclude_user' => [2]
        ]);

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 7);
        $this->assertEquals(count($data['result'][0]), 8);
        $this->assertEquals(count($data['result'][0]['school']), 5);
        $this->assertEquals($data['result'][0]['school']['id'], 1);
        $this->assertEquals($data['result'][0]['school']['name'], "Morbi Corporation");
        $this->assertEquals($data['result'][0]['school']['short_name'], "turpis");
        $this->assertEquals($data['result'][0]['school']['logo'], null);
        $this->assertEquals($data['result'][0]['school']['background'], null);
        $this->assertEquals(count($data['result'][0]['roles']), 1);
        $this->assertEquals($data['result'][0]['roles'][0], "student");
        $this->assertEquals($data['result'][0]['id'], 11);
        $this->assertEquals($data['result'][0]['firstname'], "Jean");
        $this->assertEquals($data['result'][0]['lastname'], "Paul");
        $this->assertEquals($data['result'][0]['nickname'], null);
        $this->assertEquals($data['result'][0]['avatar'], "un_token_new");
        $this->assertEquals($data['result'][0]['sis'], null);
        $this->assertEquals(count($data['result'][1]), 8);
        $this->assertEquals(count($data['result'][1]['school']), 5);
        $this->assertEquals($data['result'][1]['school']['id'], 1);
        $this->assertEquals($data['result'][1]['school']['name'], "Morbi Corporation");
        $this->assertEquals($data['result'][1]['school']['short_name'], "turpis");
        $this->assertEquals($data['result'][1]['school']['logo'], null);
        $this->assertEquals($data['result'][1]['school']['background'], null);
        $this->assertEquals(count($data['result'][1]['roles']), 1);
        $this->assertEquals($data['result'][1]['roles'][0], "super_admin");
        $this->assertEquals($data['result'][1]['id'], 7);
        $this->assertEquals($data['result'][1]['firstname'], "Arthur");
        $this->assertEquals($data['result'][1]['lastname'], "Flachs");
        $this->assertEquals($data['result'][1]['nickname'], null);
        $this->assertEquals($data['result'][1]['avatar'], null);
        $this->assertEquals($data['result'][1]['sis'], null);
        $this->assertEquals(count($data['result'][2]), 8);
        $this->assertEquals(count($data['result'][2]['school']), 5);
        $this->assertEquals($data['result'][2]['school']['id'], 1);
        $this->assertEquals($data['result'][2]['school']['name'], "Morbi Corporation");
        $this->assertEquals($data['result'][2]['school']['short_name'], "turpis");
        $this->assertEquals($data['result'][2]['school']['logo'], null);
        $this->assertEquals($data['result'][2]['school']['background'], null);
        $this->assertEquals(count($data['result'][2]['roles']), 1);
        $this->assertEquals($data['result'][2]['roles'][0], "admin");
        $this->assertEquals($data['result'][2]['id'], 6);
        $this->assertEquals($data['result'][2]['firstname'], "Guillaume");
        $this->assertEquals($data['result'][2]['lastname'], "Masmejean");
        $this->assertEquals($data['result'][2]['nickname'], null);
        $this->assertEquals($data['result'][2]['avatar'], null);
        $this->assertEquals($data['result'][2]['sis'], null);
        $this->assertEquals(count($data['result'][3]), 8);
        $this->assertEquals(count($data['result'][3]['school']), 5);
        $this->assertEquals($data['result'][3]['school']['id'], 1);
        $this->assertEquals($data['result'][3]['school']['name'], "Morbi Corporation");
        $this->assertEquals($data['result'][3]['school']['short_name'], "turpis");
        $this->assertEquals($data['result'][3]['school']['logo'], null);
        $this->assertEquals($data['result'][3]['school']['background'], null);
        $this->assertEquals(count($data['result'][3]['roles']), 1);
        $this->assertEquals($data['result'][3]['roles'][0], "instructor");
        $this->assertEquals($data['result'][3]['id'], 5);
        $this->assertEquals($data['result'][3]['firstname'], "Sébastien");
        $this->assertEquals($data['result'][3]['lastname'], "Sayegh");
        $this->assertEquals($data['result'][3]['nickname'], null);
        $this->assertEquals($data['result'][3]['avatar'], null);
        $this->assertEquals($data['result'][3]['sis'], null);
        $this->assertEquals(count($data['result'][4]), 8);
        $this->assertEquals(count($data['result'][4]['school']), 5);
        $this->assertEquals($data['result'][4]['school']['id'], 1);
        $this->assertEquals($data['result'][4]['school']['name'], "Morbi Corporation");
        $this->assertEquals($data['result'][4]['school']['short_name'], "turpis");
        $this->assertEquals($data['result'][4]['school']['logo'], null);
        $this->assertEquals($data['result'][4]['school']['background'], null);
        $this->assertEquals(count($data['result'][4]['roles']), 1);
        $this->assertEquals($data['result'][4]['roles'][0], "student");
        $this->assertEquals($data['result'][4]['id'], 4);
        $this->assertEquals($data['result'][4]['firstname'], "Salim");
        $this->assertEquals($data['result'][4]['lastname'], "Bendacha");
        $this->assertEquals($data['result'][4]['nickname'], null);
        $this->assertEquals($data['result'][4]['avatar'], null);
        $this->assertEquals($data['result'][4]['sis'], null);
        $this->assertEquals(count($data['result'][5]), 8);
        $this->assertEquals(count($data['result'][5]['school']), 5);
        $this->assertEquals($data['result'][5]['school']['id'], 1);
        $this->assertEquals($data['result'][5]['school']['name'], "Morbi Corporation");
        $this->assertEquals($data['result'][5]['school']['short_name'], "turpis");
        $this->assertEquals($data['result'][5]['school']['logo'], null);
        $this->assertEquals($data['result'][5]['school']['background'], null);
        $this->assertEquals(count($data['result'][5]['roles']), 1);
        $this->assertEquals($data['result'][5]['roles'][0], "academic");
        $this->assertEquals($data['result'][5]['id'], 3);
        $this->assertEquals($data['result'][5]['firstname'], "Christophe");
        $this->assertEquals($data['result'][5]['lastname'], "Robert");
        $this->assertEquals($data['result'][5]['nickname'], null);
        $this->assertEquals($data['result'][5]['avatar'], null);
        $this->assertEquals($data['result'][5]['sis'], null);
        $this->assertEquals(count($data['result'][6]), 8);
        $this->assertEquals(count($data['result'][6]['school']), 5);
        $this->assertEquals($data['result'][6]['school']['id'], 1);
        $this->assertEquals($data['result'][6]['school']['name'], "Morbi Corporation");
        $this->assertEquals($data['result'][6]['school']['short_name'], "turpis");
        $this->assertEquals($data['result'][6]['school']['logo'], null);
        $this->assertEquals($data['result'][6]['school']['background'], null);
        $this->assertEquals(count($data['result'][6]['roles']), 1);
        $this->assertEquals($data['result'][6]['roles'][0], "super_admin");
        $this->assertEquals($data['result'][6]['id'], 1);
        $this->assertEquals($data['result'][6]['firstname'], "Paul");
        $this->assertEquals($data['result'][6]['lastname'], "Boussekey");
        $this->assertEquals($data['result'][6]['nickname'], null);
        $this->assertEquals($data['result'][6]['avatar'], null);
        $this->assertEquals($data['result'][6]['sis'], null);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    public function testGetList()
    {
        $this->setIdentity(4);

        $data = $this->jsonRpc('user.getList', []);
        $this->assertEquals(count($data), 3);
        $this->assertEquals(count($data['result']), 2);
        $this->assertEquals(count($data['result']['list']), 8);
        $this->assertEquals(count($data['result']['list'][0]), 15);
        $this->assertEquals($data['result']['list'][0]['contact_state'], 3);
        $this->assertEquals($data['result']['list'][0]['contacts_count'], 7);
        $this->assertEquals(count($data['result']['list'][0]['school']), 5);
        $this->assertEquals($data['result']['list'][0]['school']['id'], 1);
        $this->assertEquals($data['result']['list'][0]['school']['name'], "Morbi Corporation");
        $this->assertEquals($data['result']['list'][0]['school']['short_name'], "turpis");
        $this->assertEquals($data['result']['list'][0]['school']['logo'], null);
        $this->assertEquals($data['result']['list'][0]['school']['background'], null);
        $this->assertEquals($data['result']['list'][0]['id'], 11);
        $this->assertEquals($data['result']['list'][0]['ambassador'], null);
        $this->assertEquals($data['result']['list'][0]['firstname'], "Jean");
        $this->assertEquals($data['result']['list'][0]['lastname'], "Paul");
        $this->assertEquals($data['result']['list'][0]['nickname'], null);
        $this->assertEquals($data['result']['list'][0]['email'], "jpaul@thestudnet.com");
        $this->assertEquals($data['result']['list'][0]['birth_date'], null);
        $this->assertEquals($data['result']['list'][0]['position'], "une position new");
        $this->assertEquals($data['result']['list'][0]['interest'], "un interet new");
        $this->assertEquals($data['result']['list'][0]['avatar'], "un_token_new");
        $this->assertEquals(count($data['result']['list'][0]['roles']), 1);
        $this->assertEquals($data['result']['list'][0]['roles'][0], "student");
        $this->assertEquals(count($data['result']['list'][0]['program']), 0);
        $this->assertEquals(count($data['result']['list'][1]), 15);
        $this->assertEquals($data['result']['list'][1]['contact_state'], 3);
        $this->assertEquals($data['result']['list'][1]['contacts_count'], 7);
        $this->assertEquals(count($data['result']['list'][1]['school']), 5);
        $this->assertEquals($data['result']['list'][1]['school']['id'], 1);
        $this->assertEquals($data['result']['list'][1]['school']['name'], "Morbi Corporation");
        $this->assertEquals($data['result']['list'][1]['school']['short_name'], "turpis");
        $this->assertEquals($data['result']['list'][1]['school']['logo'], null);
        $this->assertEquals($data['result']['list'][1]['school']['background'], null);
        $this->assertEquals($data['result']['list'][1]['id'], 7);
        $this->assertEquals($data['result']['list'][1]['firstname'], "Arthur");
        $this->assertEquals($data['result']['list'][1]['lastname'], "Flachs");
        $this->assertEquals($data['result']['list'][1]['nickname'], null);
        $this->assertEquals($data['result']['list'][1]['ambassador'], null);
        $this->assertEquals($data['result']['list'][1]['email'], "aflachs@thestudnet.com");
        $this->assertEquals($data['result']['list'][1]['birth_date'], null);
        $this->assertEquals($data['result']['list'][1]['position'], null);
        $this->assertEquals($data['result']['list'][1]['interest'], null);
        $this->assertEquals($data['result']['list'][1]['avatar'], null);
        $this->assertEquals(count($data['result']['list'][1]['roles']), 1);
        $this->assertEquals($data['result']['list'][1]['roles'][0], "super_admin");
        $this->assertEquals(count($data['result']['list'][1]['program']), 0);
        $this->assertEquals(count($data['result']['list'][2]), 15);
        $this->assertEquals($data['result']['list'][2]['contact_state'], 3);
        $this->assertEquals($data['result']['list'][2]['contacts_count'], 7);
        $this->assertEquals(count($data['result']['list'][2]['school']), 5);
        $this->assertEquals($data['result']['list'][2]['school']['id'], 1);
        $this->assertEquals($data['result']['list'][2]['school']['name'], "Morbi Corporation");
        $this->assertEquals($data['result']['list'][2]['school']['short_name'], "turpis");
        $this->assertEquals($data['result']['list'][2]['school']['logo'], null);
        $this->assertEquals($data['result']['list'][2]['school']['background'], null);
        $this->assertEquals($data['result']['list'][2]['id'], 6);
        $this->assertEquals($data['result']['list'][2]['firstname'], "Guillaume");
        $this->assertEquals($data['result']['list'][2]['lastname'], "Masmejean");
        $this->assertEquals($data['result']['list'][2]['nickname'], null);
        $this->assertEquals($data['result']['list'][2]['email'], "gmasmejean@thestudnet.com");
        $this->assertEquals($data['result']['list'][2]['birth_date'], null);
        $this->assertEquals($data['result']['list'][2]['position'], null);
        $this->assertEquals($data['result']['list'][2]['interest'], null);
        $this->assertEquals($data['result']['list'][2]['avatar'], null);
        $this->assertEquals(count($data['result']['list'][2]['roles']), 1);
        $this->assertEquals($data['result']['list'][2]['roles'][0], "admin");
        $this->assertEquals(count($data['result']['list'][2]['program']), 0);
        $this->assertEquals(count($data['result']['list'][3]), 15);
        $this->assertEquals($data['result']['list'][3]['contact_state'], 3);
        $this->assertEquals($data['result']['list'][3]['contacts_count'], 7);
        $this->assertEquals(count($data['result']['list'][3]['school']), 5);
        $this->assertEquals($data['result']['list'][3]['school']['id'], 1);
        $this->assertEquals($data['result']['list'][3]['school']['name'], "Morbi Corporation");
        $this->assertEquals($data['result']['list'][3]['school']['short_name'], "turpis");
        $this->assertEquals($data['result']['list'][3]['school']['logo'], null);
        $this->assertEquals($data['result']['list'][3]['school']['background'], null);
        $this->assertEquals($data['result']['list'][3]['id'], 5);
        $this->assertEquals($data['result']['list'][3]['firstname'], "Sébastien");
        $this->assertEquals($data['result']['list'][3]['lastname'], "Sayegh");
        $this->assertEquals($data['result']['list'][3]['nickname'], null);
        $this->assertEquals($data['result']['list'][3]['email'], "ssayegh@thestudnet.com");
        $this->assertEquals($data['result']['list'][3]['birth_date'], null);
        $this->assertEquals($data['result']['list'][3]['position'], null);
        $this->assertEquals($data['result']['list'][3]['interest'], null);
        $this->assertEquals($data['result']['list'][3]['avatar'], null);
        $this->assertEquals(count($data['result']['list'][3]['roles']), 1);
        $this->assertEquals($data['result']['list'][3]['roles'][0], "instructor");
        $this->assertEquals(count($data['result']['list'][3]['program']), 0);
        $this->assertEquals(count($data['result']['list'][4]), 15);
        $this->assertEquals($data['result']['list'][4]['contact_state'], 0);
        $this->assertEquals($data['result']['list'][4]['contacts_count'], 7);
        $this->assertEquals(count($data['result']['list'][4]['school']), 5);
        $this->assertEquals($data['result']['list'][4]['school']['id'], 1);
        $this->assertEquals($data['result']['list'][4]['school']['name'], "Morbi Corporation");
        $this->assertEquals($data['result']['list'][4]['school']['short_name'], "turpis");
        $this->assertEquals($data['result']['list'][4]['school']['logo'], null);
        $this->assertEquals($data['result']['list'][4]['school']['background'], null);
        $this->assertEquals($data['result']['list'][4]['id'], 4);
        $this->assertEquals($data['result']['list'][4]['firstname'], "Salim");
        $this->assertEquals($data['result']['list'][4]['lastname'], "Bendacha");
        $this->assertEquals($data['result']['list'][4]['nickname'], null);
        $this->assertEquals($data['result']['list'][4]['email'], "sbendacha@thestudnet.com");
        $this->assertEquals($data['result']['list'][4]['birth_date'], null);
        $this->assertEquals($data['result']['list'][4]['position'], null);
        $this->assertEquals($data['result']['list'][4]['interest'], null);
        $this->assertEquals($data['result']['list'][4]['avatar'], null);
        $this->assertEquals(count($data['result']['list'][4]['roles']), 1);
        $this->assertEquals($data['result']['list'][4]['roles'][0], "student");
        $this->assertEquals(count($data['result']['list'][4]['program']), 0);
        $this->assertEquals(count($data['result']['list'][5]), 15);
        $this->assertEquals($data['result']['list'][5]['contact_state'], 3);
        $this->assertEquals($data['result']['list'][5]['contacts_count'], 7);
        $this->assertEquals(count($data['result']['list'][5]['school']), 5);
        $this->assertEquals($data['result']['list'][5]['school']['id'], 1);
        $this->assertEquals($data['result']['list'][5]['school']['name'], "Morbi Corporation");
        $this->assertEquals($data['result']['list'][5]['school']['short_name'], "turpis");
        $this->assertEquals($data['result']['list'][5]['school']['logo'], null);
        $this->assertEquals($data['result']['list'][5]['school']['background'], null);
        $this->assertEquals($data['result']['list'][5]['id'], 3);
        $this->assertEquals($data['result']['list'][5]['firstname'], "Christophe");
        $this->assertEquals($data['result']['list'][5]['lastname'], "Robert");
        $this->assertEquals($data['result']['list'][5]['nickname'], null);
        $this->assertEquals($data['result']['list'][5]['email'], "crobert@thestudnet.com");
        $this->assertEquals($data['result']['list'][5]['birth_date'], null);
        $this->assertEquals($data['result']['list'][5]['position'], null);
        $this->assertEquals($data['result']['list'][5]['interest'], null);
        $this->assertEquals($data['result']['list'][5]['avatar'], null);
        $this->assertEquals(count($data['result']['list'][5]['roles']), 1);
        $this->assertEquals($data['result']['list'][5]['roles'][0], "academic");
        $this->assertEquals(count($data['result']['list'][5]['program']), 0);
        $this->assertEquals(count($data['result']['list'][6]), 15);
        $this->assertEquals($data['result']['list'][6]['contact_state'], 3);
        $this->assertEquals($data['result']['list'][6]['contacts_count'], 7);
        $this->assertEquals(count($data['result']['list'][6]['school']), 5);
        $this->assertEquals($data['result']['list'][6]['school']['id'], 1);
        $this->assertEquals($data['result']['list'][6]['school']['name'], "Morbi Corporation");
        $this->assertEquals($data['result']['list'][6]['school']['short_name'], "turpis");
        $this->assertEquals($data['result']['list'][6]['school']['logo'], null);
        $this->assertEquals($data['result']['list'][6]['school']['background'], null);
        $this->assertEquals($data['result']['list'][6]['id'], 2);
        $this->assertEquals($data['result']['list'][6]['firstname'], "Xuan-Anh");
        $this->assertEquals($data['result']['list'][6]['lastname'], "Hoang");
        $this->assertEquals($data['result']['list'][6]['nickname'], null);
        $this->assertEquals($data['result']['list'][6]['email'], "xhoang@thestudnet.com");
        $this->assertEquals($data['result']['list'][6]['birth_date'], null);
        $this->assertEquals($data['result']['list'][6]['position'], null);
        $this->assertEquals($data['result']['list'][6]['interest'], null);
        $this->assertEquals($data['result']['list'][6]['avatar'], null);
        $this->assertEquals(count($data['result']['list'][6]['roles']), 1);
        $this->assertEquals($data['result']['list'][6]['roles'][0], "admin");
        $this->assertEquals(count($data['result']['list'][6]['program']), 0);
        $this->assertEquals(count($data['result']['list'][7]), 15);
        $this->assertEquals($data['result']['list'][7]['contact_state'], 3);
        $this->assertEquals($data['result']['list'][7]['contacts_count'], 7);
        $this->assertEquals(count($data['result']['list'][7]['school']), 5);
        $this->assertEquals($data['result']['list'][7]['school']['id'], 1);
        $this->assertEquals($data['result']['list'][7]['school']['name'], "Morbi Corporation");
        $this->assertEquals($data['result']['list'][7]['school']['short_name'], "turpis");
        $this->assertEquals($data['result']['list'][7]['school']['logo'], null);
        $this->assertEquals($data['result']['list'][7]['school']['background'], null);
        $this->assertEquals($data['result']['list'][7]['id'], 1);
        $this->assertEquals($data['result']['list'][7]['firstname'], "Paul");
        $this->assertEquals($data['result']['list'][7]['lastname'], "Boussekey");
        $this->assertEquals($data['result']['list'][7]['nickname'], null);
        $this->assertEquals($data['result']['list'][7]['email'], "pboussekey@thestudnet.com");
        $this->assertEquals($data['result']['list'][7]['birth_date'], null);
        $this->assertEquals($data['result']['list'][7]['position'], null);
        $this->assertEquals($data['result']['list'][7]['interest'], null);
        $this->assertEquals($data['result']['list'][7]['avatar'], null);
        $this->assertEquals(count($data['result']['list'][7]['roles']), 1);
        $this->assertEquals($data['result']['list'][7]['roles'][0], "super_admin");
        $this->assertEquals(count($data['result']['list'][7]['program']), 0);
        $this->assertEquals($data['result']['count'], 8);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }
    /**
     * @depends testCanAddUser
     */
    /*public function testGet($id)
    {
        $this->setIdentity(1);

        $data = $this->jsonRpc('user.get', array('id' => $id));
        $this->assertEquals(count($data), 3);
        $this->assertEquals(count($data['result']), 21);
        $this->assertEquals(count($data['result']['origin']), 2);
        $this->assertEquals($data['result']['origin']['id'], 1);
        $this->assertEquals($data['result']['origin']['short_name'], "Afghanistan");
        $this->assertEquals(count($data['result']['nationality']), 2);
        $this->assertEquals($data['result']['nationality']['id'], 1);
        $this->assertEquals($data['result']['nationality']['short_name'], "Afghanistan");
        $this->assertEquals($data['result']['gender'], "m");
        $this->assertEquals($data['result']['contact_state'], 3);
        $this->assertEquals($data['result']['contacts_count'], 7);
        $this->assertEquals(count($data['result']['school']), 5);
        $this->assertEquals($data['result']['school']['id'], 1);
        $this->assertEquals($data['result']['school']['name'], "Morbi Corporation");
        $this->assertEquals($data['result']['school']['short_name'], "turpis");
        $this->assertEquals($data['result']['school']['logo'], null);
        $this->assertEquals($data['result']['school']['background'], null);
        $this->assertEquals($data['result']['id'], 11);
        $this->assertEquals($data['result']['firstname'], "Jean");
        $this->assertEquals($data['result']['lastname'], "Paul");
        $this->assertEquals($data['result']['nickname'], null);
        $this->assertEquals($data['result']['email'], "jpaul@thestudnet.com");
        $this->assertEquals($data['result']['birth_date'], null);
        $this->assertEquals($data['result']['position'], "une position new");
        $this->assertEquals($data['result']['school_id'], 1);
        $this->assertEquals($data['result']['ambassador'], null);
        $this->assertEquals($data['result']['interest'], "un interet new");
        $this->assertEquals($data['result']['avatar'], "un_token_new");
        $this->assertEquals($data['result']['has_email_notifier'], 1);
        $this->assertEquals($data['result']['background'], null);
        $this->assertEquals(count($data['result']['roles']), 1);
        $this->assertEquals($data['result']['roles'][0], "student");
        $this->assertEquals(count($data['result']['program']), 0);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    public function testCanImportUser()
    {
        $this->setIdentity(5);

        $data = $this->jsonRpc('user.import', ['data' => [["email" => "rgilbertjiuhj0@homestead.com","firstname" => "Rachel","lastname" => "Gilbert","role" => "student","uid" => "d3312d09-837d-47c0-9926-c38122081293000"],["email" => "rgilbert0@homestead.com","firstname" => "Rachel","lastname" => "Gilbert","role" => "student","uid" => "d3312d09-837d-47c0-9926-c38122081293"],["email" => "rgilbertlkmlkm0@homestead.com","firstname" => "Rachel","lastname" => "Gilbert","role" => "student","uid" => "d3312d09-837d-47c0-9926-c38122081293"],["email" => "rgilbert0@homestead.com","firstname" => "Rachel","lastname" => "Gilbert","role" => "student","uid" => "d3312d09-837d-47c0-9926-c38ùmlùm122081293"]]]);

        $this->assertEquals(count($data), 3);
        $this->assertEquals(count($data['result']), 2);
        $this->assertEquals(count($data['result'][0]), 3);
        $this->assertEquals(count($data['result'][0]['field']), 5);
        $this->assertEquals($data['result'][0]['field']['email'], "rgilbertlkmlkm0@homestead.com");
        $this->assertEquals($data['result'][0]['field']['firstname'], "Rachel");
        $this->assertEquals($data['result'][0]['field']['lastname'], "Gilbert");
        $this->assertEquals($data['result'][0]['field']['role'], "student");
        $this->assertEquals($data['result'][0]['field']['uid'], "d3312d09-837d-47c0-9926-c38122081293");
        $this->assertEquals($data['result'][0]['code'], - 38002);
        $this->assertEquals($data['result'][0]['message'], "uid email");
        $this->assertEquals(count($data['result'][1]), 3);
        $this->assertEquals(count($data['result'][1]['field']), 5);
        $this->assertEquals($data['result'][1]['field']['email'], "rgilbert0@homestead.com");
        $this->assertEquals($data['result'][1]['field']['firstname'], "Rachel");
        $this->assertEquals($data['result'][1]['field']['lastname'], "Gilbert");
        $this->assertEquals($data['result'][1]['field']['role'], "student");
        $this->assertEquals($data['result'][1]['field']['uid'], "d3312d09-837d-47c0-9926-c38ùmlùm122081293");
        $this->assertEquals($data['result'][1]['code'], - 38001);
        $this->assertEquals($data['result'][1]['message'], "duplicate email");
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    // DELETE

    /**
     * @depends testCanAddUser
     */
    /*public function testDelete($id)
    {
        $this->setIdentity(1);

        $data = $this->jsonRpc('user.delete', array('id' => $id));

        $this->assertEquals(count($data), 3);
        $this->assertEquals(count($data['result']), 1);
        $this->assertEquals($data['result'][$id], 1);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    public function testAddlanguage()
    {
        $this->setIdentity(1);

        $data = $this->jsonRpc('user.addLanguage', array('language' => array('name' => 'french'),'language_level' => 1));

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    public function testDeletelanguage()
    {
        $this->setIdentity(1);

        $data = $this->jsonRpc('user.deleteLanguage', array('id' => 1));

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    */
}
