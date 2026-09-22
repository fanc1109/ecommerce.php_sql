-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema ecommerce
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema ecommerce
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `ecommerce` DEFAULT CHARACTER SET utf8 ;
USE `ecommerce` ;

-- -----------------------------------------------------
-- Table `ecommerce`.`Usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecommerce`.`Usuario` (
  `id_usuario` INT NOT NULL AUTO_INCREMENT,
  `nome_usuario` VARCHAR(45) NULL,
  `senha_usuario` VARCHAR(45) NULL,
  `email_usuario` VARCHAR(45) NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE INDEX `email_usuario_UNIQUE` (`email_usuario` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ecommerce`.`Pedido`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecommerce`.`Pedido` (
  `id_pedido` INT NOT NULL AUTO_INCREMENT,
  `forma_pagamento` VARCHAR(45) NOT NULL,
  `data_pedido` DATETIME NOT NULL,
  `valor_total_pedido` DECIMAL(4,2) NOT NULL,
  `Usuario_id_usuario` INT NOT NULL,
  PRIMARY KEY (`id_pedido`, `Usuario_id_usuario`),
  INDEX `fk_Pedido_Usuario1_idx` (`Usuario_id_usuario` ASC) ,
  CONSTRAINT `fk_Pedido_Usuario1`
    FOREIGN KEY (`Usuario_id_usuario`)
    REFERENCES `ecommerce`.`Usuario` (`id_usuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ecommerce`.`Produto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecommerce`.`Produto` (
  `id_produto` INT NOT NULL AUTO_INCREMENT,
  `foto_produto` VARCHAR(45) NULL,
  `preco_produto` DECIMAL(4,2) NOT NULL,
  `nome_produto` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id_produto`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ecommerce`.`Contem`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecommerce`.`Contem` (
  `Pedido_id_pedido` INT NOT NULL,
  `Produto_id_produto` INT NOT NULL,
  `quantidade_contem` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`Pedido_id_pedido`, `Produto_id_produto`),
  INDEX `fk_Pedido_has_Produto_Produto1_idx` (`Produto_id_produto` ASC) ,
  INDEX `fk_Pedido_has_Produto_Pedido_idx` (`Pedido_id_pedido` ASC) ,
  CONSTRAINT `fk_Pedido_has_Produto_Pedido`
    FOREIGN KEY (`Pedido_id_pedido`)
    REFERENCES `ecommerce`.`Pedido` (`id_pedido`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Pedido_has_Produto_Produto1`
    FOREIGN KEY (`Produto_id_produto`)
    REFERENCES `ecommerce`.`Produto` (`id_produto`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
