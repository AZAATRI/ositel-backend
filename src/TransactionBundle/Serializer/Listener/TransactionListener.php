<?php

namespace TransactionBundle\Serializer\Listener;

use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use TransactionBundle\Entity\Transaction;

class TransactionListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            [
                'event' => Events::POST_SERIALIZE,
                'format' => 'json',
                'class' => Transaction::class,
                'method' => 'onPreSerialize',
            ]
        ];
    }

    public static function onPreSerialize(ObjectEvent $event)
    {
        // Possibilité de récupérer l'objet qui a été sérialisé
        /**
         * Transaction
         */

        $object = $event->getObject();

        // Possibilité de modifier le tableau après sérialisation
        $event->getVisitor()->addData('created_at_prepared', $object->getCreatedAt()->format('Y-m-d'));
        $event->getVisitor()->addData('tags_prepared', $object->getStringTags());

    }
}