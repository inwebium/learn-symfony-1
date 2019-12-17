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
        $commentedPosts = $posts->findCommented();

        return $this->render('blog/index.html.twig', [
            'latestPosts' => $latestPosts,
            'commentedPosts' => $commentedPosts,
            'breadcrumbs' => $this->breadcrumbs
        ]);
    }
    
    /**
     * @Route(
     *      "/posts/", 
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
        $this->breadcrumbs[] = [
            'route' => 'blog_list',
            'title' => 'List'
        ];
        
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
     *      name="blog_post"
     * )
     */
    public function detail(\App\Entity\Post $post) : \Symfony\Component\HttpFoundation\Response
    {
        $this->breadcrumbs[] = [
            'route' => 'blog_list',
            'title' => 'List'
        ];
        
        $this->breadcrumbs[] = [
            'route' => 'blog_post',
            'title' => $post->getTitle()
        ];
        
        $comment = new \App\Entity\Comment();
        $commentForm = $this->createForm(\App\Form\CommentType::class, $comment);
        
        return $this->render('blog/detail.html.twig', [
            'post' => $post,
            'comments' => $post->getComments(),
            'commentForm' => $commentForm->createView(),
            'breadcrumbs' => $this->breadcrumbs
        ]);
    }
    
    /**
     * @Route(
     *      "/post/{slug}/comment/add", 
     *      methods={"POST"}, 
     *      name="blog_post_comment_add"
     * )
     */
    public function addComment(\Symfony\Component\HttpFoundation\Request $request)
    {
        $newComment = new \App\Entity\Comment();
        
        $form = $this->createForm(\App\Form\CommentType::class, $newComment);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $newComment = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($newComment);
            $entityManager->flush();
            
            return $this->json(
                $this->normalizer->normalize(
                    $newComment, 
                    null, 
                    [
                        'groups' => ['view']
                    ]
                ),
                200
            );
        } else {
            return $this->json($data, 500);
        }
    }
}
