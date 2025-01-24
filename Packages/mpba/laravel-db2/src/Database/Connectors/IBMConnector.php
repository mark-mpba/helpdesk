<?php

namespace mpba\DB2\Database\Connectors;

/**
 * Class IBMConnector
 */
class IBMConnector extends DB2Connector
{
    /**
     * @return string
     */
    protected function getDsn(array $config)
    {
        $dsn = "ibm:DRIVER={$config['driverName']};DATABASE={$config['database']};HOSTNAME={$config['host']};PORT={$config['port']};PROTOCOL=TCPIP;";

        return $dsn;
    }
}
