<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Service\CartService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path:'/cart')]
class CartController extends AbstractController
{
    private $doctrine;
    private $repository;
    private $cart;
    //Le inyectamos CartService como una dependencia
    public  function __construct(ManagerRegistry $doctrine, CartService $cart)
    {
        $this->doctrine = $doctrine;
        $this->repository = $doctrine->getRepository(Producto::class);
        $this->cart = $cart;
    }

    // En principi no vaig a usar esta ruta
    #[Route('/', name: 'cart')]
    public function index(): JsonResponse
    {
        return new JsonResponse($this->cart->getCart(), Response::HTTP_OK);
    }

    #[Route('/add/{id}', name: 'cart_add', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function cart_add(int $id): JsonResponse
    {
        $producto = $this->repository->find($id);
        if (!$producto)
            return new JsonResponse("[]", Response::HTTP_NOT_FOUND);

        $this->cart->add($id);

        $data = array_sum($this->cart->getCart());
	    
        // $data = [
        //     "id"=> $producto->getId(),
        //     "nombre" => $producto->getNombre(),
        //     "price" => $producto->getPrice(),
        //     "photo" => $producto->getPhoto(),
        //     "quantity" => $this->cart->getCart()[$producto->getId()]
        // ];
        return new JsonResponse($data, Response::HTTP_OK);

    }

    #[Route('/remove/{id}', name: 'cart_remove', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function cart_remove(int $id): JsonResponse
    {
        $this->cart->remove($id);
        $data = array_sum($this->cart->getCart());
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/quantity', name:'cart_quantity', methods:['GET'])]
    public function cart_quantity(): JsonResponse
    {
        return new JsonResponse(array_sum($this->cart->getCart()), Response::HTTP_OK);
    }

    #[Route('/products', name:'cart_products', methods:['GET'])]
    public function cart_products(): JsonResponse
    {
        $cart = $this->cart->getCart();
        $data = [];
        while ($quantity = current($cart)) {
            $producto = $this->repository->find(key($cart));
            $data[$producto->getId()] = [
                "nombre" => $producto->getNombre(),
                "price" => $producto->getPrice(),
                "photo" => $producto->getPhoto(),
                "quantity" => $quantity,
                "totalPrice" => $quantity * $producto->getPrice()
            ];
            next($cart);
        }
        return new JsonResponse($data, Response::HTTP_OK);
    }
}
