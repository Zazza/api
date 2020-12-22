<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201222115333 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE static_currency_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE static_transaction_reason_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE static_transaction_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE transaction_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE wallet_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE static_currency (id INT NOT NULL, name VARCHAR(8) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE static_transaction_reason (id INT NOT NULL, name VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE static_transaction_type (id INT NOT NULL, name VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE transaction (id INT NOT NULL, reason_id INT NOT NULL, type_id INT NOT NULL, currency_id INT NOT NULL, wallet_id INT NOT NULL, amount INT NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE wallet (id INT NOT NULL, user_id INT NOT NULL, currency_id INT NOT NULL, amount INT NOT NULL DEFAULT 0, PRIMARY KEY(id))');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE static_currency_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE static_transaction_reason_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE static_transaction_type_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE transaction_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE wallet_id_seq CASCADE');
        $this->addSql('DROP TABLE static_currency');
        $this->addSql('DROP TABLE static_transaction_reason');
        $this->addSql('DROP TABLE static_transaction_type');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE wallet');
    }
}
