<?php
namespace ModuleTest\Api\Service;

use ModuleTest\Api\AbstractService;

class LibraryTest extends AbstractService
{
    public static $session;

    public static function setUpBeforeClass()
    {
        system('phing -q reset-db deploy-db');
        parent::setUpBeforeClass();
    }

   
    public function testLibraryAdd()
    {
        $this->setIdentity(1, 1);
        $data = $this->jsonRpc(
            'library.add', [
            'name' => 'super file',
            'text' => 'super cool',
            'folder_name' => 'folder'
            ]
        );
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 13); 
        $this->assertEquals($data['result']['id'] , 4); 
        $this->assertEquals($data['result']['name'] , "super file"); 
        $this->assertEquals($data['result']['link'] , null); 
        $this->assertEquals($data['result']['token'] , null); 
        $this->assertEquals($data['result']['type'] , "text"); 
        $this->assertEquals(!empty($data['result']['created_date']) , true); 
        $this->assertEquals($data['result']['deleted_date'] , null); 
        $this->assertEquals($data['result']['updated_date'] , null); 
        $this->assertEquals($data['result']['folder_id'] , null); 
        $this->assertEquals($data['result']['owner_id'] , 1); 
        $this->assertEquals($data['result']['box_id'] , null); 
        $this->assertEquals($data['result']['global'] , 0); 
        $this->assertEquals($data['result']['text'] , "super cool"); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 
        

    }
    
    public function testLibraryAdd2()
    {
        $this->setIdentity(1, 1);
        $data = $this->jsonRpc(
            'library.add', [
            'name' => 'super file',
            'text' => 'super cool',
            'folder_name' => 'folder',
            'global' => false
            ]
        );
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 13); 
        $this->assertEquals($data['result']['id'] , 5); 
        $this->assertEquals($data['result']['name'] , "super file"); 
        $this->assertEquals($data['result']['link'] , null); 
        $this->assertEquals($data['result']['token'] , null); 
        $this->assertEquals($data['result']['type'] , "text"); 
        $this->assertEquals(!empty($data['result']['created_date']) , true); 
        $this->assertEquals($data['result']['deleted_date'] , null); 
        $this->assertEquals($data['result']['updated_date'] , null); 
        $this->assertEquals($data['result']['folder_id'] , null); 
        $this->assertEquals($data['result']['owner_id'] , 1); 
        $this->assertEquals($data['result']['box_id'] , null); 
        $this->assertEquals($data['result']['global'] , 0); 
        $this->assertEquals($data['result']['text'] , "super cool"); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }
    
    public function testLibraryAdd3()
    {
        $this->setIdentity(1, 1);
        $data = $this->jsonRpc(
            'library.add', [
            'name' => 'super file',
            'text' => 'super cool',
            'folder_name' => 'folder',
            'global' => true
            ]
        );
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 13); 
        $this->assertEquals($data['result']['id'] , 6); 
        $this->assertEquals($data['result']['name'] , "super file"); 
        $this->assertEquals($data['result']['link'] , null); 
        $this->assertEquals($data['result']['token'] , null); 
        $this->assertEquals($data['result']['type'] , "text"); 
        $this->assertEquals(!empty($data['result']['created_date']) , true); 
        $this->assertEquals($data['result']['deleted_date'] , null); 
        $this->assertEquals($data['result']['updated_date'] , null); 
        $this->assertEquals($data['result']['folder_id'] , null); 
        $this->assertEquals($data['result']['owner_id'] , 1); 
        $this->assertEquals($data['result']['box_id'] , null); 
        $this->assertEquals($data['result']['global'] , 1); 
        $this->assertEquals($data['result']['text'] , "super cool"); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 


    }
    
    public function testLibraryGetList()
    {
        $this->setIdentity(1, 1);
        $data = $this->jsonRpc(
            'library.getList', [
            'user_id' => 1,
            'folder_name' => 'folder'
            ]
        );
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 4); 
        $this->assertEquals($data['result']['count'] , 3); 
        $this->assertEquals(count($data['result']['documents']) , 3); 
        $this->assertEquals(count($data['result']['documents'][0]) , 12); 
        $this->assertEquals($data['result']['documents'][0]['id'] , 6); 
        $this->assertEquals($data['result']['documents'][0]['name'] , "super file"); 
        $this->assertEquals($data['result']['documents'][0]['link'] , null); 
        $this->assertEquals($data['result']['documents'][0]['token'] , null); 
        $this->assertEquals($data['result']['documents'][0]['type'] , "text"); 
        $this->assertEquals(!empty($data['result']['documents'][0]['created_date']) , true); 
        $this->assertEquals($data['result']['documents'][0]['deleted_date'] , null); 
        $this->assertEquals($data['result']['documents'][0]['updated_date'] , null); 
        $this->assertEquals($data['result']['documents'][0]['folder_id'] , null); 
        $this->assertEquals($data['result']['documents'][0]['owner_id'] , 1); 
        $this->assertEquals($data['result']['documents'][0]['box_id'] , null); 
        $this->assertEquals($data['result']['documents'][0]['text'] , "super cool"); 
        $this->assertEquals(count($data['result']['documents'][1]) , 12); 
        $this->assertEquals($data['result']['documents'][1]['id'] , 5); 
        $this->assertEquals($data['result']['documents'][1]['name'] , "super file"); 
        $this->assertEquals($data['result']['documents'][1]['link'] , null); 
        $this->assertEquals($data['result']['documents'][1]['token'] , null); 
        $this->assertEquals($data['result']['documents'][1]['type'] , "text"); 
        $this->assertEquals(!empty($data['result']['documents'][1]['created_date']) , true); 
        $this->assertEquals($data['result']['documents'][1]['deleted_date'] , null); 
        $this->assertEquals($data['result']['documents'][1]['updated_date'] , null); 
        $this->assertEquals($data['result']['documents'][1]['folder_id'] , null); 
        $this->assertEquals($data['result']['documents'][1]['owner_id'] , 1); 
        $this->assertEquals($data['result']['documents'][1]['box_id'] , null); 
        $this->assertEquals($data['result']['documents'][1]['text'] , "super cool"); 
        $this->assertEquals(count($data['result']['documents'][2]) , 12); 
        $this->assertEquals($data['result']['documents'][2]['id'] , 4); 
        $this->assertEquals($data['result']['documents'][2]['name'] , "super file"); 
        $this->assertEquals($data['result']['documents'][2]['link'] , null); 
        $this->assertEquals($data['result']['documents'][2]['token'] , null); 
        $this->assertEquals($data['result']['documents'][2]['type'] , "text"); 
        $this->assertEquals(!empty($data['result']['documents'][2]['created_date']) , true); 
        $this->assertEquals($data['result']['documents'][2]['deleted_date'] , null); 
        $this->assertEquals($data['result']['documents'][2]['updated_date'] , null); 
        $this->assertEquals($data['result']['documents'][2]['folder_id'] , null); 
        $this->assertEquals($data['result']['documents'][2]['owner_id'] , 1); 
        $this->assertEquals($data['result']['documents'][2]['box_id'] , null); 
        $this->assertEquals($data['result']['documents'][2]['text'] , "super cool"); 
        $this->assertEquals($data['result']['folder'] , null); 
        $this->assertEquals($data['result']['parent'] , null); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 


    }
    
     public function testLibraryGetList2()
    {
        $this->setIdentity(1, 1);
        $data = $this->jsonRpc(
            'library.getList', [
            'user_id' => 1,
            'global' => true
            ]
        );
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 4); 
        $this->assertEquals($data['result']['count'] , 3); 
        $this->assertEquals(count($data['result']['documents']) , 3); 
        $this->assertEquals(count($data['result']['documents'][0]) , 12); 
        $this->assertEquals($data['result']['documents'][0]['id'] , 6); 
        $this->assertEquals($data['result']['documents'][0]['name'] , "super file"); 
        $this->assertEquals($data['result']['documents'][0]['link'] , null); 
        $this->assertEquals($data['result']['documents'][0]['token'] , null); 
        $this->assertEquals($data['result']['documents'][0]['type'] , "text"); 
        $this->assertEquals(!empty($data['result']['documents'][0]['created_date']) , true); 
        $this->assertEquals($data['result']['documents'][0]['deleted_date'] , null); 
        $this->assertEquals($data['result']['documents'][0]['updated_date'] , null); 
        $this->assertEquals($data['result']['documents'][0]['folder_id'] , null); 
        $this->assertEquals($data['result']['documents'][0]['owner_id'] , 1); 
        $this->assertEquals($data['result']['documents'][0]['box_id'] , null); 
        $this->assertEquals($data['result']['documents'][0]['text'] , "super cool"); 
        $this->assertEquals(count($data['result']['documents'][1]) , 12); 
        $this->assertEquals($data['result']['documents'][1]['id'] , 5); 
        $this->assertEquals($data['result']['documents'][1]['name'] , "super file"); 
        $this->assertEquals($data['result']['documents'][1]['link'] , null); 
        $this->assertEquals($data['result']['documents'][1]['token'] , null); 
        $this->assertEquals($data['result']['documents'][1]['type'] , "text"); 
        $this->assertEquals(!empty($data['result']['documents'][1]['created_date']) , true); 
        $this->assertEquals($data['result']['documents'][1]['deleted_date'] , null); 
        $this->assertEquals($data['result']['documents'][1]['updated_date'] , null); 
        $this->assertEquals($data['result']['documents'][1]['folder_id'] , null); 
        $this->assertEquals($data['result']['documents'][1]['owner_id'] , 1); 
        $this->assertEquals($data['result']['documents'][1]['box_id'] , null); 
        $this->assertEquals($data['result']['documents'][1]['text'] , "super cool"); 
        $this->assertEquals(count($data['result']['documents'][2]) , 12); 
        $this->assertEquals($data['result']['documents'][2]['id'] , 4); 
        $this->assertEquals($data['result']['documents'][2]['name'] , "super file"); 
        $this->assertEquals($data['result']['documents'][2]['link'] , null); 
        $this->assertEquals($data['result']['documents'][2]['token'] , null); 
        $this->assertEquals($data['result']['documents'][2]['type'] , "text"); 
        $this->assertEquals(!empty($data['result']['documents'][2]['created_date']) , true); 
        $this->assertEquals($data['result']['documents'][2]['deleted_date'] , null); 
        $this->assertEquals($data['result']['documents'][2]['updated_date'] , null); 
        $this->assertEquals($data['result']['documents'][2]['folder_id'] , null); 
        $this->assertEquals($data['result']['documents'][2]['owner_id'] , 1); 
        $this->assertEquals($data['result']['documents'][2]['box_id'] , null); 
        $this->assertEquals($data['result']['documents'][2]['text'] , "super cool"); 
        $this->assertEquals($data['result']['folder'] , null); 
        $this->assertEquals($data['result']['parent'] , null); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 
    }
    
    
    
     public function testLibraryUpdate()
    {
        $this->setIdentity(1, 1);
        $data = $this->jsonRpc(
            'library.update', [
            'id' => 1,
            'folder_id' => 1
            ]
        );
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals($data['result'] , 0); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }
    
     public function testLibraryUpdate2()
    {
        $this->setIdentity(1, 1);
        $data = $this->jsonRpc(
            'library.update', [
            'id' => 1,
            'type' => 'text',
            'token' => 'token',
             'name' => 'library title'
            ]
        );
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 13); 
        $this->assertEquals($data['result']['id'] , 1); 
        $this->assertEquals($data['result']['name'] , "library title"); 
        $this->assertEquals($data['result']['link'] , null); 
        $this->assertEquals($data['result']['token'] , "token"); 
        $this->assertEquals($data['result']['type'] , "text"); 
        $this->assertEquals($data['result']['created_date'] , null); 
        $this->assertEquals($data['result']['deleted_date'] , null); 
        $this->assertEquals(!empty($data['result']['updated_date']) , true); 
        $this->assertEquals($data['result']['folder_id'] , null); 
        $this->assertEquals($data['result']['owner_id'] , null); 
        $this->assertEquals($data['result']['box_id'] , null); 
        $this->assertEquals($data['result']['global'] , 0); 
        $this->assertEquals($data['result']['text'] , null); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }
    /*
    public function testLibraryGetSession()
    {
        $this->setIdentity(1, 1);
        $data = $this->jsonRpc(
            'library.getSession', [
                'id' => 1,
            ]
        );
        
        $this->printCreateTest($data);
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 13);
        $this->assertEquals($data['result']['id'], 4);
        $this->assertEquals($data['result']['name'], "super file");
        $this->assertEquals($data['result']['link'], null);
        $this->assertEquals($data['result']['token'], null);
        $this->assertEquals($data['result']['type'], "text");
        $this->assertEquals(!empty($data['result']['created_date']), true);
        $this->assertEquals($data['result']['deleted_date'], null);
        $this->assertEquals($data['result']['updated_date'], null);
        $this->assertEquals($data['result']['folder_id'], null);
        $this->assertEquals($data['result']['owner_id'], 1);
        $this->assertEquals($data['result']['box_id'], null);
        $this->assertEquals($data['result']['global'], 0);
        $this->assertEquals($data['result']['text'], "super cool");
        $this->assertEquals($data['jsonrpc'], 2.0);

    }*/

    
   
}
