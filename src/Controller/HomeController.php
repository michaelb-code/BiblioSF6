<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(LivreRepository $livreRepo): Response
    {
        $livres = $livreRepo->findAll();
        // dd($livres);
        
        // EXERCICE : 
            // 1 - Cette route doit se lancer quand on est à la racine du projet (c'est à dire, actuellement on doit mettre comme url http://127.0.0.1:8000/home   j'aimerai qu'elle soit atteinte sur http://127.0.0.1:8000/ (c'est à dire la racine du nom de domaine si j'étais en ligne))

            // 2 - Affichez la liste des livres sous forme de vignette en appelant le fichier _vignette.html.twig
            

        return $this->render('home/index.html.twig', [
            'livres' => $livres,
        ]);
    }
}
