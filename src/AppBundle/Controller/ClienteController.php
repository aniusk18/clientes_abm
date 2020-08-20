<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cliente;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Cliente controller.
 *
 * @Route("cliente")
 */
class ClienteController extends Controller
{
    /**
     * Lists all cliente entities.
     *
     * @Route("/", name="cliente_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $page_size = $this->getParameter('resultados_busqueda');
        $nro_pag = $request->get('pag','1');
        $clientes = $em->getRepository('AppBundle:Cliente')->findByPage($nro_pag);
        $total = $em->getRepository('AppBundle:Cliente')->count();
        $last_page = ceil($total/$page_size);
        return $this->render('cliente/index.html.twig', array(
            'clientes' => $clientes,
            'last_page' => $last_page,
            'nro_pag' => $nro_pag,
        ));
    }

    /**
     * Creates a new cliente entity.
     *
     * @Route("/new", name="cliente_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $cliente = new Cliente();
        $form = $this->createForm('AppBundle\Form\ClienteType', $cliente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cliente);
            $em->flush();

            return $this->redirectToRoute('cliente_show', array('id' => $cliente->getId()));
        }

        return $this->render('cliente/new.html.twig', array(
            'cliente' => $cliente,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a cliente entity.
     *
     * @Route("/{id}", name="cliente_show")
     * @Method("GET")
     */
    public function showAction(Cliente $cliente)
    {
        $deleteForm = $this->createDeleteForm($cliente);

        return $this->render('cliente/show.html.twig', array(
            'cliente' => $cliente,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing cliente entity.
     *
     * @Route("/{id}/edit", name="cliente_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Cliente $cliente)
    {
        $deleteForm = $this->createDeleteForm($cliente);
        $editForm = $this->createForm('AppBundle\Form\ClienteType', $cliente);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cliente_edit', array('id' => $cliente->getId()));
        }

        return $this->render('cliente/edit.html.twig', array(
            'cliente' => $cliente,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a cliente by id.
     *
     * @Route("/{id}/delete", name="cliente_delete_c")
     * @Method({"GET", "POST"})
     */
    public function deleteCAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $cliente = $em->getRepository('AppBundle:Cliente')->find($id);
        if($cliente){
            try {
                $em->remove($cliente);
                $em->flush();
                $response = new Response(
                    Response::HTTP_OK
                );
                return $response;
            }catch (Exception $e) {
                $response = new Response(
                    $e
                );
                return $response;
            }
        }else{
            $response = new Response(
                Response::HTTP_NOT_FOUND
            );
            return $response;
        }
    }

    /**
     * Deletes a cliente entity.
     *
     * @Route("/{id}", name="cliente_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Cliente $cliente)
    {
        $form = $this->createDeleteForm($cliente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($cliente);
            $em->flush();
        }

        return $this->redirectToRoute('cliente_index');
    }

    /**
     * Creates a form to delete a cliente entity.
     *
     * @param Cliente $cliente The cliente entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Cliente $cliente)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cliente_delete', array('id' => $cliente->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Search a cliente by term.
     *
     * @Route("search", name="cliente_search")
     */
    public function SearchAction(Request $request)
    {
        $nro_pag = $request->get('pag','1');
        $page_size = $this->getParameter('resultados_busqueda');
        $text = addslashes(strtolower(trim($request->get('text'))));
        if(!empty($text)){
            $terms = array();
            foreach(explode(' ', $text) as $term) {
                if(empty($term)) continue;
                if(in_array($term, $terms)) continue;
                $terms[] = $term;
            }

            $em = $this->getDoctrine()->getManager();
            $conn = $em->getConnection();

            $sql = 'SELECT * FROM cliente WHERE (';
            foreach ($terms as $k => $term) {
                if($k != 0){
                    $sql .=' OR ';
                }
                $sql .=' nombre LIKE "%'.$term.'%" OR ';
                $sql .=' apellido LIKE "%'.$term.'%" OR ';
                $sql .=' email LIKE "%'.$term.'%" OR ';
                $sql .=' grupo_cliente LIKE "%'.$term.'%" ';
            }
            $sql .=') ';
            $sql_pager = $sql. " LIMIT ".(($nro_pag-1)*$page_size).", ".$page_size." ";
            $sth = $conn->prepare($sql_pager);
            $sth->execute();
            $clientes = $sth->fetchAll();
            $last_page = ceil(count($clientes)/$page_size);
            if($clientes){
                $clientes_=array();
                foreach ($clientes as $key => $cliente) {
                    $clientes_[$key]["id"]=$cliente["id"];
                    $clientes_[$key]["nombre"]=$cliente["nombre"];
                    $clientes_[$key]["apellido"]=$cliente["apellido"];
                    $clientes_[$key]["email"]=$cliente["email"];
                    $clientes_[$key]["grupo_cliente"]=unserialize($cliente["grupo_cliente"]);
                }
                $clientes_=json_encode($clientes_);
                $data = array('clientes' =>$clientes_,'lastpage'=>$last_page,'nro_pag'=>$nro_pag);
                $response = new JsonResponse();
                $response->setData($data);
                return $response;
            }else{
            $response = new Response(
                Response::HTTP_NOT_FOUND
            );
            return $response;

            }
        }else{
            $response = new Response(
                Response::HTTP_NOT_FOUND
            );
            return $response;
        }
    }

    /**
     * get all clientes
     *
     * @Route("get_all_clientes", name="get_all_clientes")
     */
    public function SearchAllClientesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $page_size = $this->getParameter('resultados_busqueda');
        $nro_pag = $request->get('pag','1');
        $conn = $em->getConnection();
        $sql = 'SELECT * FROM cliente LIMIT '.(($nro_pag-1)*$page_size).', '.$page_size.'';

        $sth = $conn->prepare($sql);
        $sth->execute();
        $clientes = $sth->fetchAll();
        $last_page = ceil(count($clientes)/$page_size);
        //var_dump($clientes); exit();
        if(!empty($clientes)){
            $clientes_=array();
            foreach ($clientes as $key => $cliente) {
                $clientes_[$key]["id"]=$cliente["id"];
                $clientes_[$key]["nombre"]=$cliente["nombre"];
                $clientes_[$key]["apellido"]=$cliente["apellido"];
                $clientes_[$key]["email"]=$cliente["email"];
                $clientes_[$key]["grupo_cliente"]=unserialize($cliente["grupo_cliente"]);
            }
            $clientes_=json_encode($clientes_);
            $data = array('clientes' =>$clientes_,'lastpage'=>$last_page,'nro_pag'=>$nro_pag);
            $response = new JsonResponse();
            $response->setData($data);
            return $response;
        }else{
            $response = new Response(
                Response::HTTP_NOT_FOUND
            );
            return $response;
        }
    }
}
