<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Nasumilu\MathML\Parser;
use InvalidArgumentException;
use Exception;
use Symfony\Component\HttpFoundation\{
    Response,
    Request
};
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

    #[Route('/', name: 'app_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('default/index.html.twig');
    }

    #[Route('/math', name: 'app_math', methods: ['POST'])]
    public function math(Request $request): Response
    {
        $error = false;
            try {
                $results = Parser::calculate($request->getContent());
            } catch (InvalidArgumentException | Exception | \ErrorException $ex) {
                $error = true;
                $results = $ex->getMessage();
            }

        return $this->render('default/math.xml.twig', [
            'error' => $error,
            'results' => $results
        ]);
    }
   
}
