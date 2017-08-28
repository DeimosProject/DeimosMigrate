<?php

namespace Deimos\Migrate;

use Deimos\ORM\Builder;
use Deimos\ORM\Transaction;

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
        $tableName = $this->builder->reflection()->getTableName(Model::class);

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
        $this->path = str_replace('\\', '/', $path);
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

        $files = iterator_to_array($regexIterator);

        uksort($files, function ($a, $b)
        {
            return strnatcmp($a, $b);
        });

        return $files;
    }

    /**
     * @param $filename
     *
     * @return mixed
     */
    public function filename($filename)
    {
        $filename = str_replace('\\', '/', $filename);
        $filename = str_replace($this->path, '', $filename);

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
     *
     * @throws \InvalidArgumentException
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

                $isSave = 0;

                if ($this->builder->transaction()->state() === Transaction::STATE_COMMIT)
                {
                    $model = $this->builder->createEntity(Model::class);

                    $model->item = $filename;

                    $isSave = $model->save();
                }

                $storage[] = [(bool)$isSave, $filename];
            }
        }

        if ($result)
        {
            $storage[] = [true, 'Already on latest version!'];
        }

        return $storage;
    }

}
