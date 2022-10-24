<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221024073018 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD image_file_name VARCHAR(255) NOT NULL, CHANGE nom_article nom_article VARCHAR(80) DEFAULT NULL, CHANGE image_article image_article VARCHAR(255) DEFAULT NULL, CHANGE prix_article prix_article DOUBLE PRECISION DEFAULT NULL, CHANGE description_article description_article VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE categorie CHANGE nom_categorie nom_categorie VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP image_file_name, CHANGE nom_article nom_article VARCHAR(80) NOT NULL, CHANGE image_article image_article VARCHAR(255) NOT NULL, CHANGE prix_article prix_article DOUBLE PRECISION NOT NULL, CHANGE description_article description_article VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE categorie CHANGE nom_categorie nom_categorie VARCHAR(80) NOT NULL');
    }
}
