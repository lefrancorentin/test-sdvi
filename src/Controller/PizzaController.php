<?php

declare(strict_types = 1);

namespace App\Controller;

use App\Entity\IngredientPizza;
use App\Service\Dao\PizzaDao;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PizzaController
 * @package App\Controller
 */
class PizzaController extends AbstractController
{
    /**
     * @param PizzaDao $pizzaDao
     * @Route("/pizzas")
     * @return Response
     */
    public function listeAction(PizzaDao $pizzaDao): Response
    {
        // récupération des différentes pizzas
        $pizzas = $pizzaDao->getAllPizzas();

        return $this->render("Pizza/liste.html.twig", [
            "pizzas" => $pizzas,
        ]);
    }

    /**
     * @param int $pizzaId
     * @Route(
     *     "/pizzas/detail-{pizzaId}",
     *     requirements={"pizzaId": "\d+"}
     * )
     * @return Response
     */
    public function detailAction(PizzaDao $pizzaDao, int $pizzaId): Response
    {

        $pizza = $pizzaDao->getDetailPizza($pizzaId);
        $qtes = $pizza->getQuantiteIngredients();

        $ingredients = [];
        $cout_fab = 0;

//        recupere les couts individuels des ingredients pour les additionner
        foreach ($qtes as $qte ){
            $ingredient = $qte->getIngredient();
            $ingredients[] = $ingredient->getNom();
            $cout_fab += $ingredient->getCout() * IngredientPizza::convertirGrammeEnKilo($qte->getQuantite());
        }
        $cout_fab = round($cout_fab, 2);

        return $this->render("Pizza/detail.html.twig", [
            "nom"           => $pizza->getNom(),
            "cout"          => $cout_fab,
            "ingredients"   => implode(', ', $ingredients)
        ]);
    }
}
