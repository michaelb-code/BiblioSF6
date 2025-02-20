<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Form\LivreType;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// pour bloquer toutes les routes pour ceux qui ne sont pas admin
// La ligne ci dessous me permet de limiter l'accès AU CONTROLLER ENTIER ET TOUTES SES ROUTES, au ROLE_ADMIN
// #[IsGranted("ROLE_ADMIN")]


//dans le package securité.yaml
// on a decommenté l'acces control et placé #[Route('/admin')] pour donner laccés qu'a l'admin
#[Route('/admin')]

final class LivreController extends AbstractController
{
    #[Route('/livre', name: 'app_livre')]
    // #[IsGranted("ROLE_ADMIN")]
    // Ici je rajoute la classe Repository de Livre qui me permet de lancer les requêtes de selection
    // En gros lorsque j'ai un SELECT à lancer sur une table, je le fais toujours au travers de son repository
    // (Rappel : Il se crée automatiquement en même temps que l'entité elle même)
    public function index(LivreRepository $livreRepo): Response
    {

        // Il existe plusieurs méthodes par défaut dans chaque repository
        // findAll() équivalent à SELECT * FROM table (je récupère tous les enregistrements sans conditions)
        // find($id) permet de récupérer un seul enregistrement en le cherchant avec son id
        // findOneBy() récupère un enregistrement en fonction de conditions transmises dans les params
        // findBy() récupère plusieurs enregistrements en fonction de conditions transmises dans les params 

        // findAll() ici, en une seule action me permet de récupérer un array à plusieurs niveau contenant la totalité des livres (comme la dernière étape de pdo->prepare->execute->fetchAll())
        $livres = $livreRepo->findAll();
        // dd($livres);

        // Je transmet ensuite $livres à mon template ce qui me permet de l'afficher 
        return $this->render('livre/index.html.twig', [
            'livres' => $livres,
        ]);
    }

    // Ici une route pour ajouter des livres à notre table
    #[Route('/livre/ajouter', name: 'app_ajouter')]
    // On va utiliser ici dans la méthode, la notion d'utilisation de notre outil Request, c'est ce qu'on appelle une "injection de dépendance" (c'est à dire à amène cette fonctionnalité dans la méthode)
    public function ajouter(Request $request, EntityManagerInterface $em)
    {

        // dd($request);
        // Normalement lorsque je traite un form en PHP, je le manipule avec la superglobale $_POST
        // On remarque grâce à la barre de debug ainsi qu'au dd()  (dump and die), que $_POST récupère bien les infos du formulaire, par contre en Symfony on va préférer utiliser l'outil "Request"
        // Cet outil englobe nombreuses informations en rapport avec une requête de l'utilisateur qu'elle soit GET ou POST 
        // dd($_POST);

        if ($request->isMethod("POST")) {
            // dd($request);
            // A l'intérieur de l'objet Request qui inclue toutes les informations de requête utilisateur, j'ai accès à l'indice request qui représente POST (et l'indice query quant à lui représente GET)
            //Lorsque j'ai des objets de type "Bag" je peux toujours appeler la méthode get() en spécifiant entre parenthèses le param que je souhaite récupérer
            $titre = $request->request->get("titre");
            $auteur = $request->request->get("auteur");

            // dd($titre, $auteur);

            // Je n'utilise plus le MySQL à la main, grâce à doctrine tout est géré au travers des objets !
            // Je dois donc instancier un objet de type Livre pour lui donner les valeurs que je viens de récupérer 
            // Par la suite je pourrais intéragir avec la bdd 
            $livre = new Livre;
            $livre->setTitre($titre);
            $livre->setAuteur($auteur);

            // dd($livre);

            // On doit maintenant gérer l'insertion de cette entité dans notre table Livre
            // Pour cela on utilise le service EntityManager qui est en charge de gérer les insertions de nos entités vers la BDD 
            // A cette étape là, l'objet Livre représente un enregistrement de la BDD, c'est l'EntityManager qui va le valider

            // La méthode persist permet de préparer une requête INSERT INTO à partir d'un objet entité 
            $em->persist($livre);
            // La méthode "flush" exécute toutes les requêtes SQL préparées par persist et c'est fini 
            $em->flush();
            // dd($livre);
            return $this->redirectToRoute("app_livre");
        }
        return $this->render("livre/formulaire.html.twig");
    }

    #[Route('/livre/nouveau', name: 'app_nouveau')]
    public function nouveau(Request $request, EntityManagerInterface $em)
    {
        $livre = new Livre;

        // Ici la méthode createForm (méthode héritée des controllers symfony), me permet de générer un formulaire que j'ai défini via ma commande symfony console make:form
        // Ici, je crée un formulaire de type : LivreType
        // Egalement, symfony me permet de lier directement une entité à ce formulaire ! 
        // Le new Livre que j'ai créé ci dessus, est donc, directement rattaché aux champs du formulaire 
        // Quand je vais saisir le titre et l'auteur, ces informations là (si valide), seront directement insérées dans les propriétés de l'objet livre, je n'aurai pas besoin de manipuler les setLivre, setAuteur contrairement à la méthode précédente 'ajouter'
        $formLivre = $this->createForm(LivreType::class, $livre);
        // A partir de cette ligne $formLivre "contient" un formulaire entier qui ne me reste plus qu'à envoyer au render

        // handleRequest : Permet à la variable formLivre de gérer les informations envoyées par le navigateur
        // Cela va permettre de vérifier si l'information est bien postée via le formulaire
        $formLivre->handleRequest($request);

        if ($formLivre->isSubmitted() && $formLivre->isvalid()) {
            $em->persist($livre);
            $em->flush();
            return $this->redirectToRoute("app_livre");
        }
        // dump($livre);
        $this->addFlash("success", "Le livre a bien été enregistré");
        return $this->render("livre/form.html.twig", ["formLivre" => $formLivre]);
    }

    // EXO : Mettre en place la route de modification des livres 
    // Etapes : 
    // 1 - Créer la route de modification qui prends en param un id d'un livre 
    // 2 - Mettre en place la methode modifier, ne pas oublier toutes les injections de dépendances...
    // Une dépendance qui gère les requêtes action
    // Une dépendance qui gère les informations reçues du formulaire
    // Une dépendance qui gère les requêtes de selection sur la table 
    // 3 - Dans cette méthode, récupérer les informations (le livre) qui correspond à cet id
    // 4 - Rattacher "ce livre" au formulaire auto généré (LivreType)
    // 5 - Créer la condition de modification ou pas 
    // 6 - flush -> redirection  
    // "Indice" : Pas besoin de nouveau template twig

    #[Route('/livre/modifier/{id}', name: 'app_modifier')]
    public function modifier(EntityManagerInterface $em, Request $request, LivreRepository $livreRepo, $id)
    // public function modifier(EntityManagerInterface $em, Request $request, Livre $livre)

    {
        // Ici je me sers de l'id récupéré en param de la route pour lancer une requête sur le repository
        // Cette requête me permet de récupérer le livre concerné par cet id
        $livre = $livreRepo->find($id);

        // Lorsque je fais la liaison entre le form et l'entité Livre, cette fois, on ne parle pas d'une entité vierge comme la route nouveau mais d'une entité existante, donc les valeurs contenues dans les props titre et auteur sont automatiquement répercutée dans les inputs du form

        // Ici dans les params de createForm on rajoute une option "edit_mode", on l'a aussi rajoutée dans les configureOptions de notre LivreType
        // Cette variable nous sert de "repère", couplée à une condition dans le label du SubmitType du form, elle nous permet d'afficher soit un bouton s'appellant enregistrer soit un bouton s'appellant Modifier 
        $formLivre = $this->createForm(LivreType::class, $livre, ["edit_mode" => true]);

        $formLivre->handleRequest($request);

        if ($formLivre->isSubmitted() && $formLivre->isvalid()) { 
            // Le reste du fonctionnement ne diffère pas du tout de la route nouveau, on peut par contre se passer du persist car l'entité existe déjà dans la table, il suffit de faire un flush 
            // $em->persist($livre);
            $em->flush();
            
            $this->addFlash("success", "Le livre a bien été modifié");
            return $this->redirectToRoute("app_livre");
        }


        return $this->render("livre/form.html.twig", ["formLivre" => $formLivre]);

        // dd($livre);
    }

    #[Route('/livre/supprimer/{id}', name: 'app_supprimer')]
    public function supprimer(EntityManagerInterface $em, Request $request, Livre $livre) {
        // Ici je profite du concept d'auto wiring de Symfony
        // Il va voir le paramètre s'appellant id dans la route, il va aussi comprendre que j'injecte dans les param de la méthode une entité Livre, il va faire le rapprochement entre les deux et récupérer directement le Livre possédant cet id ! 
        // Cette fonctionnalité m'évite de faire la requête sur le repository pour récupérer les informations du livre qui possède cet id !
        // Grâce à ça, j'économise quelques lignes de code :) 


        if($request->isMethod("POST")) {
            $em->remove($livre);
            $em->flush();
            // La méthode addFlash me permet de gérer des "messages flash" ce sont des messages généralement de notification à l'utilisateur du succès ou de l'échec d'une opération
            // Une fois les messages affichés ils sont automatiquement supprimé dès le prochain chargement de page
            // Ici je transmet un message flash ayant pour type "success"  (je le fais correspondre volontairement au code couleur bootstrap success=vert) et le message "Le livre a bien été supprimé"
            // Ensuite je dois gérer l'affichage dans base.html
            $this->addFlash("success", "Le livre a bien été supprimé");
            return $this->redirectToRoute("app_livre");
        }

        // Par défaut, j'envoie sur ce nouveau template "supprimer", sur lequel se trouve un form ayant pour seul champ un bouton submit indiquant "confirmation de suppression", ainsi, si le controller comprend une methode POST, c'est à dire que l'utilisateur confirme la suppression (je tomberai ainsi dans le if ci dessus et je "remove" l'enregistrement de la table)
        return $this->render("livre/supprimer.html.twig", ["livre" => $livre]);
        // Avec le code des modales, je n'atteint jamais le render, je passe uniquement dans la redirection
    }

}