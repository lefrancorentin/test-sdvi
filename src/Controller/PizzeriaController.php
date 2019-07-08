<?php

declare(strict_types = 1);


namespace App\Controller;

use App\Entity\IngredientPizza;
use App\Service\Dao\PizzeriaDao;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PizzeriaController
 * @package App\Controller
 */
class PizzeriaController extends AbstractController
{
    /**
     * @param PizzeriaDao $pizzeriaDao
     * @Route("/pizzerias")
     * @return Response
     */
    public function listeAction(PizzeriaDao $pizzeriaDao): Response
    {
        // récupération des différentes pizzéria de l'application
        $pizzerias = $pizzeriaDao->getAllPizzerias();

        return $this->render("Pizzeria/liste.html.twig", [
            "pizzerias" => $pizzerias,
        ]);
    }

    /**
     * @param int $pizzeriaId
     * @Route(
     *     "/pizzerias/carte-{pizzeriaId}",
     *     requirements={"pizzeriaId": "\d+"}
     * )
     * @return Response
     */
    public function detailAction(PizzeriaDao $pizzeriaDao, int $pizzeriaId): Response
    {
        $pizzeria = $pizzeriaDao->getCartePizzeria($pizzeriaId);
        $marge = $pizzeria->getMarge();
        $pizzas = $pizzeria->getPizzas();

        $prix_pizzas = [];

//        pour chaque pizza de la pizzeria calcule le cout de fabrication et le multiplie par la marge
        foreach ($pizzas as $pizza) {
            $qtes = $pizza->getQuantiteIngredients();
            $cout_fab = 0;
            foreach ($qtes as $qte ){
                $ingredient = $qte->getIngredient();
                $ingredients[] = $ingredient->getNom();
                $cout_fab += $ingredient->getCout() * IngredientPizza::convertirGrammeEnKilo($qte->getQuantite());
            }
            $prix_pizzas[$pizza->getNom()] = round($cout_fab * $marge, 2);
        }

        return $this->render("Pizzeria/carte.html.twig", [
            'nom'            => $pizzeria->getNom(),
            'prix_pizzas'    => $prix_pizzas
        ]);
    }
}
