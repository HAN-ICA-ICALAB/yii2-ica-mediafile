<?php

/**
 * Given the path (or url) to an image, fetch the image and convert it to PNG 
 * data.
 *
 * Usage:
 * $converter = new ImageToPngConverter();
 * $converter->setPath($path);
 * $converter->convert();
 * $pngData = $converter->getPngData();
 *
 * If the path does not contain a file type extension, you must call the 
 * setType method before you call the convert method.
 *
 */

namespace icalab\mediafile\components;

use Yii;
use yii\base\Exception;

class ImageToPngConverter
{
    protected $path = null;
    protected $imageData = null;
    protected $pngData = null;

    protected $type = null;

    /**
     * Manually set the type of the supplied image.
     * @param requestedType the mime type of the image to convert
     */
    public function setType($requestedType)
    {
        if($requestedType == 'image/jpeg' || $requestedType == 'jpg' || $requestedType == 'jpeg')
        {
            $this->type = 'jpg';
        }
        elseif($requestedType == 'image/png' || $requestedType == 'png')
        {
            $this->type = 'png';
        }
        elseif($requestedType == 'image/gif' || $requestedType == 'gif')
        {
            $this->type = 'gif';
        }
        else
        {
            throw new Exception("Unknown image type $requestedType");
        }
    }

    /**
     * Set type type of the supplied image based on its file name.
     */
    private function determineImageTypeFromPath($path = null)
    {
        if(! $path )
        {
            $path = $this->path;
        }
        if(! $path )
        {
            throw new Exception("Unable to determine image type from empty path.");
        }

     // Attempt to determine the original image type.
        $imageType = null;
        // If we're dealing with an external image, use curl to determine the 
        // type as per http://stackoverflow.com/questions/2610713/get-mime-type-of-external-file-using-curl-and-php
        if(preg_match('/^https*:/i', $path))
        {
            $type = '';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $path);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_NOBODY, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $results = explode("\n", trim(curl_exec($ch)));
            foreach($results as $line)
            {
                if (strtok($line, ':') == 'Content-Type') {
                    $parts = explode(":", $line);
                    $type = trim($parts[1]);
                }
            }

            if($type != 'image/jpeg' && $type != 'image/png' && $type != 'image/gif')
            {
                throw new Exception("Request for image with unknown type '$type' for item with path '" . $this->path . "' and type '".$type."'.");
            }

            if($type == 'image/jpeg')
            {
                $imageType = 'jpg';
            }
            if($type == 'image/png')
            {
                $imageType = 'png';
            }
            if($type == 'image/gif')
            {
                $imageType = 'gif';
            }
        }
        // We're dealing with a local path. Guess image type based on the extension.
        else
        {
            if(! preg_match('/\.([^\.]+)$/', $path, $matches))
            {
                throw new Exception("Unable to determine file type extension in path " . $this->path);
            }
            $extension = strtolower(trim($matches[1]));
            if($extension == 'jpeg' || $extension == 'jpg')
            {
                $imageType = 'jpg';
            }
            if($extension == 'png')
            {
                $imageType = 'png';
            }
            if($extension == 'gif')
            {
                $imageType = 'gif';
            }
        }

        if(! $imageType)
        {
            throw new Exception("Unable to determine file type from path " . $path);
        }

        $this->type = $imageType;

    }


    /**
     * Convert the image at the supplied path to png data.
     */
    public function convert()
    {
        if($this->path === null)
        {
            throw new Exception("Unable to convert image if path is not set.");
        }

        try 
        {
            $this->loadData();
            $this->convertData();
        }
        catch(Exception $e)
        {
            throw new Exception("Unable to convert image: " . $e->getMessage());
        }
    }

    /**
     * Load the data at the supplied path.
     */
    protected function loadData()
    {
        if($this->path === null)
        {
            throw new Exception("Unable to load image if path is not set.");
        }

        // Read in the original image data.
        $imageType = $this->type;
        if(! $imageType)
        {
            $this->determineImageTypeFromPath();
            $imageType = $this->type;
            if(! $imageType)
            {
                throw new Exception("Unable to load data as image type could not be determined.");
            }
        }
        try
        {
            if($imageType == 'jpg')
            {
                $this->imageData = @imagecreatefromjpeg($this->path);
                if(! $this->imageData)
                {
                    throw new Exception("Unable to create JPEG image using GD");
                }
            }
            if($imageType == 'png')
            {
                $this->imageData = @imagecreatefrompng($this->path);
                if(! $this->imageData)
                {
                    throw new Exception("Unable to create PNG image using GD");
                }
                // PNG images can be transparent. We need to do some extra 
                // processing to preserve transparency.
                $background = @imagecolorallocate($this->imageData, 0, 0, 0);
                imagecolortransparent($this->imageData, $background);
                imagealphablending($this->imageData, false);
                imagesavealpha($this->imageData, true);
            }
            if($imageType == 'gif')
            {
                $this->imageData = @imagecreatefromgif($this->path);
                if(! $this->imageData)
                {
                    throw new Exception("Unable to create GIF image using GD");
                }
                // GIF images can be transparent.
                $background = @imagecolorallocate($this->imageData, 0, 0, 0);
                @imagecolortransparent($this->imageData, $background);
            }
            if(! $this->imageData)
            {
                throw new Exception("Unable to create image using GD");
            }
        }
        catch(Exception $e)
        {
            throw new Exception("Loading data from path " . $this->path . " failed: " . $e->getMessage());
        }

    }

    /**
     * Convert previously loaded data to PNG.
     */
    protected function convertData()
    {
        if($this->path === null)
        {
            throw new Exception("Unable to load image if path is not set.");
        }
        if(! $this->imageData)
        {
            throw new Exception("Unable to convert image without loaded image data.");
        }

        ob_start();
        imagepng($this->imageData, NULL, 9);
        $this->pngData = ob_get_contents();
        ob_end_clean();

    }

    /**
     * Getters and setters.
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getPngData()
    {
        return $this->pngData;
    }

}

