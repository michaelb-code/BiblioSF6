<?php

namespace App\Controller; // Le namespace du controller, App correspond au dossier src (norme PS4 de l'autoload)


// Les use sont des classes utilisées par le controller, on en rajoutera d'autres petit à petit
// Généralement ces classes sont dans "vendor"
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    // Ici le #Route etc, c'est ce qu'on appelle une annotation PHP, c'est un commentaire particulier et est une nouveauté de php8 avec symfony6, le premier param correspond à l'url qui permettra d'accèder à l'exécution del a méthode qui suit, le second param le "name" correspond au nom donné à cette route lorsque l'on créera des liens 

    // Ci dessous, la méthode qui suit directement la déclaration de l'annotation est lancée auotmatiquement lorsque l'on accède à cette url  (/test)
    public function index(): Response
    {

        // Cette méthode lance la méthode render qui permet de générer un affichage à l'utilisateur, ici elle appelle le template test/index.html.twig et transmet une variable "controller_name" valant "TestController"
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    #[Route('/test/accueil', name: 'app_accueil')]
    public function accueil()
    {

        // Je récupère ici des données en brut, des variables que je déclare
        // Je pourrais ensuite les transmettre à mon template et les afficher si je le souhaite 
        // Le but plus tard sera évidemment de récupérer plutôt des données venant de la BDD

        $nombre = 45;
        $prenom = "mika";

        return $this->render("base.html.twig", [
            "nombre" => $nombre, 
            "prenom" => $prenom
        ]);
    }

    // On créé une nouvelle route pour montrer l'héritage des modèles twig 
    #[Route('/test/heritage', name: "app_heritage")]
    public function heritage()
    {
        return $this->render("test/heritage.html.twig");
    }

    // Ici nouvelle route pour montrer que l'héritage des templates est "transitif" c'est à dire que si B hérite de A et que C hérite de B, alors au final, C possèdera à la fois les éléments de B et de A (et on pourra toujours rédéfinir les blocs de notre choix)
    #[Route('/test/transitif', name: "app_transitif")]
    public function transitif()
    {
        return $this->render("test/transitif.html.twig");
    }

    // Route pour les tests tableaux array en twig 
    #[Route('/test/tableau', name: "app_tableau")]
    public function tableau()
    {
        $tab = ["jour" => "06", "mois" => "fevrier", "annee" => 2025];


        return $this->render("test/tableau.html.twig",
    [
        "tableau" => $tab,
        "tableau2" => [40, "test", true],
        "nombre" => 5, 
        "chaine" => ""
    ]);
    }
// le "?" permet de rendre l'argument facultatif
    #[Route('/test/salutation/{prenom?}', name: "app_salutation")]
    public function salutation($prenom)
    {
        return $this->render("test/salutation.html.twig", ["prenom" => $prenom]);
        
        // EXO : Créez la vue et affichez dans la balise h1, Bonjour prenom (qui sera le prenom saisi dans l'url)
    }
      // EXERCICE : 
    // Créez une nouvelle route qui va prendre 2 paramètres dans l'url et qui va afficher la valeur de l'addition, la multiplication, la soustraction et la division des deux nombres passés en paramètres 
    // Il faudra gérer le fait de ne pas pouvoir faire une division par 0
    #[Route('/test/calcul/{chiffre1}/{chiffre2}', name: "app_calcul")]
    public function calcul($chiffre1, $chiffre2)
    {
        $division = $chiffre2 != 0 ? $chiffre1 / $chiffre2 : "Erreur";
        return $this->render("test/calcul.html.twig", [
            "chiffre1" => $chiffre1,
            "chiffre2" => $chiffre2,
            "addition" => $chiffre1 + $chiffre2,
            "multiplication" => $chiffre1 * $chiffre2,
            "soustraction" => $chiffre1 - $chiffre2,
            // "division" => $chiffre1 / $chiffre2,

            "division" => $division
        ]);
    }
}
