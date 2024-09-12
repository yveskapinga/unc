<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240911113730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE interfederation CHANGE sif_id sif_id INT DEFAULT NULL, CHANGE siege_id siege_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE membership DROP fee_amount, DROP fee_paid_at, DROP currency');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE interfederation CHANGE sif_id sif_id INT NOT NULL, CHANGE siege_id siege_id INT NOT NULL');
        $this->addSql('ALTER TABLE membership ADD fee_amount NUMERIC(10, 0) DEFAULT NULL, ADD fee_paid_at DATETIME DEFAULT NULL, ADD currency VARCHAR(10) NOT NULL');
    }
}
