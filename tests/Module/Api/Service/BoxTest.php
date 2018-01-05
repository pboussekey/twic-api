<?php
namespace ModuleTest\Api\Service;

use ModuleTest\Api\AbstractService;
use Box\Model\Document;
use Box\Model\Session;

class BoxTest extends AbstractService
{
    public static $session;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
    }

    public function testDocument()
    {
        $m_document = new Document([
            'id' => 'id',
            'name' => 'name',
            'createdAt' => 'createdAt',
            'modifiedAt' => 'modifiedAt',
            'status' => 'status'
            
        ]);
        
        $this->assertEquals($m_document->getId() , "id"); 
        $this->assertEquals($m_document->getName() , "name"); 
        $this->assertEquals($m_document->getCreatedAt() , "createdAt"); 
        $this->assertEquals($m_document->getModifiedAt() , "modifiedAt"); 
        $this->assertEquals($m_document->getStatus() , "status"); 
        
        $data = $m_document->jsonSerialize();
        $this->assertEquals(count($data) , 5); 
        $this->assertEquals($data['status'] , "status"); 
        $this->assertEquals($data['modifiedAt'] , "modifiedAt"); 
        $this->assertEquals($data['createdAt'] , "createdAt"); 
        $this->assertEquals($data['name'] , "name"); 
        $this->assertEquals($data['id'] , "id"); 

    }
    
     public function testSession()
    {
        $m_session = new Session([
            'id' => 'id',
            'name' => 'name',
            'createdAt' => 'createdAt',
            'modifiedAt' => 'modifiedAt',
            'status' => 'status'
            
        ]);
        
        $this->assertEquals($m_session->getId() , "id"); 
        $this->assertEquals($m_session->getName() , "name"); 
        $this->assertEquals($m_session->getCreatedAt() , "createdAt"); 
        $this->assertEquals($m_session->getModifiedAt() , "modifiedAt"); 
        $this->assertEquals($m_session->getStatus() , "status"); 
        
        $data = $m_session->jsonSerialize();
        $this->assertEquals(count($data) , 5); 
        $this->assertEquals($data['status'] , "status"); 
        $this->assertEquals($data['modifiedAt'] , "modifiedAt"); 
        $this->assertEquals($data['createdAt'] , "createdAt"); 
        $this->assertEquals($data['name'] , "name"); 
        $this->assertEquals($data['id'] , "id"); 

    }

}
