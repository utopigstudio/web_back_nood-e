<?php

namespace App;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use \Illuminate\Support\Facades\Log;

trait ImageTrait
{

    public static $ACCEPTED_IMAGE_MIME_TYPES = ['image/gif','image/jpeg','image/png','image/x-png','image/webp'];
    public static $MIME_TYPE_EXTENSION = [
        'image/gif' => 'gif',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/x-png' => 'png',
        'image/webp' => 'webp',
    ];

    public $stored = 'public';

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->image_fields)) {
            $this->setImageAttribute($value, $key);
            return $this;
        }
        return parent::setAttribute($key, $value);
    }

    // image fields in this array will return the image url when accessed with their name.
    // To get the actual stored field value they must be accessed via "_{$field_name}_rawfieldvalue"
    public function getAttribute($key)
    {
        if (substr($key, 0, 1) == '_' && Str::endsWith($key,'_rawfieldvalue')) {
            $original_fieldname = substr($key, 1, -14);
            return parent::getAttribute($original_fieldname);
        }

        if (in_array($key, $this->image_fields) && $this->getAttributeValue($key)) {
            if (array_key_exists($key, $this->attributes) || $this->hasGetMutator($key)) {
                return Storage::disk($this->stored);
            }
        }
        return parent::getAttribute($key);
    }

    public function setImageAttribute($value, $key = 'image')
    {
        $attribute_name = $key;
        $prefix = isset($this->image_prefixes[$key]) ? $this->image_prefixes[$key] : ($key . '-');

        // if the image was erased
        if ($value == null) {
            // delete the image from disk
            // \Storage::disk($disk)->delete($this->{$attribute_name});

            // set null in the database column
            $this->attributes[$attribute_name] = null;

            return;
        }

        //if a new image was loaded
        if (Str::startsWith($value, 'data:image')) {
            $imageName = $this->uploadImage($value, $prefix);
            if(!$imageName) {
                throw new \Exception("The file type is not supported.");
                return;
            }
            $this->attributes[$attribute_name] = $imageName;
            return;
        }

        // set the value directly if we are setting a new image directly
        if (!Str::startsWith($value, config('app.images_storage_read_path'))) {
            $this->attributes[$attribute_name] = $value;
            return;
        }

        // remove the path if we are copying a value from another model (or saving this same model without changing the value)
        if (Str::startsWith($value, config('app.images_storage_read_path'))) {
            $this->attributes[$attribute_name] = substr($value, strlen(config('app.images_storage_read_path')));
            return;
        }
    }

    public function uploadImage($value, $prefix)
    {
        $disk = $this->stored;
        $destination_path = ''; // must be empty or end with a "/"

        $mime_type = mime_content_type($value);
        $accepted_mime_types = self::$ACCEPTED_IMAGE_MIME_TYPES;
        if (!in_array($mime_type, $accepted_mime_types)) {
            Log::error('User uploaded a file of type '.$mime_type.' but accepted types are '.implode(',', $accepted_mime_types));
            return false;
        }

        if ($mime_type == 'image/gif') {
            $data = substr($value, strpos($value, ',') + 1);
            $processed_contents = base64_decode($data);
        } else {
            $processed_contents = $this->resampleImage($value);
        }

        $filename = $prefix . md5($value.time()).'.'.last(explode('.', self::$MIME_TYPE_EXTENSION[$mime_type]));
        Storage::disk($disk)->put($destination_path . $filename, $processed_contents);

        return $destination_path . $filename;
    }

    /**
     * @param $file_contents
     * @return \Psr\Http\Message\StreamInterface
     */
    public function resampleImage($file_contents)
    {
        $higher_quality = false;

        $max_res = $higher_quality ? 4000 : 2600;
        $quality = $higher_quality ? 90 : 80;
        $image = Image::read($file_contents);

        // only resize if width larger than max_res
        $w = $image->width();
        $h = $image->height();
        if ($w > $max_res || $h > $max_res) {
            // resize the longest side and keep aspect ratio for the other one.
            $max_res_w = ($w > $h) ? $max_res : null;
            $max_res_h = ($w > $h) ? null : $max_res;
            // resize the image to a width of $max_res and constrain aspect ratio (auto height)
            $image = $image->resize($max_res_w, $max_res_h, function ($constraint) {
                $constraint->aspectRatio();
            });
        }
        return $image->toJpeg()->toFilePointer(null, $quality);  // use format to reconvert the image.
    }
    public function storeBase64Image($base64Image, $directory = 'images')
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
            $type = strtolower($type[1]); 

            if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
                throw new HttpResponseException(response()->json(['error' => 'Invalid image type'], 400));
            }

            $base64Image = str_replace(' ', '+', $base64Image);
            $image = base64_decode($base64Image);

            if ($image === false) {
                throw new HttpResponseException(response()->json(['error' => 'Base64 decode failed'], 400));
            }

            $filename = uniqid() . '.' . $type;

            $path = $directory . '/' . $filename;
            Storage::disk('public')->put($path, $image);

            return $path;
        }

        throw new HttpResponseException(response()->json(['error' => 'Invalid base64 image format'], 400));
    }

    public function deleteImage($image)
    {
        if (Storage::disk($this->stored)->exists($image)) {
            return Storage::disk($this->stored)->delete($image);
        }

        return false;
    }

    public function updateImage($image, $newImage, $prefix)
    {
        if ($newImage) {
            $this->deleteImage($image);
            return $this->uploadImage($newImage, $prefix);
        }

        return $image;
    }
    
}
