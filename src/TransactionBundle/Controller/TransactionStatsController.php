<?php

namespace TransactionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use TransactionBundle\Entity\Transaction;
use TransactionBundle\Repository\TransactionRepository;

class TransactionStatsController extends Controller
{
    /**
     * @Route("/stats",name="transactions_stats")
     */
    public function listTransactionsAction()
    {
        $transactions = $this->getDoctrine()->getRepository(Transaction::class)->getAllTransactions();
        return $this->render('TransactionBundle:Transaction:list.html.twig', array('transactions'=>$transactions));
    }


}
