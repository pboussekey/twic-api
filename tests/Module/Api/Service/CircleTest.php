<?php
namespace ModuleTest\Api\Service;

use ModuleTest\Api\AbstractService;

class CircleTest extends AbstractService
{
    public static function setUpBeforeClass()
    {
        system('bin/phing -q reset-db deploy-db');

        parent::setUpBeforeClass();
    }

    public function testAddCircle()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('circle.add', [
            'name' => 'gnam'
        ]);

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);

        return $data['result'];
    }

    /**
     * @depends testAddCircle
     */
    public function testUpdateCircle($circle_id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('circle.update', [
            'id' => $circle_id,
            'name' => 'gnam'
        ]);

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['result'], 0);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    /**
     * @depends testAddCircle
     */
    public function testGetListCircle($circle_id)
    {
        
        $this->setIdentity(1);
        $data = $this->jsonRpc('circle.getList', [
            'id' => $circle_id
        ]);

        $this->assertEquals(count($data), 3);
        $this->assertEquals(count($data['result']), 1);
        $this->assertEquals(count($data['result'][0]), 2);
        $this->assertEquals($data['result'][0]['id'], 1);
        $this->assertEquals($data['result'][0]['name'], "gnam");
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    public function testCreateSchool()
    {
        // ADD SCHOOL
        $this->setIdentity(1,1);
        $data = $this->jsonRpc('page.add', [
          'title' => 'université de monaco',
          'short_title' => 'IUM buisness school',
          'logo' => 'token',
          'description' => 'une description',
          'website' => 'www.ium.com','programme' => 'super programme','background' => 'background',
          'type' => 'organization',
          'phone' => '+33480547852','contact' => 'contact@ium.com','contact_id' => 1,
          'address' => [
              "street_no" => 12,
              "street_type" => "rue",
              "street_name" => "du stade",
              "city" => ["name" => "Monaco"],
              "country" => ["name" => "Monaco"]
            ]]);
        
            $this->assertEquals(count($data) , 3);
            $this->assertEquals($data['id'] , 1); 
            $this->assertEquals($data['result'] , 1);
            $this->assertEquals($data['jsonrpc'] , 2.0);

        return $data['result'];
    }

    /**
     * @depends testAddCircle
     * @depends testCreateSchool
     */
    public function testAddCircleSchool($circle_id, $school_id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('circle.addOrganizations', [
            'id' => $circle_id,
            'organizations' => $school_id
        ]);

        $this->assertEquals(count($data), 3);
        $this->assertEquals(count($data['result']), 1);
        $this->assertEquals($data['result'][$school_id], 1);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    /**
     * @depends testAddCircle
     * @depends testCreateSchool
     */
    public function testgetCircle($circle_id, $school_id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('circle.get', [
            'id' => $circle_id
        ]);

        $this->assertEquals(count($data), 3);
        $this->assertEquals(count($data['result']), 3);
        $this->assertEquals(count($data['result']['organizations']), 1);
        $this->assertEquals(count($data['result']['organizations'][0]), 2);
        $this->assertEquals($data['result']['organizations'][0]['circle_id'], $circle_id);
        $this->assertEquals($data['result']['organizations'][0]['organization_id'], $school_id);
        $this->assertEquals($data['result']['id'], $circle_id);
        $this->assertEquals($data['result']['name'], "gnam");
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    /**
     * @depends testAddCircle
     * @depends testCreateSchool
     */
    public function testRemoveCircleOrganization($circle_id, $school_id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('circle.deleteOrganizations', [
            'id' => $circle_id,
            'organizations' => $school_id
        ]);

        $this->assertEquals(count($data), 3);
        $this->assertEquals(count($data['result']), 1);
        $this->assertEquals($data['result'][$school_id], 1);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    /**
     * @depends testAddCircle
     */
    public function testDeleteCircle($circle_id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('circle.delete', [
            'id' => $circle_id
        ]);

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }
}
