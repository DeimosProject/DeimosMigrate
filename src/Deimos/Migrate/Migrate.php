<?php

namespace Deimos\Migrate;

use Deimos\ORM\Builder;
use Deimos\ORM\Reflection;

class Migrate
{

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var string
     */
    protected $path;

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;

        $this->init();
    }

    /**
     * init database
     */
    protected function init()
    {
        $tableName = Reflection::getTableName(Model::class);

        $this->builder->rawQuery("CREATE TABLE IF NOT EXISTS `{$tableName}` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `item` VARCHAR(400) NOT NULL,
            PRIMARY KEY (`id`),
            INDEX `itemIndex` (`item`)
        )
        COLLATE='utf8_general_ci'
        ENGINE=InnoDB;");
    }

    /**
     * @param $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return \RegexIterator
     */
    public function scan()
    {
        $path = realpath($this->path);

        $recursiveDirectoryIterator = new \RecursiveDirectoryIterator($path);

        $iterator      = new \RecursiveIteratorIterator($recursiveDirectoryIterator, \RecursiveIteratorIterator::SELF_FIRST);
        $regexIterator = new \RegexIterator($iterator, '/^.+\.sql$/i', \RecursiveRegexIterator::GET_MATCH);

        return $regexIterator;
    }

    /**
     * @param $filename
     *
     * @return mixed
     */
    public function filename($filename)
    {
        $filename = str_replace($this->path, '', $filename);
        $filename = str_replace('\\', '/', $filename);

        return trim($filename, '\\/');
    }

    /**
     * @param $filename
     *
     * @return bool
     */
    public function isMake($filename)
    {
        return $this->builder->queryEntity(Model::class)
                ->where('item', $filename)
                ->count() > 0;
    }

    /**
     * @return array
     */
    public function run()
    {
        $result  = true;
        $storage = [];

        foreach ($this->scan() as $item => $value)
        {
            $path = realpath($item);

            $filename = $this->filename($item);

            if (!$this->isMake($filename))
            {
                $result = false;

                $sql = file_get_contents($path);

                $this->builder->rawQuery($sql);

                $error = $this->builder->connection()->errorInfo();

                $isSave = 0;

                if ($error[0] === '00000' || $error[0] === '01000')
                {
                    $model = $this->builder->createEntity(Model::class);

                    $model->item = $filename;

                    $isSave = $model->save();
                }

                if ($isSave)
                {
                    $storage[] = $filename . ' -- commit';
                }
                else
                {
                    $storage[] = $filename . ' -- error';
                }
            }
        }

        if ($result)
        {
            $storage[] = 'Already on latest version!';
        }

        return $storage;
    }

}