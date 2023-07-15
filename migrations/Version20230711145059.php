<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230711145059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC5C011B5');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC5C011B5 FOREIGN KEY (figure_id) REFERENCES figure (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE illustration DROP FOREIGN KEY FK_D67B9A4236AC99F1');
        $this->addSql('ALTER TABLE illustration ADD CONSTRAINT FK_D67B9A4236AC99F1 FOREIGN KEY (link) REFERENCES figure (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC5C011B5');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC5C011B5 FOREIGN KEY (figure_id) REFERENCES figure (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE illustration DROP FOREIGN KEY FK_D67B9A4236AC99F1');
        $this->addSql('ALTER TABLE illustration ADD CONSTRAINT FK_D67B9A4236AC99F1 FOREIGN KEY (link) REFERENCES figure (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
