<?php
namespace ModuleTest\Api\Service;

use ModuleTest\Api\AbstractService;

class GroupTest extends AbstractService
{
    public static $session;

    public static function setUpBeforeClass()
    {
        system('phing -q reset-db deploy-db');
        parent::setUpBeforeClass();
    }

    public function testPageAdd()
    {
        $this->setIdentity(1, 1);
        $data = $this->jsonRpc(
            'page.add', [
            'title' => 'super title',
            'logo' => 'logo',
            'background' => 'background',
            'description' => 'description',
            'confidentiality' => 1,
            'type' => 'course',
            'admission' => 'free',
            'start_date' => '2015-00-00 00:00:00',
            'end_date' => '2016-00-00 00:00:00',
            'location' => 'location',
            'page_id' => 1,
            'is_published' => true,
            'users' => [
                ['user_id' => 1,'role' => 'admin', 'state' => 'member'],
                ['user_id' => 2,'role' => 'admin', 'state' => 'member'],
                ['user_id' => 3,'role' => 'user', 'state' => 'member'],
                ['user_id' => 4,'role' => 'user', 'state' => 'member'],
                ['user_id' => 5,'role' => 'user', 'state' => 'member'],
            ],
            'docs' => [
                ['name' => 'name', 'link' => 'link', 'type' => 'type']
            ]
            ]
        );
        
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
        return $data['id'];
    }
    
    
    /**
     * @depends testPageAdd
     */
    public function testGetChannel($id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('page.get', [ 'id' => $id ]);
        
        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc('conversation.get', [ 'id' => $data['result'][$id]['conversation_id'] ]);
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 10); 
        $this->assertEquals($data['result']['item_id'] , null); 
        $this->assertEquals(count($data['result']['message']) , 6); 
        $this->assertEquals($data['result']['message']['id'] , null); 
        $this->assertEquals($data['result']['message']['text'] , null); 
        $this->assertEquals($data['result']['message']['library_id'] , null); 
        $this->assertEquals($data['result']['message']['type'] , null); 
        $this->assertEquals($data['result']['message']['created_date'] , null); 
        $this->assertEquals($data['result']['message']['user_id'] , null); 
        $this->assertEquals(count($data['result']['conversation_user']) , 1); 
        $this->assertEquals($data['result']['conversation_user']['read_date'] , null); 
        $this->assertEquals(count($data['result']['users']) , 5); 
        $this->assertEquals($data['result']['users'][0] , 1); 
        $this->assertEquals($data['result']['users'][1] , 2); 
        $this->assertEquals($data['result']['users'][2] , 3); 
        $this->assertEquals($data['result']['users'][3] , 4); 
        $this->assertEquals($data['result']['users'][4] , 5); 
        $this->assertEquals($data['result']['nb_users'] , 5); 
        $this->assertEquals($data['result']['role'] , "admin"); 
        $this->assertEquals($data['result']['page_id'] , 1); 
        $this->assertEquals($data['result']['id'] , 1); 
        $this->assertEquals($data['result']['name'] , "superTitle"); 
        $this->assertEquals($data['result']['type'] , 1); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 


        
        
    }
    
    /**
     * @depends testPageAdd
     */
    public function testUpdateChannelName($id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'page.update', [
            'id' => $id,
             'title' => "Updated page name"
            ]
        );
        
        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc('page.get', [ 'id' => $id ]);
        
        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc('conversation.get', [ 'id' => $data['result'][$id]['conversation_id'] ]);
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 10); 
        $this->assertEquals($data['result']['item_id'] , null); 
        $this->assertEquals(count($data['result']['message']) , 6); 
        $this->assertEquals($data['result']['message']['id'] , null); 
        $this->assertEquals($data['result']['message']['text'] , null); 
        $this->assertEquals($data['result']['message']['library_id'] , null); 
        $this->assertEquals($data['result']['message']['type'] , null); 
        $this->assertEquals($data['result']['message']['created_date'] , null); 
        $this->assertEquals($data['result']['message']['user_id'] , null); 
        $this->assertEquals(count($data['result']['conversation_user']) , 1); 
        $this->assertEquals($data['result']['conversation_user']['read_date'] , null); 
        $this->assertEquals(count($data['result']['users']) , 5); 
        $this->assertEquals($data['result']['users'][0] , 1); 
        $this->assertEquals($data['result']['users'][1] , 2); 
        $this->assertEquals($data['result']['users'][2] , 3); 
        $this->assertEquals($data['result']['users'][3] , 4); 
        $this->assertEquals($data['result']['users'][4] , 5); 
        $this->assertEquals($data['result']['nb_users'] , 5); 
        $this->assertEquals($data['result']['role'] , "admin"); 
        $this->assertEquals($data['result']['page_id'] , 1); 
        $this->assertEquals($data['result']['id'] , 1); 
        $this->assertEquals($data['result']['name'] , "updatedPageName"); 
        $this->assertEquals($data['result']['type'] , 1); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }
    
    /**
     * @depends testPageAdd
     */
    public function testItemAdd($id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'item.add', [
            'page_id' => $id,
            'title' => 'Ma Section 1',
            'points' => 5,
            'description' => 'une description de section',
            'type' => 'FOLDER',
            'is_available' => false,
            'is_published' => false,
            //'start_date',
            //'end_date',
            //'parent_id'
            ]
        );
        
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
        
        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'item.add', [
            'page_id' => $id,
            'title' => 'Ma Section 2',
            'description' => 'une description de section 2',
            'type' => 'FOLDER',
            'points' => 6,
            'is_available' => false,
            'is_published' => false,
            //'start_date',
            //'end_date',
            //'parent_id'
            ]
        );
        
        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'item.add', [
            'page_id' => $id,
            'title' => 'Ma Section 3',
            'description' => 'une description de section 3',
            'type' => 'FOLDER',
            'points' => 7,
            'is_available' => false,
            'is_published' => false,
            //'start_date',
            //'end_date',
            //'parent_id'
            ]
        );
        
        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'item.add', [
            'page_id' => $id,
            'title' => 'Live class',
            'description' => 'Liveclass description',
            'type' => 'LC',
            'points' => 10,
            'is_available' => false,
            'is_published' => true,
            //'start_date',
            //'end_date',
            'parent_id' => $data['result']
            ]
        );
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 4);
        $this->assertEquals($data['jsonrpc'], 2.0);
        
        return $data['result'];
    }
    
     
    /**
     * @depends testItemAdd
     */
    public function testGetLiveClassConversation($id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('item.get', [ 'id' => $id ]);
        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc('conversation.get', [ 'id' => $data['result']['conversation_id']]);
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 10); 
        $this->assertEquals($data['result']['item_id'] , 4); 
        $this->assertEquals(count($data['result']['message']) , 6); 
        $this->assertEquals($data['result']['message']['id'] , null); 
        $this->assertEquals($data['result']['message']['text'] , null); 
        $this->assertEquals($data['result']['message']['library_id'] , null); 
        $this->assertEquals($data['result']['message']['type'] , null); 
        $this->assertEquals($data['result']['message']['created_date'] , null); 
        $this->assertEquals($data['result']['message']['user_id'] , null); 
        $this->assertEquals(count($data['result']['conversation_user']) , 1); 
        $this->assertEquals($data['result']['conversation_user']['read_date'] , null); 
        $this->assertEquals(count($data['result']['users']) , 5); 
        $this->assertEquals($data['result']['users'][0] , 1); 
        $this->assertEquals($data['result']['users'][1] , 2); 
        $this->assertEquals($data['result']['users'][2] , 3); 
        $this->assertEquals($data['result']['users'][3] , 4); 
        $this->assertEquals($data['result']['users'][4] , 5); 
        $this->assertEquals($data['result']['nb_users'] , 0); 
        $this->assertEquals($data['result']['page_id'] , 1); 
        $this->assertEquals($data['result']['id'] , 2); 
        $this->assertEquals($data['result']['name'] , "Chat"); 
        $this->assertEquals($data['result']['type'] , 3); 
        $this->assertEquals(count($data['result']['options']) , 3); 
        $this->assertEquals($data['result']['options']['record'] , false); 
        $this->assertEquals($data['result']['options']['nb_user_autorecord'] , 2); 
        $this->assertEquals(count($data['result']['options']['rules']) , 10); 
        $this->assertEquals($data['result']['options']['rules']['autoPublishCamera'] , true); 
        $this->assertEquals($data['result']['options']['rules']['autoPublishMicrophone'] , false); 
        $this->assertEquals(count($data['result']['options']['rules']['archive']) , 1); 
        $this->assertEquals(count($data['result']['options']['rules']['archive'][0]) , 1); 
        $this->assertEquals(count($data['result']['options']['rules']['archive'][0]['roles']) , 1); 
        $this->assertEquals($data['result']['options']['rules']['archive'][0]['roles'][0] , "admin"); 
        $this->assertEquals(count($data['result']['options']['rules']['raiseHand']) , 1); 
        $this->assertEquals(count($data['result']['options']['rules']['raiseHand'][0]) , 1); 
        $this->assertEquals(count($data['result']['options']['rules']['raiseHand'][0]['roles']) , 1); 
        $this->assertEquals($data['result']['options']['rules']['raiseHand'][0]['roles'][0] , "user"); 
        $this->assertEquals(count($data['result']['options']['rules']['publish']) , 1); 
        $this->assertEquals(count($data['result']['options']['rules']['publish'][0]) , 1); 
        $this->assertEquals(count($data['result']['options']['rules']['publish'][0]['roles']) , 1); 
        $this->assertEquals($data['result']['options']['rules']['publish'][0]['roles'][0] , "admin"); 
        $this->assertEquals(count($data['result']['options']['rules']['askDevice']) , 1); 
        $this->assertEquals(count($data['result']['options']['rules']['askDevice'][0]) , 1); 
        $this->assertEquals(count($data['result']['options']['rules']['askDevice'][0]['roles']) , 1); 
        $this->assertEquals($data['result']['options']['rules']['askDevice'][0]['roles'][0] , "admin"); 
        $this->assertEquals(count($data['result']['options']['rules']['askScreen']) , 1); 
        $this->assertEquals(count($data['result']['options']['rules']['askScreen'][0]) , 1); 
        $this->assertEquals(count($data['result']['options']['rules']['askScreen'][0]['roles']) , 1); 
        $this->assertEquals($data['result']['options']['rules']['askScreen'][0]['roles'][0] , "admin"); 
        $this->assertEquals(count($data['result']['options']['rules']['forceMute']) , 1); 
        $this->assertEquals(count($data['result']['options']['rules']['forceMute'][0]) , 1); 
        $this->assertEquals(count($data['result']['options']['rules']['forceMute'][0]['roles']) , 1); 
        $this->assertEquals($data['result']['options']['rules']['forceMute'][0]['roles'][0] , "admin"); 
        $this->assertEquals(count($data['result']['options']['rules']['forceUnpublish']) , 1); 
        $this->assertEquals(count($data['result']['options']['rules']['forceUnpublish'][0]) , 1); 
        $this->assertEquals(count($data['result']['options']['rules']['forceUnpublish'][0]['roles']) , 1); 
        $this->assertEquals($data['result']['options']['rules']['forceUnpublish'][0]['roles'][0] , "admin"); 
        $this->assertEquals(count($data['result']['options']['rules']['kick']) , 1); 
        $this->assertEquals(count($data['result']['options']['rules']['kick'][0]) , 1); 
        $this->assertEquals(count($data['result']['options']['rules']['kick'][0]['roles']) , 1); 
        $this->assertEquals($data['result']['options']['rules']['kick'][0]['roles'][0] , "admin"); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 
 

    }
    
    public function testAddGroup()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'group.add', [
            'item_id' => 1,
            'name' => "ungroup"
            ]
        );
        
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 1);
        $this->assertEquals($data['result'][0], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }
    
    public function testAddGroup2()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'group.add', [
            'item_id' => 2,
            'name' => "ungroup2"
            ]
        );
        
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 1);
        $this->assertEquals($data['result'][0], 2);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }
    
    public function testAddUser()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'item.addUsers', [
            'id' => 1,
            'group_name' => "ungroup",
            'user_ids' => [3]
            ]
        );
        
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], true);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }
    
    public function testGroupGetList()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'group.getList', [
            'item_id' => 1
            ]
        );
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 1);
        $this->assertEquals(count($data['result'][1]), 1);
        $this->assertEquals(count($data['result'][1][0]), 3);
        $this->assertEquals($data['result'][1][0]['id'], 1);
        $this->assertEquals($data['result'][1][0]['name'], "ungroup");
        $this->assertEquals($data['result'][1][0]['item_id'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
        
        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'group.getList', [
            'item_id' => [1,2]
            ]
        );
        
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 2);
        $this->assertEquals(count($data['result'][1]), 1);
        $this->assertEquals(count($data['result'][1][0]), 3);
        $this->assertEquals($data['result'][1][0]['id'], 1);
        $this->assertEquals($data['result'][1][0]['name'], "ungroup");
        $this->assertEquals($data['result'][1][0]['item_id'], 1);
        $this->assertEquals(count($data['result'][2]), 1);
        $this->assertEquals(count($data['result'][2][0]), 3);
        $this->assertEquals($data['result'][2][0]['id'], 2);
        $this->assertEquals($data['result'][2][0]['name'], "ungroup2");
        $this->assertEquals($data['result'][2][0]['item_id'], 2);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }
    
    public function testDeleteGroup()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'group.delete', [
            'id' => 1,
            ]
        );
        
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }
    
    public function testGroupGetList1()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'group.getList', [
            'item_id' => [1,2]
            ]
        );
        
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 2);
        $this->assertEquals(count($data['result'][1]), 0);
        $this->assertEquals(count($data['result'][2]), 1);
        $this->assertEquals(count($data['result'][2][0]), 3);
        $this->assertEquals($data['result'][2][0]['id'], 2);
        $this->assertEquals($data['result'][2][0]['name'], "ungroup2");
        $this->assertEquals($data['result'][2][0]['item_id'], 2);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }
}
