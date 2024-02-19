<?php


namespace App\Services\FileUpload;

use App\Services\FileUpload\Trait\ResizeImage;
use Exception;
use Illuminate\Database\Eloquent\Model;


class FileUploadMediaService
{

    use ResizeImage;
    /**
     * @var
     */
    private
        $model,
        $media,
        $mediaCollection = [],
        $file,
        $fileName,
        $image,
        $disk = 'public',
        $files = [];

/*
 * FileService($file)->store()
 * */

    public function make($file)
    {
        if (is_iterable($file)) {
            foreach ($file as $item){
                $this->files[] = (new FileUploadService())->make($item);
            }
        } else {
            $this->file = (new FileUploadService())->make($file);
        }

        return $this;
    }

    /**
     * @param Model $model
     * @return $this
     */
    public function setModel(Model $model)
    {
        $this->model = $model;

        $this->setMedia();

        return $this;
    }

   /**
     * @param string $path # Not real path just folder name
     * @param string $disk # ['local', 'public', 's3', ...]
     */
    public function store($collection = 'default')
    {
        if ($this->mediaCollection){
            $this->storeMany($collection, $this->disk);
            return;
        }

        $this->media->toMediaCollection($collection, $this->disk);
    }

    protected function setMedia()
    {
        if ($this->files) {
            $this->setMultiMedia();

            return $this;
        }

        $this->fileName = $this->file->store();

        $this->media = $this->model->addMedia($this->file->getFilePath());
    }

    protected function setMultiMedia()
    {
        foreach ($this->files as $file){
            $file->store();
            $this->mediaCollection[] = $this->model->addMedia($file->getFilePath());
        }
    }

    protected function storeMany($collection, $disk)
    {
        foreach ($this->mediaCollection as $media){
            $media->toMediaCollection($collection, $disk);
        }
    }


    /**
     * @param string $disk
     * @return FileUploadMediaService
     */
    public function disk($disk)
    {
        $this->disk = $disk;

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
     * @return mixed
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param $name
     * @param $arguments
     * @return $this
     */
    public function __call($name, $arguments)
    {
        try{
            if ($this->mediaCollection){
                foreach ($this->mediaCollection as $media) {
                    $media->$name(...$arguments);
                }
            }else{
                $this->media?->$name(...$arguments);
            }

        }catch(Exception $e){

        }

        try{
            if ($this->files){
                foreach ($this->files as $file) {
                    $file->$name(...$arguments);
                }
            }else{
                $this->file?->$name(...$arguments);
            }
        }catch(Exception $e){

        }

        return $this;
    }

}
