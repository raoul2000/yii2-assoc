-- MySQL Script generated by MySQL Workbench
-- Sun Jun  2 12:58:11 2019
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema yii2_assoc
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Table `arhistory`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `arhistory` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `table_name` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `row_id` INT(11) NOT NULL,
  `event` SMALLINT(6) NOT NULL,
  `created_at` INT(11) NOT NULL,
  `created_by` INT(11) NULL DEFAULT NULL,
  `field_name` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `old_value` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `new_value` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `index-1` (`table_name` ASC),
  INDEX `index-2` (`table_name` ASC, `row_id` ASC),
  INDEX `index-3` (`table_name` ASC, `field_name` ASC),
  INDEX `index-4` (`event` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 15
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `auth_rule`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `auth_rule` (
  `name` VARCHAR(64) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `data` BLOB NULL DEFAULT NULL,
  `created_at` INT(11) NULL DEFAULT NULL,
  `updated_at` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`name`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `auth_item`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `auth_item` (
  `name` VARCHAR(64) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `type` SMALLINT(6) NOT NULL,
  `description` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `rule_name` VARCHAR(64) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `data` BLOB NULL DEFAULT NULL,
  `created_at` INT(11) NULL DEFAULT NULL,
  `updated_at` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`name`),
  INDEX `rule_name` (`rule_name` ASC),
  INDEX `idx-auth_item-type` (`type` ASC),
  CONSTRAINT `auth_item_ibfk_1`
    FOREIGN KEY (`rule_name`)
    REFERENCES `auth_rule` (`name`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `auth_assignment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `auth_assignment` (
  `item_name` VARCHAR(64) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `user_id` VARCHAR(64) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `created_at` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`item_name`, `user_id`),
  INDEX `idx-auth_assignment-user_id` (`user_id` ASC),
  CONSTRAINT `auth_assignment_ibfk_1`
    FOREIGN KEY (`item_name`)
    REFERENCES `auth_item` (`name`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `auth_item_child`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `auth_item_child` (
  `parent` VARCHAR(64) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `child` VARCHAR(64) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  PRIMARY KEY (`parent`, `child`),
  INDEX `child` (`child` ASC),
  CONSTRAINT `auth_item_child_ibfk_1`
    FOREIGN KEY (`parent`)
    REFERENCES `auth_item` (`name`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `auth_item_child_ibfk_2`
    FOREIGN KEY (`child`)
    REFERENCES `auth_item` (`name`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `address`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `address` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `line_1` VARCHAR(128) NULL,
  `line_2` VARCHAR(128) NULL,
  `line_3` VARCHAR(128) NULL,
  `zip_code` VARCHAR(45) NULL,
  `city` VARCHAR(45) NULL,
  `country` VARCHAR(45) NULL,
  `note` VARCHAR(128) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `contact`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `contact` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(128) CHARACTER SET 'utf8' NULL DEFAULT NULL COMMENT 'name',
  `firstname` VARCHAR(128) NULL,
  `created_at` DATETIME NULL DEFAULT NULL,
  `updated_at` DATETIME NULL DEFAULT NULL,
  `is_deleted` TINYINT(1) NULL DEFAULT '0',
  `uuid` CHAR(36) CHARACTER SET 'utf8' NOT NULL,
  `address_id` INT UNSIGNED NULL DEFAULT NULL,
  `is_natural_person` TINYINT(1) NOT NULL,
  `birthday` DATE NULL,
  `gender` TINYINT(1) NULL COMMENT '1 = Male\n2 = female',
  `email` VARCHAR(128) NULL,
  `note` VARCHAR(128) NULL,
  `phone_1` VARCHAR(15) NULL,
  `phone_2` VARCHAR(15) NULL,
  `date_1` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `uuid_UNIQUE` (`uuid` ASC),
  INDEX `fk_contact_address1_idx` (`address_id` ASC),
  CONSTRAINT `fk_contact_address1`
    FOREIGN KEY (`address_id`)
    REFERENCES `address` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `migration`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `migration` (
  `version` VARCHAR(180) NOT NULL,
  `apply_time` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`version`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `user` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `email` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `password_hash` VARCHAR(60) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `auth_key` VARCHAR(32) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `unconfirmed_email` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `registration_ip` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `flags` INT(11) NOT NULL DEFAULT '0',
  `confirmed_at` INT(11) NULL DEFAULT NULL,
  `blocked_at` INT(11) NULL DEFAULT NULL,
  `updated_at` INT(11) NOT NULL,
  `created_at` INT(11) NOT NULL,
  `last_login_at` INT(11) NULL DEFAULT NULL,
  `auth_tf_key` VARCHAR(16) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `auth_tf_enabled` TINYINT(1) NULL DEFAULT '0',
  `password_changed_at` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `idx_user_username` (`username` ASC),
  UNIQUE INDEX `idx_user_email` (`email` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `profile`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `profile` (
  `user_id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `public_email` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `gravatar_email` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `gravatar_id` VARCHAR(32) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `location` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `website` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `timezone` VARCHAR(40) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `bio` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `fk_profile_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `social_account`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `social_account` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NULL DEFAULT NULL,
  `provider` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `client_id` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `code` VARCHAR(32) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `email` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `username` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `data` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `created_at` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `idx_social_account_provider_client_id` (`provider` ASC, `client_id` ASC),
  UNIQUE INDEX `idx_social_account_code` (`code` ASC),
  INDEX `fk_social_account_user` (`user_id` ASC),
  CONSTRAINT `fk_social_account_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `token`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `token` (
  `user_id` INT(11) NULL DEFAULT NULL,
  `code` VARCHAR(32) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `type` SMALLINT(6) NOT NULL,
  `created_at` INT(11) NOT NULL,
  UNIQUE INDEX `idx_token_user_id_code_type` (`user_id` ASC, `code` ASC, `type` ASC),
  CONSTRAINT `fk_token_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `bank_account`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bank_account` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `contact_id` INT(10) UNSIGNED NOT NULL,
  `contact_name` VARCHAR(45) NULL,
  `name` VARCHAR(45) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `initial_value` DECIMAL(6,2) NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `fk_bank_account_contact1_idx` (`contact_id` ASC),
  CONSTRAINT `fk_bank_account_contact1`
    FOREIGN KEY (`contact_id`)
    REFERENCES `contact` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `transaction_pack`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `transaction_pack` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(128) NULL,
  `reference_date` DATE NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `bank_account_id` INT(11) UNSIGNED NULL,
  `type` TINYINT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_transaction_pack_bank_account1_idx` (`bank_account_id` ASC),
  CONSTRAINT `fk_transaction_pack_bank_account1`
    FOREIGN KEY (`bank_account_id`)
    REFERENCES `bank_account` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `category`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `category` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `contact_id` INT(10) UNSIGNED NULL,
  `type` VARCHAR(7) NOT NULL,
  `name` VARCHAR(140) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_category_contact1_idx` (`contact_id` ASC),
  CONSTRAINT `fk_category_contact1`
    FOREIGN KEY (`contact_id`)
    REFERENCES `contact` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `transaction`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `transaction` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `from_account_id` INT(11) UNSIGNED NOT NULL,
  `to_account_id` INT(11) UNSIGNED NOT NULL,
  `value` DECIMAL(6,2) UNSIGNED NULL,
  `description` VARCHAR(128) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `is_verified` TINYINT(1) NULL DEFAULT 0,
  `reference_date` DATE NULL,
  `code` VARCHAR(10) NULL,
  `transaction_pack_id` INT UNSIGNED NULL,
  `type` VARCHAR(10) NULL DEFAULT NULL,
  `orders_value_total` DECIMAL(6,2) NULL DEFAULT NULL,
  `category_id` INT UNSIGNED NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_transaction_bank_account1_idx` (`from_account_id` ASC),
  INDEX `fk_transaction_bank_account2_idx` (`to_account_id` ASC),
  INDEX `fk_transaction_transaction_pack1_idx` (`transaction_pack_id` ASC),
  INDEX `fk_transaction_category1_idx` (`category_id` ASC),
  CONSTRAINT `fk_transaction_bank_account1`
    FOREIGN KEY (`from_account_id`)
    REFERENCES `bank_account` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_transaction_bank_account2`
    FOREIGN KEY (`to_account_id`)
    REFERENCES `bank_account` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_transaction_transaction_pack1`
    FOREIGN KEY (`transaction_pack_id`)
    REFERENCES `transaction_pack` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_transaction_category1`
    FOREIGN KEY (`category_id`)
    REFERENCES `category` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `product`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `product` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  `value` DECIMAL(6,2) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `valid_date_start` DATE NULL,
  `valid_date_end` DATE NULL,
  `description` TEXT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `order`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `order` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `to_contact_id` INT(10) UNSIGNED NOT NULL,
  `from_contact_id` INT(10) UNSIGNED NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `value` DECIMAL(6,2) UNSIGNED NULL DEFAULT 0,
  `valid_date_start` DATE NULL,
  `valid_date_end` DATE NULL,
  `transactions_value_total` DECIMAL(6,2) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_order_product1_idx` (`product_id` ASC),
  INDEX `fk_order_contact1_idx` (`to_contact_id` ASC),
  INDEX `fk_order_contact2_idx` (`from_contact_id` ASC),
  CONSTRAINT `fk_order_product1`
    FOREIGN KEY (`product_id`)
    REFERENCES `product` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_order_contact1`
    FOREIGN KEY (`to_contact_id`)
    REFERENCES `contact` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_order_contact2`
    FOREIGN KEY (`from_contact_id`)
    REFERENCES `contact` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `attachment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `attachment` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `model` VARCHAR(45) NULL,
  `hash` VARCHAR(45) NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `itemId` INT UNSIGNED NULL,
  `size` INT NULL,
  `type` VARCHAR(45) NULL,
  `mime` VARCHAR(45) NULL,
  `category_id` INT NULL,
  `note` VARCHAR(128) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `order_transaction`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `order_transaction` (
  `order_id` INT UNSIGNED NOT NULL,
  `transaction_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`order_id`, `transaction_id`),
  INDEX `fk_order_has_transaction_transaction1_idx` (`transaction_id` ASC),
  INDEX `fk_order_has_transaction_order1_idx` (`order_id` ASC),
  CONSTRAINT `fk_order_has_transaction_order1`
    FOREIGN KEY (`order_id`)
    REFERENCES `order` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_order_has_transaction_transaction1`
    FOREIGN KEY (`transaction_id`)
    REFERENCES `transaction` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `session`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `session` (
  `id` CHAR(40) NOT NULL,
  `expire` INT NULL,
  `data` BLOB NULL,
  `user_id` INT(11) NULL,
  `last_write` INT(11) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `config`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `config` (
  `id` VARCHAR(128) NOT NULL,
  `value` TEXT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
