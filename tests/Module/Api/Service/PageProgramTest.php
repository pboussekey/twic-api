<?php
namespace ModuleTest\Api\Service;

use ModuleTest\Api\AbstractService;

class PageProgramTest extends AbstractService
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
    * 
     * @depends testPageAdd
     */
    public function testAddProgram($page_id)
    {
        $this->setIdentity(1, 1);
        $data = $this->jsonRpc(
            'pageprogram.add', [
                'page_id' => $page_id,
                'name' => 'mba'
        ]);

        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals($data['result'] , 1);
        $this->assertEquals($data['jsonrpc'] , 2.0);
        
        return $data['result'];
    }
    
    /**
     *
     * @depends testPageAdd
     */
    public function testAddProgramEmba($page_id)
    {
        $this->setIdentity(1, 1);
        $data = $this->jsonRpc(
            'pageprogram.add', [
                'page_id' => $page_id,
                'name' => 'emba'
            ]);
        
        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals($data['result'] , 2);
        $this->assertEquals($data['jsonrpc'] , 2.0);
        
        return $data['result'];
    }
   
    /**
     * @depends testAddProgramEmba
     * @depends testAddProgram
     */
    public function testAddProgramUser($program_id, $program_id1)
    {
        
        $this->setIdentity(1, 1);
        $data = $this->jsonRpc(
            'pageprogramuser.add', [
                'user_id' => 1,
                'page_program_id' => $program_id1,
            ]);
        
        $this->setIdentity(1, 1);
        $data = $this->jsonRpc(
            'pageprogramuser.add', [
                'page_program_id' => $program_id,
            ]);
        
        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals($data['result'] , 1);
        $this->assertEquals($data['jsonrpc'] , 2.0);

        $data = $this->jsonRpc(
            'pageprogramuser.add', [
                'page_program_id' => $program_id,
                'user_id' => 2,
            ]);
        
        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals($data['result'] , 1);
        $this->assertEquals($data['jsonrpc'] , 2.0); 

        return $data['result'];
    }
    
    /**
     * @depends testPageAdd
     */
    public function testGetList($page_id)
    {
        $this->setIdentity(1, 1);
        $data = $this->jsonRpc(
            'pageprogram.getList', [
                'page_id' => $page_id,
            ]);
        
        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals(count($data['result']) , 2);
        $this->assertEquals(count($data['result']['list']) , 2);
        $this->assertEquals(count($data['result']['list'][0]) , 3);
        $this->assertEquals($data['result']['list'][0]['id'] , 1);
        $this->assertEquals($data['result']['list'][0]['name'] , "mba");
        $this->assertEquals($data['result']['list'][0]['page_id'] , 1);
        $this->assertEquals(count($data['result']['list'][1]) , 3);
        $this->assertEquals($data['result']['list'][1]['id'] , 2);
        $this->assertEquals($data['result']['list'][1]['name'] , "emba");
        $this->assertEquals($data['result']['list'][1]['page_id'] , 1);
        $this->assertEquals($data['result']['count'] , 2);
        $this->assertEquals($data['jsonrpc'] , 2.0);
        
        return $data['result'];
    }
    
    /**
     * @depends testPageAdd
     */
    public function testGetListWithSearch($page_id)
    {
        $this->setIdentity(1, 1);
        $data = $this->jsonRpc(
            'pageprogram.getList', [
                'page_id' => $page_id,
                'search' => 'emb'
            ]);
        
        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals(count($data['result']) , 2);
        $this->assertEquals(count($data['result']['list']) , 1);
        $this->assertEquals(count($data['result']['list'][0]) , 3);
        $this->assertEquals($data['result']['list'][0]['id'] , 2);
        $this->assertEquals($data['result']['list'][0]['name'] , "emba");
        $this->assertEquals($data['result']['list'][0]['page_id'] , 1);
        $this->assertEquals($data['result']['count'] , 1);
        $this->assertEquals($data['jsonrpc'] , 2.0);
        
        return $data['result'];
    }
    
    /**
     * @depends testAddProgram
     */
    public function testDeleteProgramUser($program_id)
    {
        $this->setIdentity(1, 1);
        $data = $this->jsonRpc(
            'pageprogramuser.delete', [
                'user_id' => 1,
                'page_program_id' => $program_id
        ]);                  
        
        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals($data['result'] , 1);
        $this->assertEquals($data['jsonrpc'] , 2.0); 
        
        return $data['result'];
    }
    
    /**
     *
     * @depends testPageAdd
     * @depends testAddProgram
     */
    public function testDeleteProgram($page_id, $program_id)
    {
        $this->setIdentity(1, 1);
        $data = $this->jsonRpc(
            'pageprogram.delete', [
                'id' => $program_id
            ]);
        
        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals($data['result'] , 1);
        $this->assertEquals($data['jsonrpc'] , 2.0); 
    }
}
