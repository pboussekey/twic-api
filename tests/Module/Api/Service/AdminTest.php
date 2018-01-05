<?php
namespace ModuleTest\Api\Service;

use ModuleTest\Api\AbstractService;

class AdminTest extends AbstractService
{
    public static $session;

    public static function setUpBeforeClass()
    {
        system('phing -q reset-db deploy-db');
        parent::setUpBeforeClass();
    }

    public function testCanGetListByRole()
    {
        $this->setIdentity(1,1);
        $data = $this->jsonRpc(
            'rolerelation.getListByRole', [
                    'role' => 0
        ]);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 1); 
        $this->assertEquals(count($data['result'][0]) , 2); 
        $this->assertEquals($data['result'][0]['role_id'] , 0); 
        $this->assertEquals($data['result'][0]['parent_id'] , 2); 
        $this->assertEquals($data['jsonrpc'] , 2.0);

    }
    
     public function testCanAddRole()
    {
        $this->setIdentity(1,1);
        $data = $this->jsonRpc(
            'role.add', [
                    'name' => 'test'
        ]);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals($data['result'] , 8); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

        
        return $data['result'];
    }
    
    /**
     * @depends testCanAddRole
     */
     public function testCanUpdateRole($role_id)
    {
        $this->setIdentity(1,1);
        $data = $this->jsonRpc(
            'role.update', [
                'id' => $role_id,
                'name' => 'updated name'
        ]);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals($data['result'] , 1); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

        
    }
    
    
    /**
     * @depends testCanAddRole
     */
     public function testCanAddUserRole($role_id)
    {
        $this->setIdentity(1,1);
        $data = $this->jsonRpc(
            'role.addUser', [
                    'role' => $role_id,
                    'user' => 1
        ]);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals($data['result'] , true); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 
        
    }
    
    /**
     * @depends testCanAddRole
     */
     public function testCanDeleteRole($role_id)
    {
        $this->setIdentity(1,1);
        $data = $this->jsonRpc(
            'role.delete', [
                    'id' => $role_id
        ]);
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals($data['result'] , 1); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }

}
