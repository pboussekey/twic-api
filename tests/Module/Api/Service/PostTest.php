<?php
namespace ModuleTest\Api\Service;

use ModuleTest\Api\AbstractService;

class PostTest extends AbstractService
{
    public static function setUpBeforeClass()
    {
        system('phing -q reset-db deploy-db');

        parent::setUpBeforeClass();
    }

    public function testPostLinkPreview()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('post.linkPreview', [
            'url' => 'http://thestudnet.com/',
        ]);

        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals(count($data['result']) , 3);
        $this->assertEquals(count($data['result']['meta']) , 8);
        $this->assertEquals($data['result']['meta']['twitter:card'] , "summary");
        $this->assertEquals($data['result']['meta']['twitter:site'] , "@TwicSLE");
        $this->assertEquals($data['result']['meta']['twitter:title'] , "TWIC, The Social Learning Environment for B-Schools");
        $this->assertEquals(!empty($data['result']['meta']['twitter:description']) , true);
        $this->assertEquals($data['result']['meta']['twitter:image'] , "https://twicbythestudnet.com/assets/images/Logo.svg");
        $this->assertEquals($data['result']['meta']['twitter:image:alt'] , "Twic logo");
        $this->assertEquals($data['result']['meta']['viewport'] , "width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1");
        $this->assertEquals(!empty($data['result']['meta']['description']) , true);
        $this->assertEquals(count($data['result']['open_graph']) , 7);
        $this->assertEquals($data['result']['open_graph']['title'] , "TWIC, The Social Learning Environment for B-Schools");
        $this->assertEquals($data['result']['open_graph']['type'] , "website");
        $this->assertEquals($data['result']['open_graph']['url'] , "https://twicbythestudnet.com");
        $this->assertEquals($data['result']['open_graph']['image'] , "https://twicbythestudnet.com/assets/images/Logo.svg");
        $this->assertEquals(!empty($data['result']['open_graph']['description']) , true);
        $this->assertEquals($data['result']['open_graph']['locale'] , "en_US");
        $this->assertEquals($data['result']['open_graph']['site_name'] , "TWIC");
        $this->assertEquals(count($data['result']['images']) , 0);
        $this->assertEquals($data['jsonrpc'] , 2.0);

    }

    public function testPostAdd()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('post.add', [
            'content' => 'content #toto @[\'U\',1]',
            'link' => 'link',
            'picture' => 'picture',
            'name_picture' => 'name_picture',
            'link_title' => 'link_title',
            'link_desc' => 'link_desc',
            //'t_page_id' => 1,
            //'t_user_id' => 1,
            'docs' => [
                ['name' => 'name', 'link' => 'link', 'type' => 'type'],
                ['name' => 'name2', 'link' => 'link2', 'type' => 'type2'],
            ]
        ]);

        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals($data['result'] , 1);
        $this->assertEquals($data['jsonrpc'] , 2.0);

        return $data['result'];
    }

    /**
     * @depends testPostAdd
     */
    public function testPostReport($post_id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('report.add', [
            'reason' => 'plein',
            'description' => 'desxcriptiovb',
            'post_id' => $post_id
        ]);

        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals($data['result'] , 1);
        $this->assertEquals($data['jsonrpc'] , 2.0);
    }


    /**
     * @depends testPostAdd
     */
    public function testPostUpdate($post_id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('post.update', [
            'id' => $post_id,
            'content' => 'content  #toto @[\'U\',1]',
            'link' => 'linkUpt',
            'picture' => 'pictureUpt',
            'name_picture' => 'name_pictureUpt',
            'link_title' => 'link_titleUpt',
            'link_desc' => 'link_descUpt',
            'docs' => [
                ['name' => 'nameUpt', 'link' => 'linkUpt', 'type' => 'typeUpt'],
                ['name' => 'name2Upt', 'link' => 'link2Upt', 'type' => 'type2Upt'],
            ]
        ]);

        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals($data['result'] , 1);
        $this->assertEquals($data['jsonrpc'] , 2.0);
    }

    /**
     * @depends testPostAdd
     */
    public function testPostMobileGet($id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('post.get', [
          'id' => $id
        ]);

        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals(count($data['result']) , 22);
        $this->assertEquals(count($data['result']['subscription']) , 5);
        $this->assertEquals(!empty($data['result']['subscription']['last_date']) , true);
        $this->assertEquals($data['result']['subscription']['action'] , "create");
        $this->assertEquals($data['result']['subscription']['sub_post_id'] , null);
        $this->assertEquals($data['result']['subscription']['user_id'] , 1);
        $this->assertEquals(count($data['result']['subscription']['data']) , 3);
        $this->assertEquals($data['result']['subscription']['data']['id'] , 1);
        $this->assertEquals($data['result']['subscription']['data']['parent_id'] , null);
        $this->assertEquals($data['result']['subscription']['data']['origin_id'] , null);
        $this->assertEquals(count($data['result']['docs']) , 2);
        $this->assertEquals(count($data['result']['docs'][0]) , 11);
        $this->assertEquals($data['result']['docs'][0]['id'] , 6);
        $this->assertEquals($data['result']['docs'][0]['name'] , "nameUpt");
        $this->assertEquals($data['result']['docs'][0]['link'] , "linkUpt");
        $this->assertEquals($data['result']['docs'][0]['token'] , null);
        $this->assertEquals($data['result']['docs'][0]['type'] , "typeUpt");
        $this->assertEquals(!empty($data['result']['docs'][0]['created_date']) , true);
        $this->assertEquals($data['result']['docs'][0]['deleted_date'] , null);
        $this->assertEquals($data['result']['docs'][0]['updated_date'] , null);
        $this->assertEquals($data['result']['docs'][0]['folder_id'] , null);
        $this->assertEquals($data['result']['docs'][0]['owner_id'] , 1);
        $this->assertEquals($data['result']['docs'][0]['box_id'] , null);
        $this->assertEquals(count($data['result']['docs'][1]) , 11);
        $this->assertEquals($data['result']['docs'][1]['id'] , 7);
        $this->assertEquals($data['result']['docs'][1]['name'] , "name2Upt");
        $this->assertEquals($data['result']['docs'][1]['link'] , "link2Upt");
        $this->assertEquals($data['result']['docs'][1]['token'] , null);
        $this->assertEquals($data['result']['docs'][1]['type'] , "type2Upt");
        $this->assertEquals(!empty($data['result']['docs'][1]['created_date']) , true);
        $this->assertEquals($data['result']['docs'][1]['deleted_date'] , null);
        $this->assertEquals($data['result']['docs'][1]['updated_date'] , null);
        $this->assertEquals($data['result']['docs'][1]['folder_id'] , null);
        $this->assertEquals($data['result']['docs'][1]['owner_id'] , 1);
        $this->assertEquals($data['result']['docs'][1]['box_id'] , null);
        $this->assertEquals($data['result']['nbr_comments'] , 0);
        $this->assertEquals($data['result']['nbr_likes'] , 0);
        $this->assertEquals($data['result']['is_liked'] , 0);
        $this->assertEquals($data['result']['id'] , 1);
        $this->assertEquals($data['result']['content'] , "content  #toto @['U',1]");
        $this->assertEquals($data['result']['user_id'] , 1);
        $this->assertEquals($data['result']['page_id'] , null);
        $this->assertEquals($data['result']['item_id'] , null);
        $this->assertEquals($data['result']['link'] , "linkUpt");
        $this->assertEquals($data['result']['picture'] , "pictureUpt");
        $this->assertEquals($data['result']['name_picture'] , "name_pictureUpt");
        $this->assertEquals($data['result']['link_title'] , "link_titleUpt");
        $this->assertEquals($data['result']['link_desc'] , "link_descUpt");
        $this->assertEquals(!empty($data['result']['created_date']) , true);
        $this->assertEquals(!empty($data['result']['updated_date']) , true);
        $this->assertEquals($data['result']['parent_id'] , null);
        $this->assertEquals($data['result']['t_page_id'] , null);
        $this->assertEquals($data['result']['t_user_id'] , 1);
        $this->assertEquals($data['result']['type'] , "post");
        $this->assertEquals($data['result']['data'] , null);
        $this->assertEquals($data['jsonrpc'] , 2.0);
    }

    public function testPostGetListId()
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('post.getListId', []);

        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals(count($data['result']) , 1);
        $this->assertEquals(count($data['result'][0]) , 2);
        $this->assertEquals(!empty($data['result'][0]['last_date']) , true);
        $this->assertEquals($data['result'][0]['id'] , 1);
        $this->assertEquals($data['jsonrpc'] , 2.0);
    }

    /**
     * @depends testPostAdd
     */
    public function testPostLike($post_id)
    {
        $this->setIdentity(2);
        $data = $this->jsonRpc('post.like', [
            'id' => $post_id,
        ]);

        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals($data['result'] , 1);
        $this->assertEquals($data['jsonrpc'] , 2.0);
    }

    /**
     * @depends testPostAdd
     */
    public function testPostUnLike($post_id)
    {
        $this->setIdentity(2);
        $data = $this->jsonRpc('post.unlike', [
            'id' => $post_id,
        ]);

        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals($data['result'] , 1);
        $this->assertEquals($data['jsonrpc'] , 2.0);
    }

    /**
     * @depends testPostAdd
     */
    public function testDelete($post_id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('post.delete', [
            'id' => $post_id,
        ]);

        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals($data['result'] , 1);
        $this->assertEquals($data['jsonrpc'] , 2.0);
    }

    /**
     * @depends testPostAdd
     */
    public function testReactivate($post_id)
    {
        $this->setIdentity(1);
        $data = $this->jsonRpc('post.reactivate', [
            'id' => $post_id,
        ]);

        $this->assertEquals(count($data) , 3);
        $this->assertEquals($data['id'] , 1);
        $this->assertEquals($data['result'] , 1);
        $this->assertEquals($data['jsonrpc'] , 2.0);
    }
}
