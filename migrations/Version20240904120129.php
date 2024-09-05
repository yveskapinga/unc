<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240904120129 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE address ADD postal_code VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE membership ADD membership_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD phone_number VARCHAR(255) DEFAULT NULL, ADD nationality VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE address DROP postal_code');
        $this->addSql('ALTER TABLE membership DROP membership_type');
        $this->addSql('ALTER TABLE `user` DROP phone_number, DROP nationality');
    }
}
