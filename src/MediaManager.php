<?php

namespace MarcoMdMj\MediaManager;

use MarcoMdMj\MediaManager\Resource;
use MarcoMdMj\MediaManager\Store\StoreInterface;
use MarcoMdMj\MediaManager\Exceptions\MediaManagerException;

/**
 * Media Files Manager
 */
class MediaManager
{
    /**
     * Instance of the store engine. 
     * @var StoreInterface
     */
    private $store;

    /**
     * Relative media path.
     * @var string
     */
    private $path;

    /**
     * Initialize the manager and import dependencies.
     * @param StoreInterface
     */
    public function __construct(StoreInterface $store)
    {
        $this->store = $store;
    }

    /**
     * Given a file resource, save it.
     * @param  Resource $resource
     * @param  boolean  $replace
     * @throws MediaManagerException
     * @return string
     */
    public function save(Resource $resource, $replace = false)
    {
        $content = $resource->raw();
        $path = $resource->path();

        if (!$replace and $this->store->exists($path)) {
            throw new MediaManagerException("The file [" . $this->media_path($path) . "] already 
                                            exists. If you want to overwrite, use the replace() 
                                            method instead.");
        }

        $this->store->save($path, $content);

        return $path;
    }

    /**
     * Given a file resource, save it. If collision, overwrite.
     * @param  Resource $resource
     * @return string|boolean
     */
    public function replace(Resource $resource)
    {
        return $this->save($resource, $replace = true);
    }

    /**
     * Delete the given file or files.
     * @param  string|array $files File or set of files
     * @return boolean
     */
    public function delete($files)
    {
        if (func_num_args() > 1) {
            $files = func_get_args();
        }

        return $this->store->delete($files);
    }

    /**
     * Rename the given $oldPathname. $newFilename is relative to current $oldPathname pathname.
     * @param  string|array $files File or set of files
     * @return boolean
     */
    public function rename($oldPathname, $newFilename)
    {
        preg_match('|^/?((?:[^/]+/)*)(?:[^/]+)$|i', $oldPathname, $path);

        if (count($path) <> 2) {
            throw new MediaManagerException('The location of the file to be renamed [' . 
                                            $oldPathname . '] is not valid.');
        }

        $newPathname = '/' . $path[1] . $newFilename;

        return $this->move($oldPathname, $newPathname);
    }

    /**
     * Move the file from $oldPathname to $newPathname
     * @param  string $oldPathname
     * @param  string $newPathname
     * @return boolean
     */
    public function move($oldPathname, $newPathname)
    {
        return $this->store->move($oldPathname, $newPathname);
    }

    /**
     * Copy the file from $from to $to
     * @param  string $from
     * @param  string $to
     * @return boolean
     */
    public function copy($from, $to)
    {
        return $this->store->copy($from, $to);
    }

    /**
     * Set or return the media path. If $path is given, append it to the media_path.
     * @param  string $path
     * @return string
     */
    public function path($path = null)
    {
        if (is_null($this->path)) {
            $this->path = config('mediamanager.path');
        }

        if (is_null($path)) {
            return $this->path;
        }
        
        return $this->path . '/' . trim($path, '\/');
    }
}