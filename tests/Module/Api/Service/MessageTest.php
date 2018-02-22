<?php
namespace ModuleTest\Api\Service;

use ModuleTest\Api\AbstractService;

class MessageTest extends AbstractService
{
    public static function setUpBeforeClass()
    {
        system('phing -q reset-db deploy-db');
        parent::setUpBeforeClass();
    }

    public function testInit()
    {
        $this->setIdentity(1, 1);
        $this->jsonRpc(
            'user.update', [
            'id' => 1,
            'nickname' => 'Nickname'
        ]);
        $this->reset();
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
        
        
        $this->reset();
        $this->setIdentity(1, 1);
        $data = $this->jsonRpc(
            'page.add', [
            'title' => 'Course page',
            'logo' => 'logo',
            'background' => 'background',
            'description' => 'description',
            'confidentiality' => 1,
            'type' => 'course',
            'admission' => 'free',
            'page_id' => $page_id,
            'users' => [
                ['user_id' => 1,'role' => 'admin', 'state' => 'member'],
                ['user_id' => 2,'role' => 'user', 'state' => 'pending']
            ],
            'tags' => [
                'toto', 'tata', 'tutu'
            ],
           
            ]
        );
        
       
    


        $this->reset();
        $this->jsonRpc('user.login', ['user' => 'crobert@thestudnet.com','password' => 'thestudnet']);
        $this->reset();
        $this->jsonRpc('user.login', ['user' => 'xhoang@thestudnet.com','password' => 'thestudnet']);

        return $page_id;
    }
    
    /**
     * @depends testInit
     */
    public function testCreateCourse($page_id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'item.add', [
            'page_id' => $page_id,
            'title' => 'Live class',
            'description' => 'Live class description',
            'type' => 'LC',
            'is_available' => false,
            'is_published' => false,
                ['user_id' => 1,'role' => 'admin', 'state' => 'member'],
                ['user_id' => 2,'role' => 'user', 'state' => 'member'],
                ['user_id' => 3,'role' => 'user', 'state' => 'member'],
                ['user_id' => 4,'role' => 'user', 'state' => 'member'],
            ]
        );
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
        
        return $data['result'];
    }
    
    /**
     * @depends testCreateCourse
     */
    public function testCreateLC($course)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'item.add', [
            'title' => 'Section',
            'description' => 'Section description',
            'type' => 'FOLDER',
            'is_available' => true,
            'is_published' => true,
            'page_id' => $course
            ]
        );
        
        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'item.add', [
            'page_id' => $course,
            'title' => 'Live class',
            'description' => 'Live class description',
            'type' => 'LC',
            'is_available' => true,
            'is_published' => true,
            'participants' => 'all',
            'parent_id' => $data['result']
            ]
        );
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 3);
        $this->assertEquals($data['jsonrpc'], 2.0);
        
        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'item.get', [
            'id' => $data['result']
            ]
        );
        
        return $data['result'];
    }
    
    /**
     * @depends testCreateLC
     */
    public function testSendMessageInLc($item)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('message.send', ['conversation_id' =>$item['conversation_id'],'text' => 'super message qwerty']);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 3); 
        $this->assertEquals($data['result']['message_id'] , 1); 
        $this->assertEquals($data['result']['conversation_id'] , 3); 
        $this->assertEquals(count($data['result']['to']) , 3); 
        $this->assertEquals($data['result']['to'][0] , 1); 
        $this->assertEquals($data['result']['to'][1] , 2); 
        $this->assertEquals($data['result']['to'][2] , 3); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

        
    }
    
    /**
     * @depends testCreateCourse
     */
    public function testSendMessageInLc2($course)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'item.add', [
            'title' => 'Section 2',
            'description' => 'Section description',
            'type' => 'FOLDER',
            'is_available' => true,
            'is_published' => true,
            'page_id' => $course
            ]
        );
        
        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'item.add', [
            'page_id' => $course,
            'title' => 'Live class',
            'description' => 'Live class description',
            'type' => 'LC',
            'is_available' => true,
            'is_published' => true,
            'participants' => 'user',
            'parent_id' => $data['result']
            ]
        );
        
        $this->reset();
        $this->setIdentity(1);
        $this->jsonRpc(
            'item.addUsers', [
            'id' => $data['result'],
            'user_ids' => [1,2,3,4]
            ]
        );
        
        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'item.get', [
            'id' => $data['result']
            ]
        );
        
        
        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc('message.send', ['conversation_id' =>$data['result']['conversation_id'],'text' => 'super message qwerty']);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 3); 
        $this->assertEquals($data['result']['message_id'] , 2); 
        $this->assertEquals($data['result']['conversation_id'] , 4); 
        $this->assertEquals(count($data['result']['to']) , 4); 
        $this->assertEquals($data['result']['to'][0] , 1); 
        $this->assertEquals($data['result']['to'][1] , 2); 
        $this->assertEquals($data['result']['to'][2] , 3); 
        $this->assertEquals($data['result']['to'][3] , 4); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 
        
    }
    
     public function testCanSendMessageError()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('message.send', ['to' =>[2,3]]);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -32000); 
        $this->assertEquals(!empty($data['error']['message']) , true); 

    }

    public function testCanSendMessage()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('message.send', ['to' =>[2,3],'text' => 'super message qwerty']);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 3); 
        $this->assertEquals($data['result']['message_id'] , 3); 
        $this->assertEquals($data['result']['conversation_id'] , 5); 
        $this->assertEquals(count($data['result']['to']) , 3); 
        $this->assertEquals($data['result']['to'][0] , 1); 
        $this->assertEquals($data['result']['to'][1] , 2); 
        $this->assertEquals($data['result']['to'][2] , 3); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 



        return $data['result'];
    }

    public function testCanSendMessageTwo()
    {
        $this->setIdentity(2);
        $data = $this->jsonRpc('message.send', ['to' => 3,'text' => 'super message deux qwerty 1']);

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 3);
        $this->assertEquals($data['result']['message_id'], 4);
        $this->assertEquals($data['result']['conversation_id'], 6);
        $this->assertEquals(count($data['result']['to']), 2);
        $this->assertEquals($data['result']['to'][0], 2);
        $this->assertEquals($data['result']['to'][1], 3);
        $this->assertEquals($data['jsonrpc'], 2.0);


        return $data['result'];
    }

    public function testCanSendMessagethree()
    {
        $this->setIdentity(3);
        $data = $this->jsonRpc('message.send', ['to' => 2,'text' => 'super message un azerty 2']);

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 3);
        $this->assertEquals($data['result']['message_id'], 5);
        $this->assertEquals($data['result']['conversation_id'], 6);
        $this->assertEquals(count($data['result']['to']), 2);
        $this->assertEquals($data['result']['to'][0], 2);
        $this->assertEquals($data['result']['to'][1], 3);
        $this->assertEquals($data['jsonrpc'], 2.0);

        return $data['result'];
    }

    /**
     * @depends testCanSendMessageTwo
     */
    public function testCanSendMessagethreebis($item)
    {
        $this->setIdentity(2);
        $data = $this->jsonRpc(
            'message.send', [
            'conversation_id' => $item['conversation_id'], 'text' => 'dernier message', 'library' => ['token' => '123456789', 'name' => 'super doc  ']]
        );

        $this->printCreateTest($data);
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 3);
        $this->assertEquals($data['result']['message_id'], 6);
        $this->assertEquals($data['result']['conversation_id'], 6);
        $this->assertEquals(count($data['result']['to']), 2);
        $this->assertEquals($data['result']['to'][0], 2);
        $this->assertEquals($data['result']['to'][1], 3);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }
    
    public function testConversationCreate()
    {
        $this->setIdentity(2);
        $data = $this->jsonRpc(
            'conversation.create', [
            'user_id' => [2, 3]
            ]
        );
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 7);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    /**
     * @depends testCanSendMessageTwo
     */
    public function testCanGetIdConv($conv)
    {
        $this->setIdentity(2);
        $data = $this->jsonRpc(
            'conversation.getIdByUser', [
            'user_id' => 3
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], $conv['conversation_id']);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    /**
     * @depends testCanSendMessageTwo
     */
    public function testCanGetIdConvAddVideo($conv)
    {
        $this->setIdentity(2);
        $data = $this->jsonRpc(
            'conversation.addVideo', [
            'id' => $conv['conversation_id']
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(!empty($data['result']), true);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    /**
     * @depends testCreateLC
     */
    public function testCanGetIdConvGetToken($conv)
    {
        $this->setIdentity(2);
        $data = $this->jsonRpc(
            'conversation.getToken', [
            'id' => $conv['page_id']
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 3);
        $this->assertEquals(!empty($data['result']['token']), true);
        $this->assertEquals(!empty($data['result']['session']), true);
        $this->assertEquals(!empty($data['result']['role']), true);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    /**
     * @depends testCreateLC
     */
    public function testCanGetIdConvGetToken2($item)
    {
        $this->setIdentity(2);
        $data = $this->jsonRpc(
            'conversation.getToken', [
            'id' => $item['conversation_id']
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 3);
        $this->assertEquals(!empty($data['result']['token']), true);
        $this->assertEquals(!empty($data['result']['session']), true);
        $this->assertEquals(!empty($data['result']['role']), true);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    /**
     * @depends testCanSendMessageTwo
     */
    public function testCanGet($conv)
    {
        $this->setIdentity(2);
        $data = $this->jsonRpc(
            'conversation.get', [
            'id' => $conv
            ]
        );
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 2); 
        $this->assertEquals(count($data['result'][6]) , 10); 
        $this->assertEquals($data['result'][6]['item_id'] , null); 
        $this->assertEquals(count($data['result'][6]['message']) , 6); 
        $this->assertEquals(count($data['result'][6]['message']['message_user']) , 1); 
        $this->assertEquals(!empty($data['result'][6]['message']['message_user']['read_date']) , true); 
        $this->assertEquals(count($data['result'][6]['message']['library']) , 6); 
        $this->assertEquals($data['result'][6]['message']['library']['id'] , 4); 
        $this->assertEquals($data['result'][6]['message']['library']['name'] , "super doc  "); 
        $this->assertEquals($data['result'][6]['message']['library']['link'] , null); 
        $this->assertEquals($data['result'][6]['message']['library']['token'] , 123456789); 
        $this->assertEquals($data['result'][6]['message']['library']['type'] , null); 
        $this->assertEquals($data['result'][6]['message']['library']['box_id'] , null); 
        $this->assertEquals($data['result'][6]['message']['id'] , 6); 
        $this->assertEquals($data['result'][6]['message']['text'] , "dernier message"); 
        $this->assertEquals(!empty($data['result'][6]['message']['created_date']) , true); 
        $this->assertEquals($data['result'][6]['message']['user_id'] , 2); 
        $this->assertEquals(count($data['result'][6]['conversation_user']) , 1); 
        $this->assertEquals($data['result'][6]['conversation_user']['read_date'] , null); 
        $this->assertEquals(count($data['result'][6]['users']) , 2); 
        $this->assertEquals($data['result'][6]['users'][0] , 2); 
        $this->assertEquals($data['result'][6]['users'][1] , 3); 
        $this->assertEquals($data['result'][6]['nb_users'] , 2); 
        $this->assertEquals($data['result'][6]['page_id'] , null); 
        $this->assertEquals($data['result'][6]['id'] , 6); 
        $this->assertEquals($data['result'][6]['name'] , "Chat"); 
        $this->assertEquals($data['result'][6]['type'] , 2); 
        $this->assertEquals(count($data['result'][6]['options']) , 3); 
        $this->assertEquals($data['result'][6]['options']['record'] , false); 
        $this->assertEquals($data['result'][6]['options']['nb_user_autorecord'] , 0); 
        $this->assertEquals(count($data['result'][6]['options']['rules']) , 10); 
        $this->assertEquals($data['result'][6]['options']['rules']['autoPublishCamera'] , true); 
        $this->assertEquals($data['result'][6]['options']['rules']['autoPublishMicrophone'] , false); 
        $this->assertEquals($data['result'][6]['options']['rules']['archive'] , false); 
        $this->assertEquals($data['result'][6]['options']['rules']['raiseHand'] , false); 
        $this->assertEquals($data['result'][6]['options']['rules']['publish'] , true); 
        $this->assertEquals($data['result'][6]['options']['rules']['askDevice'] , false); 
        $this->assertEquals($data['result'][6]['options']['rules']['askScreen'] , false); 
        $this->assertEquals($data['result'][6]['options']['rules']['forceMute'] , false); 
        $this->assertEquals($data['result'][6]['options']['rules']['forceUnpublish'] , false); 
        $this->assertEquals($data['result'][6]['options']['rules']['kick'] , false); 
        $this->assertEquals(count($data['result'][4]) , 10); 
        $this->assertEquals($data['result'][4]['item_id'] , 5); 
        $this->assertEquals(count($data['result'][4]['message']) , 6); 
        $this->assertEquals(count($data['result'][4]['message']['message_user']) , 1); 
        $this->assertEquals($data['result'][4]['message']['message_user']['read_date'] , null); 
        $this->assertEquals($data['result'][4]['message']['library'] , null); 
        $this->assertEquals($data['result'][4]['message']['id'] , 2); 
        $this->assertEquals($data['result'][4]['message']['text'] , "super message qwerty"); 
        $this->assertEquals(!empty($data['result'][4]['message']['created_date']) , true); 
        $this->assertEquals($data['result'][4]['message']['user_id'] , 1); 
        $this->assertEquals(count($data['result'][4]['conversation_user']) , 1); 
        $this->assertEquals($data['result'][4]['conversation_user']['read_date'] , null); 
        $this->assertEquals(count($data['result'][4]['users']) , 4); 
        $this->assertEquals($data['result'][4]['users'][0] , 1); 
        $this->assertEquals($data['result'][4]['users'][1] , 2); 
        $this->assertEquals($data['result'][4]['users'][2] , 3); 
        $this->assertEquals($data['result'][4]['users'][3] , 4); 
        $this->assertEquals($data['result'][4]['nb_users'] , 0); 
        $this->assertEquals($data['result'][4]['page_id'] , null); 
        $this->assertEquals($data['result'][4]['id'] , 4); 
        $this->assertEquals($data['result'][4]['name'] , "Chat"); 
        $this->assertEquals($data['result'][4]['type'] , 3); 
        $this->assertEquals(count($data['result'][4]['options']) , 3); 
        $this->assertEquals($data['result'][4]['options']['record'] , false); 
        $this->assertEquals($data['result'][4]['options']['nb_user_autorecord'] , 2); 
        $this->assertEquals(count($data['result'][4]['options']['rules']) , 10); 
        $this->assertEquals($data['result'][4]['options']['rules']['autoPublishCamera'] , true); 
        $this->assertEquals($data['result'][4]['options']['rules']['autoPublishMicrophone'] , false); 
        $this->assertEquals(count($data['result'][4]['options']['rules']['archive']) , 1); 
        $this->assertEquals(count($data['result'][4]['options']['rules']['archive'][0]) , 1); 
        $this->assertEquals(count($data['result'][4]['options']['rules']['archive'][0]['roles']) , 1); 
        $this->assertEquals($data['result'][4]['options']['rules']['archive'][0]['roles'][0] , "admin"); 
        $this->assertEquals(count($data['result'][4]['options']['rules']['raiseHand']) , 1); 
        $this->assertEquals(count($data['result'][4]['options']['rules']['raiseHand'][0]) , 1); 
        $this->assertEquals(count($data['result'][4]['options']['rules']['raiseHand'][0]['roles']) , 1); 
        $this->assertEquals($data['result'][4]['options']['rules']['raiseHand'][0]['roles'][0] , "user"); 
        $this->assertEquals(count($data['result'][4]['options']['rules']['publish']) , 1); 
        $this->assertEquals(count($data['result'][4]['options']['rules']['publish'][0]) , 1); 
        $this->assertEquals(count($data['result'][4]['options']['rules']['publish'][0]['roles']) , 1); 
        $this->assertEquals($data['result'][4]['options']['rules']['publish'][0]['roles'][0] , "admin"); 
        $this->assertEquals(count($data['result'][4]['options']['rules']['askDevice']) , 1); 
        $this->assertEquals(count($data['result'][4]['options']['rules']['askDevice'][0]) , 1); 
        $this->assertEquals(count($data['result'][4]['options']['rules']['askDevice'][0]['roles']) , 1); 
        $this->assertEquals($data['result'][4]['options']['rules']['askDevice'][0]['roles'][0] , "admin"); 
        $this->assertEquals(count($data['result'][4]['options']['rules']['askScreen']) , 1); 
        $this->assertEquals(count($data['result'][4]['options']['rules']['askScreen'][0]) , 1); 
        $this->assertEquals(count($data['result'][4]['options']['rules']['askScreen'][0]['roles']) , 1); 
        $this->assertEquals($data['result'][4]['options']['rules']['askScreen'][0]['roles'][0] , "admin"); 
        $this->assertEquals(count($data['result'][4]['options']['rules']['forceMute']) , 1); 
        $this->assertEquals(count($data['result'][4]['options']['rules']['forceMute'][0]) , 1); 
        $this->assertEquals(count($data['result'][4]['options']['rules']['forceMute'][0]['roles']) , 1); 
        $this->assertEquals($data['result'][4]['options']['rules']['forceMute'][0]['roles'][0] , "admin"); 
        $this->assertEquals(count($data['result'][4]['options']['rules']['forceUnpublish']) , 1); 
        $this->assertEquals(count($data['result'][4]['options']['rules']['forceUnpublish'][0]) , 1); 
        $this->assertEquals(count($data['result'][4]['options']['rules']['forceUnpublish'][0]['roles']) , 1); 
        $this->assertEquals($data['result'][4]['options']['rules']['forceUnpublish'][0]['roles'][0] , "admin"); 
        $this->assertEquals(count($data['result'][4]['options']['rules']['kick']) , 1); 
        $this->assertEquals(count($data['result'][4]['options']['rules']['kick'][0]) , 1); 
        $this->assertEquals(count($data['result'][4]['options']['rules']['kick'][0]['roles']) , 1); 
        $this->assertEquals($data['result'][4]['options']['rules']['kick'][0]['roles'][0] , "admin"); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 
 



    }

    /**
     * @depends testCanSendMessageTwo
     */
    public function testCanGetList($conv)
    {
        $this->setIdentity(2);
        $data = $this->jsonRpc(
            'conversation.getList', [
                'type' => [2,3]
           ]
        );
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 4); 
        $this->assertEquals(count($data['result'][0]) , 9); 
        $this->assertEquals($data['result'][0]['item_id'] , null); 
        $this->assertEquals(count($data['result'][0]['message']) , 6); 
        $this->assertEquals($data['result'][0]['message']['id'] , 6); 
        $this->assertEquals($data['result'][0]['message']['text'] , "dernier message"); 
        $this->assertEquals($data['result'][0]['message']['library_id'] , 4); 
        $this->assertEquals($data['result'][0]['message']['type'] , null); 
        $this->assertEquals(!empty($data['result'][0]['message']['created_date']) , true); 
        $this->assertEquals($data['result'][0]['message']['user_id'] , 2); 
        $this->assertEquals(count($data['result'][0]['conversation_user']) , 1); 
        $this->assertEquals($data['result'][0]['conversation_user']['read_date'] , null); 
        $this->assertEquals(count($data['result'][0]['users']) , 2); 
        $this->assertEquals($data['result'][0]['users'][0] , 2); 
        $this->assertEquals($data['result'][0]['users'][1] , 3); 
        $this->assertEquals($data['result'][0]['nb_users'] , 2); 
        $this->assertEquals($data['result'][0]['page_id'] , null); 
        $this->assertEquals($data['result'][0]['id'] , 6); 
        $this->assertEquals($data['result'][0]['name'] , "Chat"); 
        $this->assertEquals($data['result'][0]['type'] , 2); 
        $this->assertEquals(count($data['result'][1]) , 9); 
        $this->assertEquals($data['result'][1]['item_id'] , null); 
        $this->assertEquals(count($data['result'][1]['message']) , 6); 
        $this->assertEquals($data['result'][1]['message']['id'] , 3); 
        $this->assertEquals($data['result'][1]['message']['text'] , "super message qwerty"); 
        $this->assertEquals($data['result'][1]['message']['library_id'] , null); 
        $this->assertEquals($data['result'][1]['message']['type'] , null); 
        $this->assertEquals(!empty($data['result'][1]['message']['created_date']) , true); 
        $this->assertEquals($data['result'][1]['message']['user_id'] , 1); 
        $this->assertEquals(count($data['result'][1]['conversation_user']) , 1); 
        $this->assertEquals(!empty($data['result'][1]['conversation_user']['read_date']) , true); 
        $this->assertEquals(count($data['result'][1]['users']) , 3); 
        $this->assertEquals($data['result'][1]['users'][0] , 1); 
        $this->assertEquals($data['result'][1]['users'][1] , 2); 
        $this->assertEquals($data['result'][1]['users'][2] , 3); 
        $this->assertEquals($data['result'][1]['nb_users'] , 3); 
        $this->assertEquals($data['result'][1]['page_id'] , null); 
        $this->assertEquals($data['result'][1]['id'] , 5); 
        $this->assertEquals($data['result'][1]['name'] , "Chat"); 
        $this->assertEquals($data['result'][1]['type'] , 2); 
        $this->assertEquals(count($data['result'][2]) , 8); 
        $this->assertEquals($data['result'][2]['item_id'] , 5); 
        $this->assertEquals(count($data['result'][2]['message']) , 6); 
        $this->assertEquals($data['result'][2]['message']['id'] , 2); 
        $this->assertEquals($data['result'][2]['message']['text'] , "super message qwerty"); 
        $this->assertEquals($data['result'][2]['message']['library_id'] , null); 
        $this->assertEquals($data['result'][2]['message']['type'] , null); 
        $this->assertEquals(!empty($data['result'][2]['message']['created_date']) , true); 
        $this->assertEquals($data['result'][2]['message']['user_id'] , 1); 
        $this->assertEquals(count($data['result'][2]['conversation_user']) , 1); 
        $this->assertEquals($data['result'][2]['conversation_user']['read_date'] , null); 
        $this->assertEquals($data['result'][2]['nb_users'] , 0); 
        $this->assertEquals($data['result'][2]['page_id'] , null); 
        $this->assertEquals($data['result'][2]['id'] , 4); 
        $this->assertEquals($data['result'][2]['name'] , "Chat"); 
        $this->assertEquals($data['result'][2]['type'] , 3); 
        $this->assertEquals(count($data['result'][3]) , 8); 
        $this->assertEquals($data['result'][3]['item_id'] , 3); 
        $this->assertEquals(count($data['result'][3]['message']) , 6); 
        $this->assertEquals($data['result'][3]['message']['id'] , 1); 
        $this->assertEquals($data['result'][3]['message']['text'] , "super message qwerty"); 
        $this->assertEquals($data['result'][3]['message']['library_id'] , null); 
        $this->assertEquals($data['result'][3]['message']['type'] , null); 
        $this->assertEquals(!empty($data['result'][3]['message']['created_date']) , true); 
        $this->assertEquals($data['result'][3]['message']['user_id'] , 1); 
        $this->assertEquals(count($data['result'][3]['conversation_user']) , 1); 
        $this->assertEquals($data['result'][3]['conversation_user']['read_date'] , null); 
        $this->assertEquals($data['result'][3]['nb_users'] , 0); 
        $this->assertEquals($data['result'][3]['page_id'] , 1); 
        $this->assertEquals($data['result'][3]['id'] , 3); 
        $this->assertEquals($data['result'][3]['name'] , "Chat"); 
        $this->assertEquals($data['result'][3]['type'] , 3); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 



    }

    public function testCanSendMessageunadeux()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('message.send', ['to' => 2,'text' => 'super message un azerty 2']);

        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 3); 
        $this->assertEquals($data['result']['message_id'] , 7); 
        $this->assertEquals($data['result']['conversation_id'] , 8); 
        $this->assertEquals(count($data['result']['to']) , 2); 
        $this->assertEquals($data['result']['to'][0] , 1); 
        $this->assertEquals($data['result']['to'][1] , 2); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 
    }

    public function testCanGetListConv()
    {
        $this->setIdentity(2);
        $data = $this->jsonRpc(
            'contact.add', [
            'user' => 3
            ]
        );
        $this->reset();
        $this->setIdentity(3);
        $data = $this->jsonRpc(
            'contact.accept', [
            'user' => 2
            ]
        );
        $this->reset();


        // 2 => 3
        $this->setIdentity(2);
        $data = $this->jsonRpc(
            'conversation.getList', [
            'contact' => true
            ]
        );
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 1); 
        $this->assertEquals(count($data['result'][0]) , 9); 
        $this->assertEquals($data['result'][0]['item_id'] , null); 
        $this->assertEquals(count($data['result'][0]['message']) , 6); 
        $this->assertEquals($data['result'][0]['message']['id'] , 6); 
        $this->assertEquals($data['result'][0]['message']['text'] , "dernier message"); 
        $this->assertEquals($data['result'][0]['message']['library_id'] , 4); 
        $this->assertEquals($data['result'][0]['message']['type'] , null); 
        $this->assertEquals(!empty($data['result'][0]['message']['created_date']) , true); 
        $this->assertEquals($data['result'][0]['message']['user_id'] , 2); 
        $this->assertEquals(count($data['result'][0]['conversation_user']) , 1); 
        $this->assertEquals($data['result'][0]['conversation_user']['read_date'] , null); 
        $this->assertEquals(count($data['result'][0]['users']) , 2); 
        $this->assertEquals($data['result'][0]['users'][0] , 2); 
        $this->assertEquals($data['result'][0]['users'][1] , 3); 
        $this->assertEquals($data['result'][0]['nb_users'] , 2); 
        $this->assertEquals($data['result'][0]['page_id'] , null); 
        $this->assertEquals($data['result'][0]['id'] , 6); 
        $this->assertEquals($data['result'][0]['name'] , "Chat"); 
        $this->assertEquals($data['result'][0]['type'] , 2); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }

    /**
     * @depends testCanSendMessageunadeux
     */
    public function testCanGetListContact()
    {
        $this->setIdentity(2);
        $data = $this->jsonRpc(
            'conversation.getList', [
            'contact' => false,
            ]
        );

        $this->printCreateTest($data);
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 2); 
        $this->assertEquals(count($data['result'][0]) , 8); 
        $this->assertEquals($data['result'][0]['item_id'] , null); 
        $this->assertEquals(count($data['result'][0]['message']) , 6); 
        $this->assertEquals($data['result'][0]['message']['id'] , 7); 
        $this->assertEquals($data['result'][0]['message']['text'] , "super message un azerty 2"); 
        $this->assertEquals($data['result'][0]['message']['library_id'] , null); 
        $this->assertEquals($data['result'][0]['message']['type'] , null); 
        $this->assertEquals(!empty($data['result'][0]['message']['created_date']) , true); 
        $this->assertEquals($data['result'][0]['message']['user_id'] , 1); 
        $this->assertEquals(count($data['result'][0]['users']) , 2); 
        $this->assertEquals($data['result'][0]['users'][0] , 1); 
        $this->assertEquals($data['result'][0]['users'][1] , 2); 
        $this->assertEquals($data['result'][0]['nb_users'] , 2); 
        $this->assertEquals($data['result'][0]['page_id'] , null); 
        $this->assertEquals($data['result'][0]['id'] , 8); 
        $this->assertEquals($data['result'][0]['name'] , "Chat"); 
        $this->assertEquals($data['result'][0]['type'] , 2); 
        $this->assertEquals(count($data['result'][1]) , 8); 
        $this->assertEquals($data['result'][1]['item_id'] , null); 
        $this->assertEquals(count($data['result'][1]['message']) , 6); 
        $this->assertEquals($data['result'][1]['message']['id'] , 3); 
        $this->assertEquals($data['result'][1]['message']['text'] , "super message qwerty"); 
        $this->assertEquals($data['result'][1]['message']['library_id'] , null); 
        $this->assertEquals($data['result'][1]['message']['type'] , null); 
        $this->assertEquals(!empty($data['result'][1]['message']['created_date']) , true); 
        $this->assertEquals($data['result'][1]['message']['user_id'] , 1); 
        $this->assertEquals(count($data['result'][1]['users']) , 3); 
        $this->assertEquals($data['result'][1]['users'][0] , 1); 
        $this->assertEquals($data['result'][1]['users'][1] , 2); 
        $this->assertEquals($data['result'][1]['users'][2] , 3); 
        $this->assertEquals($data['result'][1]['nb_users'] , 3); 
        $this->assertEquals($data['result'][1]['page_id'] , null); 
        $this->assertEquals($data['result'][1]['id'] , 5); 
        $this->assertEquals($data['result'][1]['name'] , "Chat"); 
        $this->assertEquals($data['result'][1]['type'] , 2); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 
    }

    /**
     * Terst du read
     *
     * @depends testCanSendMessageunadeux
     */
    public function testCanGetListConvBeforeRead()
    {
        // 2 => 3
        $this->setIdentity(3);
        $data = $this->jsonRpc(
            'conversation.getList', [
            'noread' => true
            ]
        );
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 2); 
        $this->assertEquals(count($data['result'][0]) , 8); 
        $this->assertEquals($data['result'][0]['item_id'] , null); 
        $this->assertEquals(count($data['result'][0]['message']) , 6); 
        $this->assertEquals($data['result'][0]['message']['id'] , 6); 
        $this->assertEquals($data['result'][0]['message']['text'] , "dernier message"); 
        $this->assertEquals($data['result'][0]['message']['library_id'] , 4); 
        $this->assertEquals($data['result'][0]['message']['type'] , null); 
        $this->assertEquals(!empty($data['result'][0]['message']['created_date']) , true); 
        $this->assertEquals($data['result'][0]['message']['user_id'] , 2); 
        $this->assertEquals(count($data['result'][0]['users']) , 2); 
        $this->assertEquals($data['result'][0]['users'][0] , 2); 
        $this->assertEquals($data['result'][0]['users'][1] , 3); 
        $this->assertEquals($data['result'][0]['nb_users'] , 2); 
        $this->assertEquals($data['result'][0]['page_id'] , null); 
        $this->assertEquals($data['result'][0]['id'] , 6); 
        $this->assertEquals($data['result'][0]['name'] , "Chat"); 
        $this->assertEquals($data['result'][0]['type'] , 2); 
        $this->assertEquals(count($data['result'][1]) , 8); 
        $this->assertEquals($data['result'][1]['item_id'] , null); 
        $this->assertEquals(count($data['result'][1]['message']) , 6); 
        $this->assertEquals($data['result'][1]['message']['id'] , 3); 
        $this->assertEquals($data['result'][1]['message']['text'] , "super message qwerty"); 
        $this->assertEquals($data['result'][1]['message']['library_id'] , null); 
        $this->assertEquals($data['result'][1]['message']['type'] , null); 
        $this->assertEquals(!empty($data['result'][1]['message']['created_date']) , true); 
        $this->assertEquals($data['result'][1]['message']['user_id'] , 1); 
        $this->assertEquals(count($data['result'][1]['users']) , 3); 
        $this->assertEquals($data['result'][1]['users'][0] , 1); 
        $this->assertEquals($data['result'][1]['users'][1] , 2); 
        $this->assertEquals($data['result'][1]['users'][2] , 3); 
        $this->assertEquals($data['result'][1]['nb_users'] , 3); 
        $this->assertEquals($data['result'][1]['page_id'] , null); 
        $this->assertEquals($data['result'][1]['id'] , 5); 
        $this->assertEquals($data['result'][1]['name'] , "Chat"); 
        $this->assertEquals($data['result'][1]['type'] , 2); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 


    }

    /**
     * @depends testCanSendMessageTwo
     */
    public function testCanGetread($conv)
    {
        $this->setIdentity(3);
        $data = $this->jsonRpc('conversation.read', ['id' => $conv['conversation_id']]);

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    /**
     * Terst du read
     *
     * @depends testCanSendMessageunadeux
     */
    public function testCanGetListConvAfterRead()
    {
        // 2 => 3
        $this->setIdentity(3);
        $data = $this->jsonRpc(
            'conversation.getList', [
            'noread' => true
            ]
        );

        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 1); 
        $this->assertEquals(count($data['result'][0]) , 8); 
        $this->assertEquals($data['result'][0]['item_id'] , null); 
        $this->assertEquals(count($data['result'][0]['message']) , 6); 
        $this->assertEquals($data['result'][0]['message']['id'] , 3); 
        $this->assertEquals($data['result'][0]['message']['text'] , "super message qwerty"); 
        $this->assertEquals($data['result'][0]['message']['library_id'] , null); 
        $this->assertEquals($data['result'][0]['message']['type'] , null); 
        $this->assertEquals(!empty($data['result'][0]['message']['created_date']) , true); 
        $this->assertEquals($data['result'][0]['message']['user_id'] , 1); 
        $this->assertEquals(count($data['result'][0]['users']) , 3); 
        $this->assertEquals($data['result'][0]['users'][0] , 1); 
        $this->assertEquals($data['result'][0]['users'][1] , 2); 
        $this->assertEquals($data['result'][0]['users'][2] , 3); 
        $this->assertEquals($data['result'][0]['nb_users'] , 3); 
        $this->assertEquals($data['result'][0]['page_id'] , null); 
        $this->assertEquals($data['result'][0]['id'] , 5); 
        $this->assertEquals($data['result'][0]['name'] , "Chat"); 
        $this->assertEquals($data['result'][0]['type'] , 2); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 


    }

    /**
     * Test la récupération des membre paginer
     **/
    public function testCanGetListId()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'user.getListId', [
            'conversation_id' => 4,
            'filter' => [
            'p' => 1,
            'n' => 3,
            'c' => ['user.id' => '>'],
            's' => '2'
            ],
            ]
        );

        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 2); 
        $this->assertEquals(count($data['result']['list']) , 0); 
        $this->assertEquals($data['result']['count'] , 0); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }

    /**
     * @depends testCanSendMessageTwo
     */
    public function testCanMessageGetList($conv)
    {
        $this->setIdentity(2);

        $data = $this->jsonRpc('message.getList', ['conversation_id' => $conv['conversation_id']]);

        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 2); 
        $this->assertEquals(count($data['result']['list']) , 3); 
        $this->assertEquals(count($data['result']['list'][0]) , 6); 
        $this->assertEquals(count($data['result']['list'][0]['message_user']) , 1); 
        $this->assertEquals(!empty($data['result']['list'][0]['message_user']['read_date']) , true); 
        $this->assertEquals(count($data['result']['list'][0]['library']) , 6); 
        $this->assertEquals($data['result']['list'][0]['library']['id'] , 4); 
        $this->assertEquals($data['result']['list'][0]['library']['name'] , "super doc  "); 
        $this->assertEquals($data['result']['list'][0]['library']['link'] , null); 
        $this->assertEquals($data['result']['list'][0]['library']['token'] , 123456789); 
        $this->assertEquals($data['result']['list'][0]['library']['type'] , null); 
        $this->assertEquals($data['result']['list'][0]['library']['box_id'] , null); 
        $this->assertEquals($data['result']['list'][0]['id'] , 6); 
        $this->assertEquals($data['result']['list'][0]['text'] , "dernier message"); 
        $this->assertEquals(!empty($data['result']['list'][0]['created_date']) , true); 
        $this->assertEquals($data['result']['list'][0]['user_id'] , 2); 
        $this->assertEquals(count($data['result']['list'][1]) , 6); 
        $this->assertEquals(count($data['result']['list'][1]['message_user']) , 1); 
        $this->assertEquals($data['result']['list'][1]['message_user']['read_date'] , null); 
        $this->assertEquals($data['result']['list'][1]['library'] , null); 
        $this->assertEquals($data['result']['list'][1]['id'] , 5); 
        $this->assertEquals($data['result']['list'][1]['text'] , "super message un azerty 2"); 
        $this->assertEquals(!empty($data['result']['list'][1]['created_date']) , true); 
        $this->assertEquals($data['result']['list'][1]['user_id'] , 3); 
        $this->assertEquals(count($data['result']['list'][2]) , 6); 
        $this->assertEquals(count($data['result']['list'][2]['message_user']) , 1); 
        $this->assertEquals(!empty($data['result']['list'][2]['message_user']['read_date']) , true); 
        $this->assertEquals($data['result']['list'][2]['library'] , null); 
        $this->assertEquals($data['result']['list'][2]['id'] , 4); 
        $this->assertEquals($data['result']['list'][2]['text'] , "super message deux qwerty 1"); 
        $this->assertEquals(!empty($data['result']['list'][2]['created_date']) , true); 
        $this->assertEquals($data['result']['list'][2]['user_id'] , 2); 
        $this->assertEquals($data['result']['count'] , 3); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }
    
    public function testCanMessageGetCount()
    {
        $this->setIdentity(2);
        $data = $this->jsonRpc('message.getCount', [
            'start_date' => '2015-10-10T06:00:00Z',
            'end_date' => '2099-10-10T06:00:00Z',
            'type' => 2,
            'organization_id' => 1]);

        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 1); 
        $this->assertEquals(count($data['result'][0]) , 3); 
        $this->assertEquals($data['result'][0]['count'] , 5); 
        $this->assertEquals($data['result'][0]['type'] , 2); 
        $this->assertEquals(!empty($data['result'][0]['created_date']) , true); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }

    /**
     * @depends testCanSendMessageunadeux
     */
    public function testCanGetListSearch()
    {
        $this->setIdentity(2);
        $data = $this->jsonRpc(
            'conversation.getList', [
            'search' => 'paul',
            'filter' => ['n' => 1],
            'contact' => false
            ]
        );
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 2); 
        $this->assertEquals(count($data['result']['list']) , 1); 
        $this->assertEquals(count($data['result']['list'][0]) , 8); 
        $this->assertEquals($data['result']['list'][0]['item_id'] , null); 
        $this->assertEquals(count($data['result']['list'][0]['message']) , 6); 
        $this->assertEquals($data['result']['list'][0]['message']['id'] , 7); 
        $this->assertEquals($data['result']['list'][0]['message']['text'] , "super message un azerty 2"); 
        $this->assertEquals($data['result']['list'][0]['message']['library_id'] , null); 
        $this->assertEquals($data['result']['list'][0]['message']['type'] , null); 
        $this->assertEquals(!empty($data['result']['list'][0]['message']['created_date']) , true); 
        $this->assertEquals($data['result']['list'][0]['message']['user_id'] , 1); 
        $this->assertEquals(count($data['result']['list'][0]['users']) , 2); 
        $this->assertEquals($data['result']['list'][0]['users'][0] , 1); 
        $this->assertEquals($data['result']['list'][0]['users'][1] , 2); 
        $this->assertEquals($data['result']['list'][0]['nb_users'] , 2); 
        $this->assertEquals($data['result']['list'][0]['page_id'] , null); 
        $this->assertEquals($data['result']['list'][0]['id'] , 8); 
        $this->assertEquals($data['result']['list'][0]['name'] , "Chat"); 
        $this->assertEquals($data['result']['list'][0]['type'] , 2); 
        $this->assertEquals($data['result']['count'] , 2); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 


    }

    /**
     * @depends testCreateLC
     */
    public function testCanStartRecord($item)
    {

        $this->setIdentity(2);
        $this->mockOpenTok();
        $data = $this->jsonRpc('videoarchive.startRecord', ['conversation_id' => $item['conversation_id']]);

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 2);
        $this->assertEquals($data['result']['status'], "started");
        $this->assertEquals($data['result']['id'], 1234);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    /**
     * @depends testCreateLC
     */
    public function testCanStopRecord($item)
    {
        $this->setIdentity(2);
        $this->mockOpenTok();
        $data = $this->jsonRpc('videoarchive.stopRecord', ['conversation_id' => $item['conversation_id']]);

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 2);
        $this->assertEquals($data['result']['status'], "stop");
        $this->assertEquals($data['result']['id'], 1234);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }    
    
    /**
     * @depends testCreateLC
     */
    public function testCheckStatus($item)
    {
        $this->setIdentity(2);
        $data = $this->jsonRpc('videoarchive.checkStatus', ['json' => [
            'id' => 1234, 'status' => 'uploaded', 'link' => 'link'
            ]
        ]);

        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals($data['result'] , 1); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }
}
