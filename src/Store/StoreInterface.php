<?php

namespace MarcoMdMj\MediaManager\Store;

use MarcoMdMj\MediaManager\Resource;

/**
 * Store interface.
 */
interface StoreInterface
{
    /**
     * Save into $path the given $raw content.
     * @param  string $path
     * @param  string $raw
     * @return boolean
     */
    public function save($path, $raw);

    /**
     * Delete the given file.
     * @param  string $file
     * @return boolean
     */
    public function delete($file);

    /**
     * Move the $oldPathname to $newPathname.
     * @param  string $oldPathname
     * @param  string $newPathname
     * @return boolean
     */
    public function move($oldPathname, $newPathname);

    /**
     * Copy the $from file to $to.
     * @param  string $from
     * @param  string $to
     * @return boolean
     */
    public function copy($from, $to);

    /**
     * Check if path exists in the selected disk.
     * @param  string $path
     * @return boolean
     */
    public function exists($path);
}