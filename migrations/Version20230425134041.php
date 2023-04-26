<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230425134041 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rencontre ADD championnat_id INT NOT NULL, ADD equipe1_id INT NOT NULL, ADD equipe2_id INT NOT NULL');
        $this->addSql('ALTER TABLE rencontre ADD CONSTRAINT FK_460C35ED627A0DA8 FOREIGN KEY (championnat_id) REFERENCES championnat (id)');
        $this->addSql('ALTER TABLE rencontre ADD CONSTRAINT FK_460C35ED4265900C FOREIGN KEY (equipe1_id) REFERENCES equipe (id)');
        $this->addSql('ALTER TABLE rencontre ADD CONSTRAINT FK_460C35ED50D03FE2 FOREIGN KEY (equipe2_id) REFERENCES equipe (id)');
        $this->addSql('CREATE INDEX IDX_460C35ED627A0DA8 ON rencontre (championnat_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_460C35ED4265900C ON rencontre (equipe1_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_460C35ED50D03FE2 ON rencontre (equipe2_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rencontre DROP FOREIGN KEY FK_460C35ED627A0DA8');
        $this->addSql('ALTER TABLE rencontre DROP FOREIGN KEY FK_460C35ED4265900C');
        $this->addSql('ALTER TABLE rencontre DROP FOREIGN KEY FK_460C35ED50D03FE2');
        $this->addSql('DROP INDEX IDX_460C35ED627A0DA8 ON rencontre');
        $this->addSql('DROP INDEX UNIQ_460C35ED4265900C ON rencontre');
        $this->addSql('DROP INDEX UNIQ_460C35ED50D03FE2 ON rencontre');
        $this->addSql('ALTER TABLE rencontre DROP championnat_id, DROP equipe1_id, DROP equipe2_id');
    }
}
