<?php

namespace MarcoMdMj\MediaManager;

use finfo;
use Carbon\Carbon;
use MarcoMdMj\DataURI\DataURIManager;
use MarcoMdMj\MediaManager\Exceptions\MediaResourceException;

/**
 * File resource. Handles raw data.
 */
class Resource
{
    /**
     * Raw data of the file.
     * @var string
     */
    private $raw = null;

    /**
     * Filename.
     * @var string
     */
    private $filename = null;

    /**
     * Pathname.
     * @var string
     */
    private $pathname = null;

    /**
     * Extension of the file.
     * @var string
     */
    private $extension = null;

    /**
     * Mimetype
     * @var string
     */
    private $mimetype = null;

    /**
     * Set of supported mimetypes.
     * @var string
     */
    private $mimetypes;

    /**
     * Init the resource. Cannot be called directly because the Resource is
     * by definition inmutable. Must use static accessors to instantiate.
     * @param string      $raw
     * @param string|null $mimetype
     */
    private function __construct($raw, $mimetype = null)
    {
        $this->raw = $raw;
        $this->detectMimetype($mimetype);
    }

    /**
     * @param  Attempt to detect the resource mimetype
     * @return [type]
     */
    private function detectMimetype($default = null)
    {
        if ($mimetype = (new finfo(FILEINFO_MIME_TYPE))->buffer($this->raw())) {
            $this->validateMimetype($mimetype);
            return $this->mimetype = $mimetype;
        }

        if (!is_null($default)) {
            $this->validateMimetype($default);
            return $this->mimetype = $default;
        }
        
        throw new MediaResourceException('The mimetype of the loaded resource could not be detected.');
    }

    /**
     * Checks if the given $mimetype is supported. Throws an exception if not.
     * 
     * @param  string $mimetype
     * @throws MediaResourceException
     */
    private function validateMimetype($mimetype) 
    {
        $this->importSupportedMimeTypes();

        if (!array_key_exists($mimetype, $this->mimetypes)) {
            throw new MediaResourceException('The mime type of the loaded media resource [' .
                                             $mimetype . '] is not supported.');
        }
    }

    /**
     * Load the list of supported mimetypes from the config file.
     * @return array
     */
    private function importSupportedMimeTypes()
    {
        $this->mimetypes = config('mediamanager.mimetypes');
    }

    /**
     * Static function to init the resource by giving a string with the raw 
     * content of the file, and the default mimetype to be used.
     * @param  string $encoded_content
     * @param  string|null $mimetype
     * @return Resource
     */
    public static function fromRaw($encoded_content, $mimetype = null)
    {
        return new static($raw, $mimetype);
    }

    /**
     * Static function to init the resource by giving a base64 encoded string
     * as the raw content of the file, and the default mimetype to be used.
     * @param  string $encoded_content
     * @param  string|null $mimetype
     * @return Resource
     */
    public static function fromBase64($encoded_content, $mimetype = null)
    {
        return new static(base64_decode($encoded_content), $mimetype);
    }

    /**
     * Static function to init the resource by giving a full data uri.
     * @param  string $uri
     * @return Resource
     * @uses   DataUriManager
     */
    public static function fromDataUri($uri)
    {
        $dataURI = DataURIManager::decode($uri);

        $instance = new static(
            $dataURI->content_decoded(),
            $dataURI->mime()
        );

        return $instance;
    }

    /**
     * Get or set the filename.
     *
     * @param  string $filename
     * @param  string $extension
     * @return string|Resource
     */
    public function filename($filename = null, $extension = null)
    {
        if (is_null($filename)) {
            return $this->getFullFilename();
        }

        $this->filename = $filename;

        if (!is_null($extension)) {
            $this->extension = $extension;
        }

        return $this;
    }

    /**
     * Get the filename of the resource (including extension).
     * @return string
     */
    private function getFullFilename()
    {
        if (is_null($this->filename)) {
            $this->filename = str_random(16);
        }

        return $this->filename . $this->getSuffixFilename() . '.' . $this->extension();
    }

    /**
     * Return the parsed suffix (if any) from the config file.
     * @return string
     */
    private function getSuffixFilename()
    {
        if ($suffix = config('mediamanager.suffix')) {
            return Carbon::now()->formatLocalized($suffix);
        }

        return null;
    }

    /**
     * Get or set (or guess) the extension.
     * @param  string|null $extension
     * @return string|Resource
     */
    public function extension($extension = null)
    {
        if (!is_null($extension)) {
            $this->extension = $extension;
            return $this;
        }

        if (!is_null($this->extension)) {
            return $this->extension;
        }

        return $this->extension = $this->getExtensionFromMimeType();
    }

    /**
     * Get the proper extension based on the mimetype.
     * @return string
     */
    private function getExtensionFromMimeType()
    {
        return $this->mimetypes[$this->mimetype];
    }

    /**
     * Get or set the pathname of the resource.
     * @param  string|null $pathname
     * @return string|Resource
     */
    public function pathname($pathname = null)
    {
        if (is_null($pathname)) {
            return $this->pathname;
        }

        $this->pathname = '/' . trim($pathname, '/');

        return $this;
    }

    /**
     * Get pathname and filename of the file.
     * @return string
     */
    public function path()
    {
        return $this->pathname() . '/' . $this->filename();
    }

    /**
     * Get the raw content of the file.
     * @return string
     */
    public function raw()
    {
        return $this->raw;
    }

    /**
     * Get the mimetype.
     * @param  string|null $mimetype
     * @return string|Resource
     */
    public function mimetype()
    {
        return $this->mimetype;
    }
}
