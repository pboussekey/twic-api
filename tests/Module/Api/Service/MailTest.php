<?php
namespace ModuleTest\Api\Service;

use ModuleTest\Api\AbstractService;

class MailTest extends AbstractService
{
    public static $session;

    public static function setUpBeforeClass()
    {
        system('phing -q reset-db deploy-db');
        parent::setUpBeforeClass();
       
    }
   
    public function testAddTpl()
    {
        $this->setIdentity(1,1); 
        $this->mockMail(false); 
        
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
    
     public function testReplaceTpl()
    {
        $this->setIdentity(1,1); 
        $this->mockMail(false); 
        
        $data = $this->jsonRpc(
            'mail.addTpl', [ 
                'name' => 'tpl_test',
                'from' => 'test@thestudnet.com',
                'from_name'=>'test',
                'subject' => 'test',
                'text' => 'test replaced',
                'content' => 'test replaced',
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
     public function testGetTplMail()
    {
        $this->setIdentity(1,1); 
        $this->mockMail(false); 

        $data = $this->jsonRpc(
            'mail.getTpl', ['name' => 'tpl_test']
        );

        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 8); 
        $this->assertEquals($data['result']['name'] , "tpl_test"); 
        $this->assertEquals($data['result']['subject'] , "test"); 
        $this->assertEquals($data['result']['from'] , "test@thestudnet.com"); 
        $this->assertEquals($data['result']['from_name'] , "test"); 
        $this->assertEquals(count($data['result']['storage']) , 3); 
        $this->assertEquals(count($data['result']['storage'][0]) , 17); 
        $this->assertEquals($data['result']['storage'][0]['is_encoded'] , false); 
        $this->assertEquals($data['result']['storage'][0]['is_mappable'] , true); 
        $this->assertEquals($data['result']['storage'][0]['is_path'] , false); 
        $this->assertEquals($data['result']['storage'][0]['datas'] , null); 
        $this->assertEquals($data['result']['storage'][0]['type'] , "text/plain"); 
        $this->assertEquals($data['result']['storage'][0]['encoding'] , "8bit"); 
        $this->assertEquals($data['result']['storage'][0]['id'] , null); 
        $this->assertEquals($data['result']['storage'][0]['disposition'] , null); 
        $this->assertEquals($data['result']['storage'][0]['filename'] , null); 
        $this->assertEquals($data['result']['storage'][0]['description'] , null); 
        $this->assertEquals($data['result']['storage'][0]['charset'] , null); 
        $this->assertEquals($data['result']['storage'][0]['boundary'] , null); 
        $this->assertEquals($data['result']['storage'][0]['location'] , null); 
        $this->assertEquals($data['result']['storage'][0]['language'] , null); 
        $this->assertEquals($data['result']['storage'][0]['content'] , "test replaced"); 
        $this->assertEquals($data['result']['storage'][0]['isStream'] , false); 
        $this->assertEquals(count($data['result']['storage'][0]['filters']) , 0); 
        $this->assertEquals(count($data['result']['storage'][1]) , 17); 
        $this->assertEquals($data['result']['storage'][1]['is_encoded'] , false); 
        $this->assertEquals($data['result']['storage'][1]['is_mappable'] , true); 
        $this->assertEquals($data['result']['storage'][1]['is_path'] , false); 
        $this->assertEquals($data['result']['storage'][1]['datas'] , null); 
        $this->assertEquals($data['result']['storage'][1]['type'] , "text/html"); 
        $this->assertEquals($data['result']['storage'][1]['encoding'] , "8bit"); 
        $this->assertEquals($data['result']['storage'][1]['id'] , null); 
        $this->assertEquals($data['result']['storage'][1]['disposition'] , null); 
        $this->assertEquals($data['result']['storage'][1]['filename'] , null); 
        $this->assertEquals($data['result']['storage'][1]['description'] , null); 
        $this->assertEquals($data['result']['storage'][1]['charset'] , null); 
        $this->assertEquals($data['result']['storage'][1]['boundary'] , null); 
        $this->assertEquals($data['result']['storage'][1]['location'] , null); 
        $this->assertEquals($data['result']['storage'][1]['language'] , null); 
        $this->assertEquals($data['result']['storage'][1]['content'] , "test replaced"); 
        $this->assertEquals($data['result']['storage'][1]['isStream'] , false); 
        $this->assertEquals(count($data['result']['storage'][1]['filters']) , 0); 
        $this->assertEquals(count($data['result']['storage'][2]) , 17); 
        $this->assertEquals($data['result']['storage'][2]['is_encoded'] , true); 
        $this->assertEquals($data['result']['storage'][2]['is_mappable'] , false); 
        $this->assertEquals($data['result']['storage'][2]['is_path'] , false); 
        $this->assertEquals($data['result']['storage'][2]['datas'] , null); 
        $this->assertEquals($data['result']['storage'][2]['type'] , "text"); 
        $this->assertEquals($data['result']['storage'][2]['encoding'] , "encoding"); 
        $this->assertEquals($data['result']['storage'][2]['id'] , null); 
        $this->assertEquals($data['result']['storage'][2]['disposition'] , "attachment"); 
        $this->assertEquals($data['result']['storage'][2]['filename'] , "filename"); 
        $this->assertEquals($data['result']['storage'][2]['description'] , null); 
        $this->assertEquals($data['result']['storage'][2]['charset'] , null); 
        $this->assertEquals($data['result']['storage'][2]['boundary'] , null); 
        $this->assertEquals($data['result']['storage'][2]['location'] , null); 
        $this->assertEquals($data['result']['storage'][2]['language'] , null); 
        $this->assertEquals($data['result']['storage'][2]['content'] , "content"); 
        $this->assertEquals($data['result']['storage'][2]['isStream'] , false); 
        $this->assertEquals(count($data['result']['storage'][2]['filters']) , 0); 
        $this->assertEquals($data['result']['flag'] , 1); 
        $this->assertEquals($data['result']['iteratorClass'] , "ArrayIterator"); 
        $this->assertEquals(count($data['result']['protectedProperties']) , 8); 
        $this->assertEquals($data['result']['protectedProperties'][0] , "name"); 
        $this->assertEquals($data['result']['protectedProperties'][1] , "subject"); 
        $this->assertEquals($data['result']['protectedProperties'][2] , "from"); 
        $this->assertEquals($data['result']['protectedProperties'][3] , "from_name"); 
        $this->assertEquals($data['result']['protectedProperties'][4] , "storage"); 
        $this->assertEquals($data['result']['protectedProperties'][5] , "flag"); 
        $this->assertEquals($data['result']['protectedProperties'][6] , "iteratorClass"); 
        $this->assertEquals($data['result']['protectedProperties'][7] , "protectedProperties"); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 

    }
    
    
    public function testGetListMail()
    {
        $this->setIdentity(1,1); 
        $this->mockMail(false); 
        
        $data = $this->jsonRpc(
            'mail.getListTpl', [  ]
        );
        
        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1); 
        $this->assertEquals(count($data['result']) , 2); 
        $this->assertEquals($data['result']['count'] , 1); 
        $this->assertEquals(count($data['result']['results']) , 1); 
        $this->assertEquals(count($data['result']['results'][0]) , 8); 
        $this->assertEquals($data['result']['results'][0]['name'] , "tpl_test"); 
        $this->assertEquals($data['result']['results'][0]['subject'] , "test"); 
        $this->assertEquals($data['result']['results'][0]['from'] , "test@thestudnet.com"); 
        $this->assertEquals($data['result']['results'][0]['from_name'] , "test"); 
        $this->assertEquals(count($data['result']['results'][0]['storage']) , 3); 
        $this->assertEquals(count($data['result']['results'][0]['storage'][0]) , 17); 
        $this->assertEquals($data['result']['results'][0]['storage'][0]['is_encoded'] , false); 
        $this->assertEquals($data['result']['results'][0]['storage'][0]['is_mappable'] , true); 
        $this->assertEquals($data['result']['results'][0]['storage'][0]['is_path'] , false); 
        $this->assertEquals($data['result']['results'][0]['storage'][0]['datas'] , null); 
        $this->assertEquals($data['result']['results'][0]['storage'][0]['type'] , "text/plain"); 
        $this->assertEquals($data['result']['results'][0]['storage'][0]['encoding'] , "8bit"); 
        $this->assertEquals($data['result']['results'][0]['storage'][0]['id'] , null); 
        $this->assertEquals($data['result']['results'][0]['storage'][0]['disposition'] , null); 
        $this->assertEquals($data['result']['results'][0]['storage'][0]['filename'] , null); 
        $this->assertEquals($data['result']['results'][0]['storage'][0]['description'] , null); 
        $this->assertEquals($data['result']['results'][0]['storage'][0]['charset'] , null); 
        $this->assertEquals($data['result']['results'][0]['storage'][0]['boundary'] , null); 
        $this->assertEquals($data['result']['results'][0]['storage'][0]['location'] , null); 
        $this->assertEquals($data['result']['results'][0]['storage'][0]['language'] , null); 
        $this->assertEquals($data['result']['results'][0]['storage'][0]['content'] , "test replaced"); 
        $this->assertEquals($data['result']['results'][0]['storage'][0]['isStream'] , false); 
        $this->assertEquals(count($data['result']['results'][0]['storage'][0]['filters']) , 0); 
        $this->assertEquals(count($data['result']['results'][0]['storage'][1]) , 17); 
        $this->assertEquals($data['result']['results'][0]['storage'][1]['is_encoded'] , false); 
        $this->assertEquals($data['result']['results'][0]['storage'][1]['is_mappable'] , true); 
        $this->assertEquals($data['result']['results'][0]['storage'][1]['is_path'] , false); 
        $this->assertEquals($data['result']['results'][0]['storage'][1]['datas'] , null); 
        $this->assertEquals($data['result']['results'][0]['storage'][1]['type'] , "text/html"); 
        $this->assertEquals($data['result']['results'][0]['storage'][1]['encoding'] , "8bit"); 
        $this->assertEquals($data['result']['results'][0]['storage'][1]['id'] , null); 
        $this->assertEquals($data['result']['results'][0]['storage'][1]['disposition'] , null); 
        $this->assertEquals($data['result']['results'][0]['storage'][1]['filename'] , null); 
        $this->assertEquals($data['result']['results'][0]['storage'][1]['description'] , null); 
        $this->assertEquals($data['result']['results'][0]['storage'][1]['charset'] , null); 
        $this->assertEquals($data['result']['results'][0]['storage'][1]['boundary'] , null); 
        $this->assertEquals($data['result']['results'][0]['storage'][1]['location'] , null); 
        $this->assertEquals($data['result']['results'][0]['storage'][1]['language'] , null); 
        $this->assertEquals($data['result']['results'][0]['storage'][1]['content'] , "test replaced"); 
        $this->assertEquals($data['result']['results'][0]['storage'][1]['isStream'] , false); 
        $this->assertEquals(count($data['result']['results'][0]['storage'][1]['filters']) , 0); 
        $this->assertEquals(count($data['result']['results'][0]['storage'][2]) , 17); 
        $this->assertEquals($data['result']['results'][0]['storage'][2]['is_encoded'] , true); 
        $this->assertEquals($data['result']['results'][0]['storage'][2]['is_mappable'] , false); 
        $this->assertEquals($data['result']['results'][0]['storage'][2]['is_path'] , false); 
        $this->assertEquals($data['result']['results'][0]['storage'][2]['datas'] , null); 
        $this->assertEquals($data['result']['results'][0]['storage'][2]['type'] , "text"); 
        $this->assertEquals($data['result']['results'][0]['storage'][2]['encoding'] , "encoding"); 
        $this->assertEquals($data['result']['results'][0]['storage'][2]['id'] , null); 
        $this->assertEquals($data['result']['results'][0]['storage'][2]['disposition'] , "attachment"); 
        $this->assertEquals($data['result']['results'][0]['storage'][2]['filename'] , "filename"); 
        $this->assertEquals($data['result']['results'][0]['storage'][2]['description'] , null); 
        $this->assertEquals($data['result']['results'][0]['storage'][2]['charset'] , null); 
        $this->assertEquals($data['result']['results'][0]['storage'][2]['boundary'] , null); 
        $this->assertEquals($data['result']['results'][0]['storage'][2]['location'] , null); 
        $this->assertEquals($data['result']['results'][0]['storage'][2]['language'] , null); 
        $this->assertEquals($data['result']['results'][0]['storage'][2]['content'] , "content"); 
        $this->assertEquals($data['result']['results'][0]['storage'][2]['isStream'] , false); 
        $this->assertEquals(count($data['result']['results'][0]['storage'][2]['filters']) , 0); 
        $this->assertEquals($data['result']['results'][0]['flag'] , 1); 
        $this->assertEquals($data['result']['results'][0]['iteratorClass'] , "ArrayIterator"); 
        $this->assertEquals(count($data['result']['results'][0]['protectedProperties']) , 8); 
        $this->assertEquals($data['result']['results'][0]['protectedProperties'][0] , "name"); 
        $this->assertEquals($data['result']['results'][0]['protectedProperties'][1] , "subject"); 
        $this->assertEquals($data['result']['results'][0]['protectedProperties'][2] , "from"); 
        $this->assertEquals($data['result']['results'][0]['protectedProperties'][3] , "from_name"); 
        $this->assertEquals($data['result']['results'][0]['protectedProperties'][4] , "storage"); 
        $this->assertEquals($data['result']['results'][0]['protectedProperties'][5] , "flag"); 
        $this->assertEquals($data['result']['results'][0]['protectedProperties'][6] , "iteratorClass"); 
        $this->assertEquals($data['result']['results'][0]['protectedProperties'][7] , "protectedProperties"); 
        $this->assertEquals($data['jsonrpc'] , 2.0); 


    }
    
    
    
   
   
}
