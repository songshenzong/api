<?php
/*
 * This file is part of the package.
 *
 * (c) 2013 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Songshenzong\Log\Bridge;

use Songshenzong\Log\DataCollector\DataCollector;
use Songshenzong\Log\DebugBarException;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\EntityManager;

/**
 * Collects Doctrine queries
 *
 * http://doctrine-project.org
 *
 * Uses the DebugStack logger to collects data about queries
 *
 * <code>
 * $debugStack = new Doctrine\DBAL\Logging\DebugStack();
 * $entityManager->getConnection()->getConfiguration()->setSQLLogger($debugStack);
 * $debugbar->addCollector(new DoctrineCollector($debugStack));
 * </code>
 */
class DoctrineCollector extends DataCollector
{
    protected $debugStack;

    /**
     * DoctrineCollector constructor.
     * @param $debugStackOrEntityManager
     * @throws DebugBarException
     */
    public function __construct($debugStackOrEntityManager)
    {
        if ($debugStackOrEntityManager instanceof EntityManager) {
            $debugStackOrEntityManager = $debugStackOrEntityManager->getConnection()->getConfiguration()->getSQLLogger();
        }
        if (!($debugStackOrEntityManager instanceof DebugStack)) {
            throw new DebugBarException("'DoctrineCollector' requires an 'EntityManager' or 'DebugStack' object");
        }
        $this->debugStack = $debugStackOrEntityManager;
    }

    /**
     * @return array
     */
    public function collect()
    {
        $queries = array();
        $totalExecTime = 0;
        foreach ($this->debugStack->queries as $q) {
            $queries[] = array(
                'sql' => $q['sql'],
                'params' => (object) $q['params'],
                'duration' => $q['executionMS'],
                'duration_str' => $this->formatDuration($q['executionMS'])
            );
            $totalExecTime += $q['executionMS'];
        }

        return array(
            'nb_statements' => count($queries),
            'accumulated_duration' => $totalExecTime,
            'accumulated_duration_str' => $this->formatDuration($totalExecTime),
            'statements' => $queries
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'doctrine';
    }




}
