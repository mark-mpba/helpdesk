<?php

namespace mpba\DB2\Database;

use Illuminate\Database\Connection;
use Illuminate\Database\Grammar;
use mpba\DB2\Database\Query\Grammars\DB2Grammar as QueryGrammar;
use mpba\DB2\Database\Query\Processors\DB2Processor;
use mpba\DB2\Database\Query\Processors\DB2ZOSProcessor;
use mpba\DB2\Database\Schema\Builder;
use mpba\DB2\Database\Schema\Grammars\DB2ExpressCGrammar;
use mpba\DB2\Database\Schema\Grammars\DB2Grammar as SchemaGrammar;
use PDO;

/**
 * Class DB2Connection
 */
class DB2Connection extends Connection
{
    /**
     * The name of the default schema.
     *
     * @var string
     */
    protected $defaultSchema;

    /**
     * The name of the current schema in use.
     *
     * @var string
     */
    protected $currentSchema;

    public function __construct(PDO $pdo, $database = '', $tablePrefix = '', array $config = [])
    {
        parent::__construct($pdo, $database, $tablePrefix, $config);
        $this->currentSchema = $this->defaultSchema = strtoupper($config['schema'] ?? null);
    }

    /**
     * Get the name of the default schema.
     *
     * @return string
     */
    public function getDefaultSchema()
    {
        return $this->defaultSchema;
    }

    /**
     * Reset to default the current schema.
     *
     * @return string
     */
    public function resetCurrentSchema()
    {
        $this->setCurrentSchema($this->getDefaultSchema());
    }

    /**
     * Set the name of the current schema.
     *
     *
     * @return string
     */
    public function setCurrentSchema($schema)
    {
        $this->statement('SET SCHEMA ?', [strtoupper($schema)]);
    }

    /**
     * Execute a system command on IBMi.
     *
     *
     * @return string
     */
    public function executeCommand($command)
    {
        $this->statement('CALL QSYS2.QCMDEXC(?)', [$command]);
    }

    /**
     * Get a schema builder instance for the connection.
     *
     * @return Builder
     */
    public function getSchemaBuilder()
    {
        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }

        return new Builder($this);
    }

    /**
     * @return Grammar
     */
    protected function getDefaultQueryGrammar()
    {
        $defaultGrammar = new QueryGrammar;

        if (array_key_exists('date_format', $this->config)) {
            $defaultGrammar->setDateFormat($this->config['date_format']);
        }

        if (array_key_exists('offset_compatibility_mode', $this->config)) {
            $defaultGrammar->setOffsetCompatibilityMode($this->config['offset_compatibility_mode']);
        }

        return $this->withTablePrefix($defaultGrammar);
    }

    /**
     * Default grammar for specified Schema
     *
     * @return Grammar
     */
    protected function getDefaultSchemaGrammar()
    {
        switch ($this->config['driver']) {
            case 'db2_expressc_odbc':
                $defaultGrammar = $this->withTablePrefix(new DB2ExpressCGrammar);
                break;
            default:
                $defaultGrammar = $this->withTablePrefix(new SchemaGrammar);
                break;
        }

        return $defaultGrammar;
    }

    /**
     * Get the default post processor instance.
     *
     * @return DB2Processor|DB2ZOSProcessor
     */
    protected function getDefaultPostProcessor()
    {
        switch ($this->config['driver']) {
            case 'db2_zos_odbc':
                $defaultProcessor = new DB2ZOSProcessor;
                break;
            default:
                $defaultProcessor = new DB2Processor;
                break;
        }

        return $defaultProcessor;
    }
}
