<?php


namespace IIsmail\FileUpload\Classes;

use IIsmail\FileUpload\Traits\CreateDirectory;
use IIsmail\FileUpload\Traits\HashFileName;
use IIsmail\FileUpload\Traits\ResizeImage;
use Exception;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class FileUploadService
{

    use ResizeImage, CreateDirectory, HashFileName;

    /**
     * @var
     */
    private
        $file,
        $fullFileName,
        $filePath,
        $quality,
        $image,
        $extension,
        $fileName = null,
        $disk = 'public';

    /**
     * FileUploadService constructor.
     * @param $file
     * @return FileUploadService
     */
    public function make($file)
    {
        $this->file = is_string($file) ? new File($file) : $file;

        if (Str::contains($file->getMimeType(), 'image')) {
            $this->image = Image::make($file);
        }

        $this->quality = config('file-upload.quality');

        $this->extension = config('file-upload.extension');

        return $this;
    }

    /**
     * @param string $path # Not real path just folder name
     * @param string $disk # ['local', 'public', 's3', ...]
     * @return string      # file name
     */
    public function store($path = '')
    {
        $fileName = $this->getFullFileName();

        if ($this->image) {

            $this->resizeImage($this->image);

            return $this->storeAsImage($path, $this->disk, $fileName);
        }

        $this->fullFileName = $this->file->storeAs($path, $fileName, $this->disk);

        // Create folder if not exists, or abort uploading
        $this->createDirectoryIfNotExists($path, $this->disk);

        $this->filePath = Storage::disk($this->disk)->path($this->fullFileName);

        return $this->fullFileName;
    }

    /**
     * @param $path
     * @param $disk
     * @return string
     */
    public function storeAsImage($path, $disk, $fileName)
    {
        $this->fullFileName = "{$path}/{$fileName}";
        
        // Create folder if not exists, or abort uploading
        $this->createDirectoryIfNotExists($path, $disk);

        $this->filePath = Storage::disk($disk)->path($this->fullFileName);

        $this->image->save($this->filePath, $this->quality);

        return $this->fullFileName;
    }

    /**
     * @param $oldFile
     * @return FileUploadService
     */
    public function delete($oldFile)
    {
        if ($oldFile && Storage::disk($this->disk)->exists($oldFile)) {
            Storage::disk($this->disk)->delete($oldFile);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @return string
     */
    protected function getFileName()
    {
        return $this->fileName ?? $this->hashFileName();
    }

    /**
     * @return string
     */
    protected function getExtension()
    {
        if ($this->image && $this->extension) {
            return $this->extension;
        }

        return $this->file->extension();
    }

    /**
     * @return string
     */
    protected function getFullFileName()
    {
        return "{$this->getFileName()}.{$this->getExtension()}";
    }

     /**
     * @param string $fileName
     * @return FileUploadService
     */
    public function fileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @param string $disk
     * @return FileUploadService
     */
    public function disk($disk)
    {
        $this->disk = $disk;

        return $this;
    }

    /**
     * @param string $extension
     * @return FileUploadService
     */
    public function extension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     *
     * @return string
     */
    public function getDisk()
    {
        return $this->disk;
    }

    /**
     *  magic method to call all methods in Intervention\Image package
     * @param $name
     * @param $arguments
     * @return FileUploadService
     */
    public function __call($name, $arguments)
    {
        try{
            $this->image?->$name(...$arguments);
        }catch(Exception $e){

        }

        return $this;
    }

}
