<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190904174038 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE attributes CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE brands CHANGE details details VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE categories CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE products ADD updated_at DATETIME NOT NULL, CHANGE brand_id brand_id INT DEFAULT NULL, CHANGE mpn mpn VARCHAR(255) DEFAULT NULL, CHANGE meta_title meta_title VARCHAR(255) DEFAULT NULL, CHANGE description description VARCHAR(255) DEFAULT NULL, CHANGE meta_description meta_description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE product_attributes CHANGE product_id product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE attributes CHANGE description description VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE brands CHANGE details details VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE categories CHANGE description description VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE product_attributes CHANGE product_id product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE products DROP updated_at, CHANGE brand_id brand_id INT DEFAULT NULL, CHANGE mpn mpn VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE meta_title meta_title VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE description description VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE meta_description meta_description VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
    }
}
