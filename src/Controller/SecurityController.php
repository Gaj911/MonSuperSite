<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    // class AuthenticationUtils permet de d'avoir les messages d'erreurs de connexion

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // permet de récup le dernier nom d'utilisateur et de l'injecter dans le return
        $lastUsername = $authenticationUtils->getLastUsername();

        // permet de recup les message d'erreurs et de les injecter aussi dans le return
        $error = $authenticationUtils->getLastAuthenticationError();


        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }
}
