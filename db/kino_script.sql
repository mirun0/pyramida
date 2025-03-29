DROP DATABASE IF EXISTS cinema;
CREATE DATABASE IF NOT EXISTS cinema DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci;
USE cinema;

CREATE TABLE IF NOT EXISTS role (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	name VARCHAR(30) NOT NULL,
    PRIMARY KEY (id)
) ENGINE = MyISAM;

CREATE INDEX idx_role_name ON role(name);

CREATE TABLE IF NOT EXISTS user (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	firstName VARCHAR(50) NOT NULL,
	lastName VARCHAR(50) NOT NULL,
	email VARCHAR(320) NOT NULL,
	password VARCHAR(64) NOT NULL,
	FK_role INT UNSIGNED REFERENCES role(id),
	PRIMARY KEY (id)
) ENGINE = MyISAM;

CREATE INDEX idx_user_email ON user(email(191));

CREATE TABLE IF NOT EXISTS hall (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (id)
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS genre (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	name VARCHAR(30) NOT NULL,
	PRIMARY KEY (id)
) ENGINE = MyISAM;

CREATE INDEX idx_genre_name ON genre(name);

CREATE TABLE IF NOT EXISTS film (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	name VARCHAR(60) NOT NULL,
	length INT UNSIGNED NOT NULL,
	releaseDate DATE NOT NULL,
	description LONGTEXT NOT NULL,
	image VARCHAR(255) NOT NULL,
	FK_genre INT UNSIGNED REFERENCES gendre(id),
	PRIMARY KEY (id)
) ENGINE = MyISAM;

CREATE INDEX idx_film_name ON film(name);

CREATE TABLE IF NOT EXISTS language (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	language VARCHAR(50) NOT NULL,
	PRIMARY KEY (id)
) ENGINE = MyISAM;

CREATE INDEX idx_language_language ON language(language);

CREATE TABLE IF NOT EXISTS film_has_dubbing (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	FK_film INT UNSIGNED REFERENCES film(id),
	FK_language INT UNSIGNED REFERENCES language(id),
	PRIMARY KEY (id)
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS film_has_subtitles (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	FK_film INT UNSIGNED REFERENCES film(id),
	FK_language INT UNSIGNED REFERENCES language(id),
	PRIMARY KEY (id)
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS film_screening (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	dateTime DATETIME NOT NULL,
	FK_hall INT UNSIGNED REFERENCES hall(id),
	FK_film INT UNSIGNED REFERENCES film(id),
	FK_film_has_dubbing INT UNSIGNED REFERENCES film_has_dubbing(id),
	FK_film_has_subtitles INT UNSIGNED REFERENCES film_has_subtitles(id),
	PRIMARY KEY (id)
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS booking (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	FK_user INT UNSIGNED REFERENCES user(id),
	FK_screening INT UNSIGNED REFERENCES film_screening(id),
	PRIMARY KEY (id)
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS seat (
	id INT UNSIGNED NOT NULL,
	FK_hall INT UNSIGNED REFERENCES hall(id),
	PRIMARY KEY (id, FK_hall)
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS booking_has_seat (
	FK_booking INT UNSIGNED NOT NULL REFERENCES booking(id),
	FK_seat INT UNSIGNED NOT NULL REFERENCES seat(id),
	PRIMARY KEY (FK_booking, FK_seat)
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS review (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	text LONGTEXT NOT NULL,
	stars INT NOT NULL,
	FK_user INT UNSIGNED REFERENCES user(id),
	FK_film INT UNSIGNED REFERENCES film(id),
	PRIMARY KEY (id)
) ENGINE = MyISAM;

INSERT INTO role(name) VALUES 
	('admin'), ('film_manager'), ('user');

INSERT INTO user(firstName, lastName, email, password, FK_role) VALUES 
	('Jan', 'Novák', 'admin', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 0),
    ('Petr', 'Svoboda', 'film_manager', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 1),
    ('Eva', 'Dvořáková', 'eva.dvorakova@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Lucie', 'Černá', 'lucie.cerna@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Karel', 'Procházka', 'karel.prochazka@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Marie', 'Veselá', 'marie.vesela@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Adam', 'Král', 'adam.kral@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Tereza', 'Benešová', 'tereza.benesova@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Jakub', 'Fiala', 'jakub.fiala@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Michaela', 'Sedláčková', 'michaela.sedlackova@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Ondřej', 'Malý', 'ondrej.maly@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Barbora', 'Kučerová', 'barbora.kucerova@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Filip', 'Horák', 'filip.horak@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Veronika', 'Marešová', 'veronika.maresova@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('David', 'Pokorný', 'david.pokorny@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Anna', 'Kratochvílová', 'anna.kratochvilova@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Tomáš', 'Němec', 'tomas.nemec@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Simona', 'Pavlíčková', 'simona.pavlickova@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Radek', 'Urban', 'radek.urban@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Kateřina', 'Dostálová', 'katerina.dostalova@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Lukáš', 'Polák', 'lukas.polak@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Nikola', 'Soukupová', 'nikola.soukupova@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Dominik', 'Šimek', 'dominik.simek@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Hana', 'Zemanová', 'hana.zemanova@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Martin', 'Vávra', 'martin.vavra@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Patrik', 'Mach', 'patrik.mach@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Alena', 'Šťastná', 'alena.stastna@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Jiří', 'Kopecký', 'jiri.kopecky@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Ivana', 'Vlčková', 'ivana.vlckova@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Roman', 'Holý', 'roman.holy@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Gabriela', 'Matoušková', 'gabriela.matouskova@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Viktor', 'Tůma', 'viktor.tuma@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Eliška', 'Kočová', 'eliska.kocova@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Pavel', 'Bartoš', 'pavel.bartos@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Jana', 'Havelková', 'jana.havelkova@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Štěpán', 'Klimeš', 'stepan.klimes@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Monika', 'Vítková', 'monika.vitkova@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Vojtěch', 'Hruška', 'vojtech.hruska@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Kristýna', 'Bláhová', 'kristyna.blahova@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Robert', 'Kříž', 'robert.kriz@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Adéla', 'Krejčová', 'adela.krejcova@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Marek', 'Štěpánek', 'marek.stepanek@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Denisa', 'Kvasničková', 'denisa.kvasnickova@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Jaroslav', 'Růžička', 'jaroslav.ruzicka@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Veronika', 'Beránková', 'veronika.berankova@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Daniel', 'Vašíček', 'daniel.vasicek@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Tereza', 'Horká', 'tereza.horka@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Kamil', 'Svatoš', 'kamil.svatos@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Andrea', 'Pospíšilová', 'andrea.pospisilova@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2),
    ('Miloslav', 'Hájek', 'miloslav.hajek@example.com', "$2y$10$8hOPtQ8wXNXEqqQddgEyF.1GwMQpv6tN63FTI6MEdg52OoizK2MeO", 2);
        
INSERT INTO hall() VALUES 
	(), (), (), ();

INSERT INTO seat(id, FK_hall) VALUES 
    -- Sál 1
    (1, 1), (2, 1), (3, 1), (4, 1), (5, 1), (6, 1), (7, 1), (8, 1), (9, 1), (10, 1),
    (11, 1), (12, 1), (13, 1), (14, 1), (15, 1), (16, 1), (17, 1), (18, 1), (19, 1), (20, 1),
    (21, 1), (22, 1), (23, 1), (24, 1), (25, 1), (26, 1), (27, 1), (28, 1), (29, 1), (30, 1),
    -- Sál 2
    (1, 2), (2, 2), (3, 2), (4, 2), (5, 2), (6, 2), (7, 2), (8, 2), (9, 2), (10, 2),
	(11, 2), (12, 2), (13, 2), (14, 2), (15, 2), (16, 2), (17, 2), (18, 2), (19, 2), (20, 2),
	(21, 2), (22, 2), (23, 2), (24, 2), (25, 2), (26, 2), (27, 2), (28, 2), (29, 2), (30, 2),
    -- Sál 3
    (1, 3), (2, 3), (3, 3), (4, 3), (5, 3), (6, 3), (7, 3), (8, 3), (9, 3), (10, 3),
	(11, 3), (12, 3), (13, 3), (14, 3), (15, 3), (16, 3), (17, 3), (18, 3), (19, 3), (20, 3),
	(21, 3), (22, 3), (23, 3), (24, 3), (25, 3), (26, 3), (27, 3), (28, 3), (29, 3), (30, 3),
    -- Sál 4
    (1, 4), (2, 4), (3, 4), (4, 4), (5, 4), (6, 4), (7, 4), (8, 4), (9, 4), (10, 4),
	(11, 4), (12, 4), (13, 4), (14, 4), (15, 4), (16, 4), (17, 4), (18, 4), (19, 4), (20, 4),
	(21, 4), (22, 4), (23, 4), (24, 4), (25, 4), (26, 4), (27, 4), (28, 4), (29, 4), (30, 4);

INSERT INTO genre(name) VALUES 
    ('Akční'),
    ('Dobrodružný'),
    ('Sci-Fi'),
    ('Horor'),
    ('Fantasy'),
    ('Drama'),
    ('Komedie'),
    ('Romantický'),
    ('Animovaný'),
    ('Dokumentární');

INSERT INTO film(name, length, releaseDate, description, image, FK_genre) VALUES 
    ('Avengers: Endgame', 181, '2019-04-26', 'Epické zakončení série Avengers.', 'avengers.jpg', 1),
    ('Interstellar', 169, '2014-11-07', 'Vesmírná cesta napříč černými děrami.', 'interstellar.jpg', 3),
    ('Paranormal Activity', 86, '2007-09-14', 'Horor o nadpřirozených událostech.', 'paranormal.jpg', 4),
    ('Pán prstenů: Návrat krále', 201, '2003-12-17', 'Velkolepé zakončení fantasy trilogie.', 'lotr.jpg', 5),
    ('Titanic', 195, '1997-12-19', 'Nezapomenutelný příběh lásky na potápějící se lodi.', 'titanic.jpg', 8),
    ('Shrek', 90, '2001-05-18', 'Animovaná pohádka o zlobrovi a jeho přátelích.', 'shrek.jpg', 9),
    ('Joker', 122, '2019-10-04', 'Příběh proměny Arthura Flecka v Jokera.', 'joker.jpg', 6),
    ('Forrest Gump', 142, '1994-07-06', 'Osudy jednoduchého muže s obrovským srdcem.', 'forrest.jpg', 6),
    ('Gladiátor', 155, '2000-05-05', 'Římský generál se stane gladiátorem.', 'gladiator.jpg', 1),
    ('Mad Max: Fury Road', 120, '2015-05-15', 'Postapokalyptická honička na plný plyn.', 'madmax.jpg', 1),
    ('Indiana Jones a Poslední křížová výprava', 127, '1989-05-24', 'Dobrodružství slavného archeologa.', 'indiana.jpg', 2),
    ('To', 135, '2017-09-08', 'Adaptace hororového bestselleru Stephena Kinga.', 'it.jpg', 4),
    ('Strážci galaxie', 121, '2014-08-01', 'Vesmírní hrdinové zachraňují galaxii.', 'guardians.jpg', 3),
    ('Nedotknutelní', 112, '2011-11-02', 'Francouzské drama o přátelství bohatého muže a pečovatele.', 'intouchables.jpg', 6),
    ('Mamma Mia!', 108, '2008-07-03', 'Hudební film s hity od ABBY.', 'mammamia.jpg', 8),
    ('Toy Story 4', 100, '2019-06-21', 'Pokračování dobrodružství oblíbených hraček.', 'toystory.jpg', 9),
    ('Bohemian Rhapsody', 134, '2018-10-24', 'Životní příběh Freddieho Mercuryho.', 'bohemian.jpg', 10),
    ('Planeta opic', 112, '1968-04-03', 'Sci-fi klasika o planetě ovládané opicemi.', 'planetaopic.jpg', 3),
    ('Schindlerův seznam', 195, '1993-11-30', 'Silný příběh o Oskaru Schindlerovi.', 'schindler.jpg', 6),
    ('Deadpool', 108, '2016-02-12', 'Drsná komiksová komedie s antihrdinou.', 'deadpool.jpg', 7);

INSERT INTO language(language) VALUES 
	('Čeština'),
    ('Angličtina'),
    ('Němčina'),
    ('Francouzština'),
    ('Španělština');

INSERT INTO film_has_dubbing(FK_film, FK_language) VALUES 
    (1, 2),  -- Avengers: Endgame (Anglicky)
    (1, 1),  -- Avengers: Endgame (Česky)
    (2, 2),  -- Interstellar (Anglicky)
    (3, 1),  -- Paranormal Activity (Česky)
    (4, 2),  -- Pán prstenů: Návrat krále (Anglicky)
    (4, 1),  -- Pán prstenů: Návrat krále (Česky)
    (5, 2),  -- Titanic (Anglicky)
    (6, 1),  -- Shrek (Česky)
    (6, 2),  -- Shrek (Anglicky)
    (7, 2),  -- Joker (Anglicky)
    (8, 2),  -- Forrest Gump (Anglicky)
    (8, 1),  -- Forrest Gump (Česky)
    (9, 2),  -- Gladiátor (Anglicky)
    (10, 2), -- Mad Max: Fury Road (Anglicky)
    (10, 1), -- Mad Max: Fury Road (Česky)
    (11, 2), -- Indiana Jones (Anglicky)
    (12, 1), -- To (Česky)
    (12, 2), -- To (Anglicky)
    (13, 2), -- Strážci galaxie (Anglicky)
    (14, 3), -- Nedotknutelní (Francouzsky)
    (14, 1), -- Nedotknutelní (Česky)
    (15, 2), -- Mamma Mia! (Anglicky)
    (15, 1), -- Mamma Mia! (Česky)
    (16, 1), -- Toy Story 4 (Česky)
    (17, 2), -- Bohemian Rhapsody (Anglicky)
    (18, 2), -- Planeta opic (Anglicky)
    (19, 2), -- Schindlerův seznam (Anglicky)
    (20, 2); -- Deadpool (Anglicky)

INSERT INTO film_has_subtitles(FK_film, FK_language) VALUES 
    (1, 1),  -- Avengers: Endgame (Titulky v češtině)
    (1, 2),  -- Avengers: Endgame (Titulky v angličtině)
    (2, 1),  -- Interstellar (Titulky v češtině)
    (4, 1),  -- Pán prstenů: Návrat krále (Titulky v češtině)
    (4, 2),  -- Pán prstenů: Návrat krále (Titulky v angličtině)
    (5, 1),  -- Titanic (Titulky v češtině)
    (5, 2),  -- Titanic (Titulky v angličtině)
    (7, 1),  -- Joker (Titulky v češtině)
    (7, 2),  -- Joker (Titulky v angličtině)
    (9, 1),  -- Gladiátor (Titulky v češtině)
    (9, 2),  -- Gladiátor (Titulky v angličtině)
    (11, 1), -- Indiana Jones (Titulky v češtině)
    (13, 1), -- Strážci galaxie (Titulky v češtině)
    (14, 1), -- Nedotknutelní (Titulky v češtině)
    (14, 2), -- Nedotknutelní (Titulky v angličtině)
    (17, 1), -- Bohemian Rhapsody (Titulky v češtině)
    (18, 1), -- Planeta opic (Titulky v češtině)
    (18, 2), -- Planeta opic (Titulky v angličtině)
    (19, 1), -- Schindlerův seznam (Titulky v češtině)
    (19, 2); -- Schindlerův seznam (Titulky v angličtině)

INSERT INTO film_screening(dateTime, FK_hall, FK_film, FK_film_has_dubbing, FK_film_has_subtitles) VALUES 
    -- Avengers Endgame (sál 1)
    ('2025-05-16 14:00:00', 1, 1, 1, 1),
    ('2025-05-17 18:00:00', 1, 1, 2, NULL),
    ('2025-05-18 20:00:00', 2, 1, 1, 2),
    -- Interstellar (sál 2)
    ('2025-05-16 17:00:00', 2, 2, 2, 1),
    ('2025-05-18 21:00:00', 3, 2, 2, NULL),
    ('2025-05-19 15:00:00', 1, 2, 1, 2),
    -- Paranormal Activity (sál 3)
    ('2025-05-16 20:00:00', 3, 3, 3, NULL),
    ('2025-05-17 22:00:00', 1, 3, 3, 1),
    -- Pán prstenů (sál 4)
    ('2025-05-16 15:30:00', 4, 4, 4, 2),
    ('2025-05-18 19:00:00', 2, 4, 4, NULL),
    ('2025-05-19 17:30:00', 3, 4, 5, 1),
    -- Titanic (sál 2)
    ('2025-05-16 19:00:00', 2, 5, 5, 1),
    ('2025-05-17 21:30:00', 3, 5, 6, NULL),
    ('2025-05-19 14:00:00', 1, 5, 5, 2),
    -- Shrek (sál 1)
    ('2025-05-16 21:30:00', 1, 6, 6, NULL),
    ('2025-05-18 16:00:00', 2, 6, 6, 1),
    -- Joker (sál 3)
    ('2025-05-16 16:00:00', 3, 7, 7, 1),
    ('2025-05-18 22:00:00', 4, 7, 7, NULL),
    -- Forrest Gump (sál 4)
    ('2025-05-16 19:30:00', 4, 8, 8, NULL),
    ('2025-05-17 15:30:00', 3, 8, 8, 1),
    -- Gladiátor (sál 2)
    ('2025-05-16 22:00:00', 2, 9, 9, 2),
    ('2025-05-18 18:30:00', 1, 9, 9, NULL),
    -- Mad Max (sál 4)
    ('2025-05-17 17:30:00', 4, 10, 10, 1),
    ('2025-05-19 20:30:00', 2, 10, 10, 2),
    -- Indiana Jones (sál 3)
    ('2025-05-17 21:00:00', 3, 11, 11, NULL),
    ('2025-05-18 17:00:00', 4, 11, 11, 1);
    
INSERT INTO booking(FK_user, FK_screening) VALUES
    -- Avengers Endgame
    (5, 1), (12, 1), (33, 1), (21, 1), (45, 2), (9, 2), (27, 2), (11, 3), (39, 3), 
    -- Interstellar
    (14, 4), (8, 4), (3, 4), (50, 5), (19, 5), (31, 6), (6, 6), 
    -- Paranormal Activity
    (1, 7), (42, 7), (37, 7), (29, 8), (40, 8), (17, 8),
    -- Pán prstenů
    (22, 9), (30, 9), (44, 9), (2, 10), (41, 10), (16, 11),
    -- Titanic
    (13, 12), (32, 12), (23, 12), (7, 13), (28, 13), (35, 14),
    -- Shrek
    (26, 15), (18, 15), (46, 15), (10, 16), (38, 16),
    -- Joker
    (47, 17), (4, 17), (15, 18),
    -- Forrest Gump
    (24, 19), (48, 19), (25, 19), (20, 20), (36, 20),
    -- Gladiátor
    (50, 21), (34, 21), (5, 22),
    -- Mad Max
    (49, 23), (9, 23), (43, 24),
    -- Indiana Jones
    (11, 25), (33, 25), (31, 26);

INSERT INTO booking_has_seat(FK_booking, FK_seat) VALUES 
    -- Každá rezervace dostane 1–4 sedadla
    (1, 1), (1, 2), (1, 3),
    (2, 5),
    (3, 10), (3, 11), (3, 12),
    (4, 15), (4, 16),
    (5, 20), (5, 21), (5, 22), (5, 23),
    (6, 6),
    (7, 25), (7, 26),
    (8, 30),
    (9, 3), (9, 4), (9, 5),
    (10, 8), (10, 9),
    (11, 13), (11, 14),
    (12, 18),
    (13, 24), (13, 27), (13, 28),
    (14, 29),
    (15, 17), (15, 19),
    (16, 1), (16, 2), (16, 3), (16, 4),
    (17, 5), (17, 6),
    (18, 7), (18, 8), (18, 9),
    (19, 10),
    (20, 11), (20, 12), (20, 13),
    (21, 14), (21, 15),
    (22, 16),
    (23, 17), (23, 18), (23, 19),
    (24, 20), (24, 21), (24, 22),
    (25, 23),
    (26, 24), (26, 25), (26, 26),
    (27, 27), (27, 28),
    (28, 29), (28, 30),
    (29, 1), (29, 2), (29, 3),
    (30, 4), (30, 5),
    (31, 6), (31, 7), (31, 8),
    (32, 9), (32, 10),
    (33, 11), (33, 12), (33, 13),
    (34, 14), (34, 15),
    (35, 16), (35, 17),
    (36, 18), (36, 19),
    (37, 20), (37, 21),
    (38, 22), (38, 23),
    (39, 24), (39, 25),
    (40, 26), (40, 27),
    (41, 28), (41, 29),
    (42, 30),
    (43, 1), (43, 2), (43, 3),
    (44, 4), (44, 5), (44, 6),
    (45, 7), (45, 8), (45, 9),
    (46, 10), (46, 11), (46, 12),
    (47, 13), (47, 14), (47, 15),
    (48, 16), (48, 17), (48, 18),
    (49, 19), (49, 20), (49, 21),
    (50, 22), (50, 23), (50, 24),
    (51, 25), (51, 26), (51, 27),
    (52, 28), (52, 29), (52, 30),
    (53, 1), (53, 2), (53, 3),
    (54, 4), (54, 5), (54, 6),
    (55, 7), (55, 8), (55, 9),
    (56, 10), (56, 11), (56, 12);

-- SET FOREIGN_KEY_CHECKS = 0;
INSERT INTO review(text, stars, FK_user, FK_film) VALUES
    -- Film 1
    ('Skvělý film! Výborné herecké výkony a napínavý děj.', 5, 3, 1),
    ('Skvělý film.', 5, 7, 1),
    -- Film 2
    ('Perfektní atmosféra, napětí až do konce!', 5, 8, 2),
    ('Trochu pomalejší rozjezd, ale celkově super.', 4, 15, 2),
    ('Pecička.', 5, 8, 2),
    -- Film 3
    ('Děj byl trochu předvídatelný, ale jinak fajn.', 3, 6, 3),
    ('Výborné efekty a zajímavá zápletka!', 5, 19, 3),
    -- Film 4
    ('Trochu nuda, ale herci to zachránili.', 2, 11, 4),
    ('Líbilo se mi to, určitě doporučuji.', 4, 22, 4),
    -- Film 5
    ('Mistrné zpracování a emoce na maximum!', 5, 1, 5),
    ('Film měl své slabé chvilky, ale celkově dobré.', 4, 10, 5),
    -- Film 6
    ('Skvělý soundtrack a vizuální efekty.', 5, 5, 6),
    ('Příběh mě moc neoslovil, ale bylo to dobře natočené.', 3, 14, 6),
    -- Film 7
    ('Nejlepší film roku, nemám co dodat!', 5, 9, 7),
    ('Čekal jsem víc originality.', 3, 17, 7),
    -- Film 8
    ('Temná atmosféra, která mě úplně pohltila.', 5, 2, 8),
    ('Zbytečně dlouhé, ale jinak dobré.', 3, 13, 8),
    -- Film 9
    ('Tenhle film mě bavil od začátku do konce.', 5, 4, 9),
    ('Slabší scénář, ale výborná kamera.', 3, 20, 9),
    -- Film 10
    ('Geniální film! Už dlouho jsem neviděl něco tak dobrého.', 5, 16, 10),
    ('Docela dobré, ale chyběla mi tam akce.', 4, 18, 10),
    -- Film 11
    ('Originální příběh a skvělá atmosféra.', 5, 12, 11),
    ('Hodně zajímavý film, ale mohl být kratší.', 4, 21, 11),
    -- Film 12
    ('Tento film mě úplně dostal, úžasné zpracování!', 5, 7, 12),
    ('Moc pomalé tempo, ale jinak fajn.', 3, 15, 12),
    -- Film 13
    ('Děsivé a napínavé, rozhodně doporučuji!', 5, 6, 13),
    ('Nebylo to špatné, ale viděl jsem lepší.', 3, 19, 13),
    -- Film 14
    ('Emotivní a nádherně natočené.', 5, 10, 14),
    ('Zajímavý koncept, ale realizace pokulhávala.', 3, 2, 14),
    -- Film 15
    ('Jednoznačně nejlepší sci-fi poslední doby.', 5, 1, 15),
    ('Dobrý film, ale očekával jsem něco jiného.', 4, 22, 15),
    -- Film 16
    ('Akce od začátku do konce, paráda!', 5, 5, 16),
    ('Nuda, ale asi jsem neměl správnou náladu.', 2, 17, 16),
    -- Film 17
    ('Vynikající herecké výkony a zajímavý děj.', 5, 8, 17),
    ('Trochu moc dlouhé, ale jinak fajn.', 4, 14, 17),
    -- Film 18
    ('Romantika v tom nejlepším podání.', 5, 9, 18),
    ('Předvídatelné, ale příjemné na koukání.', 3, 18, 18),
    -- Film 19
    ('Naprosto šílené, ale skvělé!', 5, 3, 19),
    ('Zajímavý vizuální styl, ale slabší scénář.', 4, 20, 19),
    -- Film 20
    ('Dojemné a silné, určitě doporučuji.', 5, 11, 20),
    ('Hodně přehnané, ale dalo se na to dívat.', 3, 12, 20);
-- SET FOREIGN_KEY_CHECKS = 1;

-- Pohled 1 - filmy podle data vydání
CREATE VIEW latest_films AS
SELECT 
    film.id AS film_id,
    film.name AS film_name,
    film.releaseDate,
    film.description,
    film.image,
    genre.name AS genre_name,
    AVG(review.stars) AS average_rating
FROM film
JOIN genre ON film.FK_genre = genre.id
LEFT JOIN review ON film.id = review.FK_film
GROUP BY film.id
ORDER BY film.releaseDate DESC;

-- select * from latest_films;

-- Pohled 2 - nejlépe hodnocené filmy
CREATE VIEW top_rated_films AS
SELECT 
    film.id AS film_id,
    film.name AS film_name,
    film.description,
    film.image,
    genre.name AS genre_name,
    AVG(review.stars) AS average_rating
FROM film
JOIN genre ON film.FK_genre = genre.id
LEFT JOIN review ON film.id = review.FK_film
GROUP BY film.id
ORDER BY average_rating DESC;
    
-- select * from top_rated_films;
   
-- Pohled 3 - filmy podle žánrů
CREATE VIEW films_by_genre AS
SELECT 
    genre.name AS genre_name,
    film.id,
    film.name AS film_name,
    film.description,
    film.image,
    film.releaseDate
FROM film
JOIN genre ON film.FK_genre = genre.id
ORDER BY genre.name, film.releaseDate DESC;

-- select * from films_by_genre;

-- Pohled 4 - nejbližší promítání
CREATE VIEW upcoming_screenings AS
SELECT 
    film_screening.id AS screening_id,
    film_screening.dateTime,
    film.id AS film_id,
    film.name AS film_name,
    hall.id AS hall_id,
    dub_lan.language AS dubbing_language,
    sub_lan.language AS subtitles_language
FROM film_screening
JOIN film ON film_screening.FK_film = film.id
JOIN hall ON film_screening.FK_hall = hall.id
LEFT JOIN film_has_dubbing ON film_screening.FK_film_has_dubbing = film_has_dubbing.id
LEFT JOIN language dub_lan ON film_has_dubbing.FK_language = dub_lan.id
LEFT JOIN film_has_subtitles ON film_screening.FK_film_has_subtitles = film_has_subtitles.id
LEFT JOIN language sub_lan ON film_has_subtitles.FK_language = sub_lan.id
WHERE film_screening.dateTime > NOW()
ORDER BY film_screening.dateTime ASC;

-- select * from upcoming_screenings;

-- Procedura 1 - přidání nového filmu
DELIMITER //

CREATE PROCEDURE add_film (
	film_name VARCHAR(60),
    film_length INT UNSIGNED,
    release_date DATE,
    description LONGTEXT,
    image_url VARCHAR(255),
    genre_id INT UNSIGNED
) BEGIN
	INSERT INTO film(name, length, releaseDate, description, image, FK_genre)
    VALUES (film_name, film_length, release_date, description, image_url, genre_id);
END //

DELIMITER ;
-- CALL add_film('Nový film', 120, '2025-12-01', 'Popis nového filmu.', 'obrazek.jpg', 1);
-- select * from film;

-- Procedura 2 - rezervace sedadel na promítání
DELIMITER //
CREATE PROCEDURE reserve_seats(
    p_user_id INT UNSIGNED,
    p_screening_id INT UNSIGNED,
    p_seat_list TEXT
)
BEGIN
    DECLARE v_booking_id INT UNSIGNED;
    DECLARE v_seat_id INT UNSIGNED;
    DECLARE v_position INT DEFAULT 1;
    DECLARE v_next_comma INT;
    DECLARE v_seat VARCHAR(10);
    DECLARE v_seat_exists INT;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Transaction rolled back due to an error';
    END;
    
    START TRANSACTION;
    
    INSERT INTO booking (FK_user, FK_screening) VALUES (p_user_id, p_screening_id);
    SET v_booking_id = LAST_INSERT_ID();
    
    seat_loop: LOOP
        SET v_next_comma = LOCATE(',', p_seat_list, v_position);
        IF v_next_comma = 0 THEN
            SET v_seat = SUBSTRING(p_seat_list, v_position);
        ELSE
            SET v_seat = SUBSTRING(p_seat_list, v_position, v_next_comma - v_position);
        END IF;

        SELECT COUNT(*) INTO v_seat_exists FROM seat WHERE id = CAST(v_seat AS UNSIGNED);
        IF v_seat_exists = 0 THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid seat ID';
        END IF;

        IF v_seat <> '' THEN
            INSERT INTO booking_has_seat (FK_booking, FK_seat)
            VALUES (v_booking_id, CAST(v_seat AS UNSIGNED));
        END IF;
        
        IF v_next_comma = 0 THEN
            LEAVE seat_loop;
        END IF;
        
        SET v_position = v_next_comma + 1;
    END LOOP;
    COMMIT;
    
END //
DELIMITER ;

-- uživatel s id 1, na promítání 1 rezervuje (1,3,5,30) sedadla
CALL reserve_seats(1, 1, '1,3,5,30');

SELECT 
    booking.id AS booking_id,
    booking.FK_user AS user_id,
    booking.FK_screening AS screening_id,
    booking_has_seat.FK_seat AS seat_id
FROM booking
LEFT JOIN booking_has_seat ON booking.id = booking_has_seat.FK_booking
where booking.FK_user = 1
ORDER BY booking.id DESC;

-- Procedura 3 - všechna promítání filmu
DELIMITER //

CREATE PROCEDURE upcoming_screenings_for_film(film_id INT UNSIGNED)
BEGIN
    SELECT 
        film_screening.id AS screening_id,
        film_screening.dateTime,
        hall.id AS hall_id,
        COUNT(DISTINCT seat.id) AS total_seats,
        COUNT(DISTINCT seat.id) - COUNT(DISTINCT booking_has_seat.FK_seat) AS available_seats
    FROM film_screening
    JOIN hall ON film_screening.FK_hall = hall.id
    JOIN seat ON seat.FK_hall = hall.id
    LEFT JOIN booking ON booking.FK_screening = film_screening.id
    LEFT JOIN booking_has_seat ON booking.id = booking_has_seat.FK_booking
    WHERE film_screening.FK_film = film_id AND film_screening.dateTime > NOW()
    GROUP BY film_screening.id, film_screening.dateTime, hall.id
    ORDER BY film_screening.dateTime;
END //

DELIMITER ;

-- CALL upcoming_screenings_for_film(1);

-- Funkce 1 - získání hodnocení v jsonu
DELIMITER //

CREATE FUNCTION get_film_reviews(film_id INT UNSIGNED)
RETURNS TEXT
reads sql data
BEGIN
    DECLARE result TEXT;
    SELECT 
        JSON_ARRAYAGG(JSON_OBJECT(
			'review_text', review.text,
			'review_stars', review.stars,
			'user_first_name', user.firstName,
			'user_last_name', user.lastName
			)
        )
    INTO result
    FROM review
    JOIN user ON review.FK_user = user.id
    WHERE review.FK_film = film_id;
    RETURN result;
END //

DELIMITER ;
-- SELECT get_film_reviews(1);

-- Funkce 2 - získání celého jména uživatele
DELIMITER //

CREATE FUNCTION get_user_full_name(user_id INT UNSIGNED)
RETURNS VARCHAR(120)
reads sql data
BEGIN
    DECLARE full_name VARCHAR(101);
    
    SELECT CONCAT(firstName, ' ', lastName)
    INTO full_name
    FROM user
    WHERE user.id = user_id;

    RETURN full_name;
END //

DELIMITER ;
-- SELECT get_user_full_name(2);

-- Funkce 3 - změna uživatelova hesla
DELIMITER //

CREATE FUNCTION change_user_password(
    user_id INT UNSIGNED,
    new_password VARCHAR(256)
)
RETURNS BOOLEAN
reads sql data
BEGIN
    DECLARE rowsUpdated INT;

    UPDATE user
    SET password = new_password
    WHERE id = user_id;
    
    RETURN ROW_COUNT() > 0;
END //

DELIMITER ;

-- select change_user_password(1, "nove heslo");
-- select * from user;

-- Trigger 1 - po smazání rezervace smaže data ve spojovací tabulce
DELIMITER //

CREATE TRIGGER after_delete_booking
AFTER DELETE ON booking
FOR EACH ROW
BEGIN
    DELETE FROM booking_has_seat WHERE FK_booking = OLD.id;
END //

DELIMITER ;

delete from booking where id = 57;
-- select * from booking_has_seat where FK_booking = 57;


-- Trigger 2 - po smazání filmu smaže data v promítání
DELIMITER //

CREATE TRIGGER after_delete_film
AFTER DELETE ON film
FOR EACH ROW
BEGIN
    DELETE FROM film_screening WHERE FK_film = OLD.id;
END //

DELIMITER ;

-- DELETE FROM film WHERE id = 1;
-- SELECT * FROM film_screening WHERE FK_film = 1;




