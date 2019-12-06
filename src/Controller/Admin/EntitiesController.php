<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/entities")
 */
class EntitiesController extends AbstractController
{
    /**
     * @Route(
     *      "/", 
     *      methods={"GET"}, 
     *      name="admin_entities_index"
     * )
     */
    public function index()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entities = [];
        $metas = $entityManager->getMetadataFactory()->getAllMetadata();
        
        foreach ($metas as $meta) {
            if ($meta->getReflectionClass()->isAbstract()) {
                continue;
            }
            
            $entities[] = [
                'name' => $meta->getName()
            ];
        }
        
        return $this->render('admin/entities/index.html.twig', [
            'entities' => $entities,
        ]);
    }
    
    /**
     * @Route(
     *      "/detail", 
     *      methods={"GET"}, 
     *      name="admin_entities_detail"
     * )
     */
    public function detail(\Symfony\Component\HttpFoundation\Request $request)
    {
        $requestedName = $request->get('name');
        
        $entity = [
            'name' => '',
            'fields' => []
        ];
        
        if ($requestedName) {
            $entityManager = $this->getDoctrine()->getManager();
            
            if (!class_exists($requestedName)) {
                throw $this->createNotFoundException("Page not found");
            }
            
            $entityMeta = $entityManager->getClassMetadata($requestedName);
            
            $entity['name'] = $entityMeta->getName();
            
            foreach ($entityMeta->getFieldNames() as $fieldName) {
                $entity['fields'][] = [
                    'name' => $fieldName,
                    'type' => $entityMeta->getTypeOfField($fieldName)
                ];
            }
            
            $entityReflection = $entityMeta->getReflectionClass();
            $entity['fileName'] = $entityReflection->getFileName();
            
            $entity['comment'] = $entityReflection->getDocComment();
        } else {
            throw $this->createNotFoundException("Page not found");
        }
            
        return $this->render('admin/entities/detail.html.twig', [
            'entity' => $entity,
        ]);
    }
}
