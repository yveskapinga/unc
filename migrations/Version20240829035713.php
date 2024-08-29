<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240829035713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE membership ADD interfederation_id INT NOT NULL, ADD fonction VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE membership ADD CONSTRAINT FK_86FFD2855E8A6A31 FOREIGN KEY (interfederation_id) REFERENCES interfederation (id)');
        $this->addSql('CREATE INDEX IDX_86FFD2855E8A6A31 ON membership (interfederation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE membership DROP FOREIGN KEY FK_86FFD2855E8A6A31');
        $this->addSql('DROP INDEX IDX_86FFD2855E8A6A31 ON membership');
        $this->addSql('ALTER TABLE membership DROP interfederation_id, DROP fonction');
    }
}
