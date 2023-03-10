<?php
namespace App\Service;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService{
    private const KEY = '_cart';
    private $requestStack;
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
    public function getSession()
    {
        return $this->requestStack->getSession();
    }
    public function getCart(): array {
        return $this->getSession()->get(self::KEY, []);
    }
    public function add(int $id){
        //https://symfony.com/doc/current/session.html
        $cart = $this->getCart();
        //Sólo añadimos si no lo está 
        if (!array_key_exists($id, $cart))
            $cart[$id] = 1;
        else {
            $cart[$id]++;
        }
        $this->getSession()->set(self::KEY, $cart);
    }
    public function remove(int $id){
        //https://symfony.com/doc/current/session.html
        // MHE QUEDAT ACÍ -> UNSET
        $cart = $this->getCart();
        unset($cart[$id]);
        $this->getSession()->set(self::KEY, $cart);
    }
}
