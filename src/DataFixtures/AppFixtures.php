<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Color;
use App\Entity\Price;
use App\Entity\Reference;
use App\Entity\Size;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create();

        $colors = ["black", "white", "red", "green", "blue", "yellow"];
        $dataColors = [];
        for ($i = 0; $i < count($colors); $i++) {
            $color = new Color();
            $color->setName($colors[$i]);
            $manager->persist($color);
            $dataColors[] = $color;
        }

        $sizes = ["XS", "S", "M", "L", "XL", "XXL"];
        $dataSizes = [];
        foreach ($sizes as $s) {
            $size = new Size();
            $size->setName($s);
            $manager->persist($size);
            $dataSizes[] = $size;
        }

        $prices = [29, 39, 49];
        $dataPrices = [];
        foreach ($prices as $p) {
            $price = new Price();
            $price->setAmount($p);
            $manager->persist($price);
            $dataPrices[] = $price;
        }

        $refs = ["Dahu", "Seoul", "Auburn"];
        $images = [
            'https://thumbs.dreamstime.com/b/la-mode-v%C3%AAtx-l-illustration-bleue-de-forme-de-t-shirt-8229384.jpg',
            'https://img.myloview.fr/images/illustration-unique-de-vecteur-de-dessin-anime-t-shirt-bleu-700-145918035.jpg',
            'https://previews.123rf.com/images/siberica/siberica1601/siberica160100173/51442205-t-shirt-croquis-homme-isol%C3%A9-sur-fond-blanc-vector-illustration-.jpg'
        ];
        $description = "Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptatum asperiores veniam iure unde voluptatibus impedit adipisci itaque, sapiente molestiae. Distinctio corporis a quod beatae fuga? Eaque excepturi ducimus magnam magni.";

        $dataRefs = [];
        for ($i = 0; $i < count($refs); $i++) {
            $ref = new Reference();
            $ref->setTitle($refs[$i])
            ->setSlug(strtolower($refs[$i]))
            ->setImage($images[$i])
            //->setDescription($description)
            ->setDescription($faker->text(200))
            ->setPrice($faker->randomElement($dataPrices));

            $manager->persist($ref);
            $dataRefs[] = $ref;
        }

        for ($i =0; $i < 20; $i++) {
            $article = new Article();
            $article->setRef($dataRefs[array_rand($dataRefs)])
            ->setColor($dataColors[array_rand($dataColors)])
            ->setSize($dataSizes[array_rand($dataSizes)])
            ->setQty(rand(0, 10));
            $manager->persist($article);
        }

        $admin = new User();
        $admin->setEmail('admin@admin.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $password = $this->hasher->hashPassword($admin, 'password');
        $admin->setPassword($password);
        $manager->persist($admin);

        for ($i = 1; $i < 6; $i++) {
            $user = new User();
            $user->setEmail('user'.$i.'@user.com');
            $user->setRoles(['ROLE_USER']);
            $password = $this->hasher->hashPassword($user, 'password');
            $user->setPassword($password);
            $manager->persist($user);
        }
        
        $manager->flush();
    }
}
