<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class   AppFixtures extends Fixture
{

    /**
     * @var UserPasswordEncoderInterface $passwordEncoder
     */
    private $passwordEncoder;

    /**
     * @var \Faker\Factory
     */
    private $faker;

    private const USERS = [
        [
            'username' => 'admin',
            'email' => 'admin@blog.com',
            'name' => 'Admin Teste',
            'password' => 'secret123#'
        ],
        [
            'username' => 'teste1',
            'email' => 'teste1@blog.com',
            'name' => 'Teste2',
            'password' => 'secret123#'
        ],
        [
            'username' => 'teste2',
            'email' => 'teste2@blog.com',
            'name' => 'Teste2',
            'password' => 'secret123#'
        ],
        [
            'username' => 'teste3',
            'email' => 'teste3@blog.com',
            'name' => 'Teste3',
            'password' => 'secret123#'
        ],
    ];

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
        for ($i = 0; $i < 100; $i++) {
            $blog = new BlogPost();
            $blog->setTitle($this->faker->realText(30));
            $blog->setPublished($this->faker->dateTimeThisYear);
            $blog->setContent($this->faker->realText());

            $authorReference = $this->getRandomUserReference();


            $blog->setAuthor($authorReference);
            $blog->setSlug($this->faker->slug);
            $this->setReference("blog_post_$i", $blog);

            $manager->persist($blog);
        }

        $manager->flush();
    }

    public function loadComments(ObjectManager $manager)
    {
        for ($i = 0; $i < 100; $i++) {
            for ($j = 0; $j < rand(1, 10); $j++) {
                $comment = new Comment();
                $comment->setContent($this->faker->realText());
                $comment->setPublished($this->faker->dateTimeThisYear);

                $authorReference = $this->getRandomUserReference();

                $comment->setAuthor($authorReference);
                $comment->setBlogPost($this->getReference("blog_post_$i"));

                $manager->persist($comment);
            }
        }

        $manager->flush();

    }

    public function loadUsers(ObjectManager $manager)
    {
        foreach (self::USERS as $userFixture){
            $user = new User();
            $user->setUsername($userFixture['username']);
            $user->setEmail($userFixture['email']);
            $user->setName($userFixture['email']);
            $user->setPassword(
                $this->passwordEncoder->encodePassword($user, $userFixture['password'])
            );

            $this->addReference('user_'.$userFixture['username'], $user);
            $manager->persist($user);

        }
        $manager->flush();
    }

    public function getRandomUserReference(): User
    {
        return $this->getReference('user_' . self::USERS[rand(0, 3)]['username']);
    }

}
