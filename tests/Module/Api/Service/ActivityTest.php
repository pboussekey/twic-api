<?php
namespace ModuleTest\Api\Service;

use ModuleTest\Api\AbstractService;

class ActivityTest extends AbstractService
{
    public static $session;

    public static function setUpBeforeClass()
    {
        system('phing -q reset-db deploy-db');
        parent::setUpBeforeClass();
    }

    public function testCanAdd()
    {
        $this->setIdentity(4);
        $data = $this->jsonRpc(
            'activity.add', [
            'activities' => [
                [
                    'date' => '2015-04-22T06:00:00Z',
                    'event' => 'navigation',
                    'object' => [
                        'id' => 3,
                        'name' => 'lms.page.users',
                        'value' => 7,
                        'data' => ["type"=>"group","id" => "1"],
                        'target' => [
                            'id' => 3,
                            'name' => 'nametarget',
                            'data' => 'datatarget'
                        ]
                    ]
                ],
                [
                    'date' => '2015-04-22T06:01:00Z',
                    'event' => 'event',
                    'object' => [
                        'id' => 3,
                        'name' => 'lms.page.timeline',
                        'value' => 5,
                        'data' => ["type"=>"group","id" => "1"],
                        'target' => [
                            'id' => 3,
                            'name' => 'nametarget',
                            'data' => 'datatarget'
                        ]
                    ]
                ]
            ]
            ]
        );
        
        $this->assertEquals(count($data), 3);
        $this->assertEquals(count($data['result']), 2);
        $this->assertEquals($data['result'][0], 1);
        $this->assertEquals($data['result'][1], 2);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    public function testCanAddTwo()
    {
        $this->setIdentity(4);
        $data = $this->jsonRpc(
            'activity.add', [
            'activities' => [[
                'date' => '2015-04-24T06:00:00Z',
                'event' => 'navigation',
                'object' => [
                    'id' => 3,
                    'name' => 'lms.page.timeline',
                    'value' => 3,
                    'data' => ["type"=>"group","id" => "1"],
                ],'target' => [
                        'id' => 3,
                    'name' => 'nametarget',
                    'data' => 'datatarget'
                ],
            ],
                [
                'date' => '2015-04-24T06:01:00Z',
                'event' => 'navigation',
                'object' => [
                    'id' => 3,
                    'name' => 'lms.page.timeline',
                    'value' => 3,
                    'data' => ["type"=>"course","id" => "2"],
                ],'target' => [
                        'id' => 3,
                    'name' => 'nametarget',
                    'data' => 'datatarget'
                ],
                ]],
            ]
        );
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 2);
        $this->assertEquals($data['result'][0], 3);
        $this->assertEquals($data['result'][1], 4);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    public function testCanGetListDate()
    {
        $this->setIdentity(1);

        $data = $this->jsonRpc(
            'activity.getList', array(
            'start_date' => '2015-04-01T00:00:00Z',
            'end_date' => '2015-05-01T00:00:00Z',
            'organization_id' => 1
            )
        );
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 2);
        $this->assertEquals($data['result']['count'], 4);
        $this->assertEquals(count($data['result']['list']), 4);
        $this->assertEquals(count($data['result']['list'][0]), 6);
        $this->assertEquals($data['result']['list'][0]['object_name'], "lms.page.users");
        $this->assertEquals($data['result']['list'][0]['id'], 1);
        $this->assertEquals($data['result']['list'][0]['event'], "navigation");
        $this->assertEquals($data['result']['list'][0]['object_data'], "{\"type\":\"group\",\"id\":\"1\"}");
        $this->assertEquals(!empty($data['result']['list'][0]['date']), true);
        $this->assertEquals($data['result']['list'][0]['user_id'], 4);
        $this->assertEquals(count($data['result']['list'][1]), 6);
        $this->assertEquals($data['result']['list'][1]['object_name'], "lms.page.timeline");
        $this->assertEquals($data['result']['list'][1]['id'], 2);
        $this->assertEquals($data['result']['list'][1]['event'], "event");
        $this->assertEquals($data['result']['list'][1]['object_data'], "{\"type\":\"group\",\"id\":\"1\"}");
        $this->assertEquals(!empty($data['result']['list'][1]['date']), true);
        $this->assertEquals($data['result']['list'][1]['user_id'], 4);
        $this->assertEquals(count($data['result']['list'][2]), 6);
        $this->assertEquals($data['result']['list'][2]['object_name'], "lms.page.timeline");
        $this->assertEquals($data['result']['list'][2]['id'], 3);
        $this->assertEquals($data['result']['list'][2]['event'], "navigation");
        $this->assertEquals($data['result']['list'][2]['object_data'], "{\"type\":\"group\",\"id\":\"1\"}");
        $this->assertEquals(!empty($data['result']['list'][2]['date']), true);
        $this->assertEquals($data['result']['list'][2]['user_id'], 4);
        $this->assertEquals(count($data['result']['list'][3]), 6);
        $this->assertEquals($data['result']['list'][3]['object_name'], "lms.page.timeline");
        $this->assertEquals($data['result']['list'][3]['id'], 4);
        $this->assertEquals($data['result']['list'][3]['event'], "navigation");
        $this->assertEquals($data['result']['list'][3]['object_data'], "{\"type\":\"course\",\"id\":\"2\"}");
        $this->assertEquals(!empty($data['result']['list'][3]['date']), true);
        $this->assertEquals($data['result']['list'][3]['user_id'], 4);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }

    public function testCanGetList()
    {
        $this->setIdentity(1);

        $data = $this->jsonRpc('activity.getList', array('event' => 'event'));
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 2);
        $this->assertEquals($data['result']['count'], 4);
        $this->assertEquals(count($data['result']['list']), 4);
        $this->assertEquals(count($data['result']['list'][0]), 6);
        $this->assertEquals($data['result']['list'][0]['object_name'], "lms.page.users");
        $this->assertEquals($data['result']['list'][0]['id'], 1);
        $this->assertEquals($data['result']['list'][0]['event'], "navigation");
        $this->assertEquals($data['result']['list'][0]['object_data'], "{\"type\":\"group\",\"id\":\"1\"}");
        $this->assertEquals(!empty($data['result']['list'][0]['date']), true);
        $this->assertEquals($data['result']['list'][0]['user_id'], 4);
        $this->assertEquals(count($data['result']['list'][1]), 6);
        $this->assertEquals($data['result']['list'][1]['object_name'], "lms.page.timeline");
        $this->assertEquals($data['result']['list'][1]['id'], 2);
        $this->assertEquals($data['result']['list'][1]['event'], "event");
        $this->assertEquals($data['result']['list'][1]['object_data'], "{\"type\":\"group\",\"id\":\"1\"}");
        $this->assertEquals(!empty($data['result']['list'][1]['date']), true);
        $this->assertEquals($data['result']['list'][1]['user_id'], 4);
        $this->assertEquals(count($data['result']['list'][2]), 6);
        $this->assertEquals($data['result']['list'][2]['object_name'], "lms.page.timeline");
        $this->assertEquals($data['result']['list'][2]['id'], 3);
        $this->assertEquals($data['result']['list'][2]['event'], "navigation");
        $this->assertEquals($data['result']['list'][2]['object_data'], "{\"type\":\"group\",\"id\":\"1\"}");
        $this->assertEquals(!empty($data['result']['list'][2]['date']), true);
        $this->assertEquals($data['result']['list'][2]['user_id'], 4);
        $this->assertEquals(count($data['result']['list'][3]), 6);
        $this->assertEquals($data['result']['list'][3]['object_name'], "lms.page.timeline");
        $this->assertEquals($data['result']['list'][3]['id'], 4);
        $this->assertEquals($data['result']['list'][3]['event'], "navigation");
        $this->assertEquals($data['result']['list'][3]['object_data'], "{\"type\":\"course\",\"id\":\"2\"}");
        $this->assertEquals(!empty($data['result']['list'][3]['date']), true);
        $this->assertEquals($data['result']['list'][3]['user_id'], 4);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }
    
    
    public function testGetConnectionsCount()
    {
        $this->setIdentity(1);

        $data = $this->jsonRpc(
            'activity.getConnections',
            ['start_date'=> '2015-04-20' , 'end_date' => '2015-04-25', 'interval_date' => 'D', 'user_id' => 4]
        );
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 2);
        $this->assertEquals(count($data['result']['2015-04-22']), 2);
        $this->assertEquals($data['result']['2015-04-22']['avg'], 60);
        $this->assertEquals($data['result']['2015-04-22']['count'], 1);
        $this->assertEquals(count($data['result']['2015-04-24']), 2);
        $this->assertEquals($data['result']['2015-04-24']['avg'], 60);
        $this->assertEquals($data['result']['2015-04-24']['count'], 1);
        $this->assertEquals($data['jsonrpc'], 2.0);
        
        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'activity.getConnections',
            ['start_date'=> '2015-04-20' , 'end_date' => '2015-04-25', 'interval_date' => 'M', 'user_id' => 4]
        );
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 1);
        $this->assertEquals(count($data['result']['2015-04']), 2);
        $this->assertEquals($data['result']['2015-04']['avg'], 60);
        $this->assertEquals($data['result']['2015-04']['count'], 2);
        $this->assertEquals($data['jsonrpc'], 2.0);

       

        $this->reset();
        $this->setIdentity(1);
        $data = $this->jsonRpc(
            'activity.getConnections',
            ['start_date'=> '2015-04-20' , 'end_date' => '2015-04-25', 'interval_date' => 'Y', 'user_id' => 4]
        );
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 1);
        $this->assertEquals(count($data['result'][2015]), 2);
        $this->assertEquals($data['result'][2015]['avg'], 60);
        $this->assertEquals($data['result'][2015]['count'], 2);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }
    
    public function testGetVisitedPages()
    {
        $this->setIdentity(1);

        $data = $this->jsonRpc(
            'activity.getPages',
            ['start_date'=> '2015-04-20' , 'end_date' => '2015-04-25']
        );
        
        $this->assertEquals($data['id'], 1);
        $this->assertEquals(count($data['result']), 3);
        $this->assertEquals(count($data['result'][0]), 6);
        $this->assertEquals($data['result'][0]['object_name'], "group.timeline");
        $this->assertEquals($data['result'][0]['count'], 2);
        $this->assertEquals(!empty($data['result'][0]['min_date']), true);
        $this->assertEquals(!empty($data['result'][0]['max_date']), true);
        $this->assertEquals($data['result'][0]['object_data'], "{\"type\":\"group\",\"id\":\"1\"}");
        $this->assertEquals(!empty($data['result'][0]['date']), true);
        $this->assertEquals(count($data['result'][1]), 6);
        $this->assertEquals($data['result'][1]['object_name'], "group.users");
        $this->assertEquals($data['result'][1]['count'], 1);
        $this->assertEquals(!empty($data['result'][1]['min_date']), true);
        $this->assertEquals(!empty($data['result'][1]['max_date']), true);
        $this->assertEquals($data['result'][1]['object_data'], "{\"type\":\"group\",\"id\":\"1\"}");
        $this->assertEquals(!empty($data['result'][1]['date']), true);
        $this->assertEquals(count($data['result'][2]), 6);
        $this->assertEquals($data['result'][2]['object_name'], "course.timeline");
        $this->assertEquals($data['result'][2]['count'], 1);
        $this->assertEquals(!empty($data['result'][2]['min_date']), true);
        $this->assertEquals(!empty($data['result'][2]['max_date']), true);
        $this->assertEquals($data['result'][2]['object_data'], "{\"type\":\"course\",\"id\":\"2\"}");
        $this->assertEquals(!empty($data['result'][2]['date']), true);
        $this->assertEquals($data['jsonrpc'], 2.0);
    }
}
