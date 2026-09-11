<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260908084642 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE absence DROP FOREIGN KEY `FK_765AE0C9907A6D7A`');
        $this->addSql('DROP INDEX IDX_765AE0C9907A6D7A ON absence');
        $this->addSql('ALTER TABLE absence ADD date DATE NOT NULL, ADD reason VARCHAR(50) NOT NULL, ADD notes LONGTEXT DEFAULT NULL, ADD justificative_filename VARCHAR(255) DEFAULT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL, CHANGE trainee_id_id trainee_id INT NOT NULL');
        $this->addSql('ALTER TABLE absence ADD CONSTRAINT FK_765AE0C936C682D0 FOREIGN KEY (trainee_id) REFERENCES trainee (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_765AE0C936C682D0 ON absence (trainee_id)');
        $this->addSql('ALTER TABLE trainee ADD first_name VARCHAR(255) NOT NULL, ADD last_name VARCHAR(255) NOT NULL, ADD photo_filename VARCHAR(255) DEFAULT NULL, DROP firstname, DROP lastname, CHANGE phone phone VARCHAR(20) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE absence DROP FOREIGN KEY FK_765AE0C936C682D0');
        $this->addSql('DROP INDEX IDX_765AE0C936C682D0 ON absence');
        $this->addSql('ALTER TABLE absence DROP date, DROP reason, DROP notes, DROP justificative_filename, DROP created_at, DROP updated_at, CHANGE trainee_id trainee_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE absence ADD CONSTRAINT `FK_765AE0C9907A6D7A` FOREIGN KEY (trainee_id_id) REFERENCES trainee (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_765AE0C9907A6D7A ON absence (trainee_id_id)');
        $this->addSql('ALTER TABLE trainee ADD firstname VARCHAR(255) NOT NULL, ADD lastname VARCHAR(255) NOT NULL, DROP first_name, DROP last_name, DROP photo_filename, CHANGE phone phone VARCHAR(255) NOT NULL');
    }
}
