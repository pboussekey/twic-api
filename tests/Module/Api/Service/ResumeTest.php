<?php
namespace ModuleTest\Api\Service;

use ModuleTest\Api\AbstractService;

class ResumeTest extends AbstractService
{
    public static $session;

    public static function setUpBeforeClass()
    {
        system('phing -q reset-db deploy-db');

        parent::setUpBeforeClass();
    }

    public function testCanAddResume()
    {
        $this->setIdentity(1);

        $data = $this->jsonRpc('resume.add', array(
            'start_date' => '2013-01-02',
            'end_date' => '2015-01-02',
            'address' => 'France',
            'logo' => 'token',
            'title' => 'super exp',
            'subtitle' => ' ingenieur R&D',
            'description' => 'plein de chose',
            'type' => 1
            ));

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);

        return $data['result'];
    }

    public function testCanAddResumeTwo()
    {
        $this->setIdentity(1);

        $data = $this->jsonRpc('resume.add', array(
            'start_date' => '2013-03-02',
            'end_date' => '2015-03-02',
            'address' => 'USA',
            'logo' => 'token2',
            'title' => 'super exp2',
            'subtitle' => ' ingenieur R&D2',
            'description' => 'plein de chose2',
            'type' => 1
        ));

        $this->reset();
        $this->setIdentity(1);

        $data = $this->jsonRpc('resume.add', array(
            'start_date' => '2013-03-02',
            'end_date' => '2015-04-02',
            'address' => 'USA',
            'logo' => 'token2',
            'title' => 'super exp2',
            'subtitle' => ' ingenieur R&D2',
            'description' => 'plein de chose2',
            'type' => 1
        ));

        $this->reset();
        $this->setIdentity(1);

        $data = $this->jsonRpc('resume.add', array(
            'start_date' => '2013-03-02',
            'address' => 'USA',
            'logo' => 'token2',
            'title' => 'super exp2',
            'subtitle' => ' ingenieur R&D2',
            'description' => 'plein de chose2',
            'type' => 1
        ));

        $this->reset();
        $this->setIdentity(1);

        $data = $this->jsonRpc('resume.add', array(
            'start_date' => '2013-03-02',
            'address' => 'USA',
            'logo' => 'token2',
            'title' => 'super exp2',
            'subtitle' => ' ingenieur R&D2',
            'description' => 'plein de chose2',
            'type' => 1
        ));

        $this->assertEquals(count($data) , 3); 
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals($data['result'] , 5);
        $this->assertEquals($data['jsonrpc'] , 2.0);
    }



    /**
     *
     * @depends testCanAddResume
     */
    public function testCanUpdateResume($resume)
    {
        $this->setIdentity(1);

        $data = $this->jsonRpc('resume.update', array(
            'id' => $resume,
            'start_date' => '2013-01-09',
            'end_date' => '2015-01-09',
            'address' => 'France UPT',
            'logo' => 'token UPT',
            'title' => 'super exp UPT',
            'subtitle' => ' ingenieur R&D UPT',
            'description' => 'plein de chose UPT',
            'type' => 2
        ));

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);

        return $data['result'];
    }

    /**
     *
     * @depends testCanAddResume
     */
    public function testCanGetResume($resume)
    {
        $this->setIdentity(1);

        $data = $this->jsonRpc('resume.get', ['id' => [1]]);

        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals(count($data['result']) , 1);
        $this->assertEquals(count($data['result'][1]) , 16);
        $this->assertEquals($data['result'][1]['id'] , 1);
        $this->assertEquals($data['result'][1]['start_date'] , "2013-01-09");
        $this->assertEquals($data['result'][1]['end_date'] , "2015-01-09");
        $this->assertEquals($data['result'][1]['address'] , "France UPT");
        $this->assertEquals($data['result'][1]['title'] , "super exp UPT");
        $this->assertEquals($data['result'][1]['subtitle'] , " ingenieur R&D UPT");
        $this->assertEquals($data['result'][1]['logo'] , "token UPT");
        $this->assertEquals($data['result'][1]['description'] , "plein de chose UPT");
        $this->assertEquals($data['result'][1]['type'] , 2);
        $this->assertEquals($data['result'][1]['user_id'] , 1);
        $this->assertEquals($data['result'][1]['publisher'] , null);
        $this->assertEquals($data['result'][1]['url'] , null);
        $this->assertEquals($data['result'][1]['cause'] , null);
        $this->assertEquals($data['result'][1]['study'] , null);
        $this->assertEquals($data['result'][1]['grade'] , null);
        $this->assertEquals($data['result'][1]['note'] , null);
        $this->assertEquals($data['jsonrpc'] , 2.0);
    }

    public function testCanGetListId()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('resume.getListId', ['user_id' => 1]);

        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals(count($data['result']) , 1);
        $this->assertEquals(count($data['result'][1]) , 5);
        $this->assertEquals($data['result'][1][0] , 1);
        $this->assertEquals($data['result'][1][1] , 2);
        $this->assertEquals($data['result'][1][2] , 3);
        $this->assertEquals($data['result'][1][3] , 4);
        $this->assertEquals($data['result'][1][4] , 5);
        $this->assertEquals($data['jsonrpc'] , 2.0);
    }

    /**
     *
     * @depends testCanAddResume
     */
    public function testCanDeleteResume($resume)
    {
        $this->setIdentity(1);

        $data = $this->jsonRpc('resume.delete', array(
            'id' => $resume
        ));

        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['result'], 1);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }
}
