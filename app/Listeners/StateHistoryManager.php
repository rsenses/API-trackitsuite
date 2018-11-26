<?php

namespace App\Listeners;

use SM\Event\TransitionEvent;

class StateHistoryManager
{
    public function postTransition(TransitionEvent $event)
    {
        $sm = $event->getStateMachine();
        $model = $sm->getObject();

        $model->addHistoryLine([
            'transition' => $event->getTransition(),
            'to' => $sm->getState()
        ]);
    }
}
