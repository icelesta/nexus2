-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: nexus2
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_log`
--

DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `log_name` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `event` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) unsigned DEFAULT NULL,
  `causer_type` varchar(255) DEFAULT NULL,
  `causer_id` bigint(20) unsigned DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `batch_uuid` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject` (`subject_type`,`subject_id`),
  KEY `causer` (`causer_type`,`causer_id`),
  KEY `activity_log_log_name_index` (`log_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `assignment_material_requisition_items`
--

DROP TABLE IF EXISTS `assignment_material_requisition_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assignment_material_requisition_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `assignment_material_requisition_id` bigint(20) unsigned NOT NULL,
  `purchase_requisition_item_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned DEFAULT NULL,
  `item_code` varchar(100) DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `item_description` text DEFAULT NULL,
  `specification` text DEFAULT NULL,
  `uom_id` bigint(20) unsigned DEFAULT NULL,
  `uom_code` varchar(50) DEFAULT NULL,
  `uom_name` varchar(100) DEFAULT NULL,
  `requested_qty` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `approved_qty` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `remaining_qty` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `warehouse_id` bigint(20) unsigned DEFAULT NULL,
  `warehouse_code` varchar(50) DEFAULT NULL,
  `warehouse_name` varchar(150) DEFAULT NULL,
  `required_date` date DEFAULT NULL,
  `delivery_location` varchar(255) DEFAULT NULL,
  `supplier_id` bigint(20) unsigned DEFAULT NULL,
  `assigned_qty` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `unit_price` decimal(18,2) NOT NULL DEFAULT 0.00,
  `quotation_number` varchar(100) DEFAULT NULL,
  `quotation_date` date DEFAULT NULL,
  `lead_time_days` int(11) DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `discount_percent` decimal(8,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `tax_id` bigint(20) unsigned DEFAULT NULL,
  `tax_name` varchar(100) DEFAULT NULL,
  `gross_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `tax_percent` decimal(8,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(18,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(18,2) NOT NULL DEFAULT 0.00,
  `is_selected_supplier` tinyint(1) NOT NULL DEFAULT 0,
  `buyer_notes` text DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'Draft',
  `approval_status` varchar(30) NOT NULL DEFAULT 'Pending',
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_amri_created_by` (`created_by`),
  KEY `fk_amri_updated_by` (`updated_by`),
  KEY `fk_amri_deleted_by` (`deleted_by`),
  KEY `idx_amri_pri` (`purchase_requisition_item_id`),
  KEY `idx_amri_supplier` (`supplier_id`),
  KEY `idx_amri_status` (`status`),
  KEY `idx_amri_approval` (`approval_status`),
  KEY `idx_amri_delivery` (`delivery_date`),
  KEY `idx_amri_amr_status` (`assignment_material_requisition_id`,`status`),
  KEY `idx_amri_supplier_status` (`supplier_id`,`status`),
  KEY `fk_assignment_item_tax` (`tax_id`),
  CONSTRAINT `fk_amri_assignment` FOREIGN KEY (`assignment_material_requisition_id`) REFERENCES `assignment_material_requisitions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_amri_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_amri_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_amri_pr_item` FOREIGN KEY (`purchase_requisition_item_id`) REFERENCES `purchase_requisition_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_amri_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_amri_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_assignment_item_tax` FOREIGN KEY (`tax_id`) REFERENCES `tax_masters` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `assignment_material_requisitions`
--

DROP TABLE IF EXISTS `assignment_material_requisitions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assignment_material_requisitions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `document_no` varchar(50) DEFAULT NULL,
  `document_date` date DEFAULT NULL,
  `purchase_requisition_id` bigint(20) unsigned NOT NULL,
  `purchase_order_id` bigint(20) unsigned DEFAULT NULL,
  `pr_number` varchar(50) DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `business_unit_id` bigint(20) unsigned DEFAULT NULL,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `section_id` bigint(20) unsigned DEFAULT NULL,
  `cost_center_id` bigint(20) unsigned DEFAULT NULL,
  `warehouse_id` bigint(20) unsigned DEFAULT NULL,
  `shipping_address_id` bigint(20) unsigned DEFAULT NULL,
  `currency_id` bigint(20) unsigned DEFAULT NULL,
  `exchange_rate` decimal(18,6) NOT NULL DEFAULT 1.000000,
  `requester_id` bigint(20) unsigned DEFAULT NULL,
  `request_date` date DEFAULT NULL,
  `required_date` date DEFAULT NULL,
  `priority` varchar(30) DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `delivery_location` text DEFAULT NULL,
  `assigned_to` bigint(20) unsigned DEFAULT NULL,
  `assigned_by` bigint(20) unsigned DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'draft',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_amr_purchase_requisition` (`purchase_requisition_id`),
  KEY `fk_amr_company` (`company_id`),
  KEY `fk_amr_business_unit` (`business_unit_id`),
  KEY `fk_amr_branch` (`branch_id`),
  KEY `fk_amr_department` (`department_id`),
  KEY `fk_amr_section` (`section_id`),
  KEY `fk_amr_cost_center` (`cost_center_id`),
  KEY `fk_amr_warehouse` (`warehouse_id`),
  KEY `fk_amr_requester` (`requester_id`),
  KEY `fk_amr_assigned_to` (`assigned_to`),
  KEY `fk_amr_assigned_by` (`assigned_by`),
  KEY `fk_amr_created_by` (`created_by`),
  KEY `fk_amr_updated_by` (`updated_by`),
  KEY `fk_amr_deleted_by` (`deleted_by`),
  KEY `fk_assignment_purchase_order` (`purchase_order_id`),
  KEY `idx_amr_currency` (`currency_id`),
  KEY `idx_assignment_shipping_address` (`shipping_address_id`),
  CONSTRAINT `assignment_material_requisitions_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `assignment_material_requisitions_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `assignment_material_requisitions_purchase_requisition_id_foreign` FOREIGN KEY (`purchase_requisition_id`) REFERENCES `purchase_requisitions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_amr_assigned_by` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_amr_assigned_to` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_amr_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_amr_business_unit` FOREIGN KEY (`business_unit_id`) REFERENCES `business_units` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_amr_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_amr_cost_center` FOREIGN KEY (`cost_center_id`) REFERENCES `cost_centers` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_amr_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_amr_currency` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_amr_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_amr_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_amr_purchase_requisition` FOREIGN KEY (`purchase_requisition_id`) REFERENCES `purchase_requisitions` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_amr_requester` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_amr_section` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_amr_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_amr_warehouse` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_assignment_purchase_order` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`),
  CONSTRAINT `fk_assignment_shipping_address` FOREIGN KEY (`shipping_address_id`) REFERENCES `shipping_addresses` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bank_accounts`
--

DROP TABLE IF EXISTS `bank_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bank_accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `business_unit_id` bigint(20) unsigned DEFAULT NULL,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `bank_code` varchar(30) NOT NULL,
  `bank_name` varchar(150) NOT NULL,
  `account_name` varchar(200) NOT NULL,
  `account_no` varchar(100) NOT NULL,
  `currency_id` bigint(20) unsigned DEFAULT NULL,
  `swift_code` varchar(50) DEFAULT NULL,
  `iban` varchar(100) DEFAULT NULL,
  `coa_id` bigint(20) unsigned DEFAULT NULL,
  `branch_name` varchar(150) DEFAULT NULL,
  `opening_balance` decimal(18,2) DEFAULT 0.00,
  `account_type` enum('Cash','Bank') NOT NULL DEFAULT 'Bank',
  `payment_method` enum('Cash','Transfer','Cheque','Giro','VirtualAccount','QRIS') NOT NULL DEFAULT 'Transfer',
  `current_balance` decimal(18,2) DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `allow_payment` tinyint(1) NOT NULL DEFAULT 1,
  `allow_receipt` tinyint(1) NOT NULL DEFAULT 1,
  `allow_transfer` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `bank_code` (`bank_code`),
  KEY `idx_bank_company` (`company_id`),
  KEY `idx_bank_branch` (`branch_id`),
  KEY `idx_bank_active` (`is_active`),
  KEY `fk_bank_currency` (`currency_id`),
  KEY `fk_bank_coa` (`coa_id`),
  CONSTRAINT `fk_bank_coa` FOREIGN KEY (`coa_id`) REFERENCES `chart_of_accounts` (`id`),
  CONSTRAINT `fk_bank_currency` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `business_unit_id` bigint(20) unsigned DEFAULT NULL,
  `branch_code` varchar(20) NOT NULL,
  `branch_name` varchar(200) NOT NULL,
  `short_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `fax` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) NOT NULL DEFAULT 'Indonesia',
  `manager_name` varchar(150) DEFAULT NULL,
  `is_head_branch` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_branch_uuid` (`uuid`),
  UNIQUE KEY `uk_branch_code` (`branch_code`),
  UNIQUE KEY `uk_branch_company` (`company_id`,`business_unit_id`,`branch_code`),
  KEY `idx_company` (`company_id`),
  KEY `idx_business_unit` (`business_unit_id`),
  KEY `idx_branch_name` (`branch_name`),
  KEY `idx_branch_city` (`city`),
  KEY `idx_branch_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `brands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `brand_code` varchar(30) NOT NULL,
  `brand_name` varchar(150) NOT NULL,
  `short_name` varchar(50) DEFAULT NULL,
  `manufacturer_name` varchar(150) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `brand_code` (`brand_code`),
  KEY `idx_brand_code` (`brand_code`),
  KEY `idx_brand_name` (`brand_name`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `business_units`
--

DROP TABLE IF EXISTS `business_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `business_units` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `business_unit_code` varchar(20) NOT NULL,
  `business_unit_name` varchar(200) NOT NULL,
  `short_name` varchar(100) DEFAULT NULL,
  `manager_id` bigint(20) unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `manager_name` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 1,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_business_unit_uuid` (`uuid`),
  UNIQUE KEY `uk_business_unit_code` (`business_unit_code`),
  KEY `idx_company` (`company_id`),
  KEY `idx_business_unit_name` (`business_unit_name`),
  KEY `idx_business_unit_active` (`is_active`),
  KEY `idx_business_unit_sort` (`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chart_of_accounts`
--

DROP TABLE IF EXISTS `chart_of_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chart_of_accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `account_code` varchar(30) NOT NULL,
  `account_name` varchar(200) NOT NULL,
  `parent_account_id` bigint(20) unsigned DEFAULT NULL,
  `account_type` varchar(30) NOT NULL,
  `normal_balance` varchar(10) NOT NULL,
  `currency_id` bigint(20) unsigned DEFAULT NULL,
  `is_control_account` tinyint(1) NOT NULL DEFAULT 0,
  `allow_manual_entry` tinyint(1) NOT NULL DEFAULT 1,
  `is_cash_account` tinyint(1) NOT NULL DEFAULT 0,
  `is_bank_account` tinyint(1) NOT NULL DEFAULT 0,
  `is_tax_account` tinyint(1) NOT NULL DEFAULT 0,
  `is_retained_earning` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `allow_posting` tinyint(1) NOT NULL DEFAULT 1,
  `require_cost_center` tinyint(1) NOT NULL DEFAULT 0,
  `require_profit_center` tinyint(1) NOT NULL DEFAULT 0,
  `require_department` tinyint(1) NOT NULL DEFAULT 0,
  `require_project` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_coa_uuid` (`uuid`),
  UNIQUE KEY `uk_company_account` (`company_id`,`account_code`),
  KEY `idx_parent` (`parent_account_id`),
  KEY `idx_account_type` (`account_type`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `companies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `company_code` varchar(20) NOT NULL,
  `company_name` varchar(200) NOT NULL,
  `legal_name` varchar(255) DEFAULT NULL,
  `short_name` varchar(100) DEFAULT NULL,
  `tax_id` varchar(50) DEFAULT NULL COMMENT 'NPWP',
  `business_license` varchar(100) DEFAULT NULL COMMENT 'NIB / SIUP',
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `fax` varchar(50) DEFAULT NULL,
  `website` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) NOT NULL DEFAULT 'Indonesia',
  `currency_code` varchar(10) NOT NULL DEFAULT 'IDR',
  `timezone` varchar(100) NOT NULL DEFAULT 'Asia/Jakarta',
  `locale` varchar(10) NOT NULL DEFAULT 'id',
  `logo` varchar(255) DEFAULT NULL,
  `is_head_office` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_company_uuid` (`uuid`),
  UNIQUE KEY `uk_company_code` (`company_code`),
  KEY `idx_company_name` (`company_name`),
  KEY `idx_company_country` (`country`),
  KEY `idx_company_active` (`is_active`),
  KEY `idx_company_created_by` (`created_by`),
  KEY `idx_company_updated_by` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cost_centers`
--

DROP TABLE IF EXISTS `cost_centers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cost_centers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `business_unit_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `section_id` bigint(20) unsigned DEFAULT NULL,
  `cost_center_code` varchar(30) NOT NULL,
  `cost_center_name` varchar(200) NOT NULL,
  `short_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `manager_name` varchar(150) DEFAULT NULL,
  `budget_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `sort_order` int(11) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_cost_center_uuid` (`uuid`),
  UNIQUE KEY `uk_cost_center_code` (`company_id`,`business_unit_id`,`branch_id`,`cost_center_code`),
  KEY `idx_company` (`company_id`),
  KEY `idx_business_unit` (`business_unit_id`),
  KEY `idx_branch` (`branch_id`),
  KEY `idx_department` (`department_id`),
  KEY `idx_cost_center_name` (`cost_center_name`),
  KEY `idx_cost_center_active` (`is_active`),
  KEY `idx_section_id` (`section_id`),
  CONSTRAINT `fk_cost_center_section` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `currencies`
--

DROP TABLE IF EXISTS `currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `currencies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `currency_code` char(3) NOT NULL,
  `currency_name` varchar(100) NOT NULL,
  `symbol` varchar(10) NOT NULL,
  `numeric_code` smallint(6) DEFAULT NULL,
  `country_name` varchar(100) DEFAULT NULL,
  `decimal_places` tinyint(4) NOT NULL DEFAULT 2,
  `thousand_separator` varchar(2) NOT NULL DEFAULT ',',
  `decimal_separator` varchar(2) NOT NULL DEFAULT '.',
  `rounding_precision` decimal(10,4) NOT NULL DEFAULT 0.0100,
  `exchange_rate` decimal(18,8) NOT NULL DEFAULT 1.00000000,
  `is_base_currency` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 1,
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_currency_uuid` (`uuid`),
  UNIQUE KEY `uk_currency_code` (`currency_code`),
  KEY `idx_currency_name` (`currency_name`),
  KEY `idx_base_currency` (`is_base_currency`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL DEFAULT 1,
  `customer_category_id` bigint(20) unsigned DEFAULT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `customer_code` varchar(50) NOT NULL,
  `customer_name` varchar(200) NOT NULL,
  `legal_name` varchar(255) DEFAULT NULL,
  `short_name` varchar(100) DEFAULT NULL,
  `customer_type` enum('Corporate','Government','Individual','Affiliate','Partner') NOT NULL DEFAULT 'Corporate',
  `industry` varchar(150) DEFAULT NULL,
  `tax_id` varchar(50) DEFAULT NULL,
  `tax_number` varchar(100) DEFAULT NULL,
  `business_license` varchar(100) DEFAULT NULL,
  `payment_term_id` bigint(20) unsigned DEFAULT NULL,
  `default_payment_term_id` bigint(20) unsigned DEFAULT NULL,
  `currency_code` varchar(10) NOT NULL DEFAULT 'IDR',
  `currency_id` bigint(20) unsigned DEFAULT NULL,
  `credit_limit` decimal(18,2) NOT NULL DEFAULT 0.00,
  `credit_days` int(11) NOT NULL DEFAULT 30,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `mobile` varchar(50) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) NOT NULL DEFAULT 'Indonesia',
  `customer_rating` decimal(4,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `pic_name` varchar(150) DEFAULT NULL,
  `pic_position` varchar(100) DEFAULT NULL,
  `ar_account_id` bigint(20) unsigned DEFAULT NULL,
  `is_preferred` tinyint(1) NOT NULL DEFAULT 0,
  `is_blacklisted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_customer_uuid` (`uuid`),
  UNIQUE KEY `uk_customer_code` (`customer_code`),
  KEY `idx_company` (`company_id`),
  KEY `idx_category` (`customer_category_id`),
  KEY `idx_payment_term` (`payment_term_id`),
  KEY `idx_customer_name` (`customer_name`),
  KEY `idx_customer_type` (`customer_type`),
  KEY `idx_customer_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_customers_before_insert` BEFORE INSERT ON `customers` FOR EACH ROW BEGIN
    IF NEW.uuid IS NULL OR NEW.uuid = '' THEN
        SET NEW.uuid = UUID();
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `business_unit_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `department_code` varchar(20) NOT NULL,
  `department_name` varchar(200) NOT NULL,
  `short_name` varchar(100) DEFAULT NULL,
  `manager_name` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_department_uuid` (`uuid`),
  UNIQUE KEY `uk_department_code` (`company_id`,`business_unit_id`,`branch_id`,`department_code`),
  KEY `idx_company` (`company_id`),
  KEY `idx_business_unit` (`business_unit_id`),
  KEY `idx_branch` (`branch_id`),
  KEY `idx_department_name` (`department_name`),
  KEY `idx_department_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `exchange_rates`
--

DROP TABLE IF EXISTS `exchange_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exchange_rates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `from_currency_id` bigint(20) unsigned NOT NULL,
  `to_currency_id` bigint(20) unsigned NOT NULL,
  `exchange_date` date NOT NULL,
  `rate_type` varchar(30) NOT NULL DEFAULT 'Spot',
  `buy_rate` decimal(18,8) NOT NULL,
  `sell_rate` decimal(18,8) NOT NULL,
  `middle_rate` decimal(18,8) NOT NULL,
  `source` varchar(100) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_exchange_rate` (`from_currency_id`,`to_currency_id`,`exchange_date`),
  UNIQUE KEY `uk_exchange_uuid` (`uuid`),
  KEY `idx_exchange_date` (`exchange_date`),
  KEY `idx_from_currency` (`from_currency_id`),
  KEY `idx_to_currency` (`to_currency_id`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fiscal_periods`
--

DROP TABLE IF EXISTS `fiscal_periods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fiscal_periods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `fiscal_year_id` bigint(20) unsigned NOT NULL,
  `period_no` tinyint(3) unsigned NOT NULL,
  `period_name` varchar(30) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Open',
  `is_closed` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_period` (`fiscal_year_id`,`period_no`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fiscal_years`
--

DROP TABLE IF EXISTS `fiscal_years`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fiscal_years` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `fiscal_code` varchar(20) NOT NULL,
  `fiscal_name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `periods` tinyint(3) unsigned NOT NULL DEFAULT 12,
  `status` varchar(20) NOT NULL DEFAULT 'Open',
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_fiscal_uuid` (`uuid`),
  UNIQUE KEY `uk_company_fiscal` (`company_id`,`fiscal_code`),
  KEY `idx_company` (`company_id`),
  KEY `idx_status` (`status`),
  KEY `idx_default` (`is_default`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `goods_receipt_items`
--

DROP TABLE IF EXISTS `goods_receipt_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `goods_receipt_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `goods_receipt_id` bigint(20) unsigned NOT NULL,
  `purchase_order_item_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `received_qty` decimal(18,4) NOT NULL,
  `uom_id` bigint(20) unsigned NOT NULL,
  `warehouse_id` bigint(20) unsigned NOT NULL,
  `accepted_qty` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `rejected_qty` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `goods_receipt_items_created_by_foreign` (`created_by`),
  KEY `goods_receipt_items_updated_by_foreign` (`updated_by`),
  KEY `goods_receipt_items_deleted_by_foreign` (`deleted_by`),
  KEY `idx_gri_grn` (`goods_receipt_id`),
  KEY `idx_gri_po_item` (`purchase_order_item_id`),
  KEY `idx_gri_item` (`item_id`),
  KEY `idx_gri_warehouse` (`warehouse_id`),
  KEY `idx_gri_uom` (`uom_id`),
  CONSTRAINT `goods_receipt_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `goods_receipt_items_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `goods_receipt_items_goods_receipt_id_foreign` FOREIGN KEY (`goods_receipt_id`) REFERENCES `goods_receipts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `goods_receipt_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `goods_receipt_items_purchase_order_item_id_foreign` FOREIGN KEY (`purchase_order_item_id`) REFERENCES `purchase_order_items` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `goods_receipt_items_uom_id_foreign` FOREIGN KEY (`uom_id`) REFERENCES `uoms` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `goods_receipt_items_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `goods_receipt_items_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `goods_receipts`
--

DROP TABLE IF EXISTS `goods_receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `goods_receipts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `grn_no` varchar(50) NOT NULL,
  `purchase_order_id` bigint(20) unsigned NOT NULL,
  `supplier_id` bigint(20) unsigned NOT NULL,
  `receipt_date` date NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'Draft',
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `goods_receipts_uuid_unique` (`uuid`),
  UNIQUE KEY `goods_receipts_grn_no_unique` (`grn_no`),
  KEY `goods_receipts_created_by_foreign` (`created_by`),
  KEY `goods_receipts_updated_by_foreign` (`updated_by`),
  KEY `goods_receipts_deleted_by_foreign` (`deleted_by`),
  KEY `idx_grn_po` (`purchase_order_id`),
  KEY `idx_grn_supplier` (`supplier_id`),
  KEY `idx_grn_status` (`status`),
  KEY `idx_grn_receipt_date` (`receipt_date`),
  CONSTRAINT `goods_receipts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `goods_receipts_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `goods_receipts_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `goods_receipts_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `goods_receipts_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `item_attachments`
--

DROP TABLE IF EXISTS `item_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_attachments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20) unsigned NOT NULL,
  `document_type` varchar(100) DEFAULT NULL,
  `document_title` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `revision_no` varchar(30) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `item_barcodes`
--

DROP TABLE IF EXISTS `item_barcodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_barcodes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20) unsigned NOT NULL,
  `barcode` varchar(150) NOT NULL,
  `barcode_type` varchar(30) DEFAULT 'CODE128',
  `is_default` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `barcode` (`barcode`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `item_batches`
--

DROP TABLE IF EXISTS `item_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_batches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20) unsigned NOT NULL,
  `batch_number` varchar(100) NOT NULL,
  `manufacture_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `quantity` decimal(18,4) DEFAULT 0.0000,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_batch` (`item_id`,`batch_number`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `item_categories`
--

DROP TABLE IF EXISTS `item_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `category_code` varchar(30) NOT NULL,
  `category_name` varchar(150) NOT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_item_categories_uuid` (`uuid`),
  UNIQUE KEY `uk_item_categories_code` (`category_code`),
  KEY `idx_item_categories_name` (`category_name`),
  KEY `idx_item_categories_parent` (`parent_id`),
  KEY `idx_item_categories_active` (`is_active`),
  KEY `idx_item_categories_sort` (`sort_order`),
  CONSTRAINT `fk_item_categories_parent` FOREIGN KEY (`parent_id`) REFERENCES `item_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `item_images`
--

DROP TABLE IF EXISTS `item_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_images` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) DEFAULT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `image_name` varchar(255) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `fk_item_images_created_by` (`created_by`),
  KEY `fk_item_images_updated_by` (`updated_by`),
  KEY `fk_item_images_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_item_images_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_item_images_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_item_images_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `item_prices`
--

DROP TABLE IF EXISTS `item_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_prices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `item_supplier_id` bigint(20) unsigned DEFAULT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `price_type` varchar(30) NOT NULL,
  `currency_id` bigint(20) unsigned NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `price` decimal(18,4) NOT NULL,
  `effective_date` date NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_item_prices_uuid` (`uuid`),
  KEY `item_id` (`item_id`),
  KEY `fk_item_prices_created_by` (`created_by`),
  KEY `fk_item_prices_updated_by` (`updated_by`),
  KEY `fk_item_prices_deleted_by` (`deleted_by`),
  KEY `idx_item_price_item` (`item_id`),
  KEY `idx_item_price_type` (`price_type`),
  KEY `idx_item_price_effective` (`effective_date`),
  KEY `idx_item_price_active` (`is_active`),
  CONSTRAINT `fk_item_prices_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_item_prices_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_item_prices_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `item_serials`
--

DROP TABLE IF EXISTS `item_serials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_serials` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20) unsigned NOT NULL,
  `serial_number` varchar(150) NOT NULL,
  `warehouse_id` bigint(20) unsigned DEFAULT NULL,
  `status` varchar(30) DEFAULT 'Available',
  PRIMARY KEY (`id`),
  UNIQUE KEY `serial_number` (`serial_number`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `item_specifications`
--

DROP TABLE IF EXISTS `item_specifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_specifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20) unsigned NOT NULL,
  `specification_name` varchar(150) NOT NULL,
  `specification_value` text DEFAULT NULL,
  `uom` varchar(50) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `item_stocks`
--

DROP TABLE IF EXISTS `item_stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_stocks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20) unsigned NOT NULL,
  `warehouse_id` bigint(20) unsigned NOT NULL,
  `qty_on_hand` decimal(18,4) DEFAULT 0.0000,
  `qty_reserved` decimal(18,4) DEFAULT 0.0000,
  `qty_available` decimal(18,4) DEFAULT 0.0000,
  `last_stock_take` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_item_wh` (`item_id`,`warehouse_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `item_suppliers`
--

DROP TABLE IF EXISTS `item_suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_suppliers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) DEFAULT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `supplier_id` bigint(20) unsigned NOT NULL,
  `purchase_uom_id` bigint(20) unsigned DEFAULT NULL,
  `supplier_item_code` varchar(100) DEFAULT NULL,
  `supplier_item_name` varchar(255) DEFAULT NULL,
  `purchase_description` text DEFAULT NULL,
  `is_preferred` tinyint(1) DEFAULT 0,
  `supplier_priority` int(11) NOT NULL DEFAULT 1,
  `lead_time_days` int(11) DEFAULT 0,
  `minimum_order_qty` decimal(18,4) DEFAULT 0.0000,
  `purchase_multiple` decimal(18,4) NOT NULL DEFAULT 1.0000,
  `default_tax_id` bigint(20) unsigned DEFAULT NULL,
  `default_discount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `last_purchase_price` decimal(18,4) DEFAULT NULL,
  `last_purchase_date` date DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_item_supplier_uuid` (`uuid`),
  KEY `item_id` (`item_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `fk_item_supplier_purchase_uom` (`purchase_uom_id`),
  KEY `fk_item_supplier_tax` (`default_tax_id`),
  KEY `fk_item_supplier_created_by` (`created_by`),
  KEY `fk_item_supplier_updated_by` (`updated_by`),
  KEY `idx_item_supplier_item` (`item_id`),
  KEY `idx_item_supplier_supplier` (`supplier_id`),
  KEY `idx_item_supplier_preferred` (`is_preferred`),
  KEY `idx_item_supplier_active` (`is_active`),
  CONSTRAINT `fk_item_supplier_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_item_supplier_purchase_uom` FOREIGN KEY (`purchase_uom_id`) REFERENCES `uoms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_item_supplier_tax` FOREIGN KEY (`default_tax_id`) REFERENCES `tax_masters` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_item_supplier_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `item_code` varchar(50) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `short_name` varchar(100) DEFAULT NULL,
  `search_name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `brand_id` bigint(20) unsigned DEFAULT NULL,
  `manufacturer_id` bigint(20) unsigned DEFAULT NULL,
  `base_uom_id` bigint(20) unsigned NOT NULL,
  `item_type` varchar(50) NOT NULL DEFAULT 'Inventory',
  `stock_type` varchar(30) DEFAULT 'Stock',
  `minimum_stock` decimal(18,4) DEFAULT 0.0000,
  `maximum_stock` decimal(18,4) DEFAULT 0.0000,
  `reorder_level` decimal(18,4) DEFAULT 0.0000,
  `reorder_quantity` decimal(18,4) DEFAULT 0.0000,
  `safety_stock` decimal(18,4) DEFAULT 0.0000,
  `economic_order_qty` decimal(18,4) DEFAULT 0.0000,
  `allow_negative_stock` tinyint(1) NOT NULL DEFAULT 0,
  `cycle_count_required` tinyint(1) NOT NULL DEFAULT 0,
  `quality_inspection_required` tinyint(1) NOT NULL DEFAULT 0,
  `is_quality_control` tinyint(1) NOT NULL DEFAULT 0,
  `is_returnable` tinyint(1) NOT NULL DEFAULT 1,
  `is_expirable` tinyint(1) NOT NULL DEFAULT 0,
  `is_hazardous` tinyint(1) NOT NULL DEFAULT 0,
  `is_consignment` tinyint(1) NOT NULL DEFAULT 0,
  `requires_certificate` tinyint(1) NOT NULL DEFAULT 0,
  `lead_time_days` int(10) unsigned DEFAULT 0,
  `default_warehouse_id` bigint(20) unsigned DEFAULT NULL,
  `warehouse_type_id` bigint(20) unsigned DEFAULT NULL,
  `purchase_uom_id` bigint(20) unsigned DEFAULT NULL,
  `sales_uom_id` bigint(20) unsigned DEFAULT NULL,
  `purchase_currency_id` bigint(20) unsigned DEFAULT NULL,
  `sales_currency_id` bigint(20) unsigned DEFAULT NULL,
  `inventory_account_id` bigint(20) unsigned DEFAULT NULL,
  `expense_account_id` bigint(20) unsigned DEFAULT NULL,
  `income_account_id` bigint(20) unsigned DEFAULT NULL,
  `country_of_origin` varchar(100) DEFAULT NULL,
  `hs_code` varchar(50) DEFAULT NULL,
  `part_number` varchar(100) DEFAULT NULL,
  `model_number` varchar(100) DEFAULT NULL,
  `drawing_number` varchar(100) DEFAULT NULL,
  `unique_number` varchar(150) DEFAULT NULL,
  `revision_number` varchar(30) DEFAULT NULL,
  `net_weight` decimal(18,4) DEFAULT 0.0000,
  `gross_weight` decimal(18,4) DEFAULT 0.0000,
  `length` decimal(18,4) DEFAULT 0.0000,
  `width` decimal(18,4) DEFAULT 0.0000,
  `height` decimal(18,4) DEFAULT 0.0000,
  `volume` decimal(18,4) DEFAULT 0.0000,
  `is_serialized` tinyint(1) DEFAULT 0,
  `is_batch_tracked` tinyint(1) DEFAULT 0,
  `is_barcoded` tinyint(1) DEFAULT 1,
  `is_qrcode` tinyint(1) DEFAULT 1,
  `is_inventory` tinyint(1) DEFAULT 1,
  `is_purchase` tinyint(1) DEFAULT 1,
  `is_subcontract` tinyint(1) NOT NULL DEFAULT 0,
  `default_purchase_lead_time` int(11) NOT NULL DEFAULT 0,
  `purchase_tolerance` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `minimum_purchase_qty` decimal(18,4) NOT NULL DEFAULT 1.0000,
  `purchase_requires_approval` tinyint(1) NOT NULL DEFAULT 0,
  `is_sales` tinyint(1) DEFAULT 0,
  `allow_discount` tinyint(1) NOT NULL DEFAULT 1,
  `minimum_sales_qty` decimal(18,4) NOT NULL DEFAULT 1.0000,
  `sales_multiple` decimal(18,4) NOT NULL DEFAULT 1.0000,
  `allow_backorder` tinyint(1) NOT NULL DEFAULT 0,
  `requires_serial_sales` tinyint(1) NOT NULL DEFAULT 0,
  `is_asset` tinyint(1) DEFAULT 0,
  `is_service` tinyint(1) DEFAULT 0,
  `is_manufacturing` tinyint(1) DEFAULT 0,
  `is_rental` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `remarks` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `barcode_type` varchar(30) NOT NULL DEFAULT 'CODE128',
  `barcode_printed` tinyint(1) NOT NULL DEFAULT 0,
  `qr_code` varchar(255) DEFAULT NULL,
  `qr_code_type` varchar(30) NOT NULL DEFAULT 'ITEM',
  `qr_printed` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_uuid` (`uuid`),
  UNIQUE KEY `uk_code` (`item_code`),
  KEY `idx_name` (`item_name`),
  KEY `idx_category` (`category_id`),
  KEY `idx_brand` (`brand_id`),
  KEY `idx_manufacturer` (`manufacturer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `journal_types`
--

DROP TABLE IF EXISTS `journal_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `journal_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `journal_types_uuid_unique` (`uuid`),
  UNIQUE KEY `journal_types_code_unique` (`code`),
  KEY `journal_types_created_by_foreign` (`created_by`),
  KEY `journal_types_updated_by_foreign` (`updated_by`),
  KEY `journal_types_deleted_by_foreign` (`deleted_by`),
  KEY `journal_types_code_index` (`code`),
  KEY `journal_types_name_index` (`name`),
  KEY `journal_types_is_active_index` (`is_active`),
  KEY `journal_types_is_system_index` (`is_system`),
  CONSTRAINT `journal_types_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `journal_types_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `journal_types_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `manufacturers`
--

DROP TABLE IF EXISTS `manufacturers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `manufacturers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `manufacturer_code` varchar(30) NOT NULL,
  `manufacturer_name` varchar(150) NOT NULL,
  `short_name` varchar(50) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_manufacturer_uuid` (`uuid`),
  UNIQUE KEY `uk_manufacturer_code` (`manufacturer_code`),
  UNIQUE KEY `uk_manufacturer_name` (`manufacturer_name`),
  KEY `idx_manufacturer_active` (`is_active`),
  KEY `idx_manufacturer_sort` (`sort_order`),
  KEY `idx_manufacturer_created_by` (`created_by`),
  KEY `idx_manufacturer_updated_by` (`updated_by`),
  CONSTRAINT `fk_manufacturer_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_manufacturer_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `master_categories`
--

DROP TABLE IF EXISTS `master_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `category_type` varchar(50) NOT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `category_code` varchar(30) NOT NULL,
  `category_name` varchar(200) NOT NULL,
  `module` varchar(50) NOT NULL DEFAULT 'ALL',
  `short_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 1,
  `level` int(11) NOT NULL DEFAULT 1,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_category_uuid` (`uuid`),
  UNIQUE KEY `uk_category_code` (`category_type`,`category_code`),
  KEY `idx_company` (`company_id`),
  KEY `idx_parent` (`parent_id`),
  KEY `idx_category_type` (`category_type`),
  KEY `idx_category_name` (`category_name`),
  KEY `idx_category_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment_term_details`
--

DROP TABLE IF EXISTS `payment_term_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_term_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_term_id` bigint(20) unsigned NOT NULL,
  `sequence_no` tinyint(3) unsigned NOT NULL,
  `description` varchar(150) NOT NULL,
  `percentage` decimal(5,2) NOT NULL,
  `due_days` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_payment_term_sequence` (`payment_term_id`,`sequence_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment_terms`
--

DROP TABLE IF EXISTS `payment_terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_terms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `term_code` varchar(30) NOT NULL,
  `term_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `due_days` smallint(5) unsigned NOT NULL DEFAULT 0,
  `discount_days` smallint(5) unsigned NOT NULL DEFAULT 0,
  `discount_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `down_payment_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `installment_count` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `grace_period_days` smallint(5) unsigned NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 1,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_payment_term_uuid` (`uuid`),
  UNIQUE KEY `uk_company_payment_term` (`company_id`,`term_code`),
  KEY `idx_company` (`company_id`),
  KEY `idx_default` (`is_default`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=397 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `profit_centers`
--

DROP TABLE IF EXISTS `profit_centers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profit_centers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `business_unit_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `profit_center_code` varchar(30) NOT NULL,
  `profit_center_name` varchar(200) NOT NULL,
  `short_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `manager_name` varchar(150) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_profit_center_uuid` (`uuid`),
  UNIQUE KEY `uk_profit_center_code` (`company_id`,`business_unit_id`,`branch_id`,`profit_center_code`),
  KEY `idx_company` (`company_id`),
  KEY `idx_business_unit` (`business_unit_id`),
  KEY `idx_branch` (`branch_id`),
  KEY `idx_profit_center_name` (`profit_center_name`),
  KEY `idx_profit_center_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchase_order_items`
--

DROP TABLE IF EXISTS `purchase_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchase_order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_order_id` bigint(20) unsigned NOT NULL,
  `assignment_material_requisition_item_id` bigint(20) unsigned DEFAULT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `item_code` varchar(100) DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `item_description` text DEFAULT NULL,
  `specification` text DEFAULT NULL,
  `ordered_qty` decimal(18,4) NOT NULL,
  `received_qty` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `remaining_qty` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `uom_id` bigint(20) unsigned NOT NULL,
  `uom_code` varchar(50) DEFAULT NULL,
  `uom_name` varchar(100) DEFAULT NULL,
  `warehouse_id` bigint(20) unsigned DEFAULT NULL,
  `warehouse_code` varchar(50) DEFAULT NULL,
  `warehouse_name` varchar(150) DEFAULT NULL,
  `supplier_id` bigint(20) unsigned DEFAULT NULL,
  `unit_price` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `discount_percent` decimal(8,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `gross_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `tax_id` bigint(20) unsigned DEFAULT NULL,
  `tax_name` varchar(100) DEFAULT NULL,
  `tax_percent` decimal(8,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `line_total` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `grand_total` decimal(18,2) NOT NULL DEFAULT 0.00,
  `required_date` date DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `delivery_location` varchar(255) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'Open',
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_order_items_created_by_foreign` (`created_by`),
  KEY `purchase_order_items_updated_by_foreign` (`updated_by`),
  KEY `purchase_order_items_deleted_by_foreign` (`deleted_by`),
  KEY `idx_poi_uom` (`uom_id`),
  KEY `idx_po_header` (`purchase_order_id`),
  KEY `idx_amr_item` (`assignment_material_requisition_item_id`),
  KEY `idx_item` (`item_id`),
  KEY `idx_supplier` (`supplier_id`),
  KEY `idx_status` (`status`),
  KEY `idx_delivery` (`delivery_date`),
  CONSTRAINT `fk_poi_amr_item` FOREIGN KEY (`assignment_material_requisition_item_id`) REFERENCES `assignment_material_requisition_items` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `purchase_order_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `purchase_order_items_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `purchase_order_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `purchase_order_items_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `purchase_order_items_uom_id_foreign` FOREIGN KEY (`uom_id`) REFERENCES `uoms` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `purchase_order_items_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchase_orders`
--

DROP TABLE IF EXISTS `purchase_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchase_orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `document_no` varchar(50) NOT NULL,
  `supplier_id` bigint(20) unsigned NOT NULL,
  `payment_term_id` bigint(20) unsigned DEFAULT NULL,
  `currency_id` bigint(20) unsigned DEFAULT NULL,
  `exchange_rate` decimal(18,6) NOT NULL DEFAULT 1.000000,
  `subtotal` decimal(18,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(18,2) NOT NULL DEFAULT 0.00,
  `approval_status` varchar(30) NOT NULL DEFAULT 'Pending',
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `generated_by` bigint(20) unsigned DEFAULT NULL,
  `generated_at` timestamp NULL DEFAULT NULL,
  `document_date` date NOT NULL,
  `purchase_requisition_id` bigint(20) unsigned DEFAULT NULL,
  `assignment_material_requisition_id` bigint(20) unsigned DEFAULT NULL,
  `pr_number` varchar(50) DEFAULT NULL,
  `amr_number` varchar(50) DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `business_unit_id` bigint(20) unsigned DEFAULT NULL,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `section_id` bigint(20) unsigned DEFAULT NULL,
  `cost_center_id` bigint(20) unsigned DEFAULT NULL,
  `warehouse_id` bigint(20) unsigned DEFAULT NULL,
  `shipping_address_id` bigint(20) unsigned DEFAULT NULL,
  `requester_id` bigint(20) unsigned DEFAULT NULL,
  `request_date` date DEFAULT NULL,
  `required_date` date DEFAULT NULL,
  `expected_delivery_date` date DEFAULT NULL,
  `priority` varchar(30) DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'Draft',
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_orders_po_no_unique` (`document_no`),
  KEY `purchase_orders_created_by_foreign` (`created_by`),
  KEY `purchase_orders_updated_by_foreign` (`updated_by`),
  KEY `purchase_orders_deleted_by_foreign` (`deleted_by`),
  KEY `idx_po_supplier` (`supplier_id`),
  KEY `idx_po_status` (`status`),
  KEY `idx_po_date` (`document_date`),
  KEY `fk_po_purchase_requisition` (`purchase_requisition_id`),
  KEY `fk_po_assignment_material_requisition` (`assignment_material_requisition_id`),
  KEY `fk_purchase_orders_shipping_address` (`shipping_address_id`),
  KEY `idx_purchase_orders_payment_term` (`payment_term_id`),
  CONSTRAINT `fk_po_assignment_material_requisition` FOREIGN KEY (`assignment_material_requisition_id`) REFERENCES `assignment_material_requisitions` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_po_purchase_requisition` FOREIGN KEY (`purchase_requisition_id`) REFERENCES `purchase_requisitions` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_purchase_orders_payment_term` FOREIGN KEY (`payment_term_id`) REFERENCES `payment_terms` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_purchase_orders_shipping_address` FOREIGN KEY (`shipping_address_id`) REFERENCES `shipping_addresses` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `purchase_orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `purchase_orders_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `purchase_orders_purchase_requisition_id_foreign` FOREIGN KEY (`purchase_requisition_id`) REFERENCES `purchase_requisitions` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `purchase_orders_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `purchase_orders_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchase_requisition_items`
--

DROP TABLE IF EXISTS `purchase_requisition_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchase_requisition_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `purchase_requisition_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `uom_id` bigint(20) unsigned NOT NULL,
  `warehouse_id` bigint(20) unsigned DEFAULT NULL,
  `quantity` decimal(18,4) NOT NULL,
  `estimated_unit_price` decimal(18,2) NOT NULL DEFAULT 0.00,
  `required_date` date DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Draft',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_requisition_items_uuid_unique` (`uuid`),
  KEY `purchase_requisition_items_uom_id_foreign` (`uom_id`),
  KEY `purchase_requisition_items_created_by_foreign` (`created_by`),
  KEY `purchase_requisition_items_updated_by_foreign` (`updated_by`),
  KEY `purchase_requisition_items_deleted_by_foreign` (`deleted_by`),
  KEY `purchase_requisition_items_purchase_requisition_id_index` (`purchase_requisition_id`),
  KEY `purchase_requisition_items_item_id_index` (`item_id`),
  KEY `purchase_requisition_items_warehouse_id_index` (`warehouse_id`),
  KEY `purchase_requisition_items_status_index` (`status`),
  KEY `purchase_requisition_items_purchase_requisition_id_status_index` (`purchase_requisition_id`,`status`),
  CONSTRAINT `purchase_requisition_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `purchase_requisition_items_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `purchase_requisition_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `purchase_requisition_items_purchase_requisition_id_foreign` FOREIGN KEY (`purchase_requisition_id`) REFERENCES `purchase_requisitions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `purchase_requisition_items_uom_id_foreign` FOREIGN KEY (`uom_id`) REFERENCES `uoms` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `purchase_requisition_items_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `purchase_requisition_items_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchase_requisitions`
--

DROP TABLE IF EXISTS `purchase_requisitions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchase_requisitions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `pr_no` varchar(255) NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `business_unit_id` bigint(20) unsigned DEFAULT NULL,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `section_id` bigint(20) unsigned DEFAULT NULL,
  `cost_center_id` bigint(20) unsigned DEFAULT NULL,
  `warehouse_id` bigint(20) unsigned DEFAULT NULL,
  `delivery_location` varchar(255) DEFAULT NULL,
  `reference_no` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `request_date` date NOT NULL,
  `required_date` date DEFAULT NULL,
  `priority` varchar(20) NOT NULL DEFAULT 'medium',
  `requester_id` bigint(20) unsigned NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Draft',
  `submitted_by` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_requisitions_uuid_unique` (`uuid`),
  UNIQUE KEY `purchase_requisitions_pr_no_unique` (`pr_no`),
  KEY `purchase_requisitions_business_unit_id_foreign` (`business_unit_id`),
  KEY `purchase_requisitions_section_id_foreign` (`section_id`),
  KEY `purchase_requisitions_cost_center_id_foreign` (`cost_center_id`),
  KEY `purchase_requisitions_submitted_by_foreign` (`submitted_by`),
  KEY `purchase_requisitions_created_by_foreign` (`created_by`),
  KEY `purchase_requisitions_updated_by_foreign` (`updated_by`),
  KEY `purchase_requisitions_deleted_by_foreign` (`deleted_by`),
  KEY `purchase_requisitions_status_index` (`status`),
  KEY `purchase_requisitions_request_date_index` (`request_date`),
  KEY `purchase_requisitions_required_date_index` (`required_date`),
  KEY `purchase_requisitions_company_id_index` (`company_id`),
  KEY `purchase_requisitions_department_id_index` (`department_id`),
  KEY `purchase_requisitions_requester_id_index` (`requester_id`),
  KEY `pr_branch_idx` (`branch_id`),
  KEY `pr_comp_branch_stat_dt_idx` (`company_id`,`branch_id`,`status`,`request_date`),
  KEY `purchase_requisitions_warehouse_id_index` (`warehouse_id`),
  CONSTRAINT `purchase_requisitions_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `purchase_requisitions_business_unit_id_foreign` FOREIGN KEY (`business_unit_id`) REFERENCES `business_units` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `purchase_requisitions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `purchase_requisitions_cost_center_id_foreign` FOREIGN KEY (`cost_center_id`) REFERENCES `cost_centers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `purchase_requisitions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `purchase_requisitions_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `purchase_requisitions_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `purchase_requisitions_requester_id_foreign` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `purchase_requisitions_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `purchase_requisitions_submitted_by_foreign` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `purchase_requisitions_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `purchase_requisitions_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `request_for_quotation_items`
--

DROP TABLE IF EXISTS `request_for_quotation_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `request_for_quotation_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `request_for_quotation_id` bigint(20) unsigned NOT NULL,
  `purchase_requisition_item_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `qty` decimal(18,4) NOT NULL,
  `uom_id` bigint(20) unsigned NOT NULL,
  `unit_price` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `discount` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `tax` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `lead_time` int(11) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `request_for_quotation_items_created_by_foreign` (`created_by`),
  KEY `request_for_quotation_items_updated_by_foreign` (`updated_by`),
  KEY `request_for_quotation_items_deleted_by_foreign` (`deleted_by`),
  KEY `idx_rfqi_rfq` (`request_for_quotation_id`),
  KEY `idx_rfqi_pr_item` (`purchase_requisition_item_id`),
  KEY `idx_rfqi_item` (`item_id`),
  KEY `idx_rfqi_uom` (`uom_id`),
  CONSTRAINT `request_for_quotation_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `request_for_quotation_items_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `request_for_quotation_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `request_for_quotation_items_purchase_requisition_item_id_foreign` FOREIGN KEY (`purchase_requisition_item_id`) REFERENCES `purchase_requisition_items` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `request_for_quotation_items_request_for_quotation_id_foreign` FOREIGN KEY (`request_for_quotation_id`) REFERENCES `request_for_quotations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `request_for_quotation_items_uom_id_foreign` FOREIGN KEY (`uom_id`) REFERENCES `uoms` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `request_for_quotation_items_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `request_for_quotations`
--

DROP TABLE IF EXISTS `request_for_quotations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `request_for_quotations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `rfq_no` varchar(50) NOT NULL,
  `purchase_requisition_id` bigint(20) unsigned NOT NULL,
  `supplier_id` bigint(20) unsigned NOT NULL,
  `rfq_date` date NOT NULL,
  `valid_until` date DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'Draft',
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `request_for_quotations_uuid_unique` (`uuid`),
  UNIQUE KEY `request_for_quotations_rfq_no_unique` (`rfq_no`),
  KEY `request_for_quotations_created_by_foreign` (`created_by`),
  KEY `request_for_quotations_updated_by_foreign` (`updated_by`),
  KEY `request_for_quotations_deleted_by_foreign` (`deleted_by`),
  KEY `idx_rfq_purchase_requisition` (`purchase_requisition_id`),
  KEY `idx_rfq_supplier` (`supplier_id`),
  KEY `idx_rfq_status` (`status`),
  KEY `idx_rfq_date` (`rfq_date`),
  CONSTRAINT `request_for_quotations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `request_for_quotations_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `request_for_quotations_purchase_requisition_id_foreign` FOREIGN KEY (`purchase_requisition_id`) REFERENCES `purchase_requisitions` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `request_for_quotations_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `request_for_quotations_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `role_permission_logs`
--

DROP TABLE IF EXISTS `role_permission_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_permission_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) unsigned NOT NULL,
  `permission_id` bigint(20) unsigned NOT NULL,
  `action` enum('assign','revoke') NOT NULL,
  `changed_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `role_code` varchar(30) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`),
  UNIQUE KEY `uk_roles_uuid` (`uuid`),
  UNIQUE KEY `uk_roles_code` (`role_code`),
  KEY `idx_company` (`company_id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_system` (`is_system`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `business_unit_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `department_id` bigint(20) unsigned NOT NULL,
  `section_code` varchar(30) NOT NULL,
  `section_name` varchar(200) NOT NULL,
  `short_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `supervisor_name` varchar(150) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_section_uuid` (`uuid`),
  UNIQUE KEY `uk_section_code` (`company_id`,`business_unit_id`,`branch_id`,`department_id`,`section_code`),
  KEY `idx_company` (`company_id`),
  KEY `idx_business_unit` (`business_unit_id`),
  KEY `idx_branch` (`branch_id`),
  KEY `idx_department` (`department_id`),
  KEY `idx_section_name` (`section_name`),
  KEY `idx_section_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shipping_addresses`
--

DROP TABLE IF EXISTS `shipping_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shipping_addresses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shipping_code` varchar(30) NOT NULL,
  `shipping_name` varchar(150) NOT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `business_unit_id` bigint(20) unsigned DEFAULT NULL,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `warehouse_id` bigint(20) unsigned DEFAULT NULL,
  `address` text NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) NOT NULL DEFAULT 'Indonesia',
  `attention` varchar(150) DEFAULT NULL,
  `contact_person` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shipping_addresses_shipping_code_unique` (`shipping_code`),
  KEY `shipping_addresses_created_by_foreign` (`created_by`),
  KEY `shipping_addresses_updated_by_foreign` (`updated_by`),
  KEY `shipping_addresses_deleted_by_foreign` (`deleted_by`),
  KEY `shipping_addresses_shipping_code_index` (`shipping_code`),
  KEY `shipping_addresses_shipping_name_index` (`shipping_name`),
  KEY `shipping_addresses_company_id_index` (`company_id`),
  KEY `shipping_addresses_business_unit_id_index` (`business_unit_id`),
  KEY `shipping_addresses_branch_id_index` (`branch_id`),
  KEY `shipping_addresses_warehouse_id_index` (`warehouse_id`),
  KEY `shipping_addresses_is_default_index` (`is_default`),
  KEY `shipping_addresses_is_active_index` (`is_active`),
  CONSTRAINT `shipping_addresses_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `shipping_addresses_business_unit_id_foreign` FOREIGN KEY (`business_unit_id`) REFERENCES `business_units` (`id`) ON DELETE SET NULL,
  CONSTRAINT `shipping_addresses_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `shipping_addresses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `shipping_addresses_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `shipping_addresses_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `shipping_addresses_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `supplier_category_id` bigint(20) unsigned DEFAULT NULL,
  `supplier_code` varchar(30) NOT NULL,
  `supplier_name` varchar(200) NOT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `company_type` varchar(50) DEFAULT NULL,
  `tax_number` varchar(100) DEFAULT NULL,
  `legal_name` varchar(255) DEFAULT NULL,
  `short_name` varchar(100) DEFAULT NULL,
  `supplier_type` enum('Supplier','Contractor','Service','Manufacturer') NOT NULL DEFAULT 'Supplier',
  `tax_id` varchar(50) DEFAULT NULL,
  `business_license` varchar(100) DEFAULT NULL,
  `payment_term_id` bigint(20) unsigned DEFAULT NULL,
  `ap_account_id` bigint(20) unsigned DEFAULT NULL,
  `currency_code` varchar(10) NOT NULL DEFAULT 'IDR',
  `contact_person` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `mobile` varchar(50) DEFAULT NULL,
  `website` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `currency_id` bigint(20) unsigned DEFAULT NULL,
  `country` varchar(100) NOT NULL DEFAULT 'Indonesia',
  `bank_name` varchar(150) DEFAULT NULL,
  `bank_account_name` varchar(150) DEFAULT NULL,
  `bank_account_no` varchar(100) DEFAULT NULL,
  `swift_code` varchar(50) DEFAULT NULL,
  `lead_time` int(11) NOT NULL DEFAULT 0,
  `vendor_rating` decimal(3,2) NOT NULL DEFAULT 0.00,
  `allow_purchase` tinyint(1) NOT NULL DEFAULT 1,
  `allow_service` tinyint(1) NOT NULL DEFAULT 1,
  `is_preferred` tinyint(1) NOT NULL DEFAULT 0,
  `bank_account_number` varchar(100) DEFAULT NULL,
  `credit_limit` decimal(18,2) NOT NULL DEFAULT 0.00,
  `opening_balance` decimal(18,2) NOT NULL DEFAULT 0.00,
  `is_blacklisted` tinyint(1) NOT NULL DEFAULT 0,
  `blacklist_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_vendor_uuid` (`uuid`),
  UNIQUE KEY `uk_vendor_code` (`supplier_code`),
  KEY `idx_company` (`company_id`),
  KEY `idx_vendor_category` (`supplier_category_id`),
  KEY `idx_payment_term` (`payment_term_id`),
  KEY `idx_vendor_name` (`supplier_name`),
  KEY `idx_vendor_type` (`supplier_type`),
  KEY `idx_vendor_active` (`is_active`),
  KEY `idx_supplier_category` (`category_id`),
  KEY `idx_supplier_currency` (`currency_id`),
  KEY `idx_supplier_ap` (`ap_account_id`),
  KEY `idx_supplier_leadtime` (`lead_time`),
  KEY `idx_supplier_preferred` (`is_preferred`),
  CONSTRAINT `fk_supplier_ap` FOREIGN KEY (`ap_account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_supplier_category` FOREIGN KEY (`category_id`) REFERENCES `master_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_supplier_currency` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `suppliers_contacts`
--

DROP TABLE IF EXISTS `suppliers_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliers_contacts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `vendor_id` bigint(20) unsigned NOT NULL,
  `contact_name` varchar(150) NOT NULL,
  `position` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `mobile` varchar(50) DEFAULT NULL,
  `extension` varchar(20) DEFAULT NULL,
  `whatsapp` varchar(50) DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_vendor_contact_uuid` (`uuid`),
  KEY `idx_vendor` (`vendor_id`),
  KEY `idx_contact_name` (`contact_name`),
  KEY `idx_email` (`email`),
  KEY `idx_primary` (`is_primary`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tax_masters`
--

DROP TABLE IF EXISTS `tax_masters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tax_masters` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `tax_code` varchar(20) NOT NULL,
  `tax_name` varchar(150) NOT NULL,
  `tax_type` varchar(30) NOT NULL,
  `tax_rate` decimal(8,4) NOT NULL DEFAULT 0.0000,
  `calculation_method` varchar(30) NOT NULL DEFAULT 'Percentage',
  `tax_account_id` bigint(20) unsigned DEFAULT NULL,
  `effective_date` date NOT NULL,
  `expired_date` date DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_inclusive` tinyint(1) NOT NULL DEFAULT 0,
  `is_withholding` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 1,
  `remarks` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_tax_uuid` (`uuid`),
  UNIQUE KEY `uk_tax_company` (`company_id`,`tax_code`),
  KEY `idx_company` (`company_id`),
  KEY `idx_tax_type` (`tax_type`),
  KEY `idx_effective_date` (`effective_date`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transaction_numberings`
--

DROP TABLE IF EXISTS `transaction_numberings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaction_numberings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `business_unit_id` bigint(20) unsigned DEFAULT NULL,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `module` varchar(100) NOT NULL,
  `document_type` varchar(100) NOT NULL,
  `document_name` varchar(150) NOT NULL,
  `prefix` varchar(30) NOT NULL,
  `suffix` varchar(30) DEFAULT NULL,
  `number_separator` varchar(5) NOT NULL DEFAULT '/',
  `format_pattern` varchar(255) NOT NULL DEFAULT '{PREFIX}/{YYYY}/{MM}/{RUNNING}',
  `running_digits` int(10) unsigned NOT NULL DEFAULT 6,
  `start_number` bigint(20) unsigned NOT NULL DEFAULT 1,
  `current_number` bigint(20) unsigned NOT NULL DEFAULT 0,
  `reset_type` enum('Never','Daily','Monthly','Yearly') NOT NULL DEFAULT 'Monthly',
  `last_generated_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaction_numberings_uuid_unique` (`uuid`),
  UNIQUE KEY `tn_company_bu_branch_document_unique` (`company_id`,`business_unit_id`,`branch_id`,`module`,`document_type`),
  KEY `transaction_numberings_created_by_foreign` (`created_by`),
  KEY `transaction_numberings_updated_by_foreign` (`updated_by`),
  KEY `transaction_numberings_deleted_by_foreign` (`deleted_by`),
  KEY `transaction_numberings_company_id_index` (`company_id`),
  KEY `transaction_numberings_business_unit_id_index` (`business_unit_id`),
  KEY `transaction_numberings_branch_id_index` (`branch_id`),
  KEY `transaction_numberings_module_index` (`module`),
  KEY `transaction_numberings_document_type_index` (`document_type`),
  KEY `transaction_numberings_is_active_index` (`is_active`),
  KEY `transaction_numberings_sort_order_index` (`sort_order`),
  KEY `tn_lookup_index` (`company_id`,`business_unit_id`,`branch_id`,`is_active`),
  CONSTRAINT `transaction_numberings_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transaction_numberings_business_unit_id_foreign` FOREIGN KEY (`business_unit_id`) REFERENCES `business_units` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transaction_numberings_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transaction_numberings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transaction_numberings_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transaction_numberings_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uoms`
--

DROP TABLE IF EXISTS `uoms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uoms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `uom_code` varchar(30) NOT NULL,
  `uom_name` varchar(100) NOT NULL,
  `symbol` varchar(20) NOT NULL,
  `category` varchar(30) NOT NULL DEFAULT 'Quantity',
  `decimal_places` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `allow_fraction` tinyint(1) NOT NULL DEFAULT 0,
  `is_base` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `remarks` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uoms_uuid_unique` (`uuid`),
  UNIQUE KEY `uoms_uom_code_unique` (`uom_code`),
  UNIQUE KEY `uoms_symbol_unique` (`symbol`),
  KEY `uoms_created_by_foreign` (`created_by`),
  KEY `uoms_updated_by_foreign` (`updated_by`),
  KEY `uoms_uom_name_index` (`uom_name`),
  KEY `uoms_category_index` (`category`),
  KEY `uoms_is_base_index` (`is_base`),
  KEY `uoms_is_active_index` (`is_active`),
  KEY `uoms_sort_order_index` (`sort_order`),
  CONSTRAINT `uoms_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `uoms_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_no` varchar(30) DEFAULT NULL,
  `uuid` char(36) NOT NULL,
  `username` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `business_unit_id` bigint(20) unsigned DEFAULT NULL,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `section_id` bigint(20) unsigned DEFAULT NULL,
  `cost_center_id` bigint(20) unsigned DEFAULT NULL,
  `profit_center_id` bigint(20) unsigned DEFAULT NULL,
  `position_id` bigint(20) unsigned DEFAULT NULL,
  `employee_id` bigint(20) unsigned DEFAULT NULL,
  `locale` varchar(10) NOT NULL DEFAULT 'id',
  `timezone` varchar(100) NOT NULL DEFAULT 'Asia/Jakarta',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_activity_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_uuid_unique` (`uuid`),
  UNIQUE KEY `users_username_unique` (`username`),
  KEY `users_uuid_index` (`uuid`),
  KEY `users_username_index` (`username`),
  KEY `users_company_id_index` (`company_id`),
  KEY `users_branch_id_index` (`branch_id`),
  KEY `users_department_id_index` (`department_id`),
  KEY `users_is_active_index` (`is_active`),
  KEY `users_cost_center_id_foreign` (`cost_center_id`),
  KEY `users_profit_center_id_foreign` (`profit_center_id`),
  CONSTRAINT `users_cost_center_id_foreign` FOREIGN KEY (`cost_center_id`) REFERENCES `cost_centers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_profit_center_id_foreign` FOREIGN KEY (`profit_center_id`) REFERENCES `profit_centers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `warehouse_types`
--

DROP TABLE IF EXISTS `warehouse_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `warehouse_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `type_code` varchar(30) NOT NULL,
  `type_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `allow_purchase` tinyint(1) DEFAULT 1,
  `allow_sales` tinyint(1) DEFAULT 1,
  `allow_transfer` tinyint(1) DEFAULT 1,
  `allow_production` tinyint(1) DEFAULT 0,
  `is_default` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `warehouses`
--

DROP TABLE IF EXISTS `warehouses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `warehouses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `warehouse_type_id` bigint(20) unsigned DEFAULT NULL,
  `warehouse_code` varchar(30) NOT NULL,
  `warehouse_name` varchar(150) NOT NULL,
  `short_name` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'Indonesia',
  `contact_person` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `allow_purchase` tinyint(1) NOT NULL DEFAULT 1,
  `allow_sales` tinyint(1) NOT NULL DEFAULT 1,
  `allow_transfer` tinyint(1) NOT NULL DEFAULT 1,
  `allow_production` tinyint(1) NOT NULL DEFAULT 0,
  `allow_negative_stock` tinyint(1) NOT NULL DEFAULT 0,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 1,
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_warehouse_uuid` (`uuid`),
  UNIQUE KEY `uk_company_wh_code` (`company_id`,`warehouse_code`),
  KEY `idx_company` (`company_id`),
  KEY `idx_branch` (`branch_id`),
  KEY `idx_warehouse_type` (`warehouse_type_id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_default` (`is_default`),
  KEY `idx_sort_order` (`sort_order`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_updated_by` (`updated_by`),
  KEY `idx_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_wh_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `fk_wh_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'nexus2'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-13  9:05:07
