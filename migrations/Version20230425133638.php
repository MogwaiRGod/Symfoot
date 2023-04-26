<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230425133638 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE joueur ADD equipe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE joueur ADD CONSTRAINT FK_FD71A9C56D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id)');
        $this->addSql('CREATE INDEX IDX_FD71A9C56D861B89 ON joueur (equipe_id)');
        $this->addSql('ALTER TABLE manager ADD equipe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE manager ADD CONSTRAINT FK_FA2425B96D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id)');
        $this->addSql('CREATE INDEX IDX_FA2425B96D861B89 ON manager (equipe_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE manager DROP FOREIGN KEY FK_FA2425B96D861B89');
        $this->addSql('DROP INDEX IDX_FA2425B96D861B89 ON manager');
        $this->addSql('ALTER TABLE manager DROP equipe_id');
        $this->addSql('ALTER TABLE joueur DROP FOREIGN KEY FK_FD71A9C56D861B89');
        $this->addSql('DROP INDEX IDX_FD71A9C56D861B89 ON joueur');
        $this->addSql('ALTER TABLE joueur DROP equipe_id');
    }
}
