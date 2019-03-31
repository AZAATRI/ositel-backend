<?php

namespace TransactionBundle\DataFixtures\ORM;
use Doctrine\Common\Persistence\ObjectManager;
use TransactionBundle\Entity\Category;
use TransactionBundle\Entity\Tag;
use TransactionBundle\Entity\Transaction;
use TransactionBundle\Entity\TransactionTag;

/**
 * Created by PhpStorm.
 * User: amine
 * Date: 24/03/2019
 * Time: 14:28
 */
class LoadTransactionsData implements \Doctrine\Common\DataFixtures\FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $categories = array();
        $transactions = array();
        $tags = array();
        for($i = 1 ; $i<=10 ; $i++){
            $category = new Category();
            $category->setTitle('Categorie title '.$i);
            $categories[$i] = $category;
            $manager->persist($category);
        }
        for($i = 1 ; $i<=15 ; $i++){
            $tag = new Tag();
            $tag->setName('Tag Name '.$i);
            $tags[$i] = $tag;
            $manager->persist($tag);
        }
        for($i = 1 ; $i<40 ; $i++){
            $transaction = new Transaction();
            $transaction->setDescription('Transaction description '.$i);
            $transaction->setAmount($i * (rand(0, 100)/100) + 10*$i);
            $transaction->setTitle('Transaction title '.$i);
            $transaction->setIsValid(true);
            $transaction->setIsInput($i % 2 ? true : false);
            $transaction->setCreatedAt(new \DateTime('now -'.random_int(0,100).' day'));
            $transaction->setCategory($categories[random_int(1,10)]);
            $transactions[$i] = $transaction;
            $manager->persist($transaction);
        }
        for($i = 1 ; $i<30 ; $i++){
            $transactionTag = new TransactionTag();
            $transactionTag->setTag($tags[random_int(1,15)]);
            $transactionTag->setTransaction($transactions[$i]);
            $manager->persist($transactionTag);
        }

        $manager->flush();
    }


}