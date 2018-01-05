<?php
namespace ModuleTest\Api\Service;

use ModuleTest\Api\AbstractService;

class PermissionTest extends AbstractService
{
    public static $session;

    public static function setUpBeforeClass()
    {
        system('phing -q reset-db deploy-db');
        parent::setUpBeforeClass();
    }

    public function testCanAdd()
    {
        $this->setIdentity(1,1);
        $data = $this->jsonRpc(
            'permission.add', [
                'libelle' => 'permission.test',
                'role' => 1
            ]
            
        );
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(!empty($data['result']) , true); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 
        
        return $data['result'];
    }
    
    /**
     * @depends testCanAdd
     */
     public function testCanGetList($permission_id)
    {
        $this->setIdentity(1,1);
        $data = $this->jsonRpc(
            'permission.getList', ['filter' => ['n' => 1, 'p' => 1, 'o' => ['permission.id DESC']]]
        );
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 2); 
        $this->assertEquals(count($data['result']['list']) , 1); 
        $this->assertEquals(count($data['result']['list'][0]) , 4); 
        $this->assertEquals(count($data['result']['list'][0]['role_permission']) , 1); 
        $this->assertEquals(!empty($data['result']['list'][0]['role_permission']['id']) , true); 
        $this->assertEquals(count($data['result']['list'][0]['role']) , 2); 
        $this->assertEquals($data['result']['list'][0]['role']['id'] , 1); 
        $this->assertEquals($data['result']['list'][0]['role']['name'] , "admin"); 
        $this->assertEquals($data['result']['list'][0]['id'] , 613); 
        $this->assertEquals($data['result']['list'][0]['libelle'] , "permission.test"); 
        $this->assertEquals($data['result']['count'] , 134); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 


    }

    /**
     * @depends testCanAdd
     */
     public function testCanUpdate($permission_id)
    {
        $this->setIdentity(1,1);
        $libelle = 'permission.test2';
        $data = $this->jsonRpc(
            'permission.update', [
                'id' => $permission_id,
                'libelle' => $libelle,
                'role' => [1,2]
            ]
        );
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals($data['result'] , 1); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 
        
        return $libelle;
    }
    
    

    /**
     * @depends testCanUpdate
     */
     public function testCanDelete($libelle)
    {
        $this->setIdentity(1,1);
        $data = $this->jsonRpc(
            'permission.delete', [
                'libelle' => $libelle
            ]
        );
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 1); 
        $this->assertEquals($data['result']['permission.test2'] , 1); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 
    }
    
}
