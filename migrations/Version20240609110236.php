<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240609110236 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quiz ADD titre_id INT DEFAULT NULL, DROP titre');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA92D54FAE5E FOREIGN KEY (titre_id) REFERENCES category (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A412FA92D54FAE5E ON quiz (titre_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA92D54FAE5E');
        $this->addSql('DROP INDEX UNIQ_A412FA92D54FAE5E ON quiz');
        $this->addSql('ALTER TABLE quiz ADD titre VARCHAR(255) NOT NULL, DROP titre_id');
    }
}
