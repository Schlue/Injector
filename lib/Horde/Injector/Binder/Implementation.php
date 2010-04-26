<?php
/**
 * TODO
 *
 * @author   Bob Mckee <bmckee@bywires.com>
 * @author   James Pepin <james@jamespepin.com>
 * @category Horde
 * @package  Horde_Injector
 */
class Horde_Injector_Binder_Implementation implements Horde_Injector_Binder
{
    /**
     * TODO
     */
    private $_implementation;

    /**
     * TODO
     */
    private $_setters;

    /**
     * TODO
     */
    public function __construct($implementation)
    {
        $this->_implementation = $implementation;
        $this->_setters = array();
    }

    /**
     * TODO
     */
    public function getImplementation()
    {
        return $this->_implementation;
    }

    /**
     * TODO
     */
    public function bindSetter($method)
    {
        $this->_setters[] = $method;
        return $this;
    }

    /**
     * TODO
     */
    public function equals(Horde_Injector_Binder $otherBinder)
    {
        return (($otherBinder instanceof Horde_Injector_Binder_Implementation) &&
                ($otherBinder->getImplementation() == $this->_implementation));
    }

    /**
     * TODO
     */
    public function create(Horde_Injector $injector)
    {
        $reflectionClass = new ReflectionClass($this->_implementation);
        $this->_validateImplementation($reflectionClass);
        $instance = $this->_getInstance($injector, $reflectionClass);
        $this->_callSetters($injector, $instance);
        return $instance;
    }

    /**
     * TODO
     */
    private function _validateImplementation(ReflectionClass $reflectionClass)
    {
        if ($reflectionClass->isAbstract() || $reflectionClass->isInterface()) {
            throw new Horde_Injector_Exception('Cannot bind interfaces or abstract classes "' . $this->_implementation . '" to an interface.');
        }
    }

    /**
     * TODO
     */
    private function _getInstance(Horde_Injector $injector, ReflectionClass $class)
    {
        return $class->getConstructor()
            ? $class->newInstanceArgs($injector->getMethodDependencies($class->getConstructor()))
            : $class->newInstance();
    }

    /**
     * TODO
     */
    private function _callSetters(Horde_Injector $injector, $instance)
    {
        foreach ($this->_setters as $setter) {
            $reflectionMethod = new ReflectionMethod($instance, $setter);
            $reflectionMethod->invokeArgs(
                $instance,
                $injector->getMethodDependencies($reflectionMethod)
            );
        }
    }
}
