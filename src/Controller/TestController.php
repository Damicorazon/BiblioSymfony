<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use stdClass;

class TestController extends AbstractController
{
    /**
     * Une route est l'équivalent d'une nouvelle page web dans notre projet.
     * Avec le composant Annotations, on peut faire correspondre une URL avec une méthode d'un controleur.
     * Cette méthode va afficher la page souhaitée
     * Cette fonction Route() prend au moins un paramètre : l'URL qui va être dans la barre d'adresse du navigateur
     * 
     * Une méthode d'un controleur qui est lié à une route doit retourner un objet de la classe Response
     * 
     * @Route("/test", name="test")
     */
    public function index(): Response
    {

        // return $this->json([
        //     'message' => 'Welcome to your new controller!',
        //     'path' => 'src/Controller/TestController.php',
        //]);

        /* La méthode render permet d'afficher le contenu d'un fichier template.
         - Le 1er paramètre est le nom du template (le nom du fichier est donné à partir du dossier 'templates')
         - Le 2eme parametre est un array dont les indices seront les noms des variables envoyées au template
        */
        return $this->render("base.html.twig");
        
    }

    /**
     * @Route("/cours/salutations", name="test_salutation")
     */
    public function salutations()
    {
        return $this->render("test/salutations.html.twig");
    }

    /**
     * Route paramétrée : {a} veut dire que cette partie de l'URL pouura prendre n'importe quelle valeur. Pour pouvoir utiliser cette valeur, on doit l'indiquer comme paramètre de la méthode calcul()
     * @Route("/test/calcul/{a}/{b?}", name="test_calcul")
     */
    public function calcul($a, $b = 1)
    {
        //$a = 10;
        //$b = 2;
        // EXO : afficher aussi le résultat de a + b, a / b et a - b
        return $this->render("test/calcul.html.twig", [ "a" => $a, "b" => $b ]);
    }

    /**
     * 
     * @Route("/cours/affichage/tableau", name="test_affichage")
     */
    public function affichage(){
        $tableau = ["nom" => "Menfin", "prenom" => "gerard", "age" => 30, "ville" => "Paris"];
        $tableau2 = ["Bonjour", 5, true, 46, false];

        // echo $tableau["prenom"];
        dump($tableau);
        dump($tableau2);
        // dd($tableau); dump and die
        return $this->render("test/affichage.html.twig", [
            "tab" => $tableau,
            "tab2" => $tableau2
            ]);
    }

    /**
     * 
     * @Route("/cours/affichage/objet", name="test_affichage_objet")
     */
    public function affichageObjet(){
        $obj = new stdClass;
        $obj->nom = "Cérien";
        $obj->prenom = "Jean";
        $obj->age = "16";
        $obj->ville = "Marseille";
        dump($obj);
        return $this->render("test/affichage.html.twig", [ "tab" => $obj]);
        
    }

}
