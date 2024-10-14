<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241014142743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE anime (id SERIAL NOT NULL, entry_author_id INT NOT NULL, studio_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, number_of_episodes INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_130459428E1C7D59 ON anime (entry_author_id)');
        $this->addSql('CREATE INDEX IDX_13045942446F285F ON anime (studio_id)');
        $this->addSql('CREATE TABLE genre (id SERIAL NOT NULL, author_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_835033F8F675F31B ON genre (author_id)');
        $this->addSql('CREATE TABLE genre_anime (genre_id INT NOT NULL, anime_id INT NOT NULL, PRIMARY KEY(genre_id, anime_id))');
        $this->addSql('CREATE INDEX IDX_AE0246874296D31F ON genre_anime (genre_id)');
        $this->addSql('CREATE INDEX IDX_AE024687794BBE89 ON genre_anime (anime_id)');
        $this->addSql('CREATE TABLE studio (id SERIAL NOT NULL, entry_author_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4A2B07B68E1C7D59 ON studio (entry_author_id)');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, uuid UUID NOT NULL, status BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME ON "user" (username)');
        $this->addSql('COMMENT ON COLUMN "user".uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE anime ADD CONSTRAINT FK_130459428E1C7D59 FOREIGN KEY (entry_author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE anime ADD CONSTRAINT FK_13045942446F285F FOREIGN KEY (studio_id) REFERENCES studio (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE genre ADD CONSTRAINT FK_835033F8F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE genre_anime ADD CONSTRAINT FK_AE0246874296D31F FOREIGN KEY (genre_id) REFERENCES genre (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE genre_anime ADD CONSTRAINT FK_AE024687794BBE89 FOREIGN KEY (anime_id) REFERENCES anime (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE studio ADD CONSTRAINT FK_4A2B07B68E1C7D59 FOREIGN KEY (entry_author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE anime DROP CONSTRAINT FK_130459428E1C7D59');
        $this->addSql('ALTER TABLE anime DROP CONSTRAINT FK_13045942446F285F');
        $this->addSql('ALTER TABLE genre DROP CONSTRAINT FK_835033F8F675F31B');
        $this->addSql('ALTER TABLE genre_anime DROP CONSTRAINT FK_AE0246874296D31F');
        $this->addSql('ALTER TABLE genre_anime DROP CONSTRAINT FK_AE024687794BBE89');
        $this->addSql('ALTER TABLE studio DROP CONSTRAINT FK_4A2B07B68E1C7D59');
        $this->addSql('DROP TABLE anime');
        $this->addSql('DROP TABLE genre');
        $this->addSql('DROP TABLE genre_anime');
        $this->addSql('DROP TABLE studio');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
