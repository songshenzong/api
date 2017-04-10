<?php

namespace Songshenzong\Log\DataCollector;

use Songshenzong\Log\DataCollector\DataCollector;
use Songshenzong\Log\DataCollector\DataCollectorInterface;


class SessionCollector extends DataCollector implements DataCollectorInterface
{
    /** @var  \Symfony\Component\HttpFoundation\Session\SessionInterface|\Illuminate\Contracts\Session\Session $session */
    protected $session;

    /**
     * Create a new SessionCollector
     *
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface|\Illuminate\Contracts\Session\Session $session
     */
    public function __construct($session)
    {
        $this -> session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        return $this -> session -> all();
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'session';
    }


}
