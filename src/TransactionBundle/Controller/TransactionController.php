<?php

namespace TransactionBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use TransactionBundle\Entity\Transaction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
use TransactionBundle\Service\TransactionStatsService;

/**
 * Transaction controller.
 *
 */
class TransactionController extends Controller
{

    /**
     * @var TransactionStatsService
     */
    private $_transactionStatsService;

    /**
     * TransactionController constructor.
     * @param $_transactionStatsService
     */
    public function __construct(TransactionStatsService $_transactionStatsService)
    {
        $this->_transactionStatsService = $_transactionStatsService;
    }


    /**
     * Lists all transaction entities.
     *
     * @Route("/", name="transaction_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $transactions = $this->getDoctrine()->getRepository(Transaction::class)->getAllTransactions();

        return $this->render('TransactionBundle:Transaction:list.html.twig', array(
            'transactions' => $transactions,
        ));
    }

    /**
     * Creates a new transaction entity.
     *
     * @Route("/new", name="transaction_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $transaction = new Transaction();
        $form = $this->createForm('TransactionBundle\Form\TransactionType', $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($transaction);
            $em->flush();

            return $this->redirectToRoute('transaction_show', array('id' => $transaction->getId()));
        }

        return $this->render('TransactionBundle:Transaction:new.html.twig', array(
            'transaction' => $transaction,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a transaction entity.
     *
     * @Route("/{id}", name="transaction_show",requirements={"id"="\d+"})
     * @Method("GET")
     */
    public function showAction(Transaction $transaction)
    {
        return $this->render('TransactionBundle:Transaction:show.html.twig', array(
            'transaction' => $transaction
        ));
    }

    /**
     * Displays a form to edit an existing transaction entity.
     *
     * @Route("/{id}/edit", name="transaction_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Transaction $transaction)
    {
        $editForm = $this->createForm('TransactionBundle\Form\TransactionType', $transaction);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $transaction->getTransactionTags()->clear();
            $transaction->setTransactionTagsRelation();
            $em->merge($transaction);
            $em->flush();
            return $this->redirectToRoute('transaction_show', array('id' => $transaction->getId()));
        }

        return $this->render('TransactionBundle:Transaction:edit.html.twig', array(
            'transaction' => $transaction,
            'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a transaction entity.
     *
     * @Route("/{id}/delete", name="transaction_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, Transaction $transaction)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($transaction);
        $em->flush();
        return $this->redirectToRoute('transaction_index');
    }

    /**
     * @Route("/stats", name="transaction_stats")
     * @Method("GET")
     */
    public function statsPortailAction(Request $request){
        $dates = $this->_transactionStatsService->getPreparedMonthsAndYears();
        return $this->render('TransactionBundle:Transaction:stats.html.twig', array(
            'dates' => $dates
        ));
    }
    /**
     * @Route("/stats/data", name="transaction_stats_data")
     * @Method("POST")
     */
    public function statsDataAction(Request $request){
        $jsonResponse = array('code' => 500);
        if($request->isXmlHttpRequest()){
            $year = $request->request->get('year');
            $month = $request->request->get('month');
            $transactionsPerMonth = $this->_transactionStatsService->monthlyTransactions($month,$year);
            $response = $this->render('TransactionBundle:Transaction:statsData.html.twig',array(
                'transactionsPerMonth' => $transactionsPerMonth,
                'inAndOutPerMonth' => $this->_transactionStatsService->inAndOutByMonth($month,$year,$transactionsPerMonth),
                'cashAmount' => $this->_transactionStatsService->cashAmount()
            ))->getContent();
            $jsonResponse['response'] = $response;
            $jsonResponse['code'] = 200;
        }
        return new JsonResponse($jsonResponse);
    }

}
