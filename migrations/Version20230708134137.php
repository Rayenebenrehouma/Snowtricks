<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230708134137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC5C011B5');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCA76ED395');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC5C011B5 FOREIGN KEY (figure_id) REFERENCES figure (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE illustration DROP FOREIGN KEY FK_D67B9A4236AC99F1');
        $this->addSql('ALTER TABLE illustration ADD CONSTRAINT FK_D67B9A4236AC99F1 FOREIGN KEY (link) REFERENCES figure (id)');
        $this->addSql('ALTER TABLE video DROP FOREIGN KEY FK_7CC7DA2CADA40271');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2CADA40271 FOREIGN KEY (link_id) REFERENCES figure (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE video DROP FOREIGN KEY FK_7CC7DA2CADA40271');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2CADA40271 FOREIGN KEY (link_id) REFERENCES figure (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC5C011B5');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCA76ED395');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC5C011B5 FOREIGN KEY (figure_id) REFERENCES figure (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE illustration DROP FOREIGN KEY FK_D67B9A4236AC99F1');
        $this->addSql('ALTER TABLE illustration ADD CONSTRAINT FK_D67B9A4236AC99F1 FOREIGN KEY (link) REFERENCES figure (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
