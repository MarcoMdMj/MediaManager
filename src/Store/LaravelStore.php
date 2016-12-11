<?php

namespace MarcoMdMj\MediaManager\Store;

use MarcoMdMj\MediaManager\Resource;
use Illuminate\Contracts\Filesystem\Factory;

/**
 * Laravel store implementation.
 */
class LaravelStore implements StoreInterface
{
    /**
     * Instance of laravel filesystem factory
     * @var Factory
     */
    private $filesystem;

    /**
     * Selected disk.
     * @var string
     */
    private $disk;

    /**
     * Instance of the Laravel file Factory with the disk loaded.
     * @var FilesystemAdapter
     */
    private $driver;

    /**
     * Initialize the store engine.
     * @param Factory $filesystem
     */
    public function __construct(Factory $filesystem)
    {
        $this->filesystem = $filesystem;
        $this->disk = config('mediamanager.disk');
        $this->driver = $this->filesystem->disk($this->disk);
    }
    
    /**
     * Save into $path the given $raw content.
     * @param  string $path
     * @param  string $raw
     * @return boolean
     */
    public function save($path, $raw)
    {
        return $this->driver->put($path, $raw);
    }

    /**
     * Delete the given file.
     * @param  string $file
     * @return boolean
     */
    public function delete($file)
    {
        return $this->driver->delete($file);
    }

    /**
     * Move the $oldPathname to $newPathname.
     * @param  string $oldPathname
     * @param  string $newPathname
     * @return boolean
     */
    public function move($oldPathname, $newPathname)
    {
        return $this->driver->move($oldPathname, $newPathname);
    }

    /**
     * Copy the $from file to $to.
     * @param  string $from
     * @param  string $to
     * @return boolean
     */
    public function copy($from, $to)
    {
        return $this->driver->copy($from, $to);
    }

    /**
     * Check if path exists in the selected disk.
     * @param  string $path
     * @return boolean
     */
    public function exists($path)
    {
        return $this->driver->exists($path);
    }
}