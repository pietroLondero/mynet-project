<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240705075251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, tag VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE url (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, url VARCHAR(255) NOT NULL, time_insert INT NOT NULL, INDEX IDX_F47645AEA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE urls_tags (url_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_87534E0781CFDAE7 (url_id), INDEX IDX_87534E07BAD26311 (tag_id), PRIMARY KEY(url_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_following (user_id INT NOT NULL, following_user_id INT NOT NULL, INDEX IDX_715F0007A76ED395 (user_id), INDEX IDX_715F00071896F387 (following_user_id), PRIMARY KEY(user_id, following_user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_likes (user_id INT NOT NULL, url_id INT NOT NULL, INDEX IDX_AB08B525A76ED395 (user_id), INDEX IDX_AB08B52581CFDAE7 (url_id), PRIMARY KEY(user_id, url_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE url ADD CONSTRAINT FK_F47645AEA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE urls_tags ADD CONSTRAINT FK_87534E0781CFDAE7 FOREIGN KEY (url_id) REFERENCES url (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE urls_tags ADD CONSTRAINT FK_87534E07BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_following ADD CONSTRAINT FK_715F0007A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_following ADD CONSTRAINT FK_715F00071896F387 FOREIGN KEY (following_user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_likes ADD CONSTRAINT FK_AB08B525A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_likes ADD CONSTRAINT FK_AB08B52581CFDAE7 FOREIGN KEY (url_id) REFERENCES url (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE url DROP FOREIGN KEY FK_F47645AEA76ED395');
        $this->addSql('ALTER TABLE urls_tags DROP FOREIGN KEY FK_87534E0781CFDAE7');
        $this->addSql('ALTER TABLE urls_tags DROP FOREIGN KEY FK_87534E07BAD26311');
        $this->addSql('ALTER TABLE user_following DROP FOREIGN KEY FK_715F0007A76ED395');
        $this->addSql('ALTER TABLE user_following DROP FOREIGN KEY FK_715F00071896F387');
        $this->addSql('ALTER TABLE user_likes DROP FOREIGN KEY FK_AB08B525A76ED395');
        $this->addSql('ALTER TABLE user_likes DROP FOREIGN KEY FK_AB08B52581CFDAE7');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE url');
        $this->addSql('DROP TABLE urls_tags');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_following');
        $this->addSql('DROP TABLE user_likes');
    }
}
