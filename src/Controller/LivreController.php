<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Livre;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\LivreRepository;
use App\Form\LivreType;


/**
 * @Route("/admin")
 */
class LivreController extends AbstractController
{
    /**
     * @Route("/livre", name="livre")
     * 
     * Pour interroger une table de la bdd (= requete SELECT), on va utiliser la classe Repository correspondante
     * (donc pour la table 'livre', on va utiliser 'LivreRepository)
     */
    public function index(LivreRepository $livreRepository): Response
    {
        $liste_livres = $livreRepository->findAll(); // SELECT * FROM livre
        return $this->render('livre/index.html.twig', [
            'livres' => $liste_livres,
        ]);
    }
    
    /**
     * @Route("/livre/ajouter", name="livre_ajouter")
     */
    public function nouveau(Request $request, EntityManagerInterface $em){
        $livre = new Livre;
        $formLivre = $this->createForm(LivreType::class, $livre);
        $formLivre->handleRequest($request);
        if( $formLivre->isSubmitted() && $formLivre->isValid() ){
            if( $fichier = $formLivre->get("couverture")->getData() ){
                $destination = $this->getParameter("dossier_images");
                $nomFichier = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                $nouveauNom = str_replace(" ", "_", $nomFichier);
                $nouveauNom .= "_" . uniqid() . "." . $fichier->guessExtension();
                /* le fichier uploadé est enregistré dans un dossier temporaire. On va le 
                    déplacer vers le dossier images avec le nouveau nom de fichier */
                $fichier->move($destination, $nouveauNom);
                $livre->setCouverture($nouveauNom);
            }
            $em->persist($livre);
            $em->flush();
            $this->addFlash("success", "Le nouveau livre a bien été ajouté");
            return $this->redirectToRoute("livre");
        }
        return $this->render("livre/ajouter.html.twig", ["formLivre" => $formLivre->createView()]);
    }

    /**
     * @Route("/livre/modifier/{id}", name="livre_modifier")
     * 
     */
    public function maj(EntityManagerInterface $em, Request $request, LivreRepository $livreRepository, $id) {
        $livre = $livreRepository->find($id);
        $formLivre = $this->createForm(LivreType::class, $livre);
        $formLivre->handleRequest($request);
        if( $formLivre->isSubmitted() && $formLivre->isValid() ){
            if( $fichier = $formLivre->get("couverture")->getData() ){
                $destination = $this->getParameter("dossier_images");
                $nomFichier = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                $nouveauNom = str_replace(" ", "_", $nomFichier);
                $nouveauNom .= "_" . uniqid() . "." . $fichier->guessExtension();
                /* le fichier uploadé est enregistré dans un dossier temporaire. On va le 
                    déplacer vers le dossier images avec le nouveau nom de fichier */
                $fichier->move($destination, $nouveauNom);
                $livre->setCouverture($nouveauNom);
            }
            $em->persist($livre);
            $em->flush();
            $this->addFlash("success", "Le livre a bien été modifié");
            return $this->redirectToRoute("livre");
        }
        return $this->render("livre/ajouter.html.twig", ["formLivre" => $formLivre->createView()]);
    }

    /**
     * Ajout pour exo liste livre
     *  
     * @Route("/livre/fiche/{id}", name="livre_fiche")
     */
    public function fiche(LivreRepository $livreRepository, $id)
    {
    $livre = $livreRepository->find($id);
        return $this->render('livre/fiche.html.twig', [
            'livre' => $livre,
        ]);
    }



    /**
     * @Route("/admin/livre/ajouter", name="livre_ajouter_v1")
     * 
     */
    public function ajouter(Request $request, EntityManagerInterface $em){
        /*
        La classe Request a des propriétés qui correspondent à toutes les variables superglobal de PHP
        ex : $_SERVER; $_GET, $_COOKIE, $_FILES, $_POST,$_SESSION 

        Pour utiliser certaines classes (nommées des services), on va utiliser l'injection de dépendance :
        on va placer un objet de cette classe dans les parametres d'une methode, et l'objet sera instancié automatiquement
        (par ex: l'objet de la classe Request contiendra toutes les valeurs des variables superglobales)

        Pour récuperer le contenu de $_POST, on utilise la propriété 'request' de cet objet
        Pour récuperer le contenu de $_GET, on utilise la propriété 'query' de cet objet
        ---------------------------------------------------------------------------------------------------------------
        La classe EntityManagerInterface va nous servir à modifier (enregistrer, mettre à jour, supprimer) des données dans les tables de la BDD
        */

        if($request->request->has("titre")) { // has = à (en gros si request à titre)
            $titre = $request->request->get("titre");
        }
        if($request->request->has("auteur")) {
            $auteur = $request->request->get("auteur");
        }

        if (!empty($titre) && !empty($auteur)) {
            $nouveauLivre = new Livre;
            $nouveauLivre->setTitre($titre);
            $nouveauLivre->setAuteur($auteur);

            /* La méthode 'persist' prépare la requête 'insert into' et la met en attente */
            $em->persist($nouveauLivre);
            /* La méthode 'flush' exécute les requêtes en attente et donc modifie la bdd */
            $em->flush();
            /* 
            La méthode 'addFlash' permet d'enregistrer dans la session, un message à afficher. Le 1er paramètre est le type du message (par ex : succes, danger, warning....), le 2eme parametre est le message a afficher.
            */
            $this->addFlash("success", "Le nouveau livre a bien été enregistré");
            return $this->redirectToRoute('livre');
        }

        
        return $this->render('livre/formulaire.html.twig');
    }

    /**
     * @Route("/livre/modifier/{id}", name="livre_modifier_v1")
     * 
     */
    public function modifier(EntityManagerInterface $em, Request $request, LivreRepository $livreRepository, $id) {
        /* La méthode 'find' récupère le livre dont l'identifiant est passé en paramètre.
            $livreAModifier sera donc un objet de la classe Entity\Livre
        */
        $livreAModifier = $livreRepository->find($id);

        if($request->isMethod("POST")) {
            $titre = $request->request->get("titre");
            $auteur = $request->request->get("auteur");
            if(!empty($titre) && !empty($auteur)){
                $livreAModifier->setTitre($titre);
                $livreAModifier->setAuteur($auteur);

                /* 
                Tous les objets de la classe Entity qui ont un id non nulle vont prmettre à l'EntityManager d'éxécuter une requête UPDATE pour mettre à jour la bdd selon les modifications des propriétés de ces objets quand on va lancer la méthode 'flush' de l'EntityManager.
                */
                $em->flush();
                $this->addFlash("success", "Le livre n°$id a bien été modifié");
                return $this->redirectToRoute('livre');
            } 
            else{
                $this->addFlash("danger", "Le titre et/ou l'auteur ne peuvent pas être vide");
            }
        } 
        return $this->render('livre/formulaire.html.twig', ["livre" => $livreAModifier]);
    }

    /**
     * @Route("/livre/supprimer/{id}", name="livre_supprimer")
     * 
     */
    public function supprimer(EntityManagerInterface $em, Request $request, LivreRepository $livreRepository, $id) {

        $livreASupprimer = $livreRepository->find($id);
        if($request->isMethod("POST") ){
            // la méthode remove prépare la requete DELETE et la met en attente
            $em->remove($livreASupprimer);
            $em->flush();
            $this->addFlash("success", "Lelivre n°id a bien été supprimé");
            return $this->redirectToRoute('livre');
        }
        return $this->render("livre/supprimer.html.twig", ["livre" => $livreASupprimer]);
    }

}