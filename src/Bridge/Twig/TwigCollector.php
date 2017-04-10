<?php
/*
 * This file is part of the package.
 *
 * (c) 2013 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Songshenzong\Log\Bridge\Twig;

use Songshenzong\Log\DataCollector\DataCollector;

/**
 * Collects data about rendered templates
 *
 * http://twig.sensiolabs.org/
 *
 * Your Twig_Environment object needs to be wrapped in a
 * TraceableTwigEnvironment object
 *
 * <code>
 * $env = new TraceableTwigEnvironment(new Twig_Environment($loader));
 * $debugbar->addCollector(new TwigCollector($env));
 * </code>
 */
class TwigCollector extends DataCollector
{
    public function __construct(TraceableTwigEnvironment $twig)
    {
        $this->twig = $twig;
    }

    public function collect()
    {
        $templates = array();
        $accuRenderTime = 0;

        foreach ($this->twig->getRenderedTemplates() as $tpl) {
            $accuRenderTime += $tpl['render_time'];
            $templates[] = array(
                'name' => $tpl['name'],
                'render_time' => $tpl['render_time'],
                'render_time_str' => $this->formatDuration($tpl['render_time'])
            );
        }

        return array(
            'nb_templates' => count($templates),
            'templates' => $templates,
            'accumulated_render_time' => $accuRenderTime,
            'accumulated_render_time_str' => $this->formatDuration($accuRenderTime)
        );
    }

    public function getName()
    {
        return 'twig';
    }



}
