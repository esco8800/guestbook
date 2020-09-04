<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class ConferenceController extends AbstractController
{
    /**
     * @Route("/hello/{name}", name="homepage")
     */
    public function index(Request $request)
    {
        $greet = '';
        if ($name = $request->query->get('name')) {
            $greet = sprintf('<h1>Hello %s!</h1>', htmlspecialchars($name));

        }
        return new Response("Hello {$greet}");
    }
}
