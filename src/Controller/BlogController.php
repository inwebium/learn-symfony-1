<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * 
 * @Route("/blog")
 */
class BlogController extends AbstractController
{
    private $breadcrumbs;
    
    public function __construct()
    {
        $this->breadcrumbs = [
            [
                'route' => 'blog_index',
                'title' => 'Blog'
            ]
        ];
    }
    
    /**
     * @Route(
     *      "/", 
     *      methods={"GET"}, 
     *      name="blog_index"
     * )
     */
    public function index(\App\Repository\PostRepository $posts) : \Symfony\Component\HttpFoundation\Response
    {
        $latestPosts = $posts->findLatest();

        return $this->render('blog/index.html.twig', [
            'posts' => $latestPosts,
            'breadcrumbs' => $this->breadcrumbs
        ]);
    }
    
    /**
     * @Route(
     *      "/posts", 
     *      methods={"GET"},
     *      name="blog_list"
     * )
     * @Route(
     *      "/posts/page{page}", 
     *      methods={"GET"},
     *      requirements={"page"="\d+"},
     *      defaults={"page": "1"},
     *      name="blog_list_paged"
     * )
     */
    public function list(\App\Repository\PostRepository $posts, int $page = 1) : \Symfony\Component\HttpFoundation\Response
    {
        $postsPage = $posts->getPage($page);
        
        if ($postsPage->getIterator()->count() < 1) {
            throw $this->createNotFoundException("Page not found");
        }
        
        return $this->render('blog/list.html.twig', [
            'posts' => $postsPage,
            'breadcrumbs' => $this->breadcrumbs
        ]);
    }
    
    /**
     * @Route(
     *      "/post/{slug}", 
     *      methods={"GET"}, 
     *      name="blog_detail"
     * )
     */
    public function detail(\App\Entity\Post $post) : \Symfony\Component\HttpFoundation\Response
    {
        $this->breadcrumbs[] = [
            'route' => 'blog_detail',
            'title' => $post->getTitle()
        ];
        
        return $this->render('blog/detail.html.twig', [
            'post' => $post,
            'breadcrumbs' => $this->breadcrumbs
        ]);
    }
}
