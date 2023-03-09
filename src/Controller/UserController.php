<?php

namespace App\Controller;
  
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

#[Route('/api', name: 'api_')]
class UserController extends AbstractController
{
    
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $em = $doctrine->getManager();

        $user = new User();

        $userExists = $doctrine->getRepository(User::class)->findOneBy(['email' => $request->get('email')]);

        if ($userExists) {
            return $this->json(['message' => 'User already exists']);
        }

        else{
            $username = $request->get('username');
            $email = $request->get('email');
            $plaintextPassword = $request->get('password');
            
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );
            $user->setPassword($hashedPassword);
            $user->setEmail($email);
            $user->setUsername($username);

        }
  
        $em->persist($user);
        $em->flush();
  
        return $this->json(['message' => 'Registered Successfully']);
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordEncoder): Response
    {
        $email = $request->get('email');
        $plainPassword = $request->get('password');

        $user = $doctrine->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            return $this->json("L'utilisateur n'existe pas" . $email, 404);
        }

        if (!$passwordEncoder->isPasswordValid($user, $plainPassword)) {
            return $this->json('Password invalide', 404);
        }

        return $this->json('Vous êtes connecté');
    }
}


