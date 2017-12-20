<?php
namespace ModuleTest\Api\Service;

use ModuleTest\Api\AbstractService;

class MailTest extends AbstractService
{
    public static $session;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
       
    }

   
    public function testAddTpl()
    {
        $this->setIdentity(1,1); 
        $this->mockMail(); 
        
        $data = $this->jsonRpc(
            'mail.addTpl', [ 
                'name' => 'tpl_test',
                'from' => 'test@thestudnet.com',
                'from_name'=>'test',
                'subject' => 'test',
                'text' => 'test',
                'content' => 'test',
                'files' => [
                    [
                        'name' => 'filename',
                        'type' => 'text',
                        'is_encoding' => 1,
                        'encoding' => 'encoding',
                        'content' => 'content'
                    ]
                ]
            ]
        );
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals($data['result'] , true); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }
    
    
    
   
   
}
