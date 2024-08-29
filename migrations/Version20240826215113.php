<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240826215113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE interfederation (id INT AUTO_INCREMENT NOT NULL, sif_id INT NOT NULL, siege_id INT NOT NULL, designation VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_86851E412F1B7F0D (sif_id), UNIQUE INDEX UNIQ_86851E41BF006E8B (siege_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE interfederation ADD CONSTRAINT FK_86851E412F1B7F0D FOREIGN KEY (sif_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE interfederation ADD CONSTRAINT FK_86851E41BF006E8B FOREIGN KEY (siege_id) REFERENCES address (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE interfederation DROP FOREIGN KEY FK_86851E412F1B7F0D');
        $this->addSql('ALTER TABLE interfederation DROP FOREIGN KEY FK_86851E41BF006E8B');
        $this->addSql('DROP TABLE interfederation');
    }
}
