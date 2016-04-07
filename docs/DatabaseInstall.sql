-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema churchis_main
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema churchis_main
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `churchis_main` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `churchis_main` ;

-- -----------------------------------------------------
-- Table `churchis_main`.`semesters`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `churchis_main`.`semesters` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` TINYTEXT NOT NULL,
  `description` TEXT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  `status` ENUM('OPEN','CLOSED') NOT NULL DEFAULT 'OPEN',
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `churchis_main`.`members`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `churchis_main`.`members` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `semester_id` INT UNSIGNED NOT NULL,
  `name` TINYTEXT NOT NULL,
  `email` VARCHAR(50) NULL COMMENT '	',
  `phone` VARCHAR(20) NULL,
  `address` TINYTEXT NULL,
  `city` TINYTEXT NULL,
  `state` VARCHAR(2) NULL,
  `zip` VARCHAR(5) NULL,
  `contact_pref` ENUM('PHONE','EMAIL','BOTH','EITHER') NULL,
  `child_care` INT UNSIGNED NOT NULL DEFAULT 0,
  `child_ages` TINYTEXT NULL DEFAULT NULL,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `semester_idx` (`semester_id` ASC),
  CONSTRAINT `semester`
    FOREIGN KEY (`semester_id`)
    REFERENCES `churchis_main`.`semesters` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `churchis_main`.`groups`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `churchis_main`.`groups` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `semester_id` INT UNSIGNED NOT NULL,
  `public_id` VARCHAR(10) NULL DEFAULT NULL,
  `name` TINYTEXT NOT NULL,
  `description` MEDIUMTEXT NULL DEFAULT NULL,
  `data` LONGTEXT NULL DEFAULT NULL,
  `max_members` INT NULL DEFAULT NULL,
  `status` ENUM('OPEN','CLOSED','FULL','CANCELED') NULL,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `semester_idx` (`semester_id` ASC),
  CONSTRAINT `semester`
    FOREIGN KEY (`semester_id`)
    REFERENCES `churchis_main`.`semesters` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `churchis_main`.`groups_members`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `churchis_main`.`groups_members` (
  `group_id` INT UNSIGNED NOT NULL,
  `member_id` INT UNSIGNED NOT NULL,
  `leader` TINYINT(1) NULL DEFAULT 0,
  PRIMARY KEY (`group_id`, `member_id`),
  INDEX `member_idx` (`member_id` ASC),
  CONSTRAINT `group`
    FOREIGN KEY (`group_id`)
    REFERENCES `churchis_main`.`groups` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `member`
    FOREIGN KEY (`member_id`)
    REFERENCES `churchis_main`.`members` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `churchis_main`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `churchis_main`.`users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` TINYTEXT NOT NULL,
  `email` VARCHAR(50) NOT NULL,
  `password` VARCHAR(50) NULL DEFAULT NULL,
  `status` ENUM('ENABLED','DISABLED','LOCKED') NOT NULL DEFAULT 'ENABLED',
  `role` ENUM('ADMIN','USER') NOT NULL DEFAULT 'USER',
  `service` ENUM('GOOGLE','LOCAL') NOT NULL,
  `service_id` VARCHAR(45) NULL DEFAULT NULL,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email` (`email` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `churchis_main`.`users_groups`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `churchis_main`.`users_groups` (
  `user_id` INT UNSIGNED NOT NULL,
  `group_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`user_id`, `group_id`),
  INDEX `group_idx` (`group_id` ASC),
  CONSTRAINT `group`
    FOREIGN KEY (`group_id`)
    REFERENCES `churchis_main`.`groups` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `user`
    FOREIGN KEY (`user_id`)
    REFERENCES `churchis_main`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `churchis_main`.`sessions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `churchis_main`.`sessions` (
  `key` VARCHAR(50) NOT NULL,
  `data` LONGTEXT NULL,
  `expires` DATETIME NOT NULL,
  PRIMARY KEY (`key`),
  UNIQUE INDEX `key_UNIQUE` (`key` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `churchis_main`.`vars`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `churchis_main`.`vars` (
  `name` VARCHAR(45) NOT NULL,
  `value` LONGTEXT NULL DEFAULT NULL,
  PRIMARY KEY (`name`))
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
