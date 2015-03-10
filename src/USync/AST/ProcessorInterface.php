<?php

namespace USync\AST;

use USync\Context;

/**
 * Processors will be executed over each node of the graph while the
 * initial Visitor traversal of it: processor goals are to prepare the
 * AST to reflect the final state of the PHP arrays that will be sent
 * to the helpers.
 *
 * Processors are stateless.
 */
interface ProcessorInterface
{
    public function execute(Node $node, Context $context);
}