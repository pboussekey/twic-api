<?php
namespace ModuleTest\Api\Service;

use ModuleTest\Api\AbstractService;

class ItemTest extends AbstractService
{
    public static function setUpBeforeClass()
    {
        system('bin/phing -q reset-db deploy-db');

        parent::setUpBeforeClass();
    }
    
    public function testInit(){
        $data = $this->jsonRpc('user.login', ['user' => 'crobert@thestudnet.com','password' => 'thestudnet']);
        $this->reset();
        $this->setIdentity(3);
        $data = $this->jsonRpc('user.registerFcm', [
          'uuid' => 3,
          'token' => 'azertyuiop',
          'package' => 'azertyuiop'
        ]);
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals($data['result'] , 1); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 
        
        $this->reset();
        $this->setIdentity(1,1);
        $data = $this->jsonRpc('page.add', [
            'title' => 'Organization',
            'type'=>'organization',
            'description' => 'description',
            'users' => [
                ['user_id' => 1,'role' => 'admin', 'state' => 'member'],
                ['user_id' => 2,'role' => 'admin', 'state' => 'member'],
                ['user_id' => 3,'role' => 'user', 'state' => 'member'],
                ['user_id' => 4,'role' => 'user', 'state' => 'member'],
                ['user_id' => 5,'role' => 'user', 'state' => 'member'],
            ]
        ]);
        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals($data['result'] , 1);
        $this->assertEquals($data['jsonrpc'] , 2.0);
    }

    public function testPageAdd()
    {
        
        $this->setIdentity(1,1);
        $data = $this->jsonRpc('page.add', [
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
        ]);

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 2);
        $this->assertEquals($data['jsonrpc'], 2.0);

        return $data['result'];
    }

    /**
     * @depends testPageAdd
     */
    public function testItemAdd($id)
    {
        $items = [];
        $this->setIdentity(1);
        $data = $this->jsonRpc('item.add', [
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
        ]);
        $section = $data['result'];
        $items[] = $data['result'];

        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals($data['result'] , 1);
        $this->assertEquals($data['jsonrpc'] , 2.0);

        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc('item.add', [
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
        ]);
        $items[] = $data['result'];

        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc('item.add', [
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
        ]);
        
        $items[] = $data['result'];

        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc('item.add', [
          'page_id' => $id,
          'title' => 'Ma Section 3',
          'description' => 'une description de section 3',
          'type' => 'PG',
          'points' => 10,
          'is_available' => false,
          'is_published' => false,
          //'start_date',
          //'end_date',
          'parent_id' => $section
        ]);
        $items[] = $data['result'];

        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals($data['result'] , 4);
        $this->assertEquals($data['jsonrpc'] , 2.0);
        
        
        $this->reset();
        $this->setIdentity(1);
        $post = $this->jsonRpc('post.add', []);
        
        
        $this->reset();
        $this->setIdentity(1);
        $quiz = $this->jsonRpc('quiz.add', []);
        
        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc('item.add', [
          'page_id' => $id,
          'title' => 'Live class',
          'description' => 'Live class description',
          'type' => 'LC',
          'is_available' => false,
          'is_published' => false,
          //'start_date',
          //'end_date',
          'quiz_id' => $quiz['result'],
          'post_id' => $post['result'],
          'parent_id' => $section
        ]);
        $items[] = $data['result'];
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals($data['result'] , 5); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 
        
        
        
        

        
        return $items;
    }
    
     /**
     * @depends testPageAdd
     */
    public function testItemAddError($id)
    {
        $this->setIdentity(3);
        $data = $this->jsonRpc('item.add', [
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
        ]);
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -38003); 
        $this->assertEquals($data['error']['message'] , "Unauthorized operation item.add"); 
        $this->assertEquals($data['error']['data'] , null); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

        
    }


    /**
     * @depends testItemAdd
     */
    public function testItemUpdate($ids)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('item.update', [
          'id' => $ids[4],
          'title' => 'Updated liveclass',
          'description' => 'une description de section',
          'is_available' => true,
          'is_published' => true,
          'start_date' => '2015-10-16T09:00:00Z',
          'end_date' => '2015-10-16T11:00:00Z',
          'notify' => true,
          'points' => 11,
          'quiz_id' => 1, 
          'post_id' => 1
        ]);
        
        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals($data['result'] , 1);
        $this->assertEquals($data['jsonrpc'] , 2.0);
    }
    
    
      /**
     * @depends testItemAdd
     */
    public function testItemUpdateAfterPublished($ids)
    {
        
        $this->setIdentity(1);
        $data = $this->jsonRpc('item.update', [
          'id' => $ids[0],
          'is_published' => true,
          'notify' => true
        ]);
        
        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc('item.update', [
          'id' => $ids[0],
          'is_published' => false,
        ]);
        
        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc('item.update', [
          'id' => $ids[4],
          'title' => 'Updated liveclass 2',
          'notify' => true
        ]);

        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals($data['result'] , 1);
        $this->assertEquals($data['jsonrpc'] , 2.0);
    }
    
    /**
     * @depends testItemAdd
     */
    public function testItemUpdateError($ids)
    {
        $this->setIdentity(3);
        $data = $this->jsonRpc('item.update', [
          'id' => $ids[4],
          'title' => 'Updated liveclass',
          'description' => 'une description de section',
          'is_available' => true,
          'is_published' => true,
          'points' => 11,
        ]);
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -38003); 
        $this->assertEquals($data['error']['message'] , "Unauthorized operation item.update"); 
        $this->assertEquals($data['error']['data'] , null); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 
    }

    /**
     * @depends testPageAdd
     */
    public function testGetListId($id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('item.getListId', [
          'page_id' => [$id],
        //  'parent_id' =>
        ]);
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 1); 
        $this->assertEquals(count($data['result'][2]) , 3); 
        $this->assertEquals($data['result'][2][0] , 1); 
        $this->assertEquals($data['result'][2][1] , 2); 
        $this->assertEquals($data['result'][2][2] , 3); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }

    /**
     * @depends testPageAdd
     */
    public function testMove()
    {
      $this->setIdentity(1);
      $data = $this->jsonRpc('item.move', [
        'id' => 3,
        'order_id' => 1
      ]);
      $this->assertEquals(count($data) , 3);
    }
    /**
     * @depends testPageAdd
     */
    public function testMove2()
    {
      $this->setIdentity(1);
      $data = $this->jsonRpc('item.move', [
        'id' => 3,
        'order_id' => 0
      ]);

      $this->assertEquals(count($data) , 3);
    }
    /**
     * @depends testPageAdd
     */
    public function testMoveError()
    {
        $this->setIdentity(3);
        $data = $this->jsonRpc('item.move', [
          'id' => 3,
          'order_id' => 1
        ]);
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -38003); 
        $this->assertEquals($data['error']['message'] , "Unauthorized operation item.move"); 
        $this->assertEquals($data['error']['data'] , null); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }

    /**
     * @depends testPageAdd
     * @depends testItemAdd
     */
    public function testGetListIdParent($id, $ids)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('item.getListId', [
          'parent_id' => $ids[0],
        ]);
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 1); 
        $this->assertEquals(count($data['result'][1]) , 2); 
        $this->assertEquals($data['result'][1][0] , 4); 
        $this->assertEquals($data['result'][1][1] , 5); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }
    
     /**
     * @depends testPageAdd
     * @depends testItemAdd
     */
    public function testGetListIdParent2($id, $ids)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('item.getListId', [
          'parent_id' => [$ids[0], $ids[1]],
        ]);
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 2); 
        $this->assertEquals(count($data['result'][1]) , 2); 
        $this->assertEquals($data['result'][1][0] , 4); 
        $this->assertEquals($data['result'][1][1] , 5); 
        $this->assertEquals(count($data['result'][2]) , 0); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 


    }



    /**
     * @depends testItemAdd
     */
    public function testGet($items)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('item.get', [
          'id' => $items
        ]);
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 5); 
        $this->assertEquals(count($data['result'][1]) , 22); 
        $this->assertEquals($data['result'][1]['post_id'] , null); 
        $this->assertEquals($data['result'][1]['quiz_id'] , null); 
        $this->assertEquals($data['result'][1]['id'] , 1); 
        $this->assertEquals($data['result'][1]['title'] , "Ma Section 1"); 
        $this->assertEquals($data['result'][1]['description'] , "une description de section"); 
        $this->assertEquals($data['result'][1]['type'] , "FOLDER"); 
        $this->assertEquals($data['result'][1]['is_available'] , 0); 
        $this->assertEquals($data['result'][1]['is_published'] , 0); 
        $this->assertEquals($data['result'][1]['order'] , 2); 
        $this->assertEquals($data['result'][1]['start_date'] , null); 
        $this->assertEquals($data['result'][1]['end_date'] , null); 
        $this->assertEquals(!empty($data['result'][1]['updated_date']) , true); 
        $this->assertEquals(!empty($data['result'][1]['created_date']) , true); 
        $this->assertEquals($data['result'][1]['parent_id'] , null); 
        $this->assertEquals($data['result'][1]['page_id'] , 2); 
        $this->assertEquals($data['result'][1]['user_id'] , 1); 
        $this->assertEquals($data['result'][1]['points'] , 5); 
        $this->assertEquals($data['result'][1]['text'] , null); 
        $this->assertEquals($data['result'][1]['library_id'] , null); 
        $this->assertEquals($data['result'][1]['participants'] , "all"); 
        $this->assertEquals($data['result'][1]['is_grade_published'] , null); 
        $this->assertEquals($data['result'][1]['conversation_id'] , null); 
        $this->assertEquals(count($data['result'][2]) , 22); 
        $this->assertEquals($data['result'][2]['post_id'] , null); 
        $this->assertEquals($data['result'][2]['quiz_id'] , null); 
        $this->assertEquals($data['result'][2]['id'] , 2); 
        $this->assertEquals($data['result'][2]['title'] , "Ma Section 2"); 
        $this->assertEquals($data['result'][2]['description'] , "une description de section 2"); 
        $this->assertEquals($data['result'][2]['type'] , "FOLDER"); 
        $this->assertEquals($data['result'][2]['is_available'] , 0); 
        $this->assertEquals($data['result'][2]['is_published'] , 0); 
        $this->assertEquals($data['result'][2]['order'] , 4); 
        $this->assertEquals($data['result'][2]['start_date'] , null); 
        $this->assertEquals($data['result'][2]['end_date'] , null); 
        $this->assertEquals($data['result'][2]['updated_date'] , null); 
        $this->assertEquals(!empty($data['result'][2]['created_date']) , true); 
        $this->assertEquals($data['result'][2]['parent_id'] , null); 
        $this->assertEquals($data['result'][2]['page_id'] , 2); 
        $this->assertEquals($data['result'][2]['user_id'] , 1); 
        $this->assertEquals($data['result'][2]['points'] , 6); 
        $this->assertEquals($data['result'][2]['text'] , null); 
        $this->assertEquals($data['result'][2]['library_id'] , null); 
        $this->assertEquals($data['result'][2]['participants'] , "all"); 
        $this->assertEquals($data['result'][2]['is_grade_published'] , null); 
        $this->assertEquals($data['result'][2]['conversation_id'] , null); 
        $this->assertEquals(count($data['result'][3]) , 22); 
        $this->assertEquals($data['result'][3]['post_id'] , null); 
        $this->assertEquals($data['result'][3]['quiz_id'] , null); 
        $this->assertEquals($data['result'][3]['id'] , 3); 
        $this->assertEquals($data['result'][3]['title'] , "Ma Section 3"); 
        $this->assertEquals($data['result'][3]['description'] , "une description de section 3"); 
        $this->assertEquals($data['result'][3]['type'] , "FOLDER"); 
        $this->assertEquals($data['result'][3]['is_available'] , 0); 
        $this->assertEquals($data['result'][3]['is_published'] , 0); 
        $this->assertEquals($data['result'][3]['order'] , 1); 
        $this->assertEquals($data['result'][3]['start_date'] , null); 
        $this->assertEquals($data['result'][3]['end_date'] , null); 
        $this->assertEquals($data['result'][3]['updated_date'] , null); 
        $this->assertEquals(!empty($data['result'][3]['created_date']) , true); 
        $this->assertEquals($data['result'][3]['parent_id'] , null); 
        $this->assertEquals($data['result'][3]['page_id'] , 2); 
        $this->assertEquals($data['result'][3]['user_id'] , 1); 
        $this->assertEquals($data['result'][3]['points'] , 7); 
        $this->assertEquals($data['result'][3]['text'] , null); 
        $this->assertEquals($data['result'][3]['library_id'] , null); 
        $this->assertEquals($data['result'][3]['participants'] , "all"); 
        $this->assertEquals($data['result'][3]['is_grade_published'] , null); 
        $this->assertEquals($data['result'][3]['conversation_id'] , null); 
        $this->assertEquals(count($data['result'][4]) , 22); 
        $this->assertEquals($data['result'][4]['post_id'] , null); 
        $this->assertEquals($data['result'][4]['quiz_id'] , null); 
        $this->assertEquals($data['result'][4]['id'] , 4); 
        $this->assertEquals($data['result'][4]['title'] , "Ma Section 3"); 
        $this->assertEquals($data['result'][4]['description'] , "une description de section 3"); 
        $this->assertEquals($data['result'][4]['type'] , "PG"); 
        $this->assertEquals($data['result'][4]['is_available'] , 0); 
        $this->assertEquals($data['result'][4]['is_published'] , 0); 
        $this->assertEquals($data['result'][4]['order'] , 1); 
        $this->assertEquals($data['result'][4]['start_date'] , null); 
        $this->assertEquals($data['result'][4]['end_date'] , null); 
        $this->assertEquals($data['result'][4]['updated_date'] , null); 
        $this->assertEquals(!empty($data['result'][4]['created_date']) , true); 
        $this->assertEquals($data['result'][4]['parent_id'] , 1); 
        $this->assertEquals($data['result'][4]['page_id'] , 2); 
        $this->assertEquals($data['result'][4]['user_id'] , 1); 
        $this->assertEquals($data['result'][4]['points'] , 10); 
        $this->assertEquals($data['result'][4]['text'] , null); 
        $this->assertEquals($data['result'][4]['library_id'] , null); 
        $this->assertEquals($data['result'][4]['participants'] , "all"); 
        $this->assertEquals($data['result'][4]['is_grade_published'] , null); 
        $this->assertEquals($data['result'][4]['conversation_id'] , null); 
        $this->assertEquals(count($data['result'][5]) , 22); 
        $this->assertEquals($data['result'][5]['post_id'] , 9); 
        $this->assertEquals($data['result'][5]['quiz_id'] , 1); 
        $this->assertEquals($data['result'][5]['id'] , 5); 
        $this->assertEquals($data['result'][5]['title'] , "Updated liveclass 2"); 
        $this->assertEquals($data['result'][5]['description'] , "une description de section"); 
        $this->assertEquals($data['result'][5]['type'] , "LC"); 
        $this->assertEquals($data['result'][5]['is_available'] , 1); 
        $this->assertEquals($data['result'][5]['is_published'] , 1); 
        $this->assertEquals($data['result'][5]['order'] , 2); 
        $this->assertEquals(!empty($data['result'][5]['start_date']) , true); 
        $this->assertEquals(!empty($data['result'][5]['end_date']) , true); 
        $this->assertEquals(!empty($data['result'][5]['updated_date']) , true); 
        $this->assertEquals(!empty($data['result'][5]['created_date']) , true); 
        $this->assertEquals($data['result'][5]['parent_id'] , 1); 
        $this->assertEquals($data['result'][5]['page_id'] , 2); 
        $this->assertEquals($data['result'][5]['user_id'] , 1); 
        $this->assertEquals($data['result'][5]['points'] , 11); 
        $this->assertEquals($data['result'][5]['text'] , null); 
        $this->assertEquals($data['result'][5]['library_id'] , null); 
        $this->assertEquals($data['result'][5]['participants'] , "all"); 
        $this->assertEquals($data['result'][5]['is_grade_published'] , null); 
        $this->assertEquals($data['result'][5]['conversation_id'] , 2); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 




 



    }

    public function testAddUsers()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('item.addUsers', [
          'id' => 1,
          'user_ids' => 3,
          //'group_id' => 0,
          //'group_name' => ''
        ]);

        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals($data['result'] , true);
        $this->assertEquals($data['jsonrpc'] , 2.0);
    }


    public function testAddUsers2()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('item.addUsers', [
          'id' => 1,
          'user_ids' => [4,5],
          'group_name' => 'Group'
        ]);

        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals($data['result'] , true);
        $this->assertEquals($data['jsonrpc'] , 2.0);
    }
    
    
    public function testAddUsers3()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('item.addUsers', [
          'id' => 1,
          'user_ids' => [6],
          'group_name' => 'Group'
        ]);

        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals($data['result'] , true);
        $this->assertEquals($data['jsonrpc'] , 2.0);
    }
    
    public function testGetListItemUser()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('item.getListItemUser', [
          'id' => 1
        ]);
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 1); 
        $this->assertEquals(count($data['result'][1]) , 4); 
        $this->assertEquals(count($data['result'][1][0]) , 7); 
        $this->assertEquals(count($data['result'][1][0]['submission']) , 4); 
        $this->assertEquals($data['result'][1][0]['submission']['id'] , null); 
        $this->assertEquals($data['result'][1][0]['submission']['submit_date'] , null); 
        $this->assertEquals($data['result'][1][0]['submission']['is_graded'] , null); 
        $this->assertEquals($data['result'][1][0]['submission']['post_id'] , null); 
        $this->assertEquals($data['result'][1][0]['group'] , null); 
        $this->assertEquals($data['result'][1][0]['id'] , 1); 
        $this->assertEquals($data['result'][1][0]['user_id'] , 3); 
        $this->assertEquals($data['result'][1][0]['item_id'] , 1); 
        $this->assertEquals($data['result'][1][0]['rate'] , null); 
        $this->assertEquals($data['result'][1][0]['submission_id'] , null); 
        $this->assertEquals(count($data['result'][1][1]) , 7); 
        $this->assertEquals(count($data['result'][1][1]['submission']) , 4); 
        $this->assertEquals($data['result'][1][1]['submission']['id'] , null); 
        $this->assertEquals($data['result'][1][1]['submission']['submit_date'] , null); 
        $this->assertEquals($data['result'][1][1]['submission']['is_graded'] , null); 
        $this->assertEquals($data['result'][1][1]['submission']['post_id'] , null); 
        $this->assertEquals(count($data['result'][1][1]['group']) , 2); 
        $this->assertEquals($data['result'][1][1]['group']['id'] , 1); 
        $this->assertEquals($data['result'][1][1]['group']['name'] , "Group"); 
        $this->assertEquals($data['result'][1][1]['id'] , 2); 
        $this->assertEquals($data['result'][1][1]['user_id'] , 4); 
        $this->assertEquals($data['result'][1][1]['item_id'] , 1); 
        $this->assertEquals($data['result'][1][1]['rate'] , null); 
        $this->assertEquals($data['result'][1][1]['submission_id'] , null); 
        $this->assertEquals(count($data['result'][1][2]) , 7); 
        $this->assertEquals(count($data['result'][1][2]['submission']) , 4); 
        $this->assertEquals($data['result'][1][2]['submission']['id'] , null); 
        $this->assertEquals($data['result'][1][2]['submission']['submit_date'] , null); 
        $this->assertEquals($data['result'][1][2]['submission']['is_graded'] , null); 
        $this->assertEquals($data['result'][1][2]['submission']['post_id'] , null); 
        $this->assertEquals(count($data['result'][1][2]['group']) , 2); 
        $this->assertEquals($data['result'][1][2]['group']['id'] , 1); 
        $this->assertEquals($data['result'][1][2]['group']['name'] , "Group"); 
        $this->assertEquals($data['result'][1][2]['id'] , 3); 
        $this->assertEquals($data['result'][1][2]['user_id'] , 5); 
        $this->assertEquals($data['result'][1][2]['item_id'] , 1); 
        $this->assertEquals($data['result'][1][2]['rate'] , null); 
        $this->assertEquals($data['result'][1][2]['submission_id'] , null); 
        $this->assertEquals(count($data['result'][1][3]) , 7); 
        $this->assertEquals(count($data['result'][1][3]['submission']) , 4); 
        $this->assertEquals($data['result'][1][3]['submission']['id'] , null); 
        $this->assertEquals($data['result'][1][3]['submission']['submit_date'] , null); 
        $this->assertEquals($data['result'][1][3]['submission']['is_graded'] , null); 
        $this->assertEquals($data['result'][1][3]['submission']['post_id'] , null); 
        $this->assertEquals(count($data['result'][1][3]['group']) , 2); 
        $this->assertEquals($data['result'][1][3]['group']['id'] , 1); 
        $this->assertEquals($data['result'][1][3]['group']['name'] , "Group"); 
        $this->assertEquals($data['result'][1][3]['id'] , 4); 
        $this->assertEquals($data['result'][1][3]['user_id'] , 6); 
        $this->assertEquals($data['result'][1][3]['item_id'] , 1); 
        $this->assertEquals($data['result'][1][3]['rate'] , null); 
        $this->assertEquals($data['result'][1][3]['submission_id'] , null); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 


    }

    public function testDeleteUsers()
    {
      $this->setIdentity(1);
      $data = $this->jsonRpc('item.deleteUsers', [
        'id' => 1,
        'user_ids' =>[3,5]
      ]);

      $this->assertEquals(count($data) , 3);
      $this->assertEquals($data['id'] , 1);
      $this->assertEquals($data['result'] , true);
      $this->assertEquals($data['jsonrpc'] , 2.0);
    }

    public function testGetListItemUserDeux()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('item.getListItemUser', [
          'id' => 1
        ]);
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 1); 
        $this->assertEquals(count($data['result'][1]) , 2); 
        $this->assertEquals(count($data['result'][1][0]) , 7); 
        $this->assertEquals(count($data['result'][1][0]['submission']) , 4); 
        $this->assertEquals($data['result'][1][0]['submission']['id'] , null); 
        $this->assertEquals($data['result'][1][0]['submission']['submit_date'] , null); 
        $this->assertEquals($data['result'][1][0]['submission']['is_graded'] , null); 
        $this->assertEquals($data['result'][1][0]['submission']['post_id'] , null); 
        $this->assertEquals(count($data['result'][1][0]['group']) , 2); 
        $this->assertEquals($data['result'][1][0]['group']['id'] , 1); 
        $this->assertEquals($data['result'][1][0]['group']['name'] , "Group"); 
        $this->assertEquals($data['result'][1][0]['id'] , 2); 
        $this->assertEquals($data['result'][1][0]['user_id'] , 4); 
        $this->assertEquals($data['result'][1][0]['item_id'] , 1); 
        $this->assertEquals($data['result'][1][0]['rate'] , null); 
        $this->assertEquals($data['result'][1][0]['submission_id'] , null); 
        $this->assertEquals(count($data['result'][1][1]) , 7); 
        $this->assertEquals(count($data['result'][1][1]['submission']) , 4); 
        $this->assertEquals($data['result'][1][1]['submission']['id'] , null); 
        $this->assertEquals($data['result'][1][1]['submission']['submit_date'] , null); 
        $this->assertEquals($data['result'][1][1]['submission']['is_graded'] , null); 
        $this->assertEquals($data['result'][1][1]['submission']['post_id'] , null); 
        $this->assertEquals(count($data['result'][1][1]['group']) , 2); 
        $this->assertEquals($data['result'][1][1]['group']['id'] , 1); 
        $this->assertEquals($data['result'][1][1]['group']['name'] , "Group"); 
        $this->assertEquals($data['result'][1][1]['id'] , 4); 
        $this->assertEquals($data['result'][1][1]['user_id'] , 6); 
        $this->assertEquals($data['result'][1][1]['item_id'] , 1); 
        $this->assertEquals($data['result'][1][1]['rate'] , null); 
        $this->assertEquals($data['result'][1][1]['submission_id'] , null); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 


    }

    public function testAddUsersDD()
    {
      $this->setIdentity(1);
      $data = $this->jsonRpc('item.addUsers', [
        'id' => 1,
        'user_ids' => [3,4,6],
        //'group_id' => 0,
        //'group_name' => ''
      ]);

      $this->assertEquals(count($data) , 3);
      $this->assertEquals($data['id'] , 1);
      $this->assertEquals($data['result'] , true);
      $this->assertEquals($data['jsonrpc'] , 2.0);
    }

    public function testGetListItemUserDD()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('item.getListItemUser', [
          'id' => 1
        ]);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 1); 
        $this->assertEquals(count($data['result'][1]) , 3); 
        $this->assertEquals(count($data['result'][1][0]) , 7); 
        $this->assertEquals(count($data['result'][1][0]['submission']) , 4); 
        $this->assertEquals($data['result'][1][0]['submission']['id'] , null); 
        $this->assertEquals($data['result'][1][0]['submission']['submit_date'] , null); 
        $this->assertEquals($data['result'][1][0]['submission']['is_graded'] , null); 
        $this->assertEquals($data['result'][1][0]['submission']['post_id'] , null); 
        $this->assertEquals($data['result'][1][0]['group'] , null); 
        $this->assertEquals($data['result'][1][0]['id'] , 1); 
        $this->assertEquals($data['result'][1][0]['user_id'] , 3); 
        $this->assertEquals($data['result'][1][0]['item_id'] , 1); 
        $this->assertEquals($data['result'][1][0]['rate'] , null); 
        $this->assertEquals($data['result'][1][0]['submission_id'] , null); 
        $this->assertEquals(count($data['result'][1][1]) , 7); 
        $this->assertEquals(count($data['result'][1][1]['submission']) , 4); 
        $this->assertEquals($data['result'][1][1]['submission']['id'] , null); 
        $this->assertEquals($data['result'][1][1]['submission']['submit_date'] , null); 
        $this->assertEquals($data['result'][1][1]['submission']['is_graded'] , null); 
        $this->assertEquals($data['result'][1][1]['submission']['post_id'] , null); 
        $this->assertEquals(count($data['result'][1][1]['group']) , 2); 
        $this->assertEquals($data['result'][1][1]['group']['id'] , 1); 
        $this->assertEquals($data['result'][1][1]['group']['name'] , "Group"); 
        $this->assertEquals($data['result'][1][1]['id'] , 2); 
        $this->assertEquals($data['result'][1][1]['user_id'] , 4); 
        $this->assertEquals($data['result'][1][1]['item_id'] , 1); 
        $this->assertEquals($data['result'][1][1]['rate'] , null); 
        $this->assertEquals($data['result'][1][1]['submission_id'] , null); 
        $this->assertEquals(count($data['result'][1][2]) , 7); 
        $this->assertEquals(count($data['result'][1][2]['submission']) , 4); 
        $this->assertEquals($data['result'][1][2]['submission']['id'] , null); 
        $this->assertEquals($data['result'][1][2]['submission']['submit_date'] , null); 
        $this->assertEquals($data['result'][1][2]['submission']['is_graded'] , null); 
        $this->assertEquals($data['result'][1][2]['submission']['post_id'] , null); 
        $this->assertEquals(count($data['result'][1][2]['group']) , 2); 
        $this->assertEquals($data['result'][1][2]['group']['id'] , 1); 
        $this->assertEquals($data['result'][1][2]['group']['name'] , "Group"); 
        $this->assertEquals($data['result'][1][2]['id'] , 4); 
        $this->assertEquals($data['result'][1][2]['user_id'] , 6); 
        $this->assertEquals($data['result'][1][2]['item_id'] , 1); 
        $this->assertEquals($data['result'][1][2]['rate'] , null); 
        $this->assertEquals($data['result'][1][2]['submission_id'] , null); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 


    }
    
    
    /**
     * @depends testPageAdd
     * @depends testItemAdd
     */
    public function testItemStarting($id, $ids)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('item.starting', [
          'id' => $ids[4]
        ]);
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals($data['result'] , null); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 
    }
    
     /**
     * @depends testPageAdd
     * @depends testItemAdd
     */
    public function testDeleteError($id, $ids)
    {
        $this->setIdentity(3);
        $data = $this->jsonRpc('item.delete', [
          'id' => $ids[4]
        ]);
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -38003); 
        $this->assertEquals($data['error']['message'] , "Unauthorized operation item.delete"); 
        $this->assertEquals($data['error']['data'] , null); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }

    /**
     * @depends testPageAdd
     * @depends testItemAdd
     */
    public function testDelete($id, $ids)
    {
      $this->setIdentity(1);
      $data = $this->jsonRpc('item.delete', [
        'id' => $ids[4]
      ]);

      $this->assertEquals(count($data) , 3);
      $this->assertEquals($data['id'] , 1);
      $this->assertEquals($data['result'] , 1);
      $this->assertEquals($data['jsonrpc'] , 2.0);
    }

}
