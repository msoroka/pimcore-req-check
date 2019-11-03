<?php

namespace App\Requirements;

class Database
{
    /**
     * @param  array  $config
     * @return array
     */
    public static function check(array $config)
    {
        $checks = [];

        $host = "{$config['host']}:{$config['port']}";
        $db = $config['name'];
        $user = $config['user'];
        $pass = $config['password'];
        $charset = 'utf8mb4';

        $options = [
            \PDO::ATTR_ERRMODE          => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        try {
            $db = new \PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }

        // storage engines
        $engines = $db->query('SHOW ENGINES;')->fetchAll(\PDO::FETCH_COLUMN, 0);

        // innodb
        $checks[] = new Check([
            'name'  => 'InnoDB Support',
            'state' => ($engines && in_array('InnoDB', $engines, true)) ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // myisam
        $checks[] = new Check([
            'name'  => 'MyISAM Support',
            'state' => ($engines && in_array('MyISAM', $engines, true)) ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // ARCHIVE
        $checks[] = new Check([
            'name'  => 'ARCHIVE Support',
            'state' => ($engines && in_array('ARCHIVE', $engines, true)) ? Check::STATE_OK : Check::STATE_WARNING,
        ]);

        // memory
        $checks[] = new Check([
            'name'  => 'MEMORY Support',
            'state' => ($engines && in_array('MEMORY', $engines, true)) ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // check database charset =>  utf-8 encoding
        $result = $db->query('SHOW VARIABLES LIKE "character\_set\_database"')->fetchAll()[0];

        $checks[] = new Check([
            'name'  => 'Database Charset utf8mb4',
            'state' => ($result && (strtolower($result['Value']) === 'utf8mb4')) ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // empty values are provided by MariaDB => 10.3
        $largePrefix = $db->query("SHOW GLOBAL VARIABLES LIKE 'innodb\_large\_prefix';")->fetchAll()[0];
        $checks[] = new Check([
            'name'  => 'innodb_large_prefix = ON ',
            'state' => ($largePrefix && !in_array(strtolower((string) $largePrefix['Value']), ['on', '1', ''],
                    true)) ? Check::STATE_ERROR : Check::STATE_OK,
        ]);

        $fileFormat = $db->query("SHOW GLOBAL VARIABLES LIKE 'innodb\_file\_format';")->fetchAll()[0];
        $checks[] = new Check([
            'name'  => 'innodb_file_format = Barracuda',
            'state' => ($fileFormat && (!empty($fileFormat['Value']) && strtolower($fileFormat['Value']) !== 'barracuda')) ? Check::STATE_ERROR : Check::STATE_OK,
        ]);


        $fileFilePerTable = $db->query("SHOW GLOBAL VARIABLES LIKE 'innodb\_file\_per\_table';")->fetchAll()[0];
        $checks[] = new Check([
            'name'  => 'innodb_file_per_table = ON',
            'state' => ($fileFilePerTable && !in_array(strtolower((string) $fileFilePerTable['Value']), ['on', '1'],
                    true)) ? Check::STATE_ERROR : Check::STATE_OK,
        ]);

        // create table
        $queryCheck = true;
        try {
            $db->exec('CREATE TABLE __pimcore_req_check (
                  id int(11) NOT NULL AUTO_INCREMENT,
                  field varchar(190) DEFAULT NULL,
                  PRIMARY KEY (id)
                ) DEFAULT CHARSET=utf8mb4;');
        } catch (\Exception $e) {
            $queryCheck = false;
        }

        $checks[] = new Check([
            'name'  => 'CREATE TABLE',
            'state' => $queryCheck ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // alter table
        $queryCheck = true;
        try {
            $db->exec('ALTER TABLE __pimcore_req_check ADD COLUMN alter_field varchar(190) NULL DEFAULT NULL');
        } catch (\Exception $e) {
            $queryCheck = false;
        }

        $checks[] = new Check([
            'name'  => 'ALTER TABLE',
            'state' => $queryCheck ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // Manage indexes
        $queryCheck = true;
        try {
            $db->exec('ALTER TABLE __pimcore_req_check
                          CHANGE COLUMN id id int(11) NOT NULL,
                          CHANGE COLUMN field field varchar(190) NULL DEFAULT NULL,
                          CHANGE COLUMN alter_field alter_field varchar(190) NULL DEFAULT NULL,
                          ADD KEY field (field),
                          DROP PRIMARY KEY ,
                         DEFAULT CHARSET=utf8mb4');

            $db->exec('ALTER TABLE __pimcore_req_check
                          CHANGE COLUMN id id int(11) NOT NULL AUTO_INCREMENT,
                          CHANGE COLUMN field field varchar(190) NULL DEFAULT NULL,
                          CHANGE COLUMN alter_field alter_field varchar(190) NULL DEFAULT NULL,
                          ADD PRIMARY KEY (id) ,
                         DEFAULT CHARSET=utf8mb4');
        } catch (\Exception $e) {
            $queryCheck = false;
        }

        $checks[] = new Check([
            'name'  => 'Manage Indexes',
            'state' => $queryCheck ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // Fulltext indexes
        $queryCheck = true;
        try {
            $db->exec('ALTER TABLE __pimcore_req_check ADD FULLTEXT INDEX `fulltextFieldIndex` (`field`)');
        } catch (\Exception $e) {
            $queryCheck = false;
        }

        $checks[] = new Check([
            'name'  => 'Fulltext Indexes',
            'state' => $queryCheck ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // insert data
        $queryCheck = true;
        try {
            $sql = "INSERT INTO __pimcore_req_check (field, alter_field) VALUES (?,?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([uniqid('', true), uniqid('', true)]);
        } catch (\Exception $e) {
            $queryCheck = false;
        }

        $checks[] = new Check([
            'name'  => 'INSERT',
            'state' => $queryCheck ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // update
        $queryCheck = true;
        try {
            $sql = "UPDATE __pimcore_req_check SET field=?, alter_field=? WHERE id=?";
            $stmt = $db->prepare($sql);
            $stmt->execute([uniqid('', true), uniqid('', true), uniqid('', true)]);
        } catch (\Exception $e) {
            $queryCheck = false;
        }

        $checks[] = new Check([
            'name'  => 'UPDATE',
            'state' => $queryCheck ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // select
        $queryCheck = true;
        try {
            $db->query('SELECT * FROM __pimcore_req_check')->fetchAll();
        } catch (\Exception $e) {
            $queryCheck = false;
        }

        $checks[] = new Check([
            'name'  => 'SELECT',
            'state' => $queryCheck ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // create view
        $queryCheck = true;
        try {
            $db->exec('CREATE OR REPLACE VIEW __pimcore_req_check_view AS SELECT * FROM __pimcore_req_check');
        } catch (\Exception $e) {
            $queryCheck = false;
        }

        $checks[] = new Check([
            'name'  => 'CREATE VIEW',
            'state' => $queryCheck ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // select from view
        $queryCheck = true;
        try {
            $db->query('SELECT * FROM __pimcore_req_check_view')->fetchAll();
        } catch (\Exception $e) {
            $queryCheck = false;
        }

        $checks[] = new Check([
            'name'  => 'SELECT (from view)',
            'state' => $queryCheck ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // delete
        $queryCheck = true;
        try {
            $sql = "DELETE FROM __pimcore_req_check WHERE id=?";
            $stmt = $db->prepare($sql);
            $stmt->execute([uniqid('', true)]);
        } catch (\Exception $e) {
            $queryCheck = false;
        }

        $checks[] = new Check([
            'name'  => 'DELETE',
            'state' => $queryCheck ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // show create view
        $queryCheck = true;
        try {
            $db->query('SHOW CREATE VIEW __pimcore_req_check_view')->fetchAll();
        } catch (\Exception $e) {
            $queryCheck = false;
        }

        $checks[] = new Check([
            'name'  => 'SHOW CREATE VIEW',
            'state' => $queryCheck ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // show create table
        $queryCheck = true;
        try {
            $db->query('SHOW CREATE TABLE __pimcore_req_check')->fetchAll();
        } catch (\Exception $e) {
            $queryCheck = false;
        }

        $checks[] = new Check([
            'name'  => 'SHOW CREATE TABLE',
            'state' => $queryCheck ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // drop view
        $queryCheck = true;
        try {
            $db->exec('DROP VIEW __pimcore_req_check_view');
        } catch (\Exception $e) {
            $queryCheck = false;
        }

        $checks[] = new Check([
            'name'  => 'DROP VIEW',
            'state' => $queryCheck ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // drop table
        $queryCheck = true;
        try {
            $db->exec('DROP TABLE __pimcore_req_check');
        } catch (\Exception $e) {
            $queryCheck = false;
        }

        $checks[] = new Check([
            'name'  => 'DROP TABLE',
            'state' => $queryCheck ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        return $checks;
    }
}