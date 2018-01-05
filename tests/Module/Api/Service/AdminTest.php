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

}
