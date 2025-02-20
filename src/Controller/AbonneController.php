<?php

namespace App\Controller;

use App\Entity\Abonne;
use App\Form\AbonneType;
use App\Repository\AbonneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// EXERCICE 
// Faire fonctionner le crud automatique Abonne
// Bloquer l'accès à ce controller uniquement pour les admin et biblio
                // Vous avez dans le security.yaml les access control, en rajoutant ici /admin devant chaque route 
// Enlever la lisibilité du password dans l'index et le show 
                // Supprimer la colonne et la cellule qui correspond à chaque tour de boucle
// Corriger les erreurs m'empechant de faire un edit et un new abonné (réglage du form)
                // Erreur en rapport avec le roles qui est un champ de type array, donc il faut le régler en mettant un ChoiceType dans le form, réglé en multiple => true et expanded => true
                // Si on reste tel quel, les nouvelles insertions et modifications d'abonnés n'auront pas le password hashé !!!! 
                // Dans le AbonneFormType Il faut mapped false le password (comme le registrationformtype)
                 // Ensuite, dans le controller à la route new et edit, il faut récupérer ce password clair pour le hasher et le set dans l'entity avec de la persist/flush
// Plutôt que d'afficher ["ROLE_USER", "ROLE_ADMIN"], on aimerait plutôt afficher des mots simples
    // ROLE_ADMIN => Directeur
    // ROLE_BIBLIO => Bibliothécaire
    // ROLE_USER / ROLE_LECTEUR => Lecteur 
    // Une boucle à gérer sur les roles user pour afficher un mot à la condition de chacun des roles

#[IsGranted("ROLE_ADMIN")]


#[Route('/admin/abonne')]
final class AbonneController extends AbstractController
{
    #[Route(name: 'app_abonne_index', methods: ['GET'])]
    public function index(AbonneRepository $abonneRepository): Response
    {
        
        return $this->render('abonne/index.html.twig', [
            'abonnes' => $abonneRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_abonne_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $abonnePasswordHasher): Response
    {
        $abonne = new Abonne();
        $form = $this->createForm(AbonneType::class, $abonne );
        $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()) {

            $plainPassword = $form->get('password')->getData();

            
            $abonne->setPassword($abonnePasswordHasher->hashPassword($abonne, $plainPassword));

            $entityManager->persist($abonne);
            $entityManager->flush();


            return $this->redirectToRoute('app_abonne_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('abonne/new.html.twig', [
            'abonne' => $abonne,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_abonne_show', methods: ['GET'])]
    public function show(Abonne $abonne): Response
    {
        return $this->render('abonne/show.html.twig', [
            'abonne' => $abonne,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_abonne_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Abonne $abonne, EntityManagerInterface $entityManager, UserPasswordHasherInterface $abonnePasswordHasher): Response
    {
        $form = $this->createForm(AbonneType::class, $abonne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();

            // encode the plain password
            $abonne->setPassword($abonnePasswordHasher->hashPassword($abonne, $plainPassword));

            // $user->setRoles(['ROLE_ADMIN']);

            $entityManager->persist($abonne);
            $entityManager->flush();

            return $this->redirectToRoute('app_abonne_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('abonne/edit.html.twig', [
            'abonne' => $abonne,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_abonne_delete', methods: ['POST'])]
    public function delete(Request $request, Abonne $abonne, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $abonne->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($abonne);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_abonne_index', [], Response::HTTP_SEE_OTHER);
    }
}
