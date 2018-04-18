<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180418084211 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE country (id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL, code INT NOT NULL, alpha2 CHAR(2) NOT NULL, alpha3 CHAR(3), name VARCHAR(45) NOT NULL, UNIQUE INDEX alpha2 (alpha2), UNIQUE INDEX alpha3 (alpha3), UNIQUE INDEX code_unique (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE additive (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE alergy (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE brand (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE certification (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE origin (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE packaging (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trace (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id BIGINT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, barcode BIGINT NOT NULL, emb_code VARCHAR(100) DEFAULT NULL, name VARCHAR(150) NOT NULL, common_name VARCHAR(150) NOT NULL, date_creation DATETIME NOT NULL, quantity VARCHAR(100) DEFAULT NULL, picture VARCHAR(100) DEFAULT NULL, producer_page VARCHAR(150) DEFAULT NULL, ingredient_picture VARCHAR(100) DEFAULT NULL, ingredients VARCHAR(100) DEFAULT NULL, nutrition_picture VARCHAR(100) DEFAULT NULL, nutriscore CHAR(1), serving_size VARCHAR(100) DEFAULT NULL, energy DOUBLE PRECISION DEFAULT NULL, energy_unit CHAR(5), level_fat DOUBLE PRECISION DEFAULT NULL, level_fat_unit CHAR(2), level_satured_fat DOUBLE PRECISION DEFAULT NULL, level_satured_fat_unit CHAR(2), level_carbohydrate DOUBLE PRECISION DEFAULT NULL, level_carbohydrate_unit CHAR(2), level_sugar DOUBLE PRECISION DEFAULT NULL, level_sugar_unit CHAR(2), level_dietary_fiber DOUBLE PRECISION DEFAULT NULL, level_dietary_fiber_unit CHAR(2), level_proteins DOUBLE PRECISION DEFAULT NULL, level_proteins_unit CHAR(2), level_salt DOUBLE PRECISION DEFAULT NULL, level_salt_unit CHAR(2), level_sodium DOUBLE PRECISION DEFAULT NULL, level_sodium_unit CHAR(2), level_alcohol DOUBLE PRECISION DEFAULT NULL, level_silica DOUBLE PRECISION DEFAULT NULL, level_silica_unit CHAR(2), level_bicarbonate DOUBLE PRECISION DEFAULT NULL, level_bicarbonate_unit CHAR(2), level_potassium DOUBLE PRECISION DEFAULT NULL, level_potassium_unit CHAR(2), level_chloride DOUBLE PRECISION DEFAULT NULL, level_chloride_unit CHAR(2), level_calcium DOUBLE PRECISION DEFAULT NULL, level_calcium_unit CHAR(2), level_magnesium DOUBLE PRECISION DEFAULT NULL, level_magnesium_unit CHAR(2), level_nitrates DOUBLE PRECISION DEFAULT NULL, level_nitrates_unit CHAR(2), level_sulfates DOUBLE PRECISION DEFAULT NULL, level_sulfates_unit CHAR(2), footprint_unit CHAR(2), UNIQUE INDEX UNIQ_D34A04AD97AE0266 (barcode), INDEX IDX_D34A04ADA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products_countries (product_id BIGINT NOT NULL, country_id SMALLINT UNSIGNED NOT NULL, INDEX IDX_F1B088B4584665A (product_id), INDEX IDX_F1B088BF92F3E70 (country_id), PRIMARY KEY(product_id, country_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products_certifications (product_id BIGINT NOT NULL, certification_id INT NOT NULL, INDEX IDX_65E286724584665A (product_id), INDEX IDX_65E28672CB47068A (certification_id), PRIMARY KEY(product_id, certification_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products_origins (product_id BIGINT NOT NULL, origin_id INT NOT NULL, INDEX IDX_3AE2F9634584665A (product_id), INDEX IDX_3AE2F96356A273CC (origin_id), PRIMARY KEY(product_id, origin_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products_places (product_id BIGINT NOT NULL, place_id INT NOT NULL, INDEX IDX_789CE77F4584665A (product_id), INDEX IDX_789CE77FDA6A219 (place_id), PRIMARY KEY(product_id, place_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products_packagings (product_id BIGINT NOT NULL, packaging_id INT NOT NULL, INDEX IDX_543CCBB74584665A (product_id), INDEX IDX_543CCBB74E7B3801 (packaging_id), PRIMARY KEY(product_id, packaging_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products_categories (product_id BIGINT NOT NULL, category_id INT NOT NULL, INDEX IDX_E8ACBE764584665A (product_id), INDEX IDX_E8ACBE7612469DE2 (category_id), PRIMARY KEY(product_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products_brands (product_id BIGINT NOT NULL, brand_id INT NOT NULL, INDEX IDX_F891CF1E4584665A (product_id), INDEX IDX_F891CF1E44F5D008 (brand_id), PRIMARY KEY(product_id, brand_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products_alergies (product_id BIGINT NOT NULL, alergy_id INT NOT NULL, INDEX IDX_CB2EE4594584665A (product_id), INDEX IDX_CB2EE4591114A35C (alergy_id), PRIMARY KEY(product_id, alergy_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products_traces (product_id BIGINT NOT NULL, trace_id INT NOT NULL, INDEX IDX_3CDD8C8A4584665A (product_id), INDEX IDX_3CDD8C8ABE0D4B70 (trace_id), PRIMARY KEY(product_id, trace_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products_additives (product_id BIGINT NOT NULL, additive_id INT NOT NULL, INDEX IDX_3049AF4A4584665A (product_id), INDEX IDX_3049AF4A384C93F0 (additive_id), PRIMARY KEY(product_id, additive_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, email VARCHAR(255) NOT NULL, token VARCHAR(100) DEFAULT NULL, token_validity DATETIME DEFAULT NULL, password VARCHAR(64) NOT NULL, date_inscription DATETIME NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', last_connexion DATETIME DEFAULT NULL, status INT NOT NULL, date_closed DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE products_countries ADD CONSTRAINT FK_F1B088B4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE products_countries ADD CONSTRAINT FK_F1B088BF92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE products_certifications ADD CONSTRAINT FK_65E286724584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE products_certifications ADD CONSTRAINT FK_65E28672CB47068A FOREIGN KEY (certification_id) REFERENCES certification (id)');
        $this->addSql('ALTER TABLE products_origins ADD CONSTRAINT FK_3AE2F9634584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE products_origins ADD CONSTRAINT FK_3AE2F96356A273CC FOREIGN KEY (origin_id) REFERENCES origin (id)');
        $this->addSql('ALTER TABLE products_places ADD CONSTRAINT FK_789CE77F4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE products_places ADD CONSTRAINT FK_789CE77FDA6A219 FOREIGN KEY (place_id) REFERENCES place (id)');
        $this->addSql('ALTER TABLE products_packagings ADD CONSTRAINT FK_543CCBB74584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE products_packagings ADD CONSTRAINT FK_543CCBB74E7B3801 FOREIGN KEY (packaging_id) REFERENCES packaging (id)');
        $this->addSql('ALTER TABLE products_categories ADD CONSTRAINT FK_E8ACBE764584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE products_categories ADD CONSTRAINT FK_E8ACBE7612469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE products_brands ADD CONSTRAINT FK_F891CF1E4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE products_brands ADD CONSTRAINT FK_F891CF1E44F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('ALTER TABLE products_alergies ADD CONSTRAINT FK_CB2EE4594584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE products_alergies ADD CONSTRAINT FK_CB2EE4591114A35C FOREIGN KEY (alergy_id) REFERENCES alergy (id)');
        $this->addSql('ALTER TABLE products_traces ADD CONSTRAINT FK_3CDD8C8A4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE products_traces ADD CONSTRAINT FK_3CDD8C8ABE0D4B70 FOREIGN KEY (trace_id) REFERENCES trace (id)');
        $this->addSql('ALTER TABLE products_additives ADD CONSTRAINT FK_3049AF4A4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE products_additives ADD CONSTRAINT FK_3049AF4A384C93F0 FOREIGN KEY (additive_id) REFERENCES additive (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE products_countries DROP FOREIGN KEY FK_F1B088BF92F3E70');
        $this->addSql('ALTER TABLE products_additives DROP FOREIGN KEY FK_3049AF4A384C93F0');
        $this->addSql('ALTER TABLE products_alergies DROP FOREIGN KEY FK_CB2EE4591114A35C');
        $this->addSql('ALTER TABLE products_brands DROP FOREIGN KEY FK_F891CF1E44F5D008');
        $this->addSql('ALTER TABLE products_categories DROP FOREIGN KEY FK_E8ACBE7612469DE2');
        $this->addSql('ALTER TABLE products_certifications DROP FOREIGN KEY FK_65E28672CB47068A');
        $this->addSql('ALTER TABLE products_origins DROP FOREIGN KEY FK_3AE2F96356A273CC');
        $this->addSql('ALTER TABLE products_packagings DROP FOREIGN KEY FK_543CCBB74E7B3801');
        $this->addSql('ALTER TABLE products_places DROP FOREIGN KEY FK_789CE77FDA6A219');
        $this->addSql('ALTER TABLE products_traces DROP FOREIGN KEY FK_3CDD8C8ABE0D4B70');
        $this->addSql('ALTER TABLE products_countries DROP FOREIGN KEY FK_F1B088B4584665A');
        $this->addSql('ALTER TABLE products_certifications DROP FOREIGN KEY FK_65E286724584665A');
        $this->addSql('ALTER TABLE products_origins DROP FOREIGN KEY FK_3AE2F9634584665A');
        $this->addSql('ALTER TABLE products_places DROP FOREIGN KEY FK_789CE77F4584665A');
        $this->addSql('ALTER TABLE products_packagings DROP FOREIGN KEY FK_543CCBB74584665A');
        $this->addSql('ALTER TABLE products_categories DROP FOREIGN KEY FK_E8ACBE764584665A');
        $this->addSql('ALTER TABLE products_brands DROP FOREIGN KEY FK_F891CF1E4584665A');
        $this->addSql('ALTER TABLE products_alergies DROP FOREIGN KEY FK_CB2EE4594584665A');
        $this->addSql('ALTER TABLE products_traces DROP FOREIGN KEY FK_3CDD8C8A4584665A');
        $this->addSql('ALTER TABLE products_additives DROP FOREIGN KEY FK_3049AF4A4584665A');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADA76ED395');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE additive');
        $this->addSql('DROP TABLE alergy');
        $this->addSql('DROP TABLE brand');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE certification');
        $this->addSql('DROP TABLE origin');
        $this->addSql('DROP TABLE packaging');
        $this->addSql('DROP TABLE place');
        $this->addSql('DROP TABLE trace');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE products_countries');
        $this->addSql('DROP TABLE products_certifications');
        $this->addSql('DROP TABLE products_origins');
        $this->addSql('DROP TABLE products_places');
        $this->addSql('DROP TABLE products_packagings');
        $this->addSql('DROP TABLE products_categories');
        $this->addSql('DROP TABLE products_brands');
        $this->addSql('DROP TABLE products_alergies');
        $this->addSql('DROP TABLE products_traces');
        $this->addSql('DROP TABLE products_additives');
        $this->addSql('DROP TABLE user');
    }
}
