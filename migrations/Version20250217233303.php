<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217233303 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post ADD forum_id INT DEFAULT NULL, CHANGE categorie_forum_id categorie_forum_id INT NOT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D29CCBAD0 FOREIGN KEY (forum_id) REFERENCES forum (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D29CCBAD0 ON post (forum_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D29CCBAD0');
        $this->addSql('DROP INDEX IDX_5A8A6C8D29CCBAD0 ON post');
        $this->addSql('ALTER TABLE post DROP forum_id, CHANGE categorie_forum_id categorie_forum_id INT DEFAULT NULL');
    }
}
