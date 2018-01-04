<?php
namespace ModuleTest\Api\Service;

use ModuleTest\Api\AbstractService;

class PostTest extends AbstractService
{
    public static function setUpBeforeClass()
    {
        system('phing -q reset-db deploy-db');
        parent::setUpBeforeClass();
    }

    public function testPostLinkPreview()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'post.linkPreview', [
            'url' => 'http://thestudnet.com/',
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 3);
        $this->assertEquals(count($data['result']['meta']), 8);
        $this->assertEquals($data['result']['meta']['twitter:card'], "summary");
        $this->assertEquals($data['result']['meta']['twitter:site'], "@TwicSLE");
        $this->assertEquals($data['result']['meta']['twitter:title'], "TWIC, The Social Learning Environment for B-Schools");
        $this->assertEquals(!empty($data['result']['meta']['twitter:description']), true);
        $this->assertEquals($data['result']['meta']['twitter:image'], "https://twicbythestudnet.com/assets/images/Logo.svg");
        $this->assertEquals($data['result']['meta']['twitter:image:alt'], "Twic logo");
        $this->assertEquals($data['result']['meta']['viewport'], "width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1");
        $this->assertEquals(!empty($data['result']['meta']['description']), true);
        $this->assertEquals(count($data['result']['open_graph']), 7);
        $this->assertEquals($data['result']['open_graph']['title'], "TWIC, The Social Learning Environment for B-Schools");
        $this->assertEquals($data['result']['open_graph']['type'], "website");
        $this->assertEquals($data['result']['open_graph']['url'], "https://twicbythestudnet.com");
        $this->assertEquals($data['result']['open_graph']['image'], "https://twicbythestudnet.com/assets/images/Logo.svg");
        $this->assertEquals(!empty($data['result']['open_graph']['description']), true);
        $this->assertEquals($data['result']['open_graph']['locale'], "en_US");
        $this->assertEquals($data['result']['open_graph']['site_name'], "TWIC");
        $this->assertEquals(count($data['result']['images']), 0);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    public function testPostAdd()
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
            'users' => [
                ['user_id' => 1,'role' => 'user', 'state' => 'member'],
                ['user_id' => 2,'role' => 'admin', 'state' => 'member'],
                ['user_id' => 3,'role' => 'admin', 'state' => 'member'],
                ['user_email' => 'contact@paul-boussekey.com','role' => 'user', 'state' => 'pending']
            ],
            'tags' => [
                'toto', 'tata', 'tutu'
            ],
            'docs' => [
                ['name' => 'name', 'link' => 'link', 'type' => 'type']
            ],
            'address' => [
              "street_no"   => "11",
              "street_name" => "Allée des Chênes",
              "city"        => ["name" => "Villefontaine", "libelle" => "VILLEFONTAINE"],
              "division"    => ["name" => "Auvergne-Rhône-Alpes"],
              "country"     => ["short_name" => "France"],
              "latitude"    => 45.601569,
              "longitude"   => 5.178744499999993,
            ],
            ]
        );
        
        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'page.add', [
            'title' => 'Course',
            'confidentiality' => 0,
            'page_id' => 1,
            'type' => 'course',
            'description' => 'description',
            'is_published' => true,
            'users' => [
                ['user_id' => 1,'role' => 'user', 'state' => 'member'],
                ['user_id' => 2,'role' => 'admin', 'state' => 'member'],
                ['user_id' => 3,'role' => 'admin', 'state' => 'member'],
                ['user_id' => 8,'role' => 'user', 'state' => 'member']
            ]
            ]
        );
        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'post.add', [
            'content' => 'content #toto @[\'U\',1]',
            'link' => 'link',
            'picture' => 'picture',
            'name_picture' => 'name_picture',
            'link_title' => 'link_title',
            'link_desc' => 'link_desc',
            't_page_id' => $data['result'],
            'page_id' => 1,
            'data' => ['test' => 'test'],
            //'t_user_id' => 1,
            'docs' => [
                ['name' => 'name', 'link' => 'link', 'type' => 'type'],
                ['name' => 'name2', 'link' => 'link2', 'type' => 'type2'],
            ]
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 7);
        $this->assertEquals($data['jsonrpc'], 2.0);

        return $data['result'];
    }
       /**
     * @depends testPostAdd
     */
    public function testPostAdd2($post_id)
    {
        $this->setIdentity(2);
        $data = $this->jsonRpc(
            'post.add', [
            'content' => 'Other post',
            't_page_id' => 1,
            ]
            );
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals($data['result'] , 8); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 
        
        return $data['result'];
    }
     
  
     /**
     * @depends testPostAdd
     */
    public function testNotifAdd($post_id)
    {
        $this->setIdentity(2);
        $data = $this->jsonRpc(
            'post.add', [
            'content' => 'Other post',
            'origin_id' => $post_id,
            'uid' => 'uid'
            ]
            );
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals($data['result'] , 9); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 
        
        return $data['result'];
    }
    
    /**
     * @depends testPostAdd
     */
    public function testCommentAdd($post_id)
    {
        $this->setIdentity(2);
        $data = $this->jsonRpc(
            'post.add', [
            'content' => 'Comment',
            'parent_id' => $post_id,
            'data' => ['test']
            ]
            );
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals($data['result'] , 10); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }
    
    /**
     * @depends testNotifAdd
     */
    public function testCommentAdd2($post_id)
    {
        $this->setIdentity(2);
        $data = $this->jsonRpc(
            'post.add', [
            'content' => 'Comment',
            'parent_id' => $post_id,
            'data' => ['test']
            ]
            );
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals($data['result'] , 11); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }


    /**
     * @depends testPostAdd
     */
    public function testPostReport($post_id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'report.add', [
            'reason' => 'plein',
            'description' => 'desxcriptiovb',
            'post_id' => $post_id
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

  /**
     * @depends testPostAdd
     */
    public function testPostUpdateError($post_id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'post.update', [
            ]
        );
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -32000); 
        $this->assertEquals(!empty($data['error']['message']) , true); 

        
    }  /**
     * @depends testPostAdd
     */
    public function testPostUpdateError2($post_id)
    {
        $this->setIdentity(6,2);
        $data = $this->jsonRpc(
            'post.update', [
            'id' => $post_id,
            'content' => 'aeae'
            
            ]
        );

        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -38003); 
        $this->assertEquals(!empty($data['error']['message']) , true); 
        
    }
    
    /**
     * @depends testPostAdd
     */
    public function testPostUpdate($post_id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'post.update', [
            'id' => $post_id,
            'content' => 'content  #toto @[\'U\',1]',
            'link' => 'linkUpt',
            'picture' => 'pictureUpt',
            'name_picture' => 'name_pictureUpt',
            'link_title' => 'link_titleUpt',
            'link_desc' => 'link_descUpt',
            'docs' => [
                ['name' => 'nameUpt', 'link' => 'linkUpt', 'type' => 'typeUpt'],
                ['name' => 'name2Upt', 'link' => 'link2Upt', 'type' => 'type2Upt'],
            ]
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
        
    }

    /**
     * @depends testPostAdd
     */
    public function testPostGet($id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'post.get', [
            'id' => [1,2,3]
            ]
        );
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 3); 
        $this->assertEquals(count($data['result'][3]) , 21); 
        $this->assertEquals($data['result'][3]['subscription'] , 0); 
        $this->assertEquals($data['result'][3]['nbr_comments'] , 0); 
        $this->assertEquals($data['result'][3]['nbr_likes'] , 0); 
        $this->assertEquals($data['result'][3]['is_liked'] , 0); 
        $this->assertEquals($data['result'][3]['id'] , 3); 
        $this->assertEquals($data['result'][3]['content'] , ""); 
        $this->assertEquals($data['result'][3]['user_id'] , null); 
        $this->assertEquals($data['result'][3]['page_id'] , null); 
        $this->assertEquals($data['result'][3]['link'] , null); 
        $this->assertEquals($data['result'][3]['picture'] , null); 
        $this->assertEquals($data['result'][3]['name_picture'] , null); 
        $this->assertEquals($data['result'][3]['link_title'] , null); 
        $this->assertEquals($data['result'][3]['link_desc'] , null); 
        $this->assertEquals(!empty($data['result'][3]['created_date']) , true); 
        $this->assertEquals($data['result'][3]['updated_date'] , null); 
        $this->assertEquals($data['result'][3]['parent_id'] , null); 
        $this->assertEquals($data['result'][3]['t_page_id'] , 2); 
        $this->assertEquals($data['result'][3]['t_user_id'] , 2); 
        $this->assertEquals($data['result'][3]['type'] , "page"); 
        $this->assertEquals(count($data['result'][3]['data']) , 4); 
        $this->assertEquals($data['result'][3]['data']['state'] , "member"); 
        $this->assertEquals($data['result'][3]['data']['user'] , 2); 
        $this->assertEquals($data['result'][3]['data']['page'] , 2); 
        $this->assertEquals($data['result'][3]['data']['type'] , "course"); 
        $this->assertEquals($data['result'][3]['item_id'] , null); 
        $this->assertEquals(count($data['result'][2]) , 21); 
        $this->assertEquals($data['result'][2]['subscription'] , 0); 
        $this->assertEquals($data['result'][2]['nbr_comments'] , 0); 
        $this->assertEquals($data['result'][2]['nbr_likes'] , 0); 
        $this->assertEquals($data['result'][2]['is_liked'] , 0); 
        $this->assertEquals($data['result'][2]['id'] , 2); 
        $this->assertEquals($data['result'][2]['content'] , ""); 
        $this->assertEquals($data['result'][2]['user_id'] , null); 
        $this->assertEquals($data['result'][2]['page_id'] , null); 
        $this->assertEquals($data['result'][2]['link'] , null); 
        $this->assertEquals($data['result'][2]['picture'] , null); 
        $this->assertEquals($data['result'][2]['name_picture'] , null); 
        $this->assertEquals($data['result'][2]['link_title'] , null); 
        $this->assertEquals($data['result'][2]['link_desc'] , null); 
        $this->assertEquals(!empty($data['result'][2]['created_date']) , true); 
        $this->assertEquals($data['result'][2]['updated_date'] , null); 
        $this->assertEquals($data['result'][2]['parent_id'] , null); 
        $this->assertEquals($data['result'][2]['t_page_id'] , 1); 
        $this->assertEquals($data['result'][2]['t_user_id'] , 3); 
        $this->assertEquals($data['result'][2]['type'] , "page"); 
        $this->assertEquals(count($data['result'][2]['data']) , 4); 
        $this->assertEquals($data['result'][2]['data']['state'] , "member"); 
        $this->assertEquals($data['result'][2]['data']['user'] , 3); 
        $this->assertEquals($data['result'][2]['data']['page'] , 1); 
        $this->assertEquals($data['result'][2]['data']['type'] , "organization"); 
        $this->assertEquals($data['result'][2]['item_id'] , null); 
        $this->assertEquals(count($data['result'][1]) , 21); 
        $this->assertEquals($data['result'][1]['subscription'] , 0); 
        $this->assertEquals($data['result'][1]['nbr_comments'] , 0); 
        $this->assertEquals($data['result'][1]['nbr_likes'] , 0); 
        $this->assertEquals($data['result'][1]['is_liked'] , 0); 
        $this->assertEquals($data['result'][1]['id'] , 1); 
        $this->assertEquals($data['result'][1]['content'] , ""); 
        $this->assertEquals($data['result'][1]['user_id'] , null); 
        $this->assertEquals($data['result'][1]['page_id'] , null); 
        $this->assertEquals($data['result'][1]['link'] , null); 
        $this->assertEquals($data['result'][1]['picture'] , null); 
        $this->assertEquals($data['result'][1]['name_picture'] , null); 
        $this->assertEquals($data['result'][1]['link_title'] , null); 
        $this->assertEquals($data['result'][1]['link_desc'] , null); 
        $this->assertEquals(!empty($data['result'][1]['created_date']) , true); 
        $this->assertEquals($data['result'][1]['updated_date'] , null); 
        $this->assertEquals($data['result'][1]['parent_id'] , null); 
        $this->assertEquals($data['result'][1]['t_page_id'] , 1); 
        $this->assertEquals($data['result'][1]['t_user_id'] , 2); 
        $this->assertEquals($data['result'][1]['type'] , "page"); 
        $this->assertEquals(count($data['result'][1]['data']) , 4); 
        $this->assertEquals($data['result'][1]['data']['state'] , "member"); 
        $this->assertEquals($data['result'][1]['data']['user'] , 2); 
        $this->assertEquals($data['result'][1]['data']['page'] , 1); 
        $this->assertEquals($data['result'][1]['data']['type'] , "organization"); 
        $this->assertEquals($data['result'][1]['item_id'] , null); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 


    }

    public function testPostGetListId()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('post.getListId', []);

        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 3); 
        $this->assertEquals(count($data['result'][0]) , 2); 
        $this->assertEquals(!empty($data['result'][0]['last_date']) , true); 
        $this->assertEquals($data['result'][0]['id'] , 7); 
        $this->assertEquals(count($data['result'][1]) , 2); 
        $this->assertEquals(!empty($data['result'][1]['last_date']) , true); 
        $this->assertEquals($data['result'][1]['id'] , 8); 
        $this->assertEquals(count($data['result'][2]) , 2); 
        $this->assertEquals(!empty($data['result'][2]['last_date']) , true); 
        $this->assertEquals($data['result'][2]['id'] , 6); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }
    
     public function testPostGetListId2()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('post.getListId', ['filter' => ['n' => 10, 'p' => 1]]);

        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 2); 
        $this->assertEquals($data['result']['count'] , 3); 
        $this->assertEquals(count($data['result']['list']) , 3); 
        $this->assertEquals(count($data['result']['list'][0]) , 2); 
        $this->assertEquals(!empty($data['result']['list'][0]['last_date']) , true); 
        $this->assertEquals($data['result']['list'][0]['id'] , 7); 
        $this->assertEquals(count($data['result']['list'][1]) , 2); 
        $this->assertEquals(!empty($data['result']['list'][1]['last_date']) , true); 
        $this->assertEquals($data['result']['list'][1]['id'] , 8); 
        $this->assertEquals(count($data['result']['list'][2]) , 2); 
        $this->assertEquals(!empty($data['result']['list'][2]['last_date']) , true); 
        $this->assertEquals($data['result']['list'][2]['id'] , 6); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 


    }


    /**
     * @depends testPostAdd
     */
    public function testPostLike($post_id)
    {
        $this->setIdentity(2);
        $data = $this->jsonRpc(
            'post.like', [
            'id' => $post_id,
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }
    
    /**
     * @depends testPostAdd
     */
      public function testCanGetListUsersWhoLiked($post_id)
    {
        $this->setIdentity(1,1);
        $data = $this->jsonRpc(
            'user.getListId', [
             'post_id' => $post_id
            ]
        );
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 1); 
        $this->assertEquals($data['result'][0] , 2); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }

    /**
     * @depends testPostAdd
     */
    public function testPostUnLike($post_id)
    {
        $this->setIdentity(2);
        $data = $this->jsonRpc(
            'post.unlike', [
            'id' => $post_id,
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }


    /**
     * @depends testPostAdd
     */
    public function testHidePost($post_id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'post.hide', [
            'id' => $post_id,
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }
    
    public function testPostGetCount()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'post.getCount', [
                'start_date' => '2015-01-01',
                'end_date' => '2099-0-1',
                'interval_date' => 'D',
                'organization_id' => 1,
                'parent' => 0
            ]
        );
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 1); 
        $this->assertEquals(count($data['result'][0]) , 3); 
        $this->assertEquals($data['result'][0]['count'] , 2); 
        $this->assertEquals(!empty($data['result'][0]['created_date']) , true); 
        $this->assertEquals($data['result'][0]['parent_id'] , 0); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }
    
    public function testPostGetCount2()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'post.getCount', [
                'start_date' => '2015-01-01',
                'end_date' => '2099-0-1',
                'interval_date' => 'D',
                'organization_id' => 1,
                'parent' => 1
            ]
        );
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 1); 
        $this->assertEquals(count($data['result'][0]) , 3); 
        $this->assertEquals($data['result'][0]['count'] , 2); 
        $this->assertEquals(!empty($data['result'][0]['created_date']) , true); 
        $this->assertEquals($data['result'][0]['parent_id'] , 1); 
        $this->assertEquals($data['jsonrpc'] , 2.0);
    }
    
    
     /**
     * @depends testPostAdd
     */
    public function testPostDeleteError($post_id)
    {
        $this->setIdentity(5,2);
        $data = $this->jsonRpc(
            'post.delete', [
            'id' => $post_id,
            ]
        );
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -38003); 
        $this->assertEquals($data['error']['message'] , "Unauthorized operation post.delete"); 
        $this->assertEquals($data['error']['data'] , null); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }

    
    /**
     * @depends testPostAdd
     */
    public function testPostDelete($post_id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'post.delete', [
            'id' => $post_id,
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    /**
     * @depends testPostAdd
     */
    public function testReactivate($post_id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'post.reactivate', [
            'id' => $post_id,
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }
}
