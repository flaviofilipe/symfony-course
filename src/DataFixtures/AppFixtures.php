<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    /**
     * @var UserPasswordEncoderInterface $passwordEncoder
     */
    private $passwordEncoder;

    /**
     * @var \Faker\Factory
     */
    private $faker;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = \Faker\Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);
        $this->loadComments($manager);
    }

    public function loadBlogPosts(ObjectManager $manager)
    {
        $user = $this->getReference('user_admin');

        for ($i = 0; $i < 100; $i++) {
            $blog = new BlogPost();
            $blog->setTitle($this->faker->realText(30));
            $blog->setPublished($this->faker->dateTimeThisYear);
            $blog->setContent($this->faker->realText());
            $blog->setAuthor($user);
            $blog->setSlug($this->faker->slug);
            $this->setReference("blog_post_$i", $blog);

            $manager->persist($blog);
        }

        $manager->flush();
    }
    public function loadComments(ObjectManager $manager)
    {
        for ($i=0; $i < 100; $i++) { 
            for ($j=0; $j < rand(1, 10); $j++) { 
                $comment = new Comment();
                $comment->setContent($this->faker->realText());
                $comment->setPublished($this->faker->dateTimeThisYear);
                $comment->setAuthor($this->getReference('user_admin'));
                $comment->setBlogPost($this->getReference("blog_post_$i"));

                $manager->persist($comment);
            }
        }

        $manager->flush();

    }

    public function loadUsers(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setEmail('admin@blog.com');
        $user->setName('Fx2');
        $user->setPassword(
            $this->passwordEncoder->encodePassword($user, '123456')
        );

        $this->addReference('user_admin', $user);

        $manager->persist($user);
        $manager->flush();
    }
}
