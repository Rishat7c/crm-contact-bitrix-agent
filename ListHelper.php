<?php
/**
 * Created by PhpStorm.
 * User: Gaysin.R
 * Date: 11.04.2019
 * Time: 9:43
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

class ListHelper {

    function insertRow($cnt = 0, $lastId = 0) {

        CModule::IncludeModule('crm');

        $serverName = "192.168.0.10, 1433";
        $connectionInfo = array( "Database"=>"CRMContact", "UID"=>"agent", "PWD"=>"1!pass@Changeme");
        $conn = sqlsrv_connect( $serverName, $connectionInfo);

        if($conn) {
//            echo "Соединение удалось.<br>\n";
        }

        $sql = "SELECT count(*) FROM Customers";

        $stmt = sqlsrv_query($conn,$sql);

        if ( $stmt )
        {
//            echo "Запрос успешен.<br>\n";
        }

        if($row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)) {
            $cntRowArray = implode($row);
        }

        if($cntRowArray) {
            $cntRepet = $cntRowArray / 5000;
            $cntRepet = round($cntRepet);
        }

        if($cntRepet >= $cnt) {

            $sql = "SELECT * FROM Customers WHERE CustomerKey BETWEEN " . $lastId . " AND " . ($lastId + 5000);

            $stmt = sqlsrv_query($conn, $sql);

            if ($stmt) {
//            echo "Запрос успешен.<br>\n";
            }

            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

                $ct = new CCrmContact(false);
                $arParams = array('HAS_PHONE' => 'Y');

                $arParams['FM']['PHONE'] = array(
                    'n0' => array(
                        'VALUE_TYPE' => 'WORK',
                        'VALUE' => $row['Mobile'],
                    )
                );
                $arParams['FM']['EMAIL'] = array(
                    'n0' => array(
                        'VALUE_TYPE' => 'WORK',
                        'VALUE' => $row['email'],
                    )
                );

                $arParams['FULL_NAME'] = $row['Surname'];
                $arParams['FIRST_NAME'] = $row['FirstName'];
                $arParams['LAST_NAME'] = $row['Surname'];
                $arParams['HAS_EMAIL'] = 'Y';
                $arParams['HAS_PHONE'] = 'Y';
                $arParams['TYPE_ID'] = 'CLIENT';
                $arParams['SOURCE_ID'] = 'WEB';
                $arParams['OPENED'] = 'Y';
                $arParams['UF_CRM_1552299391'] = $row['AccountNo']; // Account Number
                $arParams['UF_CRM_1547175379744'] = $row['MeterNo']; // MeterNo
                $arParams['UF_CRM_1551874977'] = $row['Vat']; // Vat
                $arParams['UF_CRM_1551876065'] = $row['ArrearsBalance']; // Net Arreas
                $arParams['UF_CRM_1547175210085'] = $row['TariffID']; // UF_CRM_1547175025783

                $new_contact_id = $ct->Add($arParams, true, array('DISABLE_USER_FIELD_CHECK' => true));


                if ($new_contact_id) {
                    $lastId = $row['CustomerKey'];
                }

            }

            $cnt++;

        } else {

            return "";

        }

        unset($serverName);
        unset($connectionInfo);
        unset($conn);
        unset($sql);
        unset($stmt);
        unset($row);
        unset($cntRowArray);
        unset($cntRepet);
        unset($arParams);
        unset($new_contact_id);

        return "ListHelper::insertRow(". $cnt .",". $lastId .");";
    }

}
