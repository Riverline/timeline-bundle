<?php

namespace Spy\TimelineBundle\Twig\Node;

use Spy\TimelineBundle\Twig\Extension\TimelineExtension;
use Twig\Compiler;
use Twig\Node\Node;

class TimelineActionThemeNode extends Node
{
    public function __construct(Node $action, Node $resources, int $lineno = 0, ?string $tag = null)
    {
        parent::__construct(['action' => $action, 'resources' => $resources], [], $lineno, $tag);
    }

    /**
     * @param Compiler $compiler
     */
    public function compile(Compiler $compiler): void
    {
        $compiler
            ->addDebugInfo($this)
            ->write('$this->env->getExtension(\''.TimelineExtension::class.'\')->setTheme(')
            ->subcompile($this->getNode('action'))
            ->raw(', [')
        ;

        foreach ($this->getNode('resources') as $resource) {
            $compiler->subcompile($resource)->raw(', ');
        }

        $compiler->raw("]);\n");
    }
}
