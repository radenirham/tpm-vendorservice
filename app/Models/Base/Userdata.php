<?php

namespace App\Models\Base;

class Userdata
{
    private $vendorUserId;
    private $vendorUserUuid;
    private $vendorUserName;
    private $vendorUserPassword;
    private $vendorUserFullname;
    private $vendorUuid;
    private $vendorCode;
    private $vendorName;
    private $vendorTaxNumber;
    private $vendorCompanyType;
    private $vendorCompanyTypeName;
    private $vendorArea;
    private $vendorAreaName;
    private $vendorCountryCode;
    private $vendorCountryName;
    private $vendorStatus;
    private $vendorFilledStatus;

    public function setVendorUserId($value) { $this->vendorUserId = $value; }
    public function setVendorUserUuid($value) { $this->vendorUserUuid = $value; }
    public function setVendorUserName($value) { $this->vendorUserName = $value; }
    public function setVendorUserPassword($value) { $this->vendorUserPassword = $value; }
    public function setVendorUserFullname($value) { $this->vendorUserFullname = $value; }
    public function setVendorUuid($value) { $this->vendorUuid = $value; }
    public function setVendorCode($value) { $this->vendorCode = $value; }
    public function setVendorName($value) { $this->vendorName = $value; }
    public function setVendorTaxNumber($value) { $this->vendorTaxNumber = $value; }
    public function setVendorCompanyType($value) { $this->vendorCompanyType = $value; }
    public function setVendorCompanyTypeName($value) { $this->vendorCompanyTypeName = $value; }
    public function setVendorArea($value) { $this->vendorArea = $value; }
    public function setVendorAreaName($value) { $this->vendorAreaName = $value; }
    public function setVendorCountryCode($value) { $this->vendorCountryCode = $value; }
    public function setVendorCountryName($value) { $this->vendorCountryName = $value; }
    public function setVendorStatus($value) { $this->vendorStatus = $value; }
    public function setVendorFilledStatus($value) { $this->vendorFilledStatus = $value; }

    public function getVendorUserId() { return $this->vendorUserId; }
    public function getVendorUserUuid() { return $this->vendorUserUuid; }
    public function getVendorUserName() { return $this->vendorUserName; }
    public function getVendorUserPassword() { return $this->vendorUserPassword; }
    public function getVendorUserFullname() { return $this->vendorUserFullname; }
    public function getVendorUuid() { return $this->vendorUuid; }
    public function getVendorCode() { return $this->vendorCode; }
    public function getVendorName() { return $this->vendorName; }
    public function getVendorTaxNumber() { return $this->vendorTaxNumber; }
    public function getVendorCompanyType() { return $this->vendorCompanyType; }
    public function getVendorCompanyTypeName() { return $this->vendorCompanyTypeName; }
    public function getVendorArea() { return $this->vendorArea; }
    public function getVendorAreaName() { return $this->vendorAreaName; }
    public function getVendorCountryCode() { return $this->vendorCountryCode; }
    public function getVendorCountryName() { return $this->vendorCountryName; }
    public function getVendorStatus() { return $this->vendorStatus; }
    public function getVendorFilledStatus() { return $this->vendorFilledStatus; }

    public function __set($key, $value)
    {
        switch ($key) {
            case 'vendorUserId': return $this->setVendorUserId($value);
            case 'vendorUserUuid': return $this->setVendorUserUuid($value);
            case 'vendorUserName': return $this->setVendorUserName($value);
            case 'vendorUserPassword': return $this->setVendorUserPassword($value);
            case 'vendorUserFullname': return $this->setVendorUserFullname($value);
            case 'vendorUuid': return $this->setVendorUuid($value);
            case 'vendorCode': return $this->setVendorCode($value);
            case 'vendorName': return $this->setVendorName($value);
            case 'vendorTaxNumber': return $this->setVendorTaxNumber($value);
            case 'vendorCompanyType': return $this->setVendorCompanyType($value);
            case 'vendorCompanyTypeName': return $this->setVendorCompanyTypeName($value);
            case 'vendorArea': return $this->setVendorArea($value);
            case 'vendorAreaName': return $this->setVendorAreaName($value);
            case 'vendorCountryCode': return $this->setVendorCountryCode($value);
            case 'vendorCountryName': return $this->setVendorCountryName($value);
            case 'vendorStatus': return $this->setVendorStatus($value);
            case 'vendorFilledStatus': return $this->setVendorFilledStatus($value);
        }
    }

    public function __get($name)
    {
        switch ($name) {
            case 'vendorUserId': return $this->getVendorUserId();
            case 'vendorUserUuid': return $this->getVendorUserUuid();
            case 'vendorUserName': return $this->getVendorUserName();
            case 'vendorUserPassword': return $this->getVendorUserPassword();
            case 'vendorUserFullname': return $this->getVendorUserFullname();
            case 'vendorUuid': return $this->getVendorUuid();
            case 'vendorCode': return $this->getVendorCode();
            case 'vendorName': return $this->getVendorName();
            case 'vendorTaxNumber': return $this->getVendorTaxNumber();
            case 'vendorCompanyType': return $this->getVendorCompanyType();
            case 'vendorCompanyTypeName': return $this->getVendorCompanyTypeName();
            case 'vendorArea': return $this->getVendorArea();
            case 'vendorAreaName': return $this->getVendorAreaName();
            case 'vendorCountryCode': return $this->getVendorCountryCode();
            case 'vendorCountryName': return $this->getVendorCountryName();
            case 'vendorStatus': return $this->getVendorStatus();
            case 'vendorFilledStatus': return $this->getVendorFilledStatus();
        }
    }
}
?>
