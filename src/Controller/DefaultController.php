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

    #[Route('/math', name: 'default')]
    public function index(Request $request): Response
    {
        $results = [
            'error' => false
        ];
        try {
            $results['results'] = Parser::calculate($request->getContent());
        } catch (InvalidArgumentException | Exception $ex) {
            $results = [
                'error' => true,
                'message' => $ex->getMessage()
            ];
        }
        return $this->json($results);
    }

}
