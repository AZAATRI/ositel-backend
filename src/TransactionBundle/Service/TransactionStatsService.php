<?php

namespace TransactionBundle\Service;
use Doctrine\ORM\EntityManagerInterface;
use TransactionBundle\Entity\Transaction;
use TransactionBundle\Repository\TransactionRepository;

/**
 * Created by PhpStorm.
 * User: amine
 * Date: 30/03/2019
 * Time: 11:35
 */
class TransactionStatsService
{
    /**
     * We need to use only one repository
     * @var TransactionRepository
     */
    private $_repository;

    function __construct(EntityManagerInterface $entityManager)
    {
        $this->_repository = $entityManager->getRepository(Transaction::class);
    }

    /**
     * Get valid transaction for a month and year
     * @param $month
     * @param $year
     * @return Transaction[]
     */
    public function monthlyTransactions($month, $year){
        return $this->_repository->getValidTransactionsByMonth($month,$year);
    }

    /**
     * Get monthly Inputs and outputs
     * @param $month
     * @param $year
     * @param array $monthlyTransactions
     * @return array
     */
    public function inAndOutByMonth($month, $year, $monthlyTransactions = array()){
        if(! $monthlyTransactions){
            $monthlyTransactions = $this->monthlyTransactions($month, $year);
        }
        $in = $out = 0.00;

        foreach ($monthlyTransactions as $transaction){
            if($transaction->isInput()){
                $in += $transaction->getAmount();
            } else{
                $out += $transaction->getAmount();
            }
        }
        return array('in' => $in, 'out' => $out);
    }

    /**
     * Cash Amount by month and year
     * @return array
     */
    public function cashAmount(){
        return $this->_repository->getCashAmountByMounths();
    }
    public function getPreparedMonthsAndYears(){
        $dates = $this->_repository->getBorneDates();
        $start    = (new \DateTime($dates['minDate']))->modify('first day of this month');
        $end      = (new \DateTime($dates['maxDate']))->modify('last day of this month');
        $interval = \DateInterval::createFromDateString('1 month');
        $period   = new \DatePeriod($start, $interval, $end);
        $calendar = array();
        foreach ($period as $dt) {
            $calendar[] = $dt->format("Y-m");
        }
        return $calendar;
    }
}