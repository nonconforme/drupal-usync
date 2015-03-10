<?php

namespace USync\AST; 

use USync\Context;

class InheritProcessor implements ProcessorInterface
{
    /**
     * List of path patterns that can use inheritance
     *
     * @todo Use this
     */
    static protected $inheritable = array(
        'entity.%',
        'field.%',
        'image.%',
        'view.%.%',
        'view.%.%.%',
    );

    public function execute(Node $node, Context $context)
    {
        $sorted = array();
        $orphans = array();
        $path = $node->getPath();

        foreach ($node->getChildren() as $key => $child) {

            if ($child->hasChild('inherit')) {
                $parent = $child->getChild('inherit')->getValue();

                if (!is_string($parent)) {
                    $context->logCritical(sprintf("%s: %s: malformed inherit directive", $path, $key));
                }
                if (!$node->hasChild($parent)) {
                    $context->logCritical(sprintf("%s: %s: cannot inherit from non existing: %s", $path, $key, $parent));
                }
                if ($key === $parent) {
                    $context->logCritical(sprintf("%s: %s: cannot inherit from itself", $path, $key));
                }

                $orphans[$key] = $parent;
            } else {
                $sorted[$key] = array();
            }
        }

        while (!empty($orphans)) {
            $count = count($orphans);
            foreach ($orphans as $key => $parent) {
                if (isset($sorted[$parent])) {
                    $sorted[$parent][] = $key;
                    $sorted[$key] = array();
                    unset($orphans[$key]);
                }
            }
            if (count($orphans) === $count) {
                $context->logCritical(sprintf("%s: circular dependency detected", $path));
            }
        }

        foreach (array_filter($sorted) as $parent => $children) {
            foreach ($children as $name) {
                $node
                  ->getChild($name)
                  ->setBaseNode(
                      $node->getChild($parent)
                  );
            }
        }
    }
}