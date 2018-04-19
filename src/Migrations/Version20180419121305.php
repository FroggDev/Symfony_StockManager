<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180419121305 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE stock (id BIGINT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock_products (id BIGINT AUTO_INCREMENT NOT NULL, stock_id BIGINT DEFAULT NULL, product_id BIGINT DEFAULT NULL, date_creation DATETIME NOT NULL, date_expire DATETIME DEFAULT NULL, INDEX IDX_FC7C0E18DCD6110 (stock_id), INDEX IDX_FC7C0E184584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE stock_products ADD CONSTRAINT FK_FC7C0E18DCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id)');
        $this->addSql('ALTER TABLE stock_products ADD CONSTRAINT FK_FC7C0E184584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product CHANGE nutriscore nutriscore CHAR(1), CHANGE energy_unit energy_unit CHAR(5), CHANGE level_fat_unit level_fat_unit CHAR(2), CHANGE level_satured_fat_unit level_satured_fat_unit CHAR(2), CHANGE level_carbohydrate_unit level_carbohydrate_unit CHAR(2), CHANGE level_sugar_unit level_sugar_unit CHAR(2), CHANGE level_dietary_fiber_unit level_dietary_fiber_unit CHAR(2), CHANGE level_proteins_unit level_proteins_unit CHAR(2), CHANGE level_salt_unit level_salt_unit CHAR(2), CHANGE level_sodium_unit level_sodium_unit CHAR(2), CHANGE level_silica_unit level_silica_unit CHAR(2), CHANGE level_bicarbonate_unit level_bicarbonate_unit CHAR(2), CHANGE level_potassium_unit level_potassium_unit CHAR(2), CHANGE level_chloride_unit level_chloride_unit CHAR(2), CHANGE level_calcium_unit level_calcium_unit CHAR(2), CHANGE level_magnesium_unit level_magnesium_unit CHAR(2), CHANGE level_nitrates_unit level_nitrates_unit CHAR(2), CHANGE level_sulfates_unit level_sulfates_unit CHAR(2), CHANGE footprint_unit footprint_unit CHAR(2)');
        $this->addSql('ALTER TABLE country CHANGE alpha3 alpha3 CHAR(3)');
        $this->addSql('ALTER TABLE user ADD stock_id BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649DCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649DCD6110 ON user (stock_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE stock_products DROP FOREIGN KEY FK_FC7C0E18DCD6110');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649DCD6110');
        $this->addSql('DROP TABLE stock');
        $this->addSql('DROP TABLE stock_products');
        $this->addSql('ALTER TABLE country CHANGE alpha3 alpha3 CHAR(3) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE product CHANGE nutriscore nutriscore CHAR(1) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE energy_unit energy_unit CHAR(5) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_fat_unit level_fat_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_satured_fat_unit level_satured_fat_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_carbohydrate_unit level_carbohydrate_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_sugar_unit level_sugar_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_dietary_fiber_unit level_dietary_fiber_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_proteins_unit level_proteins_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_salt_unit level_salt_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_sodium_unit level_sodium_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_silica_unit level_silica_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_bicarbonate_unit level_bicarbonate_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_potassium_unit level_potassium_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_chloride_unit level_chloride_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_calcium_unit level_calcium_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_magnesium_unit level_magnesium_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_nitrates_unit level_nitrates_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_sulfates_unit level_sulfates_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE footprint_unit footprint_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('DROP INDEX UNIQ_8D93D649DCD6110 ON user');
        $this->addSql('ALTER TABLE user DROP stock_id');
    }
}
