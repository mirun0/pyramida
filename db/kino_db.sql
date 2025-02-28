CREATE DATABASE cinema
CHAR SET utf16 COLLATE utf16_czech_ci;

USE cinema;

CREATE TABLE Genre (
	id int unsigned PRIMARY KEY AUTO_INCREMENT,
    name varchar(30) NOT NULL /* UPRAVIT DELKU VARCHARU */
) engine = MyISAM;

CREATE TABLE Film (
	id int unsigned PRIMARY KEY AUTO_INCREMENT,
    name varchar(60) NOT NULL, /* UPRAVIT DELKU VARCHARU */
    length int unsigned NOT NULL,
    FK_genre int unsigned NOT NULL,
    FOREIGN KEY (FK_genre) REFERENCES Genre(id)
) engine = MyISAM;

CREATE TABLE Language (
	id int unsigned PRIMARY KEY AUTO_INCREMENT,
    language varchar(50) NOT NULL /* UPRAVIT DELKU VARCHARU */
) engine = MyISAM;

CREATE TABLE Film_has_dubbing (
	id int unsigned PRIMARY KEY AUTO_INCREMENT,
    FK_film int unsigned NOT NULL,
    FK_language int unsigned NOT NULL,
    FOREIGN KEY (FK_film) REFERENCES Film(id),
	FOREIGN KEY (FK_language) REFERENCES Language(id)
) engine = MyISAM;

CREATE TABLE Film_has_subtitles (
	id int unsigned PRIMARY KEY AUTO_INCREMENT,
    FK_film int unsigned NOT NULL,
    FK_language int unsigned NOT NULL,
    FOREIGN KEY (FK_film) REFERENCES Film(id),
	FOREIGN KEY (FK_language) REFERENCES Language(id)
) engine = MyISAM;

CREATE TABLE Hall (
	id int unsigned PRIMARY KEY AUTO_INCREMENT
) engine = MyISAM;

CREATE TABLE Film_screening (
	id int unsigned PRIMARY KEY AUTO_INCREMENT,
	FK_film int unsigned NOT NULL,
    FK_hall int unsigned NOT NULL,
    dateTime datetime NOT NULL,
    UNIQUE (FK_hall, dateTime),
    FOREIGN KEY (FK_film) REFERENCES Film(id),
    FOREIGN KEY (FK_hall) REFERENCES Hall(id)
) engine = MyISAM;

CREATE TABLE Seat (
	id int unsigned PRIMARY KEY AUTO_INCREMENT,
	seatNumber int unsigned NOT NULL,
	rowNumber int unsigned NOT NULL,
	FK_hall int unsigned NOT NULL,
    UNIQUE (seatNumber, rowNumber, FK_hall),
    FOREIGN KEY (FK_hall) REFERENCES Hall(id)
) engine = MyISAM;

CREATE TABLE Booking (
	id int unsigned PRIMARY KEY AUTO_INCREMENT,
	FK_FilmScreening int unsigned NOT NULL,
    -- FK_seat int unsigned NOT NULL,
    FOREIGN KEY (FK_FilmScreening) REFERENCES Film_screening(id)
    -- FOREIGN KEY (FK_seat) REFERENCES Seat(id)
) engine = MyISAM;

CREATE TABLE Booking_has_seat (
	id int unsigned PRIMARY KEY AUTO_INCREMENT,
	FK_booking int unsigned NOT NULL,
    FK_seat int unsigned NOT NULL,
    FOREIGN KEY (FK_booking) REFERENCES Booking(id),
    FOREIGN KEY (FK_seat) REFERENCES Seat(id)
) engine = MyISAM;

CREATE TABLE Customer (
	id int unsigned PRIMARY KEY AUTO_INCREMENT,
    FirstName varchar(50) NOT NULL, /* UPRAVIT DELKU VARCHARU */
    LastName varchar(50) NOT NULL
    -- FK_membershipLevel int unsigned NOT NULL,
    -- FOREIGN KEY (FK_membershipLevel) REFERENCES Membership_level(id)
) engine = MyISAM;

/* CREATE TABLE Membership_level (
	id int unsigned PRIMARY KEY AUTO_INCREMENT,
    name varchar(30) NOT NULL
); */

-- INSERTS
INSERT INTO Genre (id, name) VALUES 
	(1, "Action"),
	(2, "Adventure"),
	(3, "Animation"),
	(4, "Biography"),
	(5, "Comedy"),
	(6, "Crime"),
	(7, "Documentary"),
	(8, "Drama"),
	(9, "Family"),
	(10, "Fantasy"),
	(11, "History"),
	(12, "Horror"),
	(13, "Musical"),
	(14, "Mystery"),
	(15, "Romance"),
	(16, "Science Fiction"),
	(17, "Sports"),
	(18, "Thriller"),
	(19, "War"),
	(20, "Western");

INSERT INTO Film (name, length, FK_genre) VALUES
	("Inception", 148, 16),  -- Science Fiction
	("The Godfather", 175, 6),  -- Crime
	("The Dark Knight", 152, 1),  -- Action
	("Forrest Gump", 142, 8),  -- Drama
	("Titanic", 195, 15),  -- Romance
	("The Conjuring", 112, 12),  -- Horror
	("Interstellar", 169, 16),  -- Science Fiction
	("Gladiator", 155, 19),  -- War
	("The Lion King", 88, 3),  -- Animation
	("Finding Nemo", 100, 3),  -- Animation
	("Jaws", 124, 18),  -- Thriller
	("The Shining", 146, 12),  -- Horror
	("Braveheart", 178, 19),  -- War
	("Pulp Fiction", 154, 6),  -- Crime
	("The Matrix", 136, 16),  -- Science Fiction
	("Schindler’s List", 195, 11),  -- History
	("Avengers: Endgame", 181, 1),  -- Action
	("The Grand Budapest Hotel", 99, 5),  -- Comedy
	("Casablanca", 102, 15),  -- Romance
	("Mad Max: Fury Road", 120, 1),  -- Action
	("The Revenant", 156, 2),  -- Adventure
	("The Wolf of Wall Street", 180, 4),  -- Biography
	("Bohemian Rhapsody", 134, 4),  -- Biography
	("The Social Network", 120, 4),  -- Biography
	("Inside Out", 95, 3),  -- Animation
	("Coco", 105, 3),  -- Animation
	("La La Land", 128, 13),  -- Musical
	("Les Misérables", 158, 13),  -- Musical
	("The Greatest Showman", 105, 13),  -- Musical
	("The Silence of the Lambs", 118, 14),  -- Mystery
	("Se7en", 127, 14),  -- Mystery
	("The Sixth Sense", 107, 14),  -- Mystery
	("The Irishman", 209, 6),  -- Crime
	("The Departed", 151, 6),  -- Crime
	("A Quiet Place", 90, 12),  -- Horror
	("It", 135, 12),  -- Horror
	("Dunkirk", 106, 19),  -- War
	("1917", 119, 19),  -- War
	("Rocky", 120, 17),  -- Sports
	("Creed", 133, 17),  -- Sports
	("The Blind Side", 129, 17),  -- Sports
	("The Good, the Bad and the Ugly", 178, 20),  -- Western
	("True Grit", 110, 20);  -- Western

INSERT INTO Language (language) VALUES 
	("English"),
	("Spanish"),
	("French"),
	("German"),
	("Italian"),
	("Japanese"),
	("Mandarin"),
	("Hindi"),
	("Russian"),
	("Korean");
    
INSERT INTO Film_has_dubbing (FK_film, FK_language) VALUES
	(1, 1),  -- Inception (English)
	(2, 1),  -- The Godfather (English)
	(3, 1),  -- The Dark Knight (English)
	(4, 1),  -- Forrest Gump (English)
	(5, 1),  -- Titanic (English)
	(6, 1),  -- The Conjuring (English)
	(7, 1),  -- Interstellar (English)
	(8, 1),  -- Gladiator (English)
	(9, 1),  -- The Lion King (English)
	(10, 1),  -- Finding Nemo (English)
	(11, 1),  -- Jaws (English)
	(12, 1),  -- The Shining (English)
	(13, 1),  -- Braveheart (English)
	(14, 1),  -- Pulp Fiction (English)
	(15, 1),  -- The Matrix (English)
	(16, 1),  -- Schindler’s List (English)
	(16, 3),  -- Schindler’s List (French)
	(16, 4),  -- Schindler’s List (German)
	(17, 1),  -- Avengers: Endgame (English)
	(18, 1),  -- The Grand Budapest Hotel (English)
	(18, 3),  -- The Grand Budapest Hotel (French)
	(19, 1),  -- Casablanca (English)
	(19, 3),  -- Casablanca (French)
	(20, 1),  -- Mad Max: Fury Road (English)
	(21, 1),  -- The Revenant (English)
	(21, 3),  -- The Revenant (French)
	(22, 1),  -- The Wolf of Wall Street (English)
	(23, 1),  -- Bohemian Rhapsody (English)
	(24, 1),  -- The Social Network (English)
	(25, 1),  -- Inside Out (English)
	(26, 1),  -- Coco (English)
	(26, 2),  -- Coco (Spanish)
	(27, 1),  -- La La Land (English)
	(28, 1),  -- Les Misérables (English)
	(28, 3),  -- Les Misérables (French)
	(29, 1),  -- The Greatest Showman (English)
	(30, 1),  -- The Silence of the Lambs (English)
	(31, 1),  -- Se7en (English)
	(32, 1),  -- The Sixth Sense (English)
	(33, 1),  -- The Irishman (English)
	(34, 1),  -- The Departed (English)
	(35, 1),  -- A Quiet Place (English)
	(36, 1),  -- It (English)
	(37, 1),  -- Dunkirk (English)
	(38, 1),  -- 1917 (English)
	(39, 1),  -- Rocky (English)
	(40, 1),  -- Creed (English)
	(41, 1),  -- The Blind Side (English)
	(42, 1),  -- The Good, the Bad and the Ugly (English)
	(42, 5),  -- The Good, the Bad and the Ugly (Italian)
	(43, 1);  -- True Grit (English)

SELECT name, length FROM Film;


-- DROP DATABASE cinema;