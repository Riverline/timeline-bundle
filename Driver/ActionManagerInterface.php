<?php

namespace Spy\TimelineBundle\Driver;

use Spy\TimelineBundle\Model\ActionInterface;

/**
 * ActionManagerInterface
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface ActionManagerInterface
{
    /**
     * @param ActionInterface $action action
     */
    public function updateAction(ActionInterface $action);

    /**
     * @param object $subject    Can be a ComponentInterface or an other one object.
     * @param string $verb       verb
     * @param array  $components An array of ComponentInterface or other objects.
     *
     * @return Action
     */
    public function create($subject, $verb, array $components = array());

    /**
     * Find a component or create it.
     *
     * @param string|object     $model      pass an object and second argument will be ignored.
     * it'll be replaced by $model->getId();
     * @param null|string|array $identifier pass an array for composite keys.
     *
     * @return ComponentInterface
     */
    public function findOrCreateComponent($model, $identifier = null);
}
