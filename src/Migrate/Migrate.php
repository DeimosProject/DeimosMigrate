<?php

namespace Deimos\Migrate;

use Deimos\Database\Database;
use Deimos\ORM\Builder;
use Deimos\ORM\ORM;
use Deimos\ORM\Transaction;

class Migrate
{

    /**
     * @var ORM
     */
    protected $orm;

    /**
     * @var string
     */
    protected $path;

    /**
     * Migrate constructor.
     *
     * @param ORM $orm
     */
    public function __construct(ORM $orm)
    {
        $this->orm = $orm;
        $this->init();
    }

    /**
     * init database
     */
    protected function init()
    {
        $this->orm->register('migrate', \Deimos\Migrate\Model::class);
        $table = $this->orm->mapTable('migrate');

        $this->orm->database()->rawQuery("CREATE TABLE IF NOT EXISTS `{$table}` (
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
        return $this->orm->repository('migrate')
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
                $transaction = $this->orm->database()->transaction();
                $sql         = file_get_contents($path);
                $result      = false;
                $isSave      = 0;

                $transaction->call(function (Database $database) use ($sql)
                {
                    return $database->exec($sql);
                });

                if ($transaction->state() === \Deimos\Database\Transaction::STATE_COMMIT)
                {
                    $isSave = $model = $this->orm->create(
                            'migrate',
                            ['item' => $filename]
                        )->id() > 0;
                }

                if (!$isSave)
                {
                    $storage[] = $filename . ' -- error';
                    break;
                }

                $storage[] = $filename . ' -- commit';
            }
        }

        if ($result)
        {
            $storage[] = 'Already on latest version!';
        }

        return $storage;
    }

}
