<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LivreController extends AbstractController
{
    #[Route('/livre', name: 'app_livre')]
    public function index(LivreRepository $livreRepo): Response
    {
        $livres = $livreRepo->findAll();
        // dd($livres);



        return $this->render('livre/index.html.twig', [
            'controller_name' => 'LivreController',
            'livres' => $livres
        ]);
    }

    // Ici une route pour ajouter des livres à notre table
    #[Route('/livre/ajouter', name: 'app_ajouter')]
    // On va utiliser ici dans la méthode, la notion d'utilisation de notre outil Request, c'est ce qu'on appelle une "injection de dépendance" (c'est à dire à amène cette fonctionnalité dans la méthode)

    public function ajouter(Request $request, EntityManagerInterface $entityManager)

    {
        // dd($request);
        // Normalement lorsque je traite un form en PHP, je le manipule avec la superglobale $_POST
        // On remarque grâce à la barre de debug ainsi qu'au dd()  (dump and die), que $_POST récupère bien les infos du formulaire, par contre en Symfony on va préférer utiliser l'outil "Request"
        // Cet outil englobe nombreuses informations en rapport avec une requête de l'utilisateur qu'elle soit GET ou POST 
        // dd($_POST);
        if ($request->isMethod('POST')) {
            // dd($request);
            // A l'intérieur de l'objet Request qui inclue toutes les informations de requête utilisateur, j'ai accès à l'indice request qui représente POST (et l'indice query quant à lui représente GET)
            //Lorsque j'ai des objets de type "Bag" je peux toujours appeler la méthode get() en spécifiant entre parenthèses le param que je souhaite récupérer
            $titre = $request->request->get('titre');
            $auteur = $request->request->get('auteur');
            // dd($titre, $auteur);
            // Je n'utilise plus le MySQL à la main, grâce à doctrine tout est géré au travers des objets !
            // Je dois donc instancier un objet de type Livre pour lui donner les valeurs que je viens de récupérer 
            // Par la suite je pourrais intéragir avec la bdd 

            $livre = new Livre();
            $livre->setTitre($titre);
            $livre->setAuteur($auteur);

            // dd($livre);

            // On doit maintenant gérer l'insertion de cette entité dans notre table Livre
            // Pour cela on utilise le service EntityManager qui est en charge de gérer les insertions de nos entités vers la BDD 
            // A cette étape là, l'objet Livre représente un enregistrement de la BDD, c'est l'EntityManager qui va le valider

            // La méthode persist permet de préparer une requête INSERT INTO à partir d'un objet entité 

            $entityManager->persist($livre);
            // La méthode "flush" exécute toutes les requêtes SQL préparées par persist et c'est fini pour l'instant
            $entityManager->flush();
            // la redirection: on redirige l'utilisateur vers la page d'accueil
            // return $this->redirectToRoute('app_livre');

        }

        return $this->render('livre/formulaire.html.twig', []);
    }
}
