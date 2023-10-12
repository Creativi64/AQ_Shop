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

class SoapController {

    /**
     * Konstruktor der Klasse. Initialiesiert der SOAP Service
     * @param type $serviceName Name vom Service
     * @param type $serviceURL Namespace vom Service
     * @param type $serviceENDPOINT URL/Aufrufadresse vom Service
     */
    function __construct($serviceName, $serviceURL, $serviceENDPOINT) {

        // Übergebene Parameter werden in Klassenvariablen gemappt
        $this->serviceName = $serviceName;
        $this->serviceURL = $serviceURL;
        $this->serviceENDPOINT = $serviceENDPOINT;
    }

    /**
     * Intialisierung der Klasse
     * Hier wird der nusoap SOAP Server gestartet
     * und die WSDL Datei konfiguriert
     */
    function Init() {
        // Neuen nusoap SOap Server bauen
        $this->oServer = new xt_soap_server($wsdl = false);
        
        // Je nach XT CharEncoding dass encoding vom soap server setzen
        if (XT_API_CHARSET == 'ISO-8859-1') {
            $this->oServer->soap_defencoding = 'ISO-8859-1';
        }
        if (XT_API_CHARSET == 'UTF-8') {
            $this->oServer->soap_defencoding = 'UTF-8';
            $this->oServer->decode_utf8 = false;
        }

        // Die WSDL Datei des Soapservers konfigurieren
        // mit Name, Namespace und Enpunkt = URL vom service
        $this->oServer->configureWSDL($this->serviceName, $this->serviceURL, $this->serviceENDPOINT);
    }

    /**
     * Neue Datentyp(en) zur WSDL Definition hinzufügen
     * @param type $aTypesArray Asso. Array mit Beschreibung vom neuen Datentyp
     */
    function addTypes($aTypesArray) {
        // Ist $aTypesArray ein array?
        if (is_array($aTypesArray))
            // Über Datentypen-Infos in array laufen
            foreach ($aTypesArray as $aType) {
                $this->oServer->wsdl->addComplexType(
                        $aType['name'], 
                        $aType['typeClass'], 
                        $aType['phpType'], 
                        $aType['compositor'], 
                        $aType['restrictionBase'], 
                        $aType['elements'], 
                        $aType['attrs'], 
                        $aType['arrayType']
                );
            }
    }

    /**
     * Fügt dem SOAP Server die Methoden und Datenmappings aus Klasse SoapMappings::getMappings()
     * hinzu.Dort wird definiert, welche SOAP Methoden nach aussen angeboten werden und wie sie mit PHP
     * verwoben sind.
     * @param type $aMappings Array mit allen Mappings
     */
    function bindMappings($aMappings) {
        // ist ein array?
        if (is_array($aMappings))
            foreach ($aMappings as $sCallName => $aParams) {
                $this->oServer->register(
                        $sCallName, 
                        $aParams['in'], 
                        $aParams['out'], 
                        false, 
                        false, 
                        false, 
                        false, 
                        $aParams['documentation']
                );
            }
    }

    
    
    /**
     * 
     * Eine SOAP Anfrage verarbeiten
     * 
     * @param type $sRequest Die SOAP Anfrage.Der komplette request wird an nusoap übergeben
     */
    function process($sRequest) {
        // Request vearbeiten
        $this->oServer->service($sRequest);
    }

}

/**
 * extend nusoap server class for database call logging
 * Class xt_soap_server
 */

/*
NuSOAP - Web Services Toolkit for PHP

Copyright (c) 2002 NuSphere Corporation

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

The NuSOAP project home is:
http://sourceforge.net/projects/nusoap/

The primary support for NuSOAP is the Help forum on the project home page.

If you have any questions or comments, please email:

Dietrich Ayala
dietrich@ganx4.com
http://dietrich.ganx4.com/nusoap

NuSphere Corporation
http://www.nusphere.com

*/
class xt_soap_server extends nusoap_server {

    function parse_request($data = '')
    {
        $this->debug('entering parse_request()');
        $this->parse_http_headers();
        $this->debug('got character encoding: ' . $this->xml_encoding);
        // uncompress if necessary
        if (isset($this->headers['content-encoding']) && $this->headers['content-encoding'] != '') {
            $this->debug('got content encoding: ' . $this->headers['content-encoding']);
            if ($this->headers['content-encoding'] == 'deflate' || $this->headers['content-encoding'] == 'gzip') {
                // if decoding works, use it. else assume data wasn't gzencoded
                if (function_exists('gzuncompress')) {
                    if ($this->headers['content-encoding'] == 'deflate' && $degzdata = @gzuncompress($data)) {
                        $data = $degzdata;
                    } elseif ($this->headers['content-encoding'] == 'gzip' && $degzdata = gzinflate(substr($data, 10))) {
                        $data = $degzdata;
                    } else {
                        $this->fault('SOAP-ENV:Client', 'Errors occurred when trying to decode the data');
                        return;
                    }
                } else {
                    $this->fault('SOAP-ENV:Client', 'This Server does not support compressed data');
                    return;
                }
            }
        }
        $this->request .= "\r\n" . $data;
        SoapHelper::setRequest($data,'xml');
        $data = $this->parseRequest($this->headers, $data);
        $this->requestSOAP = $data;
        $this->debug('leaving parse_request');

    }

    function send_response()
    {

        $this->debug('Enter send_response');
        if ($this->fault) {
            $payload = $this->fault->serialize();
            $this->outgoing_headers[] = "HTTP/1.0 500 Internal Server Error";
            $this->outgoing_headers[] = "Status: 500 Internal Server Error";
        } else {
            $payload = $this->responseSOAP;
            // Some combinations of PHP+Web server allow the Status
            // to come through as a header.  Since OK is the default
            // just do nothing.
            // $this->outgoing_headers[] = "HTTP/1.0 200 OK";
            // $this->outgoing_headers[] = "Status: 200 OK";
        }


        SoapHelper::setResponse($payload);
        SoapHelper::logRequest('test');

        // add debug data if in debug mode
        if (isset($this->debug_flag) && $this->debug_flag) {
            $payload .= $this->getDebugAsXMLComment();
        }
        $this->outgoing_headers[] = "Server: $this->title Server v$this->version";
        preg_match('/\$Revisio' . 'n: ([^ ]+)/', $this->revision, $rev);
        $this->outgoing_headers[] = "X-SOAP-Server: $this->title/$this->version (" . $rev[1] . ")";
        // Let the Web server decide about this
        //$this->outgoing_headers[] = "Connection: Close\r\n";
        $payload = $this->getHTTPBody($payload);
        $type = $this->getHTTPContentType();
        $charset = $this->getHTTPContentTypeCharset();
        $this->outgoing_headers[] = "Content-Type: $type" . ($charset ? '; charset=' . $charset : '');
        //begin code to compress payload - by John
        // NOTE: there is no way to know whether the Web server will also compress
        // this data.
        if (strlen($payload) > 1024 && isset($this->headers) && isset($this->headers['accept-encoding'])) {
            if (strstr($this->headers['accept-encoding'], 'gzip')) {
                if (function_exists('gzencode')) {
                    if (isset($this->debug_flag) && $this->debug_flag) {
                        $payload .= "<!-- Content being gzipped -->";
                    }
                    $this->outgoing_headers[] = "Content-Encoding: gzip";
                    $payload = gzencode($payload);
                } else {
                    if (isset($this->debug_flag) && $this->debug_flag) {
                        $payload .= "<!-- Content will not be gzipped: no gzencode -->";
                    }
                }
            } elseif (strstr($this->headers['accept-encoding'], 'deflate')) {
                // Note: MSIE requires gzdeflate output (no Zlib header and checksum),
                // instead of gzcompress output,
                // which conflicts with HTTP 1.1 spec (http://www.w3.org/Protocols/rfc2616/rfc2616-sec3.html#sec3.5)
                if (function_exists('gzdeflate')) {
                    if (isset($this->debug_flag) && $this->debug_flag) {
                        $payload .= "<!-- Content being deflated -->";
                    }
                    $this->outgoing_headers[] = "Content-Encoding: deflate";
                    $payload = gzdeflate($payload);
                } else {
                    if (isset($this->debug_flag) && $this->debug_flag) {
                        $payload .= "<!-- Content will not be deflated: no gzcompress -->";
                    }
                }
            }
        }
        //end code
        $this->outgoing_headers[] = "Content-Length: " . strlen($payload);
        reset($this->outgoing_headers);
        foreach ($this->outgoing_headers as $hdr) {
            header($hdr, FALSE);
        }
        print $payload;
        $this->response = join("\r\n", $this->outgoing_headers) . "\r\n\r\n" . $payload;

    }

}