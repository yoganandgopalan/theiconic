<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Exception;
class CustomersController extends Controller
{
    /**
     * @Route("/customers/")
     * @Method("GET")
     */
    public function getAction()
    {     
        $cacheService = $this->get('cache_service');
        $customers = $cacheService->getAll();
	
        if (empty($customers)) {
            $database = $this->get('database_service')->getDatabase();
            $customers = $database->customers->find();
            $customers = iterator_to_array($customers);
            foreach ($customers as $customer) {
                $user = ['name' => $customer['name'], 'age' => $customer['age']];
                $cacheService->set($customer['_id'], json_encode($user));
            }
        }

        return new JsonResponse($customers);
    }

    /**
     * @Route("/customers/")
     * @Method("POST")
     */
    public function postAction(Request $request)
    {
        $database = $this->get('database_service')->getDatabase();
        $customers = json_decode($request->getContent());
        $cacheService = $this->get('cache_service');

        if (empty($customers)) {
            return new JsonResponse(['status' => 'No donuts for you'], 400);
        }
        foreach ($customers as $customer) {
            $result = $database->customers->insertOne($customer);
            $cacheService->set($result->getInsertedId(), json_encode($customer));
        }

        return new JsonResponse(['status' => 'Customers successfully created']);
    }

    /**
     * @Route("/customers/")
     * @Method("DELETE")
     */
    public function deleteAction()
    {
        $database = $this->get('database_service')->getDatabase();
        $database->customers->drop();
        $cacheService = $this->get('cache_service');
        $cacheService->delAll();

        return new JsonResponse(['status' => 'Customers successfully deleted']);
    }
}