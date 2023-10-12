<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2021 xt:Commerce GmbH All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # https://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce GmbH, www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce GmbH, Maximilianstrasse 9, 6020 Innsbruck
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

defined('_VALID_CALL') or die('Direct Access is not allowed.');

use Rah\Danpu\Dump;
use Rah\Danpu\Exception;
use Rah\Danpu\Export;

class xt_dump_export extends Export
{
    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->config->disableAutoCommit(true);
        $this->config->disableForeignKeyChecks(true);

        $this->connect();
        $this->tmpFile();
        $this->open($this->temp, 'wb');
        $this->getTables();
        //$this->lock();
        $this->write("SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
");
        $this->dump();
        $this->unlock();
        $this->close();
        $this->move();
    }

    protected function dump()
    {
        $this->write('-- '.date('c').' - '.$this->config->dsn, false);

        if ($this->config->disableAutoCommit === true) {
            $this->write('SET AUTOCOMMIT = 0');
        }

        if ($this->config->disableForeignKeyChecks === true) {
            $this->write('SET FOREIGN_KEY_CHECKS = 0');
        }

        if ($this->config->disableUniqueKeyChecks === true) {
            $this->write('SET UNIQUE_CHECKS = 0');
        }

        if ($this->config->createDatabase === true) {
            $this->write(
                'CREATE DATABASE IF NOT EXISTS `'.$this->database.'` '.
                'DEFAULT CHARACTER SET = '.$this->escape($this->config->encoding)
            );
            $this->write('USE `'.$this->database.'`');
        }

        $this->dumpTables();
        $this->dumpViews();
        $this->dumpTriggers();
        //$this->dumpEvents();

        if ($this->config->disableForeignKeyChecks === true) {
            $this->write('SET FOREIGN_KEY_CHECKS = 1');
        }

        if ($this->config->disableUniqueKeyChecks === true) {
            $this->write('SET UNIQUE_CHECKS = 1');
        }

        if ($this->config->disableAutoCommit === true) {
            $this->write('COMMIT');
            $this->write('SET AUTOCOMMIT = 1');
        }

        $this->write("\n-- Completed on: ".date('c'), false);
    }

    /**
     * @inheritDoc
     */
    protected function tmpFile()
    {
        if (($this->temp = tempnam($this->config->tmp, 'tmp_')) === false) {
            throw new Exception('Unable to create a temporary file, check the configured tmp directory.');
        }
    }

    protected function lock()
    {
        global $db;

        $this->tables->execute();
        $table = array();

        while ($a = $this->tables->fetch(\PDO::FETCH_ASSOC)) {
            $table[] = current($a);
        }

        foreach($table as $t)
        {
            $db->Execute('LOCK TABLE `'.$t.'` WRITE');
        }

        return !$table || count($table);
    }
}