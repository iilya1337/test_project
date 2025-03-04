<?php

namespace App\DataFixtures;

use App\Entity\Blog;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{

    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('admin@gmail.com');
        $user->setRoles(['ROLE_ADMIN']);
        $password = $this->hasher->hashPassword($user, 'admin@gmail.com');
        $user->setPassword($password);
        $manager->persist($user);

        $users = [];
        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setEmail('user' . $i . '@gmail.com');
            $password = $this->hasher->hashPassword($user, 'pass_1234');
            $user->setPassword($password);
            $manager->persist($user);

            $users[] = $user;
        }

        $tags = [];
        for ($i = 0; $i < 20; $i++) {
            $tag = new Tag();
            $tag->setName('Tags ' . $i);
            $manager->persist($tag);

            $tags[] = $tag;
        }

        for ($i = 0; $i < 1000; $i++) {

            $blog = new Blog($users[random_int(0, 19)]);
            $blog->setTitle('Blog ' . $i);
            $blog->setDescription('Description ' . $i);
            $blog->setText('Text ' . $i);
            $blog->setTags(new ArrayCollection([$tags[random_int(0, 19)]]));
            $manager->persist($blog);

        }

        $manager->flush();
    }
}
