<?php
/**
 * Post
 */
namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Application\Service\Post as PostService;

/**
 * Plugin Post
 */
class Post extends AbstractPlugin
{

    protected $post;

    /**
     */
    public function __construct(PostService $post)
    {
        $this->post = $post;
    }

    public function add($apikey, $content, $link, $page_id)
    {
        return $this->post->addFromApi($apikey, $content, $link, $page_id);
    }


}
