## Popis databázového modelu pro správu filmových projekcí

Tato databáze slouží k uchovávání informací o filmech, jejich promítání, uživatelích, rezervacích a dalších souvisejících údajích.

### Hlavní entity a jejich atributy

#### `film` (Filmy)
- Ukládá informace o filmech.
- Atributy: `id`, `name`, `length`, `releaseDate`, `description`, `image`, `FK_genre`.

#### `genre` (Žánr)
- Obsahuje různé filmové žánry.
- Atributy: `id`, `name`.
- Vazba 1:N s `film` (každý film patří do jednoho žánru).

#### `language` (Jazyky)
- Uchovává seznam jazyků, ve kterých může být film dabován nebo opatřen titulky.
- Atributy: `id`, `language`.

#### `film_has_dubbing` (Dabování filmů)
- Propojuje filmy s dostupnými dabovanými jazyky.
- Atributy: `FK_film`, `language_id`.

#### `film_has_subtitles` (Titulky k filmům)
- Určuje, v jakých jazycích má film dostupné titulky.
- Atributy: `FK_film`, `FK_language`.

#### `film_screening` (Projekce filmů)
- Reprezentuje jednotlivá promítání filmů.
- Atributy: `id`, `dateTime`, `FK_hall`, `FK_film`.

#### `hall` (Sály)
- Uchovává informace o sálech v kině.
- Atributy: `id`.
- Vazba 1:N s `film_screening` (v jednom sále probíhá více promítání).

#### `seat` (Sedadla)
- Obsahuje informace o sedadlech v sálech.
- Atributy: `id`, `seatNumber`, `rowNumber`, `FK_hall`.

#### `user` (Uživatelé)
- Uchovává informace o registrovaných uživatelích.
- Atributy: `id`, `firstName`, `lastName`, `email`, `password`, `FK_role`.

#### `role` (Role uživatelů)
- Definuje role uživatelů v systému (např. administrátor, běžný uživatel).
- Atributy: `id`, `name`.

#### `review` (Recenze filmů)
- Umožňuje uživatelům hodnotit a recenzovat filmy.
- Atributy: `id`, `text`, `stars`, `FK_user`, `FK_film`.

#### `booking` (Rezervace vstupenek)
- Uchovává informace o rezervacích provedených uživateli.
- Atributy: `id`, `FK_user`, `FK_screening`.

#### `booking_has_seat` (Rezervovaná sedadla)
- Propojuje rezervace s konkrétními sedadly.
- Atributy: `FK_booking`, `FK_seat`.

### Vazby mezi entitami
- Každý **film** patří do jednoho **žánru** (1:N).
- Každý **film** může mít více verzí dabingu a titulků (M:N).
- Každý **film_screening** probíhá v jednom **sále**, ale v jednom sále může být více promítání (N:1).
- Každé **sedadlo** patří do jednoho **sálu** (N:1).
- Každý **uživatel** může psát recenze (N:M s `review`).
- Každý **uživatel** může provádět více **rezervací** (1:N).
- Každá **rezervace** může obsahovat více **sedadel** (M:N).