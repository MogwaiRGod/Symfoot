<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230428064705 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE championnat CHANGE vainqueur_id vainqueur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE championnat ADD CONSTRAINT FK_AB8C220773C35EE FOREIGN KEY (vainqueur_id) REFERENCES equipe (id)');
        $this->addSql('CREATE INDEX IDX_AB8C220773C35EE ON championnat (vainqueur_id)');
        $this->addSql('ALTER TABLE equipe ADD description LONGTEXT DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_460C35ED4265900C ON rencontre');
        $this->addSql('DROP INDEX UNIQ_460C35ED50D03FE2 ON rencontre');
        $this->addSql('ALTER TABLE rencontre ADD CONSTRAINT FK_460C35ED4265900C FOREIGN KEY (equipe1_id) REFERENCES equipe (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_460C35ED4265900C ON rencontre (equipe1_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_460C35ED50D03FE2 ON rencontre (equipe2_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rencontre DROP FOREIGN KEY FK_460C35ED4265900C');
        $this->addSql('DROP INDEX UNIQ_460C35ED4265900C ON rencontre');
        $this->addSql('DROP INDEX UNIQ_460C35ED50D03FE2 ON rencontre');
        $this->addSql('CREATE INDEX UNIQ_460C35ED4265900C ON rencontre (equipe1_id)');
        $this->addSql('CREATE INDEX UNIQ_460C35ED50D03FE2 ON rencontre (equipe2_id)');
        $this->addSql('ALTER TABLE championnat DROP FOREIGN KEY FK_AB8C220773C35EE');
        $this->addSql('DROP INDEX IDX_AB8C220773C35EE ON championnat');
        $this->addSql('ALTER TABLE championnat CHANGE vainqueur_id vainqueur_id INT NOT NULL');
        $this->addSql('ALTER TABLE equipe DROP description');
    }
}
