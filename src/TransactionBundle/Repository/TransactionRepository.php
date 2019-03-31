<?php

namespace TransactionBundle\Repository;
use Doctrine\ORM\EntityRepository;

/**
 * Created by PhpStorm.
 * User: amine
 * Date: 24/03/2019
 * Time: 16:46
 */
class TransactionRepository extends EntityRepository
{

    public function getAllTransactions(){

        $transactions = $this->createQueryBuilder('transaction')
                            ->join('transaction.category','category')
                            ->getQuery()->getResult();
        return $transactions;
    }

    public function getBorneDates(){
        $transactions = $this->createQueryBuilder('transaction')
            ->select('MAX(transaction.createdAt) as maxDate,MIN(transaction.createdAt) as minDate')
            ->andWhere('transaction.isValid = :isValid')
            ->setParameter('isValid',true)
            ->getQuery()->getSingleResult();
        return $transactions;
    }

    public function getValidTransactionsByMonth($month, $year){
        $transactions = $this->createQueryBuilder('transaction')
            ->where('MONTH(transaction.createdAt) = :month')
            ->andWhere('YEAR(transaction.createdAt) = :year')
            ->andWhere('transaction.isValid = :isValid')
            ->setParameter('month', $month)
            ->setParameter('year', $year)
            ->setParameter('isValid',true)
            ->getQuery()->getResult();
        return $transactions;
    }

    /**
     * Cash amount by years and months
     * @return array
     */
    public function getCashAmountByMounths(){
        $transactions = $this->createQueryBuilder('transaction')
            ->select('YEAR(transaction.createdAt) as transactionYear,MONTH(transaction.createdAt) as transactionMonth,
                SUM(CASE WHEN transaction.isInput = true then transaction.amount else -1*transaction.amount END) as monthlyAmount')
            ->where('transaction.isValid = :isValid')
            ->setParameter('isValid',true)
            ->groupBy('transactionYear')
            ->addGroupBy('transactionMonth')
            ->orderBy('transactionYear')
            ->addOrderBy('transactionMonth')
            ->getQuery()->getResult();
        return $transactions;
    }
}