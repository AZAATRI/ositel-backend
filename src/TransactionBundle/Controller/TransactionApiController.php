<?php

namespace TransactionBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Request;
use TransactionBundle\Service\TransactionStatsService;

/**
 * TransactionApi controller.
 * Préparation backend angular
 *
 */
class TransactionApiController extends Controller
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
     * @Get(
     *     path = "/getDates",
     *     name = "api_transaction_dates"
     * )
     * @View
     */
    public function datesAction()
    {
        return $this->_transactionStatsService->getPreparedMonthsAndYears();
    }
    /**
     * @Get(
     *     path = "/transactionStats",
     *     name = "api_transaction_dates"
     * )
     * @View
     */
    public function getStatsAction(Request $request)
    {
        $year = $request->query->get('year');
        $month = $request->query->get('month');
        $transactionsPerMonth = $this->_transactionStatsService->monthlyTransactions($month,$year);
        return array(
            'transactionsPerMonth' => $transactionsPerMonth,
            'inAndOutPerMonth' => $this->_transactionStatsService->inAndOutByMonth($month,$year,$transactionsPerMonth),
            'cashAmount' => $this->_transactionStatsService->cashAmount()
        );
    }
}
