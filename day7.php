<?php
declare(strict_types = 1);

/**
 * Define a tree node
 */
class TreeNode
{
    public readonly int $size;
    public readonly string $name;

    public TreeNode | null $parent;
    public SplDoublyLinkedList $children;

    public function __construct( $size, $name ) {
        $this->size = $size;
        $this->name = $name;
        $this->parent = null;
        $this->children = new SplDoublyLinkedList();
    }

    public function addNode( Treenode $child ) {
        $child->parent = $this;
        $this->children->push($child);
    }
}

/**
 * Create an N-ary tree
 */
class TreeBuilder
{
    private TreeNode $root;
    private TreeNode $current;

    public function __construct() {
        $this->root = new TreeNode( 0, '/');
        $this->current = $this->root;
    }

    /**
     * cd : Handle the 'cd' command
     * @param string $dir       The directory name
     * 
     * @return TreeNode
     */
    public function cd(string $dir): TreeNode {
        switch( $dir ) {
            // cd to root
            case '/':
                $this->current = $this->root;
                break;
            // cd to parent directory
            case '..':
                $this->current = $this->current->parent;
                break;
            // cd to child directory
            default:
                $found = false;
                foreach( $this->current->children as $child ){
                    if( $child->name == $dir ) {
                        $this->current = $child;
                        $found = true;
                        break;
                    }
                }
                // If child directory does not exist, create it.
                if( !$found ){
                    $node = new TreeNode(0, $dir);
                    $this->current->addNode($node);
                    $this->current = $node;
                }
                break;
        }
        return $this->current;
    }

    public function processCommand(string $cmd): void {
        $parts = explode(' ', $cmd);
        if( $parts[0] == '$' ) {
            // Actual command
            switch( $parts[1] ) {
                // Change directory
                case 'cd':
                    $this->cd( $parts[2] );
                    break;
                case 'ls':
                    // Nothing to do... File/Directory list follows
                    break;
            }
        } else {
            // We either have 'dir' or a filesize
            if( $parts[0] == 'dir' ) {
                $size = 0;
            } else {
                $size = (int)$parts[0];
            }
            $this->current->addNode( new TreeNode($size, $parts[1]));
        }
    }

    public function getTree() : TreeNode {
        return $this->root;
    }
}

/**
 * Walk the file tree created by TreeBuilder to solve the problems.
 * class: TreeWalker
 */
class TreeWalker {

    private $sizeArray;

    /**
     * printTree : Recursively output a node to the console
     * @param int $level          Node depth
     * @param TreeNode $node      The node to be displayed
     * 
     * @return void
     */
    public function printTree( int $level, TreeNode $node): void {
        for( $i = 0; $i < $level; $i++ ) {
            echo '    ';
        }
        echo $node->size . " : " . $node->name . "\n";
        foreach( $node->children as $child ) {
            $this->printTree($level+1, $child);
        }
    }

    /**
     * sumDirectories : Recursively Sum directory sizes and put the totals in an array
     * 
     * @return int
     */
    private function sumDirectories(TreeNode $node): int {
        // If a node has children, it is a directory
        if( $node->children->count() > 0 ){
            $total = 0;
            foreach($node->children as $child) {
                $total += $this->sumDirectories($child);
            }
            $this->sizeArray[$node->name] = $total;
            return $total; 
        } else {
            return $node->size;
        }
    }

    /**
     * part1 : Find the biggest directory that is less than 100000 size
     * @param TreeNode $node
     * 
     * @return int
     */
    public function part1(TreeNode $node): int {
        $this->sizeArray = array();
        // Collect all the directories and their sizes into an array
        $total = $this->sumDirectories( $node );
        // reverse sort the array by value
        arsort($this->sizeArray);
        // Find the first directory less than 100k
        foreach($this->sizeArray as $directory => $dirsize) {
            if( $dirsize < 100000 ) {
                echo "Directory " . $directory . " => " . $dirsize . "\n";
                break;
            }
        }
        return $total;
    }

    /**
     * part2 : Find the smallest directory that frees off enough space 30000000 size in space
     * @param TreeNode $node
     * @param int $totalUsed    The total size used by the directory tree.
     * 
     * @return void
     */
    public function part2(TreeNode $node, int $totalUsed): void {
        $this->sizeArray = array();
        // Total free is disk size 70000000 - totalUsed.
        // Total reuired is 30000000 - Total free. 
        $totalRequired = 30000000 - (70000000 - $totalUsed);
        // Collect all the directories and their sizes into an array
        $this->sumDirectories( $node );
        // ascending sort by value
        asort($this->sizeArray);
        // Find the first directory that frees off enough space.
        foreach($this->sizeArray as $key => $value) {
            if( $value > $totalRequired ) {
                echo "Directory " . $key . " => " . $value . "\n";
                break;
            }
        }
    }
}

$tb = new TreeBuilder();
$file = new SplFileObject("day7input.txt", "r");
while( !$file->eof()) {
    $tb->processCommand( trim($file->fgets()) );
}

$tw = new TreeWalker();
// Not required... but it's a sanity check that the tree is built correctly
$tw->printTree( 0, $tb->getTree());

echo "\n\nPart 1:\n";
$totalUsed = $tw->part1($tb->getTree());
echo "Part 2:\n";
$tw->part2($tb->getTree(), $totalUsed);
