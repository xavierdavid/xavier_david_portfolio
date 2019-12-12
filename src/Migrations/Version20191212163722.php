<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191212163722 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE experience (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, mission VARCHAR(255) NOT NULL, company VARCHAR(255) NOT NULL, period VARCHAR(255) NOT NULL, url VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, published TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_590C1033DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, mail VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, period VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, published TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_2FB3D0EE3DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE education (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, certification VARCHAR(255) NOT NULL, school VARCHAR(255) NOT NULL, period VARCHAR(255) NOT NULL, url VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, published TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_DB0A5ED23DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, extension VARCHAR(255) NOT NULL, alt VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE experience ADD CONSTRAINT FK_590C1033DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE3DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE education ADD CONSTRAINT FK_DB0A5ED23DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE article ADD image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E663DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_23A0E663DA5256D ON article (image_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE experience DROP FOREIGN KEY FK_590C1033DA5256D');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE3DA5256D');
        $this->addSql('ALTER TABLE education DROP FOREIGN KEY FK_DB0A5ED23DA5256D');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E663DA5256D');
        $this->addSql('DROP TABLE experience');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE education');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP INDEX UNIQ_23A0E663DA5256D ON article');
        $this->addSql('ALTER TABLE article DROP image_id');
    }
}
