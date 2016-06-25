<?php namespace PSpec\Filters;

use FilterIterator;
use Iterator;

/**
 * Used to filter paths by their basename and pathname. Assumes it's iterating
 * over SplFileInfo objects.
 */
class FilePathIterator extends FilterIterator
{
    private $basename_include;

    public function __construct(
        Iterator $iterator,
        $basename_include = Defaults::MATCH_ALL
    ) {
        parent::__construct($iterator);
        $this->basename_include = $basename_include;
    }

    public function accept()
    {
        $file_info = $this->getInnerIterator()->current();
        return $this->basename_include === Defaults::MATCH_ALL || preg_match($this->basename_include, $file_info->getBaseName());
    }
}
