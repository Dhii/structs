<?php

use Dhii\Structs\Struct;
use Dhii\Structs\Ty;

require dirname(__DIR__) . '/vendor/autoload.php';

/**
 * @property-read int       $value
 * @property-read Node|null $left
 * @property-read Node|null $right
 */
class Node extends Struct
{
    /**
     * @inheritDoc
     */
    public static function propTypes() : array
    {
        return [
            'value' => Ty::int(),
            'left' => Ty::nullable(Ty::object(Node::class)),
            'right' => Ty::nullable(Ty::object(Node::class)),
        ];
    }

    /**
     * Creates a new node instance.
     *
     * @param int       $value
     * @param Node|null $left
     * @param Node|null $right
     *
     * @return static
     */
    public static function create(int $value, Node $left = null, Node $right = null)
    {
        return new static(compact('value', 'left', 'right'));
    }

    /**
     * Attaches a node to another.
     *
     * @param Node|null $node
     * @param Node      $child
     *
     * @return Node|null
     */
    public static function attach(?Node $node, Node $child)
    {
        // If attaching to null, child becomes the root
        if ($node === null) {
            return $child;
        }

        // If equal value, ignore the child
        if ($node->value === $child->value) {
            return $node;
        }

        // If smaller value, return a derived root with the child attached to its left sub-tree
        if ($child->value < $node->value) {
            return Node::derive($node, [
                'left' => Node::attach($node->left, $child)
            ]);
        }

        // If larger value, return a derived root with the child attached to its right sub-tree
        return Node::derive($node, [
            'right' => Node::attach($node->right, $child)
        ]);
    }

    /**
     * Prints the contents of a tree.
     *
     * @param Node $node
     */
    public static function traverse(Node $node)
    {
        if ($node->left !== null) {
            Node::traverse($node->left);
        }

        echo " {$node->value} ";

        if ($node->right !== null) {
            Node::traverse($node->right);
        }
    }
}

$values = [5, 11, 9, 2, 0, -6, 8, 23];

$nodes = array_map([Node::class, 'create'], $values);
$tree = array_reduce($nodes, [Node::class, 'attach'], null);

Node::traverse($tree);
