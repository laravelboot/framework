<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/17 10:48
 * @version
 */
namespace LaravelBoot\Foundation\Database\Migrations;

use Illuminate\Database\Migrations\DatabaseMigrationRepository as IlluminateDatabaseMigrationRepository;

/**
 * Class DatabaseMigrationRepository.
 */
class DatabaseMigrationRepository extends IlluminateDatabaseMigrationRepository
{
    /**
     * Get the last migration batch on path.
     *
     * @param $files
     *
     * @return array
     */
    public function getLastOnPath($files)
    {
        $query = $this->table()->whereIn('migration', $files);

        return $query->orderBy('migration', 'desc')->get()->all();
    }
}
