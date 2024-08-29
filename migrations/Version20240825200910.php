<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240825200910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category ADD author_id INT DEFAULT NULL, ADD image VARCHAR(255) DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1F675F31B FOREIGN KEY (author_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_64C19C1F675F31B ON category (author_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1F675F31B');
        $this->addSql('DROP INDEX IDX_64C19C1F675F31B ON category');
        $this->addSql('ALTER TABLE category DROP author_id, DROP image, DROP created_at');
    }
}
