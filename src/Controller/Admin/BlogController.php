<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/blog")
 */
class BlogController extends AbstractController
{
    /**
     * @Route(
     *      "/", 
     *      methods={"GET"}, 
     *      name="admin_blog_index"
     * )
     */
    public function index()
    {
        return $this->render('admin/blog/index.html.twig', [
            'controller_name' => 'BlogController',
        ]);
    }
    
    /**
     * @Route(
     *      "/posts/", 
     *      methods={"GET"},
     *      name="admin_blog_posts"
     * )
     * @Route(
     *      "/posts/page{page}", 
     *      methods={"GET"},
     *      requirements={"page"="\d+"},
     *      defaults={"page": "1"},
     *      name="admin_blog_posts_paged"
     * )
     */
    public function posts(\App\Repository\PostRepository $posts, int $page = 1): \Symfony\Component\HttpFoundation\Response
    {
        $postsPage = $posts->getPage($page);
        
        if ($postsPage->getIterator()->count() < 1) {
            throw $this->createNotFoundException("Page not found");
        }
        
        return $this->render('admin/blog/posts.html.twig', [
            'posts' => $postsPage
        ]);
    }
    
    /**
     * @Route(
     *      "/posts/new/", 
     *      methods={"GET", "POST"}, 
     *      name="admin_blog_post_new"
     * )
     */
    public function newPost(\Symfony\Component\HttpFoundation\Request $request)
    {
        $post = new \App\Entity\Post();
        
        $form = $this->createForm(\App\Form\PostType::class, $post, [
            'method' => 'POST'
        ]);
        
        $this->handlePostForm($request, $form, $post);
        
        if ($form->isSubmitted() && $form->getErrors()->count() == 0) {
            return $this->redirectToRoute('admin_blog_posts');
        } else {
            return $this->render('admin/blog/newPost.html.twig', [
                'form' => $form->createView(),
                'formErrors' => $form->getErrors()
            ]);
        }
    }
    
    /**
     * @Route(
     *      "/posts/edit/{slug}", 
     *      methods={"GET", "POST"}, 
     *      name="admin_blog_post_edit"
     * )
     */
    public function editPost(\Symfony\Component\HttpFoundation\Request $request, \App\Entity\Post $post)
    {
        $form = $this->createForm(\App\Form\PostType::class, $post, [
            'method' => 'POST'
        ]);
        
        $this->handlePostForm($request, $form, $post);
        
        if ($form->isSubmitted() && $form->getErrors()->count() == 0) {
            return $this->redirectToRoute('admin_blog_posts');
        } else {
            return $this->render('admin/blog/newPost.html.twig', [
                'form' => $form->createView(),
                'formErrors' => $form->getErrors()
            ]);
        }
    }
    
    private function handlePostForm(\Symfony\Component\HttpFoundation\Request &$request, \Symfony\Component\Form\Form &$form, \App\Entity\Post &$post)
    {
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
        }
    }
}
