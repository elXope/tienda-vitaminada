<?php

namespace App\Controller;
use App\Entity\Producto;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path:'/api')]
class ApiController extends AbstractController
{
    #[Route('/show/{id}', name: 'api-show',  methods: ['GET'])]
    public function show(ManagerRegistry $doctrine, $id): JsonResponse
    {
        $repository = $doctrine->getRepository(Producto::class);
        $producto = $repository->find($id);
        if (!$producto)
            return new JsonResponse("[]", Response::HTTP_NOT_FOUND);
        
        $data = [
            "id"=> $producto->getId(),
            "nombre" => $producto->getNombre(),
            "price" => $producto->getPrice(),
            "photo" => $producto->getPhoto()
         ];
        return new JsonResponse($data, Response::HTTP_OK);
    }
}
