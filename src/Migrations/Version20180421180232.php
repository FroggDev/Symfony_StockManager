<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180421180232 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product ADD footprint DOUBLE PRECISION DEFAULT NULL, CHANGE nutriscore nutriscore CHAR(1), CHANGE energy_unit energy_unit CHAR(5), CHANGE level_fat_unit level_fat_unit CHAR(2), CHANGE level_satured_fat_unit level_satured_fat_unit CHAR(2), CHANGE level_carbohydrate_unit level_carbohydrate_unit CHAR(2), CHANGE level_sugar_unit level_sugar_unit CHAR(2), CHANGE level_dietary_fiber_unit level_dietary_fiber_unit CHAR(2), CHANGE level_proteins_unit level_proteins_unit CHAR(2), CHANGE level_salt_unit level_salt_unit CHAR(2), CHANGE level_sodium_unit level_sodium_unit CHAR(2), CHANGE level_silica_unit level_silica_unit CHAR(2), CHANGE level_bicarbonate_unit level_bicarbonate_unit CHAR(2), CHANGE level_potassium_unit level_potassium_unit CHAR(2), CHANGE level_chloride_unit level_chloride_unit CHAR(2), CHANGE level_calcium_unit level_calcium_unit CHAR(2), CHANGE level_magnesium_unit level_magnesium_unit CHAR(2), CHANGE level_nitrates_unit level_nitrates_unit CHAR(2), CHANGE level_sulfates_unit level_sulfates_unit CHAR(2), CHANGE footprint_unit footprint_unit CHAR(2)');
        $this->addSql('ALTER TABLE country CHANGE alpha3 alpha3 CHAR(3)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE country CHANGE alpha3 alpha3 CHAR(3) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE product DROP footprint, CHANGE nutriscore nutriscore CHAR(1) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE energy_unit energy_unit CHAR(5) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_fat_unit level_fat_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_satured_fat_unit level_satured_fat_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_carbohydrate_unit level_carbohydrate_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_sugar_unit level_sugar_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_dietary_fiber_unit level_dietary_fiber_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_proteins_unit level_proteins_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_salt_unit level_salt_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_sodium_unit level_sodium_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_silica_unit level_silica_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_bicarbonate_unit level_bicarbonate_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_potassium_unit level_potassium_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_chloride_unit level_chloride_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_calcium_unit level_calcium_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_magnesium_unit level_magnesium_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_nitrates_unit level_nitrates_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE level_sulfates_unit level_sulfates_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE footprint_unit footprint_unit CHAR(2) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
