<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $content = 
            '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. '
            . 'Maecenas rhoncus efficitur urna, sed tempor lorem ultricies '
            . 'sit amet. Cras libero metus, bibendum eu finibus at, '
            . 'imperdiet non lorem. Fusce iaculis ultrices velit id '
            . 'sodales. Vivamus feugiat rhoncus erat, et venenatis enim '
            . 'ullamcorper ut. Integer quis facilisis ipsum, et egestas '
            . 'nisi.</p>'
            . '<h2>Was it fish?</h2>'
            . '<p>Aenean nisi mauris, varius non ante eu, gravida '
            . 'lobortis tortor. Integer ornare egestas sem, id posuere '
            . 'dolor mollis eu. Donec fringilla lacus nunc, eu dapibus '
            . 'diam condimentum eget. Sed consectetur elementum sem, at '
            . 'consectetur enim luctus in. Suspendisse convallis facilisis '
            . 'rutrum.</p>';
        
        $post = new \App\Entity\Post();
        $post
            ->setContent($content)
            ->setTitle('Foo and fish')
            ->setSlug('foo-and-fish');
        $manager->persist($post);
        
        $post = new \App\Entity\Post();
        $post
            ->setContent($content)
            ->setTitle('Bar and chocolate')
            ->setSlug('bar-and-chocolate');
        $manager->persist($post);
        
        $post = new \App\Entity\Post();
        $post
            ->setContent($content)
            ->setTitle('Experienced user')
            ->setSlug('experienced-user');
        $manager->persist($post);
        
        $post = new \App\Entity\Post();
        $post
            ->setContent($content)
            ->setTitle('Hello world')
            ->setSlug('hello-world');
        $manager->persist($post);
        
        $post = new \App\Entity\Post();
        $post
            ->setContent($content)
            ->setTitle('Excuse me')
            ->setSlug('excuse-me');
        $manager->persist($post);
        
        for ($i = 0; $i < 17; $i++) {
            sleep(2);
            
            $post = new \App\Entity\Post();
            
            $post
                ->setContent($content)
                ->setTitle('Post №' . $i)
                ->setSlug('post-number-' . $i);
            
            $manager->persist($post);
        }

        $manager->flush();
    }
}
