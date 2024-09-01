<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240901090854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post ADD parent_id INT DEFAULT NULL, ADD is_validated TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D727ACA70 FOREIGN KEY (parent_id) REFERENCES post (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D727ACA70 ON post (parent_id)');
        $this->addSql('ALTER TABLE topic ADD content LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D727ACA70');
        $this->addSql('DROP INDEX IDX_5A8A6C8D727ACA70 ON post');
        $this->addSql('ALTER TABLE post DROP parent_id, DROP is_validated');
        $this->addSql('ALTER TABLE topic DROP content');
    }
}
