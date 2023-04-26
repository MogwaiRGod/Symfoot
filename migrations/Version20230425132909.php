<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230425132909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE championnat (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE joueur ADD caracteristiques_id INT NOT NULL');
        $this->addSql('ALTER TABLE joueur ADD CONSTRAINT FK_FD71A9C5B2639FE4 FOREIGN KEY (caracteristiques_id) REFERENCES caracteristique (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FD71A9C5B2639FE4 ON joueur (caracteristiques_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE championnat');
        $this->addSql('ALTER TABLE joueur DROP FOREIGN KEY FK_FD71A9C5B2639FE4');
        $this->addSql('DROP INDEX UNIQ_FD71A9C5B2639FE4 ON joueur');
        $this->addSql('ALTER TABLE joueur DROP caracteristiques_id');
    }
}
