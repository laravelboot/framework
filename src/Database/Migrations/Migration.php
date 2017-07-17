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

use Illuminate\Database\ConnectionInterface;

/**
 * Class Migration.
 */
abstract class Migration
{
    /**
     * @var \Illuminate\Database\ConnectionInterface
     */
    protected $connection;

    /**
     * @var \Illuminate\Database\Schema\Builder
     */
    protected $schema;

    /**
     * Migration constructor.
     *
     * @param \Illuminate\Database\ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
        $this->schema = call_user_func([
            $connection,
            'getSchemaBuilder',
        ]);
    }

    /**
     * Migration's down handler.
     *
     * @return mixed
     */
    abstract public function down();

    /**
     * Get connection instance.
     *
     * @return string
     */
    public function getConnection()
    {
        return '';
    }

    /**
     * Migration's up handler.
     *
     * @return mixed
     */
    abstract public function up();
}
