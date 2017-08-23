<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170823143515 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE purchases DROP FOREIGN KEY FK_AA6431FE12469DE2');
        $this->addSql('DROP INDEX IDX_AA6431FE12469DE2 ON purchases');
        $this->addSql('ALTER TABLE purchases DROP category_id');
        $this->addSql('ALTER TABLE waste DROP FOREIGN KEY FK_2E76A48811A842B9');
        $this->addSql('ALTER TABLE waste DROP FOREIGN KEY FK_2E76A48821B47B45');
        $this->addSql('DROP INDEX IDX_2E76A48821B47B45 ON waste');
        $this->addSql('DROP INDEX IDX_2E76A48811A842B9 ON waste');
        $this->addSql('ALTER TABLE waste DROP waste_type_category_id, DROP waste_type_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE purchases ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE purchases ADD CONSTRAINT FK_AA6431FE12469DE2 FOREIGN KEY (category_id) REFERENCES waste_type_category (id)');
        $this->addSql('CREATE INDEX IDX_AA6431FE12469DE2 ON purchases (category_id)');
        $this->addSql('ALTER TABLE waste ADD waste_type_category_id INT DEFAULT NULL, ADD waste_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE waste ADD CONSTRAINT FK_2E76A48811A842B9 FOREIGN KEY (waste_type_category_id) REFERENCES waste_type_category (id)');
        $this->addSql('ALTER TABLE waste ADD CONSTRAINT FK_2E76A48821B47B45 FOREIGN KEY (waste_type_id) REFERENCES waste_type (id)');
        $this->addSql('CREATE INDEX IDX_2E76A48821B47B45 ON waste (waste_type_id)');
        $this->addSql('CREATE INDEX IDX_2E76A48811A842B9 ON waste (waste_type_category_id)');
    }
}
