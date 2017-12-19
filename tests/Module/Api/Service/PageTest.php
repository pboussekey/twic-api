<?php
namespace ModuleTest\Api\Service;

use ModuleTest\Api\AbstractService;

class PageTest extends AbstractService
{
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
            'type' => 'organization',
            'admission' => 'free',
            'start_date' => '2015-00-00 00:00:00',
            'end_date' => '2016-00-00 00:00:00',
            'location' => 'location',
            'page_id' => 1,
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
        
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);

        $page_id = $data['result'];
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

        return $page_id;
    }
    
  
    /**
     * @depends testPageAdd
     */
    public function testPageAddParent($page_id)
    {
        $this->setIdentity(1, 1);
        $data = $this->jsonRpc(
            'page.add', [
            'title' => 'super title parent',
            'logo' => 'logo',
            'background' => 'background',
            'description' => 'description',
            'type' => 'organization',
            'circle_id' => 1,
            'users' => 1,
            'docs' => 1,
            'page_id' => $page_id,
            'confidentiality' => 0
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 2);
        $this->assertEquals($data['jsonrpc'], 2.0);
        
        return $data['result'];
    }
    
  
    
    /**
     * @depends testPageAdd
     */
    public function testPageAdd2($page_id)
    {
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

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 3);
        $this->assertEquals($data['jsonrpc'], 2.0);

        return $data['result'];
    }
    
     /**
     * @depends testPageAdd
     */
    public function testPageAdd3($page_id)
    {
        $this->setIdentity(1, 1);
        $data = $this->jsonRpc(
            'page.add', [
            'title' => 'Event page',
            'logo' => 'logo',
            'background' => 'background',
            'description' => 'description',
            'confidentiality' => 0,
            'type' => 'event',
            'admission' => 'free',
            'users' => [
                ['user_id' => 1,'role' => 'admin', 'state' => 'member'],
                ['user_id' => 2,'role' => 'user', 'state' => 'pending']
            ]
           
            ]
        );
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals($data['result'] , 4); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

        return $data['result'];
    }


    /**
     * @depends testPageAdd
     */
    public function testPageAddError($page_id)
    {
        $this->setIdentity(1, 2);
        $data = $this->jsonRpc(
            'page.add', [
            'title' => 'Organization',
            'logo' => 'logo',
            'background' => 'background',
            'description' => 'description',
            'confidentiality' => 1,
            'type' => 'organization',
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
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -38003); 
        $this->assertEquals($data['error']['message'] , "Unauthorized operation page.add"); 
        $this->assertEquals($data['error']['data'] , null); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 


    }
    
     /**
     * @depends testPageAdd
     */
    public function testPageAddError2($page_id)
    {
        $this->setIdentity(1, 2);
        $data = $this->jsonRpc(
            'page.add', [
            'title' => 'Organization',
            'logo' => 'logo',
            'background' => 'background',
            'description' => 'description',
            'confidentiality' => 1,
            'type' => 'course',
            'admission' => 'free',
            'users' => [
                ['user_id' => 1,'role' => 'admin', 'state' => 'member'],
                ['user_id' => 2,'role' => 'user', 'state' => 'pending']
            ],
            'tags' => [
                'toto', 'tata', 'tutu'
            ],
           
            ]
        );
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -38003); 
        $this->assertEquals($data['error']['message'] , "Unauthorized operation page.add"); 
        $this->assertEquals($data['error']['data'] , null); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 


    }


    /**
     * @depends testPageAdd
     */
    public function testgetListRelationId($page_id)
    {
        $this->setIdentity(1, 1);
        $data = $this->jsonRpc(
            'page.getListRelationId', [
            'parent_id' => $page_id
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 1);
        $this->assertEquals(count($data['result'][1]), 2);
        $this->assertEquals($data['result'][1][0], 1);
        $this->assertEquals($data['result'][1][1], 2);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    public function testgetListRelationIdChild()
    {
        $this->setIdentity(1, 1);
        $data = $this->jsonRpc(
            'page.getListRelationId', [
            'children_id' => 2
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 1);
        $this->assertEquals(count($data['result'][2]), 1);
        $this->assertEquals($data['result'][2][0], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    /**
     * @depends testPageAdd
     */
    public function testPageAddTag($page_id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'page.addTag', [
            'id' => $page_id,
            'tag' => 'superTag'
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 4);
        $this->assertEquals($data['jsonrpc'], 2.0);

        return $data['result'];
    }

    /**
     * @depends testPageAdd
     * @depends testPageAddTag
     */
    public function testPageremoveTag($page_id, $tag_id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'page.removeTag', [
            'id' => $page_id,
            'tag_id' => $tag_id
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    /**
     * @depends testPageAdd
     */
    public function testPageGet($page_id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('page.get', ['id' => $page_id, 'type' => 'organization']);

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 22);
        $this->assertEquals(count($data['result']['address']), 14);
        $this->assertEquals(count($data['result']['address']['city']), 1);
        $this->assertEquals($data['result']['address']['city']['name'], "Villefontaine");
        $this->assertEquals(count($data['result']['address']['division']), 2);
        $this->assertEquals($data['result']['address']['division']['id'], 54);
        $this->assertEquals($data['result']['address']['division']['name'], "Auvergne-Rhône-Alpes");
        $this->assertEquals($data['result']['address']['country'], null);
        $this->assertEquals($data['result']['address']['id'], 1);
        $this->assertEquals($data['result']['address']['street_no'], 11);
        $this->assertEquals($data['result']['address']['street_type'], null);
        $this->assertEquals($data['result']['address']['street_name'], "Allée des Chênes");
        $this->assertEquals($data['result']['address']['longitude'], 5.1787445);
        $this->assertEquals($data['result']['address']['latitude'], 45.601569);
        $this->assertEquals($data['result']['address']['door'], null);
        $this->assertEquals($data['result']['address']['building'], null);
        $this->assertEquals($data['result']['address']['apartment'], null);
        $this->assertEquals($data['result']['address']['floor'], null);
        $this->assertEquals($data['result']['address']['timezone'], "Europe/Paris");
        $this->assertEquals(count($data['result']['owner']), 4);
        $this->assertEquals($data['result']['owner']['id'], 1);
        $this->assertEquals($data['result']['owner']['text'], "Paul Boussekey");
        $this->assertEquals($data['result']['owner']['img'], null);
        $this->assertEquals($data['result']['owner']['type'], "user");
        $this->assertEquals(count($data['result']['user']), 5);
        $this->assertEquals($data['result']['user']['id'], 1);
        $this->assertEquals($data['result']['user']['firstname'], "Paul");
        $this->assertEquals($data['result']['user']['lastname'], "Boussekey");
        $this->assertEquals($data['result']['user']['avatar'], null);
        $this->assertEquals($data['result']['user']['ambassador'], null);
        $this->assertEquals(count($data['result']['tags']), 3);
        $this->assertEquals(count($data['result']['tags'][0]), 3);
        $this->assertEquals($data['result']['tags'][0]['id'], 1);
        $this->assertEquals($data['result']['tags'][0]['name'], "toto");
        $this->assertEquals($data['result']['tags'][0]['weight'], 2);
        $this->assertEquals(count($data['result']['tags'][1]), 3);
        $this->assertEquals($data['result']['tags'][1]['id'], 2);
        $this->assertEquals($data['result']['tags'][1]['name'], "tata");
        $this->assertEquals($data['result']['tags'][1]['weight'], 2);
        $this->assertEquals(count($data['result']['tags'][2]), 3);
        $this->assertEquals($data['result']['tags'][2]['id'], 3);
        $this->assertEquals($data['result']['tags'][2]['name'], "tutu");
        $this->assertEquals($data['result']['tags'][2]['weight'], 2);
        $this->assertEquals($data['result']['role'], "admin");
        $this->assertEquals($data['result']['state'], "member");
        $this->assertEquals($data['result']['id'], 1);
        $this->assertEquals($data['result']['title'], "super title");
        $this->assertEquals($data['result']['logo'], "logo");
        $this->assertEquals($data['result']['background'], "background");
        $this->assertEquals($data['result']['description'], "description");
        $this->assertEquals($data['result']['confidentiality'], 0);
        $this->assertEquals($data['result']['admission'], "free");
        $this->assertEquals(!empty($data['result']['start_date']), true);
        $this->assertEquals(!empty($data['result']['end_date']), true);
        $this->assertEquals($data['result']['location'], "location");
        $this->assertEquals($data['result']['type'], "organization");
        $this->assertEquals($data['result']['user_id'], 1);
        $this->assertEquals($data['result']['owner_id'], 1);
        $this->assertEquals($data['result']['website'], null);
        $this->assertEquals($data['result']['conversation_id'], null);
        $this->assertEquals($data['result']['is_published'], 0);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }
       /**
     * @depends testPageAdd
     * @depends testPageAdd2
     */
    public function testPageGet2($page_id, $page_id2)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('page.get', ['id' => [$page_id, $page_id2]]);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 2); 
        $this->assertEquals(count($data['result'][1]) , 22); 
        $this->assertEquals(count($data['result'][1]['address']) , 14); 
        $this->assertEquals(count($data['result'][1]['address']['city']) , 1); 
        $this->assertEquals($data['result'][1]['address']['city']['name'] , "Villefontaine"); 
        $this->assertEquals(count($data['result'][1]['address']['division']) , 2); 
        $this->assertEquals($data['result'][1]['address']['division']['id'] , 54); 
        $this->assertEquals($data['result'][1]['address']['division']['name'] , "Auvergne-Rhône-Alpes"); 
        $this->assertEquals($data['result'][1]['address']['country'] , null); 
        $this->assertEquals($data['result'][1]['address']['id'] , 1); 
        $this->assertEquals($data['result'][1]['address']['street_no'] , 11); 
        $this->assertEquals($data['result'][1]['address']['street_type'] , null); 
        $this->assertEquals($data['result'][1]['address']['street_name'] , "Allée des Chênes"); 
        $this->assertEquals($data['result'][1]['address']['longitude'] , 5.1787445); 
        $this->assertEquals($data['result'][1]['address']['latitude'] , 45.601569); 
        $this->assertEquals($data['result'][1]['address']['door'] , null); 
        $this->assertEquals($data['result'][1]['address']['building'] , null); 
        $this->assertEquals($data['result'][1]['address']['apartment'] , null); 
        $this->assertEquals($data['result'][1]['address']['floor'] , null); 
        $this->assertEquals($data['result'][1]['address']['timezone'] , "Europe/Paris"); 
        $this->assertEquals(count($data['result'][1]['owner']) , 4); 
        $this->assertEquals($data['result'][1]['owner']['id'] , 1); 
        $this->assertEquals($data['result'][1]['owner']['text'] , "Paul Boussekey"); 
        $this->assertEquals($data['result'][1]['owner']['img'] , null); 
        $this->assertEquals($data['result'][1]['owner']['type'] , "user"); 
        $this->assertEquals(count($data['result'][1]['user']) , 5); 
        $this->assertEquals($data['result'][1]['user']['id'] , 1); 
        $this->assertEquals($data['result'][1]['user']['firstname'] , "Paul"); 
        $this->assertEquals($data['result'][1]['user']['lastname'] , "Boussekey"); 
        $this->assertEquals($data['result'][1]['user']['avatar'] , null); 
        $this->assertEquals($data['result'][1]['user']['ambassador'] , null); 
        $this->assertEquals(count($data['result'][1]['tags']) , 3); 
        $this->assertEquals(count($data['result'][1]['tags'][0]) , 3); 
        $this->assertEquals($data['result'][1]['tags'][0]['id'] , 1); 
        $this->assertEquals($data['result'][1]['tags'][0]['name'] , "toto"); 
        $this->assertEquals($data['result'][1]['tags'][0]['weight'] , 2); 
        $this->assertEquals(count($data['result'][1]['tags'][1]) , 3); 
        $this->assertEquals($data['result'][1]['tags'][1]['id'] , 2); 
        $this->assertEquals($data['result'][1]['tags'][1]['name'] , "tata"); 
        $this->assertEquals($data['result'][1]['tags'][1]['weight'] , 2); 
        $this->assertEquals(count($data['result'][1]['tags'][2]) , 3); 
        $this->assertEquals($data['result'][1]['tags'][2]['id'] , 3); 
        $this->assertEquals($data['result'][1]['tags'][2]['name'] , "tutu"); 
        $this->assertEquals($data['result'][1]['tags'][2]['weight'] , 2); 
        $this->assertEquals($data['result'][1]['role'] , "admin"); 
        $this->assertEquals($data['result'][1]['state'] , "member"); 
        $this->assertEquals($data['result'][1]['id'] , 1); 
        $this->assertEquals($data['result'][1]['title'] , "super title"); 
        $this->assertEquals($data['result'][1]['logo'] , "logo"); 
        $this->assertEquals($data['result'][1]['background'] , "background"); 
        $this->assertEquals($data['result'][1]['description'] , "description"); 
        $this->assertEquals($data['result'][1]['confidentiality'] , 0); 
        $this->assertEquals($data['result'][1]['admission'] , "free"); 
        $this->assertEquals(!empty($data['result'][1]['start_date']) , true); 
        $this->assertEquals(!empty($data['result'][1]['end_date']) , true); 
        $this->assertEquals($data['result'][1]['location'] , "location"); 
        $this->assertEquals($data['result'][1]['type'] , "organization"); 
        $this->assertEquals($data['result'][1]['user_id'] , 1); 
        $this->assertEquals($data['result'][1]['owner_id'] , 1); 
        $this->assertEquals($data['result'][1]['website'] , null); 
        $this->assertEquals($data['result'][1]['conversation_id'] , null); 
        $this->assertEquals($data['result'][1]['is_published'] , 0); 
        $this->assertEquals(count($data['result'][3]) , 22); 
        $this->assertEquals($data['result'][3]['address'] , null); 
        $this->assertEquals(count($data['result'][3]['owner']) , 4); 
        $this->assertEquals($data['result'][3]['owner']['id'] , 1); 
        $this->assertEquals($data['result'][3]['owner']['text'] , "super title"); 
        $this->assertEquals($data['result'][3]['owner']['img'] , "logo"); 
        $this->assertEquals($data['result'][3]['owner']['type'] , "organization"); 
        $this->assertEquals(count($data['result'][3]['user']) , 5); 
        $this->assertEquals($data['result'][3]['user']['id'] , 1); 
        $this->assertEquals($data['result'][3]['user']['firstname'] , "Paul"); 
        $this->assertEquals($data['result'][3]['user']['lastname'] , "Boussekey"); 
        $this->assertEquals($data['result'][3]['user']['avatar'] , null); 
        $this->assertEquals($data['result'][3]['user']['ambassador'] , null); 
        $this->assertEquals(count($data['result'][3]['tags']) , 3); 
        $this->assertEquals(count($data['result'][3]['tags'][0]) , 3); 
        $this->assertEquals($data['result'][3]['tags'][0]['id'] , 1); 
        $this->assertEquals($data['result'][3]['tags'][0]['name'] , "toto"); 
        $this->assertEquals($data['result'][3]['tags'][0]['weight'] , 2); 
        $this->assertEquals(count($data['result'][3]['tags'][1]) , 3); 
        $this->assertEquals($data['result'][3]['tags'][1]['id'] , 2); 
        $this->assertEquals($data['result'][3]['tags'][1]['name'] , "tata"); 
        $this->assertEquals($data['result'][3]['tags'][1]['weight'] , 2); 
        $this->assertEquals(count($data['result'][3]['tags'][2]) , 3); 
        $this->assertEquals($data['result'][3]['tags'][2]['id'] , 3); 
        $this->assertEquals($data['result'][3]['tags'][2]['name'] , "tutu"); 
        $this->assertEquals($data['result'][3]['tags'][2]['weight'] , 2); 
        $this->assertEquals($data['result'][3]['role'] , "admin"); 
        $this->assertEquals($data['result'][3]['state'] , "member"); 
        $this->assertEquals($data['result'][3]['id'] , 3); 
        $this->assertEquals($data['result'][3]['title'] , "Course page"); 
        $this->assertEquals($data['result'][3]['logo'] , "logo"); 
        $this->assertEquals($data['result'][3]['background'] , "background"); 
        $this->assertEquals($data['result'][3]['description'] , "description"); 
        $this->assertEquals($data['result'][3]['confidentiality'] , 1); 
        $this->assertEquals($data['result'][3]['admission'] , "free"); 
        $this->assertEquals($data['result'][3]['start_date'] , null); 
        $this->assertEquals($data['result'][3]['end_date'] , null); 
        $this->assertEquals($data['result'][3]['location'] , null); 
        $this->assertEquals($data['result'][3]['type'] , "course"); 
        $this->assertEquals($data['result'][3]['user_id'] , 1); 
        $this->assertEquals($data['result'][3]['owner_id'] , 1); 
        $this->assertEquals($data['result'][3]['website'] , null); 
        $this->assertEquals($data['result'][3]['conversation_id'] , 1); 
        $this->assertEquals($data['result'][3]['is_published'] , 0); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 



    }

    
      /**
     * @depends testPageAdd
     */
    public function testPageGetError($page_id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('page.get', []);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -32000); 
        $this->assertEquals(!empty($data['error']['message']) , true); 

    }

    /**
     * @depends testPageAdd
     */
    public function testPageGetListId()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'page.getListId', [
            'type' => 'organization',
            'search' => 'super',
            'member_id' => [1],
            'filter' => ['n' => 5,'p' => 1],
            'start_date' => '2015-00-00 00:00:00',
            'end_date' => '2099-00-00 00:00:00',
            'strict_dates' => true,
            'exclude' => [3],
            'is_member_admin' => true
            /*
            $parent_id = null,
            $tags
            */
            ]
        );
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 2); 
        $this->assertEquals(count($data['result']['list']) , 1); 
        $this->assertEquals($data['result']['list'][0] , 1); 
        $this->assertEquals($data['result']['count'] , 1); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 
    }

    /**
     * @depends testPageAdd
     */
    public function testPageUpdate($page_id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'page.update', [
            'id' => $page_id,
            'title' => 'super title upt',
            'logo' => 'logo upt',
            'background' => 'background upt',
            'description' => 'description upt',
            'confidentiality' => 2,
            'type' => 'event',
            'admission' => 'free',
            'start_date' => '2018-00-00 00:00:00',
            'end_date' => '2019-00-00 00:00:00',
            'location' => 'location upt',
            'users' => [
                ['user_id' => 1,'role' => 'user', 'state' => 'member'],
                ['user_id' => 2,'role' => 'admin', 'state' => 'member'],
                ['user_email' => 'user@thestudnet.com','role' => 'user', 'state' => 'pending'],
            ],
            'tags' => [
                'toto', 'tata', 'tutu', 'toutou'
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
            'libelle' => 'gnam',
            'custom' => '{obj}',
            ]
        );
    
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }
    
    
    /**
     * @depends testPageAdd
     * @depends testPageAdd3
     */
    public function testPageUpdate2($parent_id, $page_id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'page.update', [
            'id' => $page_id,
            'title' => 'update title',
            'page_id' => $parent_id,
            'circle_id' => 1,
            ]
        );
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }
    
     /**
     * @depends testPageAdd2
     * @depends testPageAddParent
     */
    public function testPageUpdate3($page_id)
    {
        $this->setIdentity(1,1);
        $this->jsonRpc(
            'post.add', [
            'page_id' => $page_id  
            
            ]
        );
        
        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'page.update', [
            'id' => $page_id,
            'is_published' => true    
            
            ]
        );
    
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }
    
    
      /**
     * @depends testPageAdd
     */
    public function testPageUpdateError($page_id)
    {
        $this->setIdentity(7,2);
        $data = $this->jsonRpc(
            'page.update', [
            'id' => $page_id,
            'title' => 'super title upt',
            'logo' => 'logo upt',
            'background' => 'background upt',
            'description' => 'description upt',
            'type' => 'event',
            'admission' => 'free',
            'start_date' => '2018-00-00 00:00:00',
            'end_date' => '2019-00-00 00:00:00',
            'location' => 'location upt',
            'users' => [
                ['user_id' => 1,'role' => 'admin', 'state' => 'member'],
                ['user_id' => 2,'role' => 'admin', 'state' => 'member'],
            ],
            'tags' => [
                'toto', 'tata', 'tutu', 'toutou'
            ],
            'docs' => [
                ['name' => 'name', 'link' => 'link', 'type' => 'type']
            ],
            'libelle' => 'gnam',
            'custom' => '{obj}',
            ]
        );
    
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -38003); 
        $this->assertEquals($data['error']['message'] , "Unauthorized operation page.update"); 
        $this->assertEquals($data['error']['data'] , null); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }


    /**
     * @depends testPageAdd
     */
    public function testPageGetCustomError()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'page.getCustom', [
            'libelle' => 'unknown']
        );
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['error']) , 3); 
        $this->assertEquals($data['error']['code'] , -32000); 
        $this->assertEquals($data['error']['message'] , "No custom fields for unknown"); 
        $this->assertEquals($data['error']['data'] , null); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }

    /**
     * @depends testPageAdd
     */
    public function testPageGetCustom()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'page.getCustom', [
            'libelle' => 'gnam']
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 3);
        $this->assertEquals($data['result']['id'], 1);
        $this->assertEquals($data['result']['libelle'], "gnam");
        $this->assertEquals($data['result']['custom'], "{obj}");
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    /**
     * @depends testPageAdd
     */
    public function testPageGetCustomById($id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'page.getCustom', [
            'id' => $id]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 3);
        $this->assertEquals($data['result']['id'], 1);
        $this->assertEquals($data['result']['libelle'], "gnam");
        $this->assertEquals($data['result']['custom'], "{obj}");
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    /**
     * @depends testPageAdd
     */
    public function testPageAddDocument($id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'page.addDocument', [
            'id' => $id,
            'library' => [
            'name' => 'azerty',
            'token' => '1234567890',
            ]
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 6);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    /**
     * @depends testPageAdd
     */
    public function testPageGetListDocument($id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'page.getListDocument', [
            'id' => $id,
            'filter' => [
            'n' => 5,
            'p' => 1,
            ]
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 4);
        $this->assertEquals($data['result']['count'], 2);
        $this->assertEquals(count($data['result']['documents']), 2);
        $this->assertEquals(count($data['result']['documents'][0]), 12);
        $this->assertEquals($data['result']['documents'][0]['id'], 6);
        $this->assertEquals($data['result']['documents'][0]['name'], "azerty");
        $this->assertEquals($data['result']['documents'][0]['link'], null);
        $this->assertEquals($data['result']['documents'][0]['token'], 1234567890);
        $this->assertEquals($data['result']['documents'][0]['type'], null);
        $this->assertEquals(!empty($data['result']['documents'][0]['created_date']), true);
        $this->assertEquals($data['result']['documents'][0]['deleted_date'], null);
        $this->assertEquals($data['result']['documents'][0]['updated_date'], null);
        $this->assertEquals($data['result']['documents'][0]['folder_id'], null);
        $this->assertEquals($data['result']['documents'][0]['owner_id'], 1);
        $this->assertEquals($data['result']['documents'][0]['box_id'], null);
        $this->assertEquals($data['result']['documents'][0]['text'], null);
        $this->assertEquals(count($data['result']['documents'][1]), 12);
        $this->assertEquals($data['result']['documents'][1]['id'], 5);
        $this->assertEquals($data['result']['documents'][1]['name'], "name");
        $this->assertEquals($data['result']['documents'][1]['link'], "link");
        $this->assertEquals($data['result']['documents'][1]['token'], null);
        $this->assertEquals($data['result']['documents'][1]['type'], "type");
        $this->assertEquals(!empty($data['result']['documents'][1]['created_date']), true);
        $this->assertEquals($data['result']['documents'][1]['deleted_date'], null);
        $this->assertEquals($data['result']['documents'][1]['updated_date'], null);
        $this->assertEquals($data['result']['documents'][1]['folder_id'], null);
        $this->assertEquals($data['result']['documents'][1]['owner_id'], 1);
        $this->assertEquals($data['result']['documents'][1]['box_id'], null);
        $this->assertEquals($data['result']['documents'][1]['text'], null);
        $this->assertEquals($data['result']['folder'], null);
        $this->assertEquals($data['result']['parent'], null);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    /**
     * @depends testPageAdd
     */
    public function testPageDeleteDocument($id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'page.deleteDocument', [
            'library_id' => 6,
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    /**
     * @depends testPageAdd
     * @depends testPageAdd2
     */
    public function testPageUserAdd($id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'pageuser.add', [
            'page_id' => $id,
            'user_id' => 4,
            'role' => 'admin',
            'state' => 'member'
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    /**
     * @depends testPageAdd
     */
    public function testSendPassword($id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('user.sendPassword', ['page_id' => $id]);

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    /**
     * @depends testPageAdd
     */
    public function testPageUserUpdate($id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'pageuser.update', [
            'page_id' => $id,
            'user_id' => 4,
            'role' => 'user',
            'state' => 'member'
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }
    
    /**
     * @depends testPageAdd2
     */
    public function testPageUserUpdate2($id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'pageuser.update', [
            'page_id' => $id,
            'user_id' => 2,
            'role' => 'user',
            'state' => 'member'
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }
    
    /**
     * @depends testPageAdd2
     */
    public function testPageUserDelete2($id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'pageuser.delete', [
            'page_id' => $id,
            'user_id' => 2
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    public function testPageUsergetListByUser()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'pageuser.getListByUser', [
            'user_id' => 4,
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 1);
        $this->assertEquals(count($data['result'][4]), 1);
        $this->assertEquals($data['result'][4][0], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    public function testPageUsergetListByUserType()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'pageuser.getListByUser', [
            'user_id' => 4,
            'type' => 'organization'
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 1);
        $this->assertEquals(count($data['result'][4]), 1);
        $this->assertEquals($data['result'][4][0], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    public function testPageUsergetListByPage()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'pageuser.getListByPage', [
            'page_id' => 1,
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 1);
        $this->assertEquals(count($data['result'][1]), 4);
        $this->assertEquals($data['result'][1][0], 1);
        $this->assertEquals($data['result'][1][1], 2);
        $this->assertEquals($data['result'][1][2], 4);
        $this->assertEquals($data['result'][1][3], 9);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    /**
     * @depends testPageAdd
     */
    public function testPageCount($id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('page.getCount', [
            'start_date' => '2015-01-01',
            'end_date' => '2099-0-1',
            'interval_date' => 'D',
            'type' => 'course',
            'organization_id' => 1
        ]);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 1); 
        $this->assertEquals(count($data['result'][0]) , 3); 
        $this->assertEquals($data['result'][0]['count'] , 1); 
        $this->assertEquals($data['result'][0]['type'] , "course"); 
        $this->assertEquals($data['result'][0]['created_date'] , "2017-12-19"); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }

    /**
     * @depends testPageAdd
     */
    public function testPageUserDelete($id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'pageuser.delete', [
            'page_id' => $id,
            'user_id' => 4,
            ]
        );

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    /**
     * @depends testPageAdd
     */
    public function testPageDelete($id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('page.delete', ['id' => $id]);

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }
}
